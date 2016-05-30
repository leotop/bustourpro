<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Формирование туров");
?><?$APPLICATION->IncludeComponent(
    "bustour:tour.designer",
    "",
    Array(
    ),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
