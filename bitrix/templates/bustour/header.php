<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    
    

    <?$APPLICATION->ShowMeta("robots")?>
    <?$APPLICATION->ShowMeta("keywords")?>
    <?$APPLICATION->ShowMeta("description")?>
    <title><?$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowHead();?>
    <?IncludeTemplateLangFile(__FILE__);?>
    <?CJSCore::Init(array("jquery"));?>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/3.0.2/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/3.0.2/css/bootstrap-theme.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->


    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/plugins/jquery/PickMeUp/css/pickmeup.css" />
    <script src="<?=SITE_TEMPLATE_PATH;?>/plugins/jquery/PickMeUp/js/jquery.pickmeup.js"></script>

    <!--автобусы-->

    <script type="text/javascript" src="/js/bus.js"></script>
   <!-- <script type="text/javascript" src="/js/booking.js"></script> -->
    <script typw="text/javascript" src="/js/json.js"></script>
    <link rel="stylesheet" href="/css/styles.css">

    <link rel="stylesheet" href="/css/jquery.fancybox.css" type="text/css" media="screen, projection" />
    <script src="/js/jquery.fancybox.js" type="text/javascript"></script>
    
    <script type="text/javascript" src="/js/inputmask.js"></script>

    <script type="text/javascript" src="/js/main.js"></script>
    
    <?
    //удаляем из сессии фильтр, если ушли из текущего раздела
    //if ($_SESSION["filter"]["URL"] != $APPLICATION->GetCurPage()) {unset($_SESSION["filter"]);}
    ?>

</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>

<br>
<div id="wrap">
<div>
    <?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel", Array(
            "ROOT_MENU_TYPE" => "top",    // РўРёРї РјРµРЅСЋ РґР»СЏ РїРµСЂРІРѕРіРѕ СѓСЂРѕРІРЅСЏ
            "MENU_CACHE_TYPE" => "N",    // РўРёРї РєРµС€РёСЂРѕРІР°РЅРёСЏ
            "MENU_CACHE_TIME" => "3600",    // Р’СЂРµРјСЏ РєРµС€РёСЂРѕРІР°РЅРёСЏ (СЃРµРє.)
            "MENU_CACHE_USE_GROUPS" => "Y",    // РЈС‡РёС‚С‹РІР°С‚СЊ РїСЂР°РІР° РґРѕСЃС‚СѓРїР°
            "MENU_CACHE_GET_VARS" => "",    // Р—РЅР°С‡РёРјС‹Рµ РїРµСЂРµРјРµРЅРЅС‹Рµ Р·Р°РїСЂРѕСЃР°
            "MAX_LEVEL" => "2",    // РЈСЂРѕРІРµРЅСЊ РІР»РѕР¶РµРЅРЅРѕСЃС‚Рё РјРµРЅСЋ
            "CHILD_MENU_TYPE" => "left",    // РўРёРї РјРµРЅСЋ РґР»СЏ РѕСЃС‚Р°Р»СЊРЅС‹С… СѓСЂРѕРІРЅРµР№
            "USE_EXT" => "N",    // РџРѕРґРєР»СЋС‡Р°С‚СЊ С„Р°Р№Р»С‹ СЃ РёРјРµРЅР°РјРё РІРёРґР° .С‚РёРї_РјРµРЅСЋ.menu_ext.php
            "DELAY" => "N",    // РћС‚РєР»Р°РґС‹РІР°С‚СЊ РІС‹РїРѕР»РЅРµРЅРёРµ С€Р°Р±Р»РѕРЅР° РјРµРЅСЋ
            "ALLOW_MULTI_SELECT" => "N",    // Р Р°Р·СЂРµС€РёС‚СЊ РЅРµСЃРєРѕР»СЊРєРѕ Р°РєС‚РёРІРЅС‹С… РїСѓРЅРєС‚РѕРІ РѕРґРЅРѕРІСЂРµРјРµРЅРЅРѕ
            ),
            false
        );?>
</div>
<?if (checkLock() == "Y") {die(); } //если система заблокирована?>
<div>
      <?if($USER->IsAuthorized()):?>
        <?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel_2", Array(
    "ROOT_MENU_TYPE" => "left",    // Тип меню для первого уровня
    "MAX_LEVEL" => "1",    // Уровень вложенности меню
    "USE_EXT" => "N",    // Подключать файлы с именами вида .тип_меню.menu_ext.php
    ),
    false
);?>
        <?endif;?>
</div>

<table style="width: 100%;">
<tr>

<td class="content">
<?if($APPLICATION->GetCurPage(true) != SITE_DIR."index.php")
    {
        echo "<legend>";
        $APPLICATION->ShowTitle(false);
        echo "</legend>";
    }
?> 
