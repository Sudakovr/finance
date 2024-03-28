<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CJSCore::Init(["jquery"]);
$this->addExternalJS("https://cdn.jsdelivr.net/momentjs/latest/moment.min.js");
$this->addExternalJS("https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js");
$this->addExternalCss("https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css");
?>

<div class="finance">
	
	<div class="finance_menu">
		<form method="post" action="<?=_U?>">
			<input class="ui-btn ui-btn-icon" type="submit" name="startFinanceOut" value="Добавить платеж" />
			<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?><input class="ui-btn ui-btn-icon" type="submit" name="startFinanceIn" value="Добавить доход" /><?endif;?>
			<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?><input class="ui-btn ui-btn-icon" type="submit" name="startLimits" value="Установить лимиты" /><?endif;?>
			<button class="ui-btn ui-btn-icon" onclick="location.href='/bizproc/processes/<?=LIST_FINANCE?>/view/0/';return false;">Мои заявки</button>
			<button class="ui-btn ui-btn-icon<?if($_REQUEST['f']=="find"):?> ui-btn-success<?endif;?>" onclick="show_hide('.filter_box');return false;">Фильтры</button>
			<button class="ui-btn ui-btn-icon" onclick="show_hide('.export_box');return false;">Выгрузить</button>
		</form>
	</div>
	
	<div class="hidden_box export_box">
		<br/>
		<p><a href="" onclick="location.href='<?if($_REQUEST['f']){echo'?'.$_SERVER['QUERY_STRING'].'&export=fact';}else{echo'?export=fact';}?>';return false;">Выгрузить ФАКТ</a><br/></p>
		<p><a href="" onclick="location.href='<?if($_REQUEST['f']){echo'?'.$_SERVER['QUERY_STRING'].'&export=plan';}else{echo'?export=plan';}?>';return false;">Выгрузить ПЛАН</a></p>
	</div>
	<div class="hidden_box filter_box<?if($_REQUEST['f']=="find"):?> hidden_box_show<?endif;?>">
		<br/> 
		<form method="get" action="">
			<input type="hidden" name="f" value="find" />
			<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
   				<div class="ui-ctl-after ui-ctl-icon-angle"></div>
				<select class="ui-ctl-element" name="in_out">   
					<option value="">Доход/Расход</option>
					<option value="find_in"<?if($_REQUEST['in_out']=="find_in"):?> selected<?endif;?>>Доход</option>
					<option value="find_out"<?if($_REQUEST['in_out']=="find_out"):?> selected<?endif;?>>Расход</option>
				</select>
			</div>
			<br/>
			<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
   				<div class="ui-ctl-after ui-ctl-icon-angle"></div>
				<select class="ui-ctl-element" name="plan_fact">
					<option value="">Факт/План</option>
					<option value="find_fact"<?if($_REQUEST['plan_fact']=="find_fact"):?> selected<?endif;?>>Факт</option>
					<option value="find_plan"<?if($_REQUEST['plan_fact']=="find_plan"):?> selected<?endif;?>>План</option>
				</select>
			</div>
			<br/>
			<!--<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
   				<div class="ui-ctl-after ui-ctl-icon-angle"></div>
				<select class="ui-ctl-element" name="kassa">
					<option value="">Касса списания</option>
					<?foreach($arParams["KASSA"] as $val):?>
					<option value="<?=$val["ID"]?>"<?if($_REQUEST['kassa']==$val["ID"]):?> selected<?endif;?>><?=$val["NAME"]?></option>
					<?endforeach;?>
				</select>
			</div>
			<br/>-->
			<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
   				<div class="ui-ctl-after ui-ctl-icon-angle"></div>
				<select class="ui-ctl-element" name="dept" onchange="letsGo(this.value)">
					<option value="">Отдел</option>.
					<?foreach($arParams["DEPT"] as $val):?>
					<option value="<?=$val["ID"]?>"<?if($_REQUEST['dept']==$val["ID"]):?> selected<?endif;?>><?=$val["NAME"]?></option>
					<?endforeach;?>
				</select>
			</div>
			<br/>
			<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
   				<div class="ui-ctl-after ui-ctl-icon-angle"></div>
				<select class="ui-ctl-element" name="state">
					<option value="">Статья расходов</option>.
					<?foreach($arParams["STATE"] as $val):?>
					<option class="id<?=$val['IBLOCK_SECTION_ID']?> idhide"<?if($_REQUEST['dept']==$val['IBLOCK_SECTION_ID']):?> style="display: block;"<?endif;?> value="<?=$val["ID"]?>"<?if($_REQUEST['state']==$val["ID"]):?> selected<?endif;?><?if($val["ACTIVE"]=="N"):?> style="font-style: italic;"<?endif;?>><?=$val["NAME"]?></option>
					<?endforeach;?>
				</select>
			</div>
			<br/>
			<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-xs">
   				<div class="ui-ctl-after ui-ctl-icon-angle"></div>
				<select class="ui-ctl-element" name="valut">
					<option value="">Валюта</option>
					<option value="RUB"<?if($_REQUEST['valut']=="RUB"):?> selected<?endif;?>>Рубль</option>
					<option value="USD"<?if($_REQUEST['valut']=="USD"):?> selected<?endif;?>>Доллар</option>
					<option value="EUR"<?if($_REQUEST['valut']=="EUR"):?> selected<?endif;?>>Евро</option>
				</select>
			</div>
			<br/>
			Планируемая дата оплаты (диапазон):
			<div class="ui-ctl ui-ctl-textbox ui-ctl-xs">
    			<div class="ui-ctl-after ui-ctl-icon-calendar"></div>
				<input class="ui-ctl-element" type="text" name="plan_data" value="<?=$_REQUEST['plan_data']?>" autocomplete="off">
			</div>
			<br/>
			Фактическая дата оплаты (диапазон):
			<div class="ui-ctl ui-ctl-textbox ui-ctl-xs">
    			<div class="ui-ctl-after ui-ctl-icon-calendar"></div>
				<input class="ui-ctl-element" type="text" name="fact_data" value="<?=$_REQUEST['fact_data']?>" autocomplete="off">
			</div>
			<br/>
			<button class="ui-btn ui-btn-success">применить</button>
			<a class="ui-btn ui-btn-icon-remove" href="<?=_U?>">сбросить фильтр</a>
		</form>
	</div>
	
	<?/*
	<br/><br/>
	
	<form method="post" action="">
		<select name="in_out" required>
			<option value hidden>Доход/Расход</option>
			<option value="in">Доход</option>
			<option value="out">Расход</option>
		</select>
		<br/> 
		<select name="plan_fact" required>
			<option value hidden>Факт/План</option>
			<option value="fact">Факт</option>
			<option value="plan">План</option>
		</select>
		<br/>
		<select name="kassa" required>
			<option value hidden>Касса списания</option>
			<?foreach($arParams["KASSA"] as $val):?>
			<option value="<?=$val["ID"]?>"><?=$val["NAME"]?></option>
			<?endforeach;?>
		</select>
		<br/>
		<select name="state" required>
			<option value hidden>Статья расходов</option>.
			<?foreach($arParams["STATE"] as $val):?>
			<option value="<?=$val["ID"]?>"><?=$val["NAME"]?></option>
			<?endforeach;?>
		</select>
		<br/>
		<input type="text" name="date" placeholder="Дата" onclick="BX.calendar({node:this,field:this,bTime:false,bHideTime:false});" />
	
		<div class="valut">
			<input type="number" name="sum" placeholder="Сумма" />
			<select name="valut" required>
				<option hidden>Валюта</option>
				<option value="RUB">Российский рубль</option>
				<option value="USD">Доллар США</option>
				<option value="EUR">Евро</option>
				<option hidden value="UAH">Гривна</option>
				<option hidden value="BYN">Белорусский рубль</option>
			</select>
		</div>

		<input type="text" name="comment" placeholder="Комментарий" />
		<br/>
		<button>Добавить</button>
	</form>
	*/?>
	<br/>
	
	<div class="finance_in">
		<h2>Доходы</h2> 
		<div class="fin_body">
			<?if($arParams['IN_SUM_FACT']>0||$arParams['IN_SUM_PLAN']>0):?>
			<div class="tab_f npl">
				<b>Факт</b> <?=$arParams['IN_SUM_FACT']?> ₽
				
				<div class="table_pager"></div>
				
			</div>
			<div class="tab_f npr">
				<b>План</b> <?=$arParams['IN_SUM_PLAN']?> ₽
				
				<div class="table_pager"></div>
				
			</div>
			<div class="clear"></div>
			<?else:?>
			--- нет записей
			<?endif;?>
		</div>
	</div>

	<div class="finance_out">
		<h2>Расходы</h2>
		<div class="fin_body">
			<?if($arParams['OUT_SUM_FACT']>0||$arParams['OUT_SUM_PLAN']>0):?>
			<div class="tab_f npl">
			
				<b>Факт</b> <?=$arParams['OUT_SUM_FACT']?> ₽
				
				<div class="table_pager">
				
				<?/*
				<table border="1">
					<tr>
						<td>План. дата</td>
						<td>Факт. дата</td>
						<td>Оригинал</td>
						<td>Сумма, ₽</td>
						<td>Касса</td>
						<td>Отдел</td>
						<td>Статья</td>
						<td>Комментарий</td>
						<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?><td></td><?endif;?>
					</tr>
					
					<?
					
					$start_page_out_fact = 0;
					$plus_page_out_fact = PAGE_LIMIT;
					if(isset($_GET['f_out'])){
						if($_GET['f_out']>2){
							$start_page_out_fact = $start_page_out_fact+$_GET['f_out']*$plus_page_out_fact;
							$plus_page_out_fact = $plus_page_out_fact+$_GET['f_out']*$plus_page_out_fact;
						}else{
							$start_page_out_fact = $start_page_out_fact+$plus_page_out_fact;
							$plus_page_out_fact = $plus_page_out_fact+$plus_page_out_fact;
						}
					}
					
					if(isset($_GET['t'])){
						echo '$start_page_out_fact:'.$start_page_out_fact;
						echo 'plus_page_out_fact:'.$plus_page_out_fact;
					}
					
					?>
					
					<?foreach($arParams['ITEMS']['OUT'] as $key => $val):?>
					<?if($val['TYPE_STATUS']['VALUE_XML_ID'] == "FACT"):?>
					<?$i_out_fact_out_fact++?>
					<?if($i_out_fact_out_fact > $start_page_out_fact && $i_out_fact_out_fact < $plus_page_out_fact+1):?>
					<tr>
						<td><?=$val['DATA']['VALUE']?></td>
						<td><?=$val['FACT_DATA']['VALUE']?></td>
						<td class="info_little"><?=$component::getMoneyFormat($val['ORIGINAL']['VALUE'])?></td>
						<td><?=$component::getMoneyFormat($val['SUM']['VALUE'])?></td>
						<td><?=CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME']?></td>
						<td><?=CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME']?></td>
						<td><?=($val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'])?></td>
						<td><?=$component::strlenComment($val['NAME'])?></td>
						<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?>
						<td align="center" class="nowrap">
							<a class="sendToEdit" data-id="<?=$val['ID']?>" data-pf="fact" href="" title="Изменить">&#10000;</a>
							<a class="sendToActive" data-id="<?=$val['ID']?>" href="" title="Удалить">&#10006;</a>
						</td>
						<?endif;?>
					</tr>
					<?endif;?>
					
					<?endif;?>
					<?endforeach;?>
					
				</table>
				
				<?
				if(isset($_GET['f'])){
					$url_pager = "?f=find&in_out=
						".$_GET['in_out']."
						&plan_fact=".$_GET['plan_fact']."
						&kassa=".$_GET['kassa']."
						&dept=".$_GET['dept']."
						&state=".$_GET['state']."
						&valut=".$_GET['valut']."
						&plan_data=".$_GET['plan_data']."
						&fact_data=".$_GET['fact_data'];
				}
				?>
				
				<?if($i_out_fact_out_fact>PAGE_LIMIT):?>
				<div class="finance_pager" data-type_io="out" data-type_pf="fact">
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if(!$_GET['f_out']):?> fp_active<?endif;?>" data-page="1" href="<?=_U?><?=$url_pager?>">1</a>
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if($_GET['f_out']==2):?> fp_active<?endif;?>" data-page="2"  href="<?=_U?><?=$url_pager?>?f_out=2">2</a>
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if($_GET['f_out']==3):?> fp_active<?endif;?>" data-page="3" href="<?=_U?><?=$url_pager?>?f_out=3">3</a>
				<?for($i=3;$i<$_GET['f_out']+1;$i++):?>
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if($_GET['f_out']==$i+1):?> fp_active<?endif;?>" data-page="<?=$i+1?>" href="<?=_U?><?=$url_pager?>?f_out=<?=$i+1?>"><?=$i+1?></a>
				<?endfor;?>
				</div>
				<?endif;?>
				*/?>
				
				</div>
				
			</div> 
			<div class="tab_f npr">
				<b>План</b> <?=$arParams['OUT_SUM_PLAN']?> ₽
				
				<div class="table_pager">
				
				<?/*
				<table border="1">
					<tr>
						<td>План. дата</td>
						<td class="fxn"></td>
						<td>Оригинал</td>
						<td>Сумма, ₽</td>
						<td>Касса</td>
						<td>Отдел</td>
						<td>Статья</td>
						<td>Комментарий</td>
						<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?><td></td><?endif;?>
					</tr>
					
					<?
					
					$start_page_out_plan = 0;
					$plus_page_out_plan = PAGE_LIMIT;
					if(isset($_GET['p_out'])){
						if($_GET['p_out']>2){
							$start_page_out_plan = $start_page_out_plan+$_GET['p_out']*$plus_page_out_plan;
							$plus_page_out_plan = $plus_page_out_plan+$_GET['p_out']*$plus_page_out_plan;
						}else{
							$start_page_out_plan = $start_page_out_plan+$plus_page_out_plan;
							$plus_page_out_plan = $plus_page_out_plan+$plus_page_out_plan;
						}
					}
					
					?>
					
					<?foreach(array_reverse((array)$arParams['ITEMS']['OUT']) as $val):?> 
					<?if($val['TYPE_STATUS']['VALUE_XML_ID'] == "PLAN"):?>
					<?$i_out_fact_out_plan++?>
					<?if($i_out_fact_out_plan > $start_page_out_plan && $i_out_fact_out_plan < $plus_page_out_plan+1):?>
					<tr<?if(strtotime(date('d.m.Y')) > strtotime($val['DATA']['VALUE'])):?> class="color_red"<?endif;?>>
						<td><?=$val['DATA']['VALUE']?></td>
						<td class="fxn"><?=date('d.m.Y')?></td>
						<td class="info_little"><?=$component::getMoneyFormat($val['ORIGINAL']['VALUE'])?></td>
						<td><?=$component::getMoneyFormat($val['SUM']['VALUE'])?></td>
						<td><?=CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME']?></td>
						<td><?=CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME']?></td>
						<td><?=($val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'])?></td>
						<td><?=$component::strlenComment($val['NAME'])?></td>
						<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?>
							<td align="center" class="nowrap">
								<a class="sendToFact" data-id="<?=$val['ID']?>" href="" title="Перевести в ФАКТ">&#10004;</a>
								<a class="sendToEdit" data-id="<?=$val['ID']?>" data-pf="plan" href="" title="Изменить">&#10000;</a>
								<a class="sendToActive" data-id="<?=$val['ID']?>" href="" title="Удалить">&#10006;</a>
							</td>
						<?endif;?>
					</tr>
					<?endif;?>
					<?endif;?>
					<?endforeach;?>
				</table>
				
				<!--<?if($i_out_fact_out_plan>PAGE_LIMIT):?>
				<a<?if(!$_GET['p_out']):?> style="font-weight:bold;"<?endif;?> href="<?=_U?><?=$url_pager?>">1</a>
				<a<?if($_GET['p_out']==2):?> style="font-weight:bold;"<?endif;?> href="<?=_U?><?=$url_pager?>?p_out=2">2</a>
				<a<?if($_GET['p_out']==3):?> style="font-weight:bold;"<?endif;?> href="<?=_U?><?=$url_pager?>?p_out=3">3</a>
				<?for($i=3;$i<$_GET['p_out']+1;$i++):?>
				<a<?if($_GET['p_out']==$i+1):?> style="font-weight:bold;"<?endif;?> href="<?=_U?><?=$url_pager?>?p_out=<?=$i+1?>"><?=$i+1?></a>
				<?endfor;?>
				<?endif;?>-->
				
				<?if($i_out_fact_out_plan>PAGE_LIMIT):?>
				<div class="finance_pager" data-type_io="out" data-type_pf="plan">
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if(!$_GET['p_out']):?> fp_active<?endif;?>" data-page="1" href="<?=_U?><?=$url_pager?>">1</a>
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if($_GET['p_out']==2):?> fp_active<?endif;?>" data-page="2"  href="<?=_U?><?=$url_pager?>?p_out=2">2</a>
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if($_GET['p_out']==3):?> fp_active<?endif;?>" data-page="3" href="<?=_U?><?=$url_pager?>?p_out=3">3</a>
				<?for($i=3;$i<$_GET['p_out']+1;$i++):?>
				<a class="pager<?if($_GET['f']=="find"):?>-f<?endif;?><?if($_GET['p_out']==$i+1):?> fp_active<?endif;?>" data-page="<?=$i+1?>" href="<?=_U?><?=$url_pager?>?p_out=<?=$i+1?>"><?=$i+1?></a>
				<?endfor;?>
				</div>
				<?endif;?>
				*/?>
				
				</div>
				
			</div>
			<div class="clear"></div>
			<?else:?>
			--- нет записей
			<?endif;?>
		</div>
	</div>
	
	<?if($GLOBALS["USER"]->IsAdmin()||$arParams['dept_perm_true']):?>
	<div class="finance_stat">
		<h2>Статистика</h2>
		<div class="fin_body">
		
			<div class="tab_f npl">
				<b>Доходы</b><br/><br/>
				<b>Факт</b><br/>
				<?if(count((array)$arParams['STATS_IN_FACT'])>0):?>
					<table class="table_stats">
						<tr class="table_stats_line">
							<td><b>Касса, отдел</b></td>
							<td width="30%"><b>Сумма</b></td>
						</tr>
					<?foreach($arParams['STATS_IN_FACT'] as $key => $val):?>
						<tr class="table_stats_line">
							<td><b><?=$key?></b></td>
							<td></td>
						</tr>
						<?foreach($val as $key_1 => $val_1):?>
						<tr class="table_stats_line">
							<td>- <?=$key_1?></td>
							<td><?=$component::getMoneyFormat($val_1)?> ₽</td>
						</tr>
						<?$ee+=$val_1?>
						<?endforeach;?>
						<tr class="table_stats_line">
							<td>Итого:</td>
							<td><b><?=$component::getMoneyFormat($ee)?> ₽</b></td>
						</tr>
						<?unset($ee)?>
					<?endforeach;?>
					</table>
				<?else:?>
				--- нет записей
				<?endif;?>
				
				<?if(isset($_GET['t'])):?>
				<br/><br/>
				<b>План</b>
				<?if(count((array)$arParams['STATS_IN_PLAN'])>0):?>
					<table class="table_stats">
						<tr class="table_stats_line">
							<td><b>Касса, отдел</b></td>
							<td width="30%"><b>Сумма</b></td>
						</tr>
					<?foreach($arParams['STATS_IN_PLAN'] as $key => $val):?>
						<tr class="table_stats_line">
							<td><b><?=$key?></b></td>
							<td></td>
						</tr>
						<?foreach($val as $key_1 => $val_1):?>
						<tr class="table_stats_line">
							<td>- <?=$key_1?></td>
							<td><?=$component::getMoneyFormat($val_1)?> ₽</td>
						</tr>
						<?$ee+=$val_1?>
						<?endforeach;?>
						<tr class="table_stats_line">
							<td>Итого:</td>
							<td><b><?=$component::getMoneyFormat($ee)?> ₽</b></td>
						</tr>
						<?unset($ee)?>
					<?endforeach;?>
					</table>
				<?else:?>
				--- нет записей
				<?endif;?>
				<?endif;?>
				
			</div>
			<div class="tab_f npr">
				<b>Расходы</b><br/><br/>
				<b>Факт</b><br/>
				<?if(count((array)$arParams['STATS_OUT_FACT'])>0):?>
					<table class="table_stats">
						<tr class="table_stats_line">
							<td><b>Касса, отдел</b></td>
							<td width="30%"><b>Сумма</b></td>
						</tr>
					<?foreach($arParams['STATS_OUT_FACT'] as $key => $val):?>
						<tr class="table_stats_line">
							<td><b><?=$key?></b></td>
							<td></td>
						</tr>
						<?foreach($val as $key_1 => $val_1):?>
						<tr class="table_stats_line">
							<td>- <?=$key_1?></td>
							<td><?=$component::getMoneyFormat($val_1)?> ₽</td>
						</tr>
						<?$ee+=$val_1?>
						<?endforeach;?>
						<tr class="table_stats_line">
							<td>Итого:</td>
							<td><b><?=$component::getMoneyFormat($ee)?> ₽</b></td>
						</tr>
						<?unset($ee)?>
					<?endforeach;?>
					</table>
				<?else:?>
				--- нет записей
				<?endif;?>
				
				<?if(isset($_GET['t'])):?>
				<br/><br/>
				<b>План</b>
				<?if(count((array)$arParams['STATS_OUT_PLAN'])>0):?>
					<table class="table_stats">
						<tr class="table_stats_line">
							<td><b>Касса, отдел</b></td>
							<td width="30%"><b>Сумма</b></td>
						</tr>
					<?foreach($arParams['STATS_OUT_PLAN'] as $key => $val):?>
						<tr class="table_stats_line">
							<td><b><?=$key?></b></td>
							<td></td>
						</tr>
						<?foreach($val as $key_1 => $val_1):?>
						<tr class="table_stats_line">
							<td>- <?=$key_1?></td>
							<td><?=$component::getMoneyFormat($val_1)?> ₽</td>
						</tr>
						<?$ee+=$val_1?>
						<?endforeach;?>
						<tr class="table_stats_line">
							<td>Итого:</td>
							<td><b><?=$component::getMoneyFormat($ee)?> ₽</b></td>
						</tr>
						<?unset($ee)?>
					<?endforeach;?>
					</table>
				<?else:?>
				--- нет записей
				<?endif;?>
				
				<?endif;?>
				
			</div>
			<div class="clear"></div>
			
		</div>
		
	</div>
	<?endif;?>

	<div class="finance_popup">
		<div class="finance_popup_opacity"></div>
		<div class="finance_popup_box">
			<form class="popup_form" method="post" action="">
				<div class="finance_popup_box_data"></div>
				<br/>
				<button class="ui-btn ui-btn-success">сохранить</button>
				<button class="ui-btn ui-btn-cancel button_popup_cancel">отмена</button>
			</form>
		</div>
	</div>
	<div class="finance_popup_confirm">
		<div class="finance_popup_opacity"></div>
		<div class="finance_popup_box_confirm"></div>
	</div>

</div>

<script type="text/javascript">
<?if($_GET['f'] == "find"):?>
<?
$url_pager = "&p_type=find&in_out=".$_GET['in_out']."&plan_fact=".$_GET['plan_fact']."&kassa=".$_GET['kassa']."&dept=".$_GET['dept']."&state=".$_GET['state']."&valut=".$_GET['valut']."&plan_data=".$_GET['plan_data']."&fact_data=".$_GET['fact_data']."&";
?>
<?if($_GET['in_out']=="find_out"):?>
$.get('<?=_U?>?f=page&type_io=out&type_pf=fact<?=$url_pager?>', function(data){$('.finance_out .npl .table_pager').html(data)})
$.get('<?=_U?>?f=page&type_io=out&type_pf=plan<?=$url_pager?>', function(data){$('.finance_out .npr .table_pager').html(data)})
<?else:?>
$.get('<?=_U?>?f=page&type_io=in&type_pf=fact<?=$url_pager?>', function(data){$('.finance_in .npl .table_pager').html(data)})
$.get('<?=_U?>?f=page&type_io=in&type_pf=plan<?=$url_pager?>', function(data){$('.finance_in .npr .table_pager').html(data)})
<?endif;?>
<?else:?>
$.get('<?=_U?>?f=page&type_io=out&type_pf=fact', function(data){$('.finance_out .npl .table_pager').html(data)})
$.get('<?=_U?>?f=page&type_io=out&type_pf=plan', function(data){$('.finance_out .npr .table_pager').html(data)})
$.get('<?=_U?>?f=page&type_io=in&type_pf=fact', function(data){$('.finance_in .npl .table_pager').html(data)})
$.get('<?=_U?>?f=page&type_io=in&type_pf=plan', function(data){$('.finance_in .npr .table_pager').html(data)})
<?endif;?>
</script>

