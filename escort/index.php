<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Для сопровождающих");
?>

<?$APPLICATION->IncludeComponent("bustour:tour.escort","",array());?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>