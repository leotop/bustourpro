<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php')?>
<? $APPLICATION->IncludeComponent(
    "webgk:support.balance", 
    ".default", 
    array(
        "COMPONENT_TEMPLATE" => ".default",
        "PAGE_QUANTITY" => "50",
        "HIDE_NULL_BALANCE" => "Y"
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/footer.php')?>