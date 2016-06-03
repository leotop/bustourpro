<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    'PARAMETERS' => array(
        'PAGE_QUANTITY' => array(
            'NAME' => GetMessage("SUPPORT_QUANTITY"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
            'DEFAULT' => 50,
            ),
            
        'HIDE_NULL_BALANCE' => array(
            'NAME' => GetMessage("SUPPORT_HIDE_NULL_BALANCE"),
            'TYPE' => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
            ),    
    ),
);
?>