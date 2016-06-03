<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<br>
<?if (!empty($arResult["ERRORS"])):?>
    <div class="alert alert-danger">
        <?= implode("<br />", $arResult["ERRORS"]);?>
    </div>
<?endif?>

<?if ($arResult["MESSAGE"]):?>
    <div class="alert alert-success">
        <?= $arResult["MESSAGE"];?>
    </div>
<?endif?>