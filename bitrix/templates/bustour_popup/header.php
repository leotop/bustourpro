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
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/3.0.2/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/3.0.2/css/bootstrap-theme.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->


    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/plugins/jquery/PickMeUp/css/pickmeup.min.css" />
    <script src="<?=SITE_TEMPLATE_PATH;?>/plugins/jquery/PickMeUp/js/jquery.pickmeup.min.js"></script>

    <!--автобусы-->

    <script type="text/javascript" src="/js/bus.js"></script>
    <script type="text/javascript" src="/js/boocking.js"></script>
    <script typw="text/javascript" src="/js/json.js"></script>
    <link rel="stylesheet" href="/css/styles.css">

    <link rel="stylesheet" href="/css/jquery.fancybox.css" type="text/css" media="screen, projection" />
    <script src="/js/jquery.fancybox.js" type="text/javascript"></script>
    
    <script type="text/javascript" src="/js/inputmask.js"></script>

    <script type="text/javascript" src="/js/main.js"></script>

</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
 <div class="popup_content">


