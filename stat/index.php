<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Статистика");
?>
<?     
global $USER;
if (!$USER->IsAdmin()) {die();}
$companyId = 10411;
$users = array();
$usersCount = 0;
$dateFilterFrom = "2015-01-01 00:00:00";


$rsOrders = CIBlockElement::GetList(array("PROPERTY_DATE_FROM"=>"DESC"),array("IBLOCK_CODE"=>"ORDERS", "PROPERTY_COMPANY"=>$companyId, "PROPERTY_STATUS"=>339, "<PROPERTY_DATE_FROM"=>date("Y-m-d 00:00:00"), ">=PROPERTY_DATE_FROM"=>$dateFilterFrom), false, false, array("ID", "PROPERTY_DATE_FROM"));
echo "Всего выполненных заказов в статусе 'Заказ одобрен'<br> в период с ".$dateFilterFrom." по ".date("Y-m-d").": <b>".$rsOrders->SelectedRowsCount()."</b><br><br>";
while($arOrder = $rsOrders->Fetch()) {
    $rsTourist = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"TOURIST", "PROPERTY_ORDER"=>$arOrder["ID"]), false, false, array("ID","NAME"));
    while($arTourist = $rsTourist->Fetch()) {
      $users[$arOrder["ID"]][] = $arTourist["NAME"];
      $usersCount++;
    }
}
echo "Всего туристов отправлено: <b>".$usersCount."</b><br><br>";
arshow($users);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>