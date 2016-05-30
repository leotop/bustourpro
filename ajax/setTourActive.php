<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?

    if ($_POST["active"] && $_POST["tour"] > 0) {
        $el = new CIBlockElement;  
        $arLoadProductArray = Array(      
            "ACTIVE" => $_POST["active"],         
        );                             
        $PRODUCT_ID = $_POST["tour"];  
        $res = $el->Update($PRODUCT_ID, $arLoadProductArray);   
    }

?>