<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();  
    $arComponentParameters = array(
        'PARAMETERS' => array(          
            'TICKET_DETAIL_PAGE' => array(
                'NAME' => GetMessage("TICKET_DETAIL_PAGE"),
                'TYPE' => 'STRING',
                'MULTIPLE' => 'N',
                'PARENT' => 'BASE',
                'DEFAULT' => "/deskman/?ID=#ID#&edit=1",
            ),
        ),
    );
?>