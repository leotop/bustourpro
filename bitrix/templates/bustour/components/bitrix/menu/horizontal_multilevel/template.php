<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult) ):?>
    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->


        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav" <?if ($USER->IsAuthorized()){?>style="width:100%;"<?}?>>
            <li>
                <div class="logo">
                    <a class="navbar-brand" href="/"><img src="/i/logo.png"/></a>
                    <?/* <a class="profile" href="/personal/"> (мой профиль)</a> */?>
                </div>
            </li>

            <?if (!$USER->IsAuthorized()) { ?>                 
                <li>
                    <a href="/personal/?register=yes">Регистрация</a>
                </li>  
                <?} else if (checkLock() == "Y") {// если система заблокирована?>                 

                <?} else {?>

                <?
                    $previousLevel = 0;
                    foreach($arResult as $arItem):?>
                    <?if ($arItem["PERMISSION"] > "D"){?>
                        <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                            <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
                            <?endif?>

                        <?if ($arItem["IS_PARENT"]):?>

                            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>active<?endif?>"><?=$arItem["TEXT"]?> <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                <?else:?>
                                <li class="dropdown" class="<?if ($arItem["SELECTED"]):?>active<?endif?>"><a class="dropdown-toggle" data-toggle="dropdown" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <?endif?>

                                <?else:?>

                                <?if ($arItem["PERMISSION"] > "D"):?>

                                    <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                                        <li><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>active<?endif?>"><?=$arItem["TEXT"]?></a></li>
                                    <?else:?>
                                    <li class="<?if ($arItem["SELECTED"]):?>active<?endif?>"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
                                <?endif?>

                                <?else:?>

                                <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                                    <li><a href="" class="<?if ($arItem["SELECTED"]):?>active<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                                <?else:?>
                                <li><a href="" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                                <?endif?>

                            <?endif?>

                            <?endif?>

                            <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
                            <?}?>
                        <?endforeach?>

                    <?if ($previousLevel > 1)://close last item tags?>
                        <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
                        <?endif?>
                    <?}?>


                <?if ($USER->IsAuthorized()){?>
                    <li><a href="/personal/"><?=$USER->GetLogin()?> (мой профиль)</a></li>
                    <li class="system_exit">
                        <a href="/?logout=yes">Выход</a>
                        <?if ($APPLICATION->GetCurPage() == "/"){?>
                            <a href="/help/" class="save_button">помощь</a>
                            <?}?>
                    </li>                   
                    <?}?>


            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
    <?if (checkLock() == "Y"){?>
        <h2>Работа системы временно заблокирована туроператором.</h2> 
        <?}?>

    <?endif?>