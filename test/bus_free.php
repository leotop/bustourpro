<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("тест");
?>
<?
       $schemes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>15),false, false, array("ID", "NAME", "PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION","PROPERTY_COMPANY"));
    while ($arScheme = $schemes->Fetch()) {
        
        $el = new CIblockElement;

        $PROP = array();
        $arScheme["PROPERTY_P_SCHEME_VALUE"] = str_replace("PP","FP",$arScheme["PROPERTY_P_SCHEME_VALUE"]);
        $PROP["P_SCHEME"] = $arScheme["PROPERTY_P_SCHEME_VALUE"]; 
        $PROP["COMPANY"] = getCurrentCompanyID();
        $PROP["BUS_DIRECTION"] = Array("VALUE" => $arScheme["PROPERTY_BUS_DIRECTION_ENUM_ID"]); 

        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // 
            "IBLOCK_SECTION" => false,          // 
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => $arScheme["NAME"],
            "ACTIVE"         => "Y",            //      
        );
            
      $res = $el->Update($arScheme["ID"], $arLoadProductArray);

    }

?>


 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>