<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    if(!CModule::IncludeModule("iblock"))
        return;

    $arComponentParameters = array(
        "PARAMETERS" => array(
            "TOUR_ID" => array(
                "PARENT" => "BASE",
                "NAME" => "ID тура",
                "TYPE" => "STRING",
                "DEFAULT" => '={$_REQUEST["TOUR_ID"]}',
            ),
        ),
    );
    
    //arshow($arComponentParameters);
?>