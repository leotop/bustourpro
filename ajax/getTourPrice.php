<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    if ($_POST["id"] && $_POST["type"] && $_POST["extraplace"] && $_POST["birthday"] && $_POST["method"] ) {
        echo getTourPrice($_POST["id"],$_POST["type"],$_POST["extraplace"],$_POST["birthday"], $_POST["method"]);
    }
?>