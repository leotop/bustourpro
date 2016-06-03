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
    ),
);
?>