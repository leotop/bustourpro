<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Конструктор туров");
?><?$APPLICATION->IncludeComponent(
    "bustour:tour.designer",
    "",
    Array(
    ),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>