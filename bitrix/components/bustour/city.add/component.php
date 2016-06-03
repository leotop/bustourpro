<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('iblock')) {
    return false;
}

$company_id = getCurrentCompanyID();
if (!$company_id) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$arResult['error'] = null;
$arResult['message'] = null;

$fd_city = (!empty($_POST['city'])? $_POST['city']: array());
$arProperty = array(
    'NAME' => !empty($fd_city['name'])? $fd_city['name']: '',
    'DIRECTION_ID' => !empty($fd_city['DIRECTION_ID'])? $fd_city['DIRECTION_ID']: '',
    'desc' => !empty($fd_city['name'])? $fd_city['name']: '',
    'photo' => !empty($fd_city['name'])? $fd_city['name']: '',
);


$IBLOCK_ID = 4;
$properties = CIBlock::GetProperties($IBLOCK_ID);
while ($prop_fields = $properties->GetNext())
{
    echo $prop_fields["ID"]." - ".$prop_fields["NAME"]."<br>";
}
die;

//$f = CIBlockElement::GetByID(4);
/*$listEl = CIBlockElement::GetProperty(4, 10);
while ($f = $listEl->GetNext()) {
    __dump($f);
}
die;

$f = CIBlockElement::GetById(19);
__dump($f->getNext(), true);*/
/*
$rsPropertyEnum = CIBlockProperty::GetPropertyEnum(19);
while ($arProperty = $rsPropertyEnum->GetNext()) {
    __dump($arProperty);
}

die;*/

$arProperty = array_map('trim', $arProperty);
if (!empty($_POST['city']) && check_bitrix_sessid()) {
    $el_id = null;
    try {
        $el = new CIBlockElement;
        $PROP = array();
        $PROP['COMAPNY_ID'] = $company_id;
        $arLoadProductArray = Array(
            "IBLOCK_ID" => 4,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $arProperty['name']
        );

        $id = $el->Add($arLoadProductArray);
        if (!$id) {
            throw new sfVException($el->LAST_ERROR);
        }

        sfForm::setSFK('tour_city__message', 'Добавление прошло успешно');
        LocalRedirect('/city/edit.php?id='. $id);
    } catch (sfVException $e) {
        $arResult['error'] = $e->getMessage();
    }
}

$arResult['city'] = $arProperty;

$this->IncludeComponentTemplate();