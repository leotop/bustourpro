<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<ul class="deskmanMenu">
<?foreach($arResult as $arItem):?>
		<li><a href="<?=$arItem["LINK"]?>" class="menr"><?=$arItem["TEXT"]?></a></li>
<?endforeach?>
</ul>
<?endif?>