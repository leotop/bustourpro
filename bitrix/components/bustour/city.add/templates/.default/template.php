<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?
//echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
//echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
//exit();
?>

<?if (!empty($arResult['error'])):?>
    <?= ShowError($arResult['error']);?>
<?endif?>
<?if (!empty($arResult['success'])):?>
    <?= ShowError($arResult['success']);?>
<?endif?>

<form name="city" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

<?=bitrix_sessid_post()?>

<table class="data-table">
<thead>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
</thead>
    <tbody>
        <tr>
            <td>Название<span class="starrequired">*</span></td>
            <td><input type="text" name="city[name]" value="<?= htmlspecialcharsEx($arResult['city']['name']); ?>"></td>
        </tr>
    </tbody>
<tfoot>
<tr>
    <td colspan="2">
        <input type="submit" name="b_city_add" value="Добавить" />
    </td>
</tr>
</tfoot>
</table>
</form>