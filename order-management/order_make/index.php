<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?> 
<? $APPLICATION->IncludeComponent("bustour:make.order", ".default", array(
	"TOUR_ID" => $_REQUEST["TOUR_ID"]
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>