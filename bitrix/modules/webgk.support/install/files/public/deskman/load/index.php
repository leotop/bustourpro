<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php')?>
<?$APPLICATION->IncludeComponent(
    "webgk:support.load", 
    ".default", 
    array(
        "SUPPORT_CRITICALY" => "5",
        "SUPPORT_CRITICALY_SUM" => "10"
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/footer.php')?>