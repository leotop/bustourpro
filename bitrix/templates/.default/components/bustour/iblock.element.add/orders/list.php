<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//выбираем швблон архив/просто заказы
 if (in_array("orders_archive",explode("/",$APPLICATION->GetCurPage()))) {
  $template = "orders_archive";  
}
else {
  $template = "orders";  
}

$APPLICATION->IncludeComponent("bustour:iblock.element.add.list", $template, $arParams, $component);
?>