<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Управление автобусами");
?>

<?$APPLICATION->IncludeComponent("bustour:tour.escort", "bus_settings", Array(
	
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>