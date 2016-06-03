<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php')?>
<? $APPLICATION->IncludeComponent(
    "webgk:support.transaction", 
    ".default", 
    array(
        "COMPONENT_TEMPLATE" => ".default",
        "PAGE_QUANTITY" => "30",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "3600"
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/footer.php')?>