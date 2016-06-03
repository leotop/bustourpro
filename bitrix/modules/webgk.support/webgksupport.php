<?
IncludeModuleLangFile(__FILE__);   

$arClasses = array(
    "webgk_support" => "install/index.php",  
    "GKSupport" => "classes/general/gksupport.php",
    "GKSupportDiscounts" => "classes/mysql/discounts.php",
    "GKSupportServices" => "classes/mysql/services.php",
    "GKSupportUserGroups" => "classes/mysql/usergroups.php",
    "GKSupportUsers" => "classes/mysql/users.php",
    "GKSupportTransactions" => "classes/mysql/transactions.php",  
    "GKSupportFiles" => "classes/mysql/files.php",
    "GKSupportSpentTime" => "classes/mysql/spent_time.php",
    "GKSupportTicketPayment" => "classes/mysql/ticket_payment.php"
);

CModule::AddAutoloadClasses("webgk.support", $arClasses);    

?>