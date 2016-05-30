<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //$_POST["birthday"] - дата рождения
    //direction - ID направления
    //date_from - дата начала тура
    if ($_POST["birthday"] && $_POST["direction"] && $_POST["date_from"] && $_POST["hotel"] && $_POST["type"]) {

        $age = getFullAgeByDate($_POST["birthday"],$_POST["date_from"]);
        $mathMethods = getMathMethods($age,$_POST["direction"], "N" , $_POST["hotel"], $_POST["type"]);
    ?>
     <option value="N">Без скидки</option>
    <?  
        if (count($mathMethods) > 0) {
            foreach ($mathMethods as $id=>$method) { ?>
            <option value="<?=$method["ID"]?>" <?if ($id==0){?> selected="selected"<?}?>><?=$method["NAME"]?></option>
            <? } 
        }  
    }
?>