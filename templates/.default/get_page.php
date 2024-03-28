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

$type_st = null;
if($_GET['type_pf'] == "fact"){
	if($_GET['type_io'] == "out"){
		$type_io = "out";
	}else{
		$type_io = "in";
	}
	$type_pf = "fact";
	$type_st = "FACT";
	$data = $arParams['ITEMS'];
}
if($_GET['type_pf'] == "plan"){
	if($_GET['type_io'] == "out"){
		$type_io = "out";
	}else{
		$type_io = "in";
	}
	$type_pf = "plan";
	$type_st = "PLAN";
	$data = array_reverse((array)$arParams['ITEMS']);
}

?>

<table border="1">
	<tr>
		<td>План. дата</td>
		<?if($type_st == "FACT"):?>
		<td>Факт. дата</td>
		<?else:?>
		<td class="fxn"></td>
		<?endif;?>
		<td>Оригинал</td>
		<td>Сумма, ₽</td>
		<!--<td>Касса</td>-->
		<td>Отдел</td>
		<td>Статья</td>
		<td>Комментарий</td>
		<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?><td></td><?endif;?>
	</tr>
	
	<?
	
	$start_page_out_fact = 0;
	$plus_page_out_fact = PAGE_LIMIT;
	if(isset($_GET['f_page']) && $_GET['f_page']>1){
		$start_page_out_fact = $plus_page_out_fact*$_GET['f_page']-$plus_page_out_fact;
		$plus_page_out_fact = $plus_page_out_fact*$_GET['f_page'];
	}else{
		$start_page_out_fact = $start_page_out_fact;
		$plus_page_out_fact = $plus_page_out_fact;
	}
	
	/*echo '$start_page_out_fact:'.$start_page_out_fact;
	echo 'plus_page_out_fact:'.$plus_page_out_fact;
	echo 'f_page:'.$_GET['f_page'];
	echo '$curr:'.$curr;*/

	?>

	<?foreach($data as $key => $val):?>
	<?if($val['TYPE_STATUS']['VALUE_XML_ID'] == $type_st):?>
	<?$i_out_fact_out_fact++?>
	<?if($i_out_fact_out_fact > $start_page_out_fact && $i_out_fact_out_fact < $plus_page_out_fact+1):?>
	<tr<?if(strtotime(date('d.m.Y')) > strtotime($val['DATA']['VALUE']) && $type_st == "PLAN"):?> class="color_red"<?endif;?>>
		<td><?=$val['DATA']['VALUE']?></td>
		<?if($type_st == "FACT"):?>
		<td><?=$val['FACT_DATA']['VALUE']?></td>
		<?else:?>
		<td class="fxn"><?=date('d.m.Y')?></td>
		<?endif;?>
		<td class="info_little"><?=$component::getMoneyFormat($val['ORIGINAL']['VALUE'])?></td>
		<td><?=$component::getMoneyFormat($val['SUM']['VALUE'])?></td>
		<!--<td><?=CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME']?></td>-->
		<td><?=CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME']?></td>
		<td><?=($val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'])?></td>
		<td><?=$component::strlenComment($val['NAME'])?></td>
		
		<?if($type_st == "FACT" && ($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true'])):?>
			<td align="center" class="nowrap">
				<a class="sendToEdit" data-id="<?=$val['ID']?>" data-in="<?=$type_io?>" data-pf="<?=$type_pf?>" href="" title="Изменить">&#10000;</a>
				<a class="sendToActive" data-id="<?=$val['ID']?>" href="" title="Удалить">&#10006;</a>
			</td>
		<?elseif($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?>
			<td align="center" class="nowrap">
				<a class="sendToFact" data-id="<?=$val['ID']?>" data-in="<?=$type_io?>" href="" title="Перевести в ФАКТ">&#10004;</a>
				<a class="sendToEdit" data-id="<?=$val['ID']?>" data-in="<?=$type_io?>" href="" title="Изменить">&#10000;</a>
				<a class="sendToActive" data-id="<?=$val['ID']?>" href="" title="Удалить">&#10006;</a>
			</td>
		<?endif;?>
	</tr>
	<?endif;?>
	
	<?endif;?>
	<?endforeach;?>
	
</table>

<?
if($_GET['p_type'] == "find"){
	$url_pager = "
		?f=page&type_io=".$type_io."
		&type_pf=".$type_pf."
		&p_type=find
		&in_out=".$_GET['in_out']."
		&plan_fact=".$_GET['plan_fact']."
		&kassa=".$_GET['kassa']."
		&dept=".$_GET['dept']."
		&state=".$_GET['state']."
		&valut=".$_GET['valut']."
		&plan_data=".$_GET['plan_data']."
		&fact_data=".$_GET['fact_data']."&";
}else{
	$url_pager = "?f=page&type_io=".$type_io."&type_pf=".$type_pf."&";
}
?>

<?if($i_out_fact_out_fact>=PAGE_LIMIT):?>
<div class="finance_pager" data-type_io="<?=$type_io?>" data-type_pf="<?=$type_pf?>">
	<?$np = $_GET['f_page'] ? $_GET['f_page']+1 : 2;?>
	<?$pp = $_GET['f_page'] ? $_GET['f_page']-1 : 1;?>
	<?$lp = ceil(($i_out_fact_out_fact/PAGE_LIMIT))?>
	<?if($_GET['f_page'] && $_GET['f_page'] != 1):?><a class="pager" data-page="<?=$pp?>" href="<?=_U?><?=$url_pager?>f_page=<?=$pp?>"><- prev</a><?endif;?>
	<a class="pager<?if(!$_GET['f_page'] || $_GET['f_page'] == 1):?> fp_active<?endif;?>" data-page="1" href="<?=_U?><?=$url_pager?>f_page=1">1</a>
	<?for($i=1;$i<=$_GET['f_page']+2;$i++):?>
		<?if($i < $lp):?>
			<a class="pager<?if($_GET['f_page']==$i+1):?> fp_active<?endif;?>" data-page="<?=$i+1?>" href="<?=_U?><?=$url_pager?>f_page=<?=$i+1?>"><?=$i+1?></a>
		<?endif;?>
	<?endfor;?>
	<?if($i < $lp):?>
	...
	<a class="pager<?if($_GET['f_page']==$lp):?> fp_active<?endif;?>" data-page="<?=$lp?>" href="<?=_U?><?=$url_pager?>f_page=<?=$lp?>"><?=$lp?></a>
	<?endif;?>
	<?if($lp!=$np-1):?><a class="pager" data-page="<?=$np?>" href="<?=_U?><?=$url_pager?>f_page=<?=$np?>">next -></a><?endif;?>
</div>
<?endif;?>

<script type="text/javascript" src="/local/components/finance/templates/.default/script.js?<?=time()?>"></script>
