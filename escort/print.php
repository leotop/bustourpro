<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Для сопровождающих");
?>
<div id="escort_bus">

    <?
        if ($_GET["print"] == "Y" && intval($_GET["id"]) > 0) {  ?> 
        <script>
            $(function(){
                print();  
            })
        </script>
        <?echo getEscortScheme($_GET["id"],$_GET["type"]);
        } else {header("location:/escort/");}
    ?>
</div>
   
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>