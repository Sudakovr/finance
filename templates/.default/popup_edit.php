<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

/**
* @var array $arParams
* @var array $arResult
* @var \CBitrixComponentTemplate $this
* @global CMain $APPLICATION
* @global CUser $USER
* @global CDatabase $DB
*/
?>

<input type="hidden" name="elem_id" value="<?=$arParams['ID']?>" />
<b><?=CIBlockSection::GetByID($arParams['OTDEL']['VALUE'])->Fetch()['NAME']?></b>
<b><?=$arParams['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($arParams['STATE']['VALUE'])->Fetch()['NAME']?></b>
<br/><br/>

<?if($_REQUEST['pf'] == "fact"):?>

Фактическая дата оплаты:<br/> 
<input class="ui-ctl-element ui-ctl-xs" type="text" name="fact_data" value="<?=$arParams['FACT_DATA']['VALUE']?>" autofocus="off" onclick="BX.calendar({node:this,field:this,bTime:false,bHideTime:true});" /><br/>

<?else:?>

Планируемая дата оплаты:<br/> 
<input class="ui-ctl-element ui-ctl-xs" type="text" name="data" value="<?=$arParams['DATA']['VALUE']?>" autofocus="off" onclick="BX.calendar({node:this,field:this,bTime:false,bHideTime:true});" /><br/>
Сумма<br/>
<input class="ui-ctl-element ui-ctl-xs" type="text" name="sum" value="<?=explode(" ",$arParams['ORIGINAL']['VALUE'])[0]?>" />
<select class="ui-ctl-element" name="valut">
	<option value="RUB"<?if(explode(" ",$arParams['ORIGINAL']['VALUE'])[1]=="RUB"):?> selected<?endif;?>>Рубль</option>
	<option value="USD"<?if(explode(" ",$arParams['ORIGINAL']['VALUE'])[1]=="USD"):?> selected<?endif;?>>Доллар</option>
	<option value="EUR"<?if(explode(" ",$arParams['ORIGINAL']['VALUE'])[1]=="EUR"):?> selected<?endif;?>>Евро</option>
</select>
<br/>
<?/*Касса:<br/>
<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
		<div class="ui-ctl-after ui-ctl-icon-angle"></div>
<select class="ui-ctl-element" name="kassa">
	<option value=""></option>
';
foreach($arParams["KASSA"] as $val){
	echo '<option value="'.$val["ID"].'"'.(($val["ID"]==$arParams['KASSA']['VALUE']) ? ' selected' : '').'>'.$val["NAME"].'</option>';
}
echo '
</select>
</div>
<br/>
Отдел:<br/>
<select class="ui-ctl-element" name="otdel">
	<option value=""></option>
';
foreach($arParams["DEPT"] as $val){
	echo '<option value="'.$val["ID"].'"'.(($val["ID"]==$arParams['OTDEL']['VALUE']) ? ' selected' : '').'>'.$val["NAME"].'</option>';
}
echo '
</select>
<br/>*/?>
Комментарий:<br/>
<div class="ui-ctl ui-ctl-textarea">
<textarea class="ui-ctl-element ui-ctl-xs" type="text" name="comment"><?=trim($arParams['NAME'])?></textarea>

<?endif;?>

</div>