<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php')?>
<?$APPLICATION->IncludeComponent(
    "webgk:support.statistic", 
    ".default", 
    array(
        "COMPONENT_TEMPLATE" => ".default",
        "TICKET_DETAIL_PAGE" => "/deskman/?ID=#ID#&edit=1"
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"].'/bitrix/footer.php')?>