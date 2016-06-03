<?
use Bitrix\Im as IM;

class CIMStatus
{
	public static $AVAILABLE_STATUSES = Array('online', 'dnd', 'away');
	public static $ONLINE_USERS = null;
	public static $FRIENDS_USERS = null;

	public static function Set($userId, $params)
	{
		$userId = intval($userId);
		if ($userId <= 0)
			return false;

		if (isset($params['STATUS']))
			$params['IDLE'] = null;

		$needToUpdate = false;

		$params = self::PrepareFields($params);
		$res = IM\StatusTable::getById($userId);
		if ($status = $res->fetch())
		{
			foreach ($params as $key => $value)
			{
				$oldValue = is_object($status[$key])? $status[$key]->toString(): $status[$key];
				$newValue = is_object($value)? $value->toString(): $value;
				if ($oldValue != $newValue)
				{
					$status[$key] = $value;
					$needToUpdate = true;
				}
			}

			if ($needToUpdate)
			{
				IM\StatusTable::update($userId, $params);
			}
		}
		else
		{
			$params['USER_ID'] = $userId;
			IM\StatusTable::add($params);

			$needToUpdate = true;
			$status = $params;
		}

		if ($needToUpdate && self::Enable())
		{
			CPullStack::AddShared(Array(
				'module_id' => 'online',
				'command' => 'user_status',
				'expiry' => 120,
				'params' => self::PrepareToPush($status)
			));
		}

		return true;
	}

	public static function SetIdle($userId, $result = true)
	{
		$date = null;
		if ($result)
		{
			$date = new Bitrix\Main\Type\DateTime();
			$date->add('-10 MINUTE');
		}
		CIMStatus::Set($userId, Array('IDLE' => $date));
	}

	public static function SetMobile($userId, $result = true)
	{
		$date = null;
		if ($result)
		{
			$date = new Bitrix\Main\Type\DateTime();
		}
		CIMStatus::Set($userId, Array('MOBILE_LAST_DATE' => $date));
	}

	private static function PrepareToPush($params)
	{
		foreach($params as $key => $value)
		{
			if ($key == 'STATUS')
			{
				$params[$key] = in_array($value, self::$AVAILABLE_STATUSES)? $value: 'online';
			}
			else if (in_array($key, Array('IDLE', 'DESKTOP_LAST_DATE', 'MOBILE_LAST_DATE', 'EVENT_UNTIL_DATE')))
			{
				$params[$key] = $value? $value->getTimestamp(): 0;
			}
			else if ($key == 'COLOR')
			{
				$params[$key] = IM\Color::getColor($value);
			}
			else
			{
				$params[$key] = $value;
			}
		}

		return $params;
	}

	private static function PrepareFields($params)
	{
		$arValues = Array();

		$arFields = IM\StatusTable::getMap();
		foreach($params as $key => $value)
		{
			if (!isset($arFields[$key]))
				continue;

			if ($key == 'STATUS')
			{
				$arValues[$key] = in_array($value, self::$AVAILABLE_STATUSES)? $value: 'online';
			}
			else
			{
				$arValues[$key] = $value;
			}
		}

		return $arValues;
	}

	public static function GetList($arParams = Array())
	{
		if (!is_array($arParams))
			$arParams = Array();

		$arID = Array();
		if (isset($arParams['ID']) && is_array($arParams['ID']) && !empty($arParams['ID']))
		{
			foreach ($arParams['ID'] as $key => $value)
				$arID[] = intval($value);
		}
		else if (isset($arParams['ID']) && intval($arParams['ID']) > 0)
		{
			$arID[] = intval($arParams['ID']);
		}

		global $USER;

		$bBusShowAll = !IsModuleInstalled('intranet') && COption::GetOptionInt('im', 'contact_list_show_all_bus');
		if(!$bBusShowAll && !isset($arParams['SKIP_CHECK']) && !isset($arParams['ID']) && is_object($USER) && $USER->GetID() > 0 && !IsModuleInstalled('intranet'))
		{
			$userId = intval($USER->GetID());
			if (isset(self::$FRIENDS_USERS[$userId]))
			{
				$arID = self::$FRIENDS_USERS[$userId];
			}
			else if (CModule::IncludeModule('socialnetwork') && CSocNetUser::IsFriendsAllowed())
			{
				$arID = Array($userId);
				$dbFriends = CSocNetUserRelations::GetList(array(),array("USER_ID" => $userId, "RELATION" => SONET_RELATIONS_FRIEND), false, false, array("ID", "FIRST_USER_ID", "SECOND_USER_ID"));
				if ($dbFriends)
				{
					while ($arFriends = $dbFriends->Fetch())
					{
						$arID[] = ($userId == $arFriends["FIRST_USER_ID"]) ? $arFriends["SECOND_USER_ID"] : $arFriends["FIRST_USER_ID"];
					}
				}
				self::$FRIENDS_USERS[$userId] = $arID;
			}
		}

		if (!self::$ONLINE_USERS)
		{
			$enable = self::Enable();

			$arUsers = Array();
			$query = new \Bitrix\Main\Entity\Query(\Bitrix\Main\UserTable::getEntity());
			$query->addSelect('ID');
			$query->addSelect('PERSONAL_GENDER');
			if ($enable)
			{
				$query->registerRuntimeField('', new \Bitrix\Main\Entity\ReferenceField('ref', 'Bitrix\Im\StatusTable', array('=this.ID' => 'ref.USER_ID')));
				$query->addSelect('ref.STATUS', 'STATUS')->addSelect('ref.IDLE', 'IDLE')->addSelect('ref.MOBILE_LAST_DATE', 'MOBILE_LAST_DATE');
			}
			$query->addFilter('>LAST_ACTIVITY_DATE', new \Bitrix\Main\DB\SqlExpression(Bitrix\Main\Application::getConnection()->getSqlHelper()->addSecondsToDateTime('-180')));
			$result = $query->exec();

			while ($arUser = $result->fetch())
			{
				$arUsers[$arUser["ID"]] = Array(
					'id' => $arUser["ID"],
					'status' => $enable && in_array($arUser['STATUS'], self::$AVAILABLE_STATUSES)? $arUser['STATUS']: 'online',
					'idle' => $enable && is_object($arUser['IDLE'])? $arUser['IDLE']->getTimestamp(): 0,
					'mobileLastDate' => $enable && is_object($arUser['MOBILE_LAST_DATE'])? $arUser['MOBILE_LAST_DATE']->getTimestamp(): 0,
				);
			}
			self::$ONLINE_USERS = $arUsers;
		}

		$arResult = Array();
		if (empty($arID))
		{
			$arResult = self::$ONLINE_USERS;
		}
		else
		{
			foreach	($arID as $userId)
			{
				$arResult[$userId] = self::$ONLINE_USERS[$userId];
			}
		}

		return Array('users' => $arResult);
	}

	public static function Enable()
	{
		return CModule::IncludeModule('pull') && CPullOptions::GetNginxStatus()? true: false;
	}
}
?>