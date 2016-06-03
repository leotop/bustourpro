<?
    IncludeModuleLangFile(__FILE__);

    if ( $USER->IsAdmin() ) {
        return array(
            'parent_menu' => 'global_menu_services',
            'section'     => 'webgk_support',
            'sort'        => 210,
            'url'         => 'webgk_support_admin.php',
            'text'        => GetMessage('WEBGK_SUPPORT_ADMIN_MENU_TEXT'),
            'title'       => GetMessage('WEBGK_SUPPORT_ADMIN_MENU_TITLE'),
            'icon'        => 'webgksupport_menu_icon',
            'module_id'   => 'webgk.support',
            'items_id'    => 'webgk_support',
            'items' => array(
                array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_SERVICES"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_SERVICES_TITLE"),
                    "url" => "webgk_support_services.php",
                    "sort" => 100
                ),                  
                 array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_USERS"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_USERS_TITLE"),
                    "url" => "webgk_support_users.php",
                    "sort" => 200
                ),
                 array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_USER_GROUPS"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_USER_GROUPS_TITLE"),
                    "url" => "webgk_support_user_groups.php",
                    "sort" => 300
                ),
                 array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_DISCOUNTS"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_DISCOUNTS_TITLE"),
                    "url" => "webgk_support_discounts.php",
                    "sort" => 400
                ),
                array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_TRANSACTIONS"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_TRANSACTIONS_TITLE"),
                    "url" => "webgk_support_transactions.php",
                    "sort" => 500
                ),
                array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_SPENT_TIME"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_SPENT_TIME_TITLE"),
                    "url" => "webgk_support_spent_time.php",
                    "sort" => 600
                ),
                array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_FILES"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_FILES_TITLE"),
                    "url" => "webgk_support_files.php",
                    "sort" => 700
                ),
                array(
                    "text" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_TICKET_PAYMENT"),                      
                    "title" => GetMessage("WEBGK_SUPPORT_ADMIN_MENU_TICKET_PAYMENT"),
                    "url" => "webgk_support_ticket_payment.php",
                    "sort" => 800
                ),
                
               
            )
        );
    }

    return false;
