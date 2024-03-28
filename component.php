<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Context;
use \Bitrix\Main\Type\DateTime;

include_once("const.php");

$arParams['dept_perm_true'] = false;
$dept_perm = $this->getUserDept($GLOBALS["USER"]->GetID());
if(array_keys($dept_perm, REDACTOR_PERM_DEPT)){
	$arParams['dept_perm_true'] = true;
}

$request = Context::getCurrent()->getRequest();
$elem_id = $request->get("e");
$export = $request->get("export");
$type_io = $request->get("type_io");
$type_pf = $request->get("type_pf");
$type_fo = $request->get("in_out");

$arParams['KASSA'] = $this->getAllKassa();
$arParams['STATE'] = $this->getState();
$arParams['DEPT'] = $this->getDept();

switch($request->get("f")){
	case "add":
		$arParams["DATA"] = $this->add("add");
		break;
	case "set":  
		$arParams["DATA"] = $this->set("set");
		break;
	case "edit":
		$this->edit($elem_id,$type_io); 
		$ts = $this->getTaskIdClose($elem_id);
		if($ts){ 
			$this->sendMessage($ts['CREATED_BY'],$ts['RESPONSIBLE_ID'],'Оплачено!<br/>'.$ts['TITLE']);
		}
		LocalRedirect(_U);
		break;
	case "get":
		$APPLICATION->RestartBuffer();
		$arParams = $this->getEditById($elem_id,$type_io);
		$this->__templateName = '.default';
		$this->IncludeComponentTemplate("popup_edit");
		die();
		break;
	case "page":
		$APPLICATION->RestartBuffer();
		
		if($type_io == "in"){
			if($_GET['p_type'] == 'find'){
				$arParams['ITEMS'] = $this->getFind($request->getQueryList(),"in");
			}else{
				$arParams['ITEMS'] = $this->getDefaultPager(IBLOCK_IN, true);
			}
		}
		if($type_io == "out"){
			if($_GET['p_type'] == 'find'){
				$arParams['ITEMS'] = $this->getFind($request->getQueryList(),"out");
			}else{
				$arParams['ITEMS'] = $this->getDefaultPager(IBLOCK_OUT, false);
			}
		}
		
		
		$this->__templateName = '.default';
		$this->IncludeComponentTemplate("get_page");
		die();
		
	case "post":
		$APPLICATION->RestartBuffer();
		if($_POST){
			if($_POST['comment']){
				$el = new CIBlockElement;
				$dataEl = [
					"NAME" => empty($_POST['comment']) ? ' ' : $_POST['comment']
				];
				$el->Update($elem_id,$dataEl);
			}
			$_POST['data'] ? CIBlockElement::SetPropertyValueCode($elem_id, "DATA", new \Bitrix\Main\Type\Date($_POST['data'])):'';
			$_POST['fact_data'] ? CIBlockElement::SetPropertyValueCode($elem_id, "FACT_DATA", new \Bitrix\Main\Type\Date($_POST['fact_data'])):'';
			//CIBlockElement::SetPropertyValueCode($elem_id, "KASSA", $_POST['kassa']);
			//CIBlockElement::SetPropertyValueCode($elem_id, "OTDEL", $_POST['otdel']);
			$_POST['sum'] && $_POST['valut'] ? CIBlockElement::SetPropertyValueCode($elem_id, "ORIGINAL", $_POST['sum']." ".$_POST['valut']):'';

			if($_POST['valut'] == "USD"){
				$_POST['sum'] ? CIBlockElement::SetPropertyValueCode($elem_id, "SUM", $_POST['sum']*$this->getMoneyConvert($_POST['sum'],'usd')):'';
			}elseif($_POST['valut'] == "EUR"){
				$_POST['sum'] ? CIBlockElement::SetPropertyValueCode($elem_id, "SUM", $_POST['sum']*$this->getMoneyConvert($_POST['sum'],'eur')):'';
			}else{
				$_POST['sum'] ? CIBlockElement::SetPropertyValueCode($elem_id, "SUM", $_POST['sum']):'';
			}
		}
		echo 'success';
		die();
		break;
	case "del":
		$this->del($elem_id); 
		$ts = $this->getTaskIdClose($elem_id);
		if($ts){
			$this->sendMessage($ts['CREATED_BY'],$ts['RESPONSIBLE_ID'],'Отклонено!<br/>'.$ts['TITLE']);
		}
		LocalRedirect(_U);
		break;
	case "find":

		if($type_fo == "find_out"){
			$arParams['ITEMS']['OUT'] = $this->getFind($request->getQueryList(),"out");
			$arParams['STATS_OUT_FACT'] = $this->statsMain($arParams['ITEMS']['OUT']);
			$arParams['STATS_OUT_PLAN'] = $this->statsMainPlan($arParams['ITEMS']['OUT']);
		}else{
			$arParams['ITEMS']['IN'] = $this->getFind($request->getQueryList(),"in");
			$arParams['STATS_IN_FACT'] = $this->statsMain($arParams['ITEMS']['IN']);
			$arParams['STATS_IN_PLAN'] = $this->statsMainPlan($arParams['ITEMS']['IN']);
		}
		
		if(isset($export) && $export == "fact"){
			$this->exportCSV($arParams['ITEMS'], "fact");
			die();
		}elseif(isset($export) && $export == "plan"){
			$this->exportCSV($arParams['ITEMS'], "plan");
			die();
		}

		// summ in
		foreach($arParams['ITEMS']['IN'] as $val){
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "FACT"){
				$in_sum_fact += $val['SUM']['VALUE'];
			}
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "PLAN"){
				$in_sum_plan += $val['SUM']['VALUE'];
			}
		}
		$arParams['IN_SUM_FACT'] = $this->getMoneyFormat($in_sum_fact) ?? 0;
		$arParams['IN_SUM_PLAN'] = $this->getMoneyFormat($in_sum_plan) ?? 0;

		// summ out
		foreach($arParams['ITEMS']['OUT'] as $val){
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "FACT"){
				$out_sum_fact += $val['SUM']['VALUE'];
			}
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "PLAN"){
				$out_sum_plan += $val['SUM']['VALUE'];
			}
		}
		$arParams['OUT_SUM_FACT'] = $this->getMoneyFormat($out_sum_fact) ?? 0;
		$arParams['OUT_SUM_PLAN'] = $this->getMoneyFormat($out_sum_plan) ?? 0;

		break;
 
	default: 
		$arParams["DATA"] = "[=]";

		$arParams['ITEMS']['IN'] = $this->getDefault(IBLOCK_IN);
		$arParams['ITEMS']['OUT'] = $this->getDefault(IBLOCK_OUT);
		$arParams['STATS_OUT_FACT'] = $this->statsMain($arParams['ITEMS']['OUT']);
		$arParams['STATS_OUT_PLAN'] = $this->statsMainPlan($arParams['ITEMS']['OUT']);
		$arParams['STATS_IN_FACT'] = $this->statsMain($arParams['ITEMS']['IN']);
		$arParams['STATS_IN_PLAN'] = $this->statsMainPlan($arParams['ITEMS']['IN']);

		if(isset($export) && $export == "fact"){
			$this->exportCSV($arParams['ITEMS'], "fact");
			die();
		}elseif(isset($export) && $export == "plan"){
			$this->exportCSV($arParams['ITEMS'], "plan");
			die();
		}

		// summ
		foreach($arParams['ITEMS']['OUT'] as $val){
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "FACT"){
				$out_sum_fact += $val['SUM']['VALUE'];
			}
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "PLAN"){
				$out_sum_plan += $val['SUM']['VALUE'];
			}
		}
		foreach($arParams['ITEMS']['IN'] as $val){
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "FACT"){
				$in_sum_fact += $val['SUM']['VALUE'];
			}
			if($val['TYPE_STATUS']['VALUE_XML_ID'] == "PLAN"){
				$in_sum_plan += $val['SUM']['VALUE'];
			}
		}
		$arParams['OUT_SUM_FACT'] = $this->getMoneyFormat($out_sum_fact) ?? 0;
		$arParams['OUT_SUM_PLAN'] = $this->getMoneyFormat($out_sum_plan) ?? 0;
		$arParams['IN_SUM_FACT'] = $this->getMoneyFormat($in_sum_fact) ?? 0;
		$arParams['IN_SUM_PLAN'] = $this->getMoneyFormat($in_sum_plan) ?? 0;

		if($_POST['startLimits']){
			$this->startLimits();
		}
		if($_POST['startFinanceOut']){
			$this->startFinanceOut();
		}
		if($_POST['startFinanceIn']){
			$this->startFinanceIn();
		}

		//$this->add($_POST);
		//LocalRedirect('/finance/');
		//print_r($_POST);
}


$this->includeComponentTemplate();