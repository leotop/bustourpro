<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

    $arComponentParameters = array(
        'PARAMETERS' => array(
            'SUPPORT_CRITICALY' => array(
                'NAME' => GetMessage("SUPPORT_CRITICALY"),
                'TYPE' => 'STRING',
                'MULTIPLE' => 'N',
                'PARENT' => 'BASE',
                'DEFAULT' => 5,
            ),  

            'SUPPORT_CRITICALY_SUM' => array(
                'NAME' => GetMessage("SUPPORT_CRITICALY_SUM"),
                'TYPE' => 'STRING',
                'MULTIPLE' => 'N',
                'PARENT' => 'BASE',
                'DEFAULT' => 10,
            ),
        ),
    );
?>