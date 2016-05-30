<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
if ($_POST["id"]) {
     $arBus = CIBlockElement::GetList(array(),array("ID"=>$_POST["id"]), false, false, array("PROPERTY_P_SCHEME"))->Fetch();
     //echo $arBus["PROPERTY_P_SCHEME_VALUE"]; 
     echo get_bus_scheme($arBus["PROPERTY_P_SCHEME_VALUE"]);
}
?>