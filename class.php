<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class FinanceComponent extends CBitrixComponent {
	
	private function statePerms($state){
		return $state;
	}

	private function checkPerms($params){
		if(in_array($params)){
			return $this->statePerms(true);
		}else{
			return $this->statePerms(false);
		}
	}

	public function getDefault($id,$pagination=false){

		$arSort = [ 
			"PROPERTY_DATA" => "DESC"
		];
 
		$arFilter = [
			"IBLOCK_ID" => $id, 
			"ACTIVE" => "Y",
		];

		/* GLOBALPERMS */
		if(!$GLOBALS["USER"]->IsAdmin()){
			/*$kassa_perm = $this->getKassaByUser($GLOBALS["USER"]->GetID());
			if($kassa_perm){
				$arFilter[] = [
					"LOGIC" => "OR",
					["CREATED_BY" => $GLOBALS["USER"]->GetID()],
					["PROPERTY_KASSA.ID" => $this->getKassaByUser($GLOBALS["USER"]->GetID())]
				];
			}else{*/
				/* CHECK PERMISSION BY DEPT */
				$dept_perm = $this->getUserDept($GLOBALS["USER"]->GetID());
				if(!array_keys($dept_perm, REDACTOR_PERM_DEPT)){
					$allTeam = $this->getUsersId($GLOBALS["USER"]->GetID());
					$arFilter["CREATED_BY"] = $allTeam;
				}
			//}
		}

		$arSelect = [
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DATE_CREATE",
			"ACTIVE_TO",
			"CREATED_BY",
			"PROPERTY_*" 
		];
		
		/* pagination set */
		if($pagination==true){
			$arNavPage = [
				"nPageSize" => 11,
				"iNumPage" => 1
			];
		}
		
		$res = CIBlockElement::GetList($arSort,$arFilter,false,$arNavPage,$arSelect);
		while($obj = $res->GetNextElement()){
			$ret[] = array_merge($obj->GetFields(), $obj->GetProperties());
			$to_perms[] = $obj->GetFields()['ID'];
		}
 
		// permissions
		if($to_perm){
			$this->checkPerms($to_perms);
		}

		return $ret;
	}

	public function getDefaultPager($id,$pagination=false){

		$arSort = [ 
			"PROPERTY_DATA" => "DESC"
		];
 
		$arFilter = [
			"IBLOCK_ID" => $id, 
			"ACTIVE" => "Y",
		];

		/* GLOBALPERMS */
		if(!$GLOBALS["USER"]->IsAdmin()){
			$dept_perm = $this->getUserDept($GLOBALS["USER"]->GetID());
			if(!array_keys($dept_perm, REDACTOR_PERM_DEPT)){
				$allTeam = $this->getUsersId($GLOBALS["USER"]->GetID());
				$arFilter["CREATED_BY"] = $allTeam;
			}
		}

		$arSelect = [
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DATE_CREATE",
			"ACTIVE_TO",
			"CREATED_BY",
			"PROPERTY_*" 
		];
		
		/* pagination set */
		if($pagination==true){
			$arNavPage = [
				"nPageSize" => 11,
				"iNumPage" => 1
			];
		}
		
		$res = CIBlockElement::GetList($arSort,$arFilter,false,$arNavPage,$arSelect);
		while($obj = $res->GetNextElement()){
			$ret[] = array_merge($obj->GetFields(), $obj->GetProperties());
		}
 
		return $ret;
	}

	public function getKassaByUser($user){

		$arSort = [ 
			"PROPERTY_DATA" => "ASC"
		];
 
		$arFilter = [
			"IBLOCK_ID" => IBLOCK_OUT_KASSA,
			"ACTIVE" => "Y",
			"PROPERTY_OWNER" => $user,
		];

		$arSelect = [
			"ID",
			"NAME",
		];

		$res = CIBlockElement::GetList($arSort,$arFilter,false,false,$arSelect);
		while($obj = $res->GetNextElement()){
			$ret[] = $obj->GetFields()['ID'];
		}
		
		return $ret;
	}

	public function getAllKassa(){
		$ret = \Bitrix\Iblock\ElementTable::getList(array(
			'filter' => array('IBLOCK_ID' => IBLOCK_OUT_KASSA),
		))->fetchAll();
		return $ret;
	}

	public function getState(){
		$ret = \Bitrix\Iblock\ElementTable::getList(array(
			'filter' => array('IBLOCK_ID' => IBLOCK_OUT_STATE),
		))->fetchAll();
		return $ret;
	}

	public function getDept(){
		$ret = \Bitrix\Iblock\SectionTable::getList(array(
			'filter' => array('IBLOCK_ID' => IBLOCK_OUT_STATE),
		))->fetchAll();
		return $ret;
	}

	public static function getMoneyFormat($int) {
		$currency = preg_replace("/[^A-Z]/ui", "", $int);
		if($currency){
			$currency = " ".$currency;
		}
		return number_format((float)$int, 2, '.', "'").$currency ?? false;
	}

	public function getMoneyConvert($int, $type) {
		$languages = @simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp");
		foreach ($languages->Valute as $lang){
			if ($lang["ID"] == 'R01235'){
				$usdtorub = round(str_replace(',','.',$lang->Value),2);
			}
			if ($lang["ID"] == 'R01239'){
				$eurtorub = round(str_replace(',','.',$lang->Value),2);
			} 
		}

		if($type == "eur"){
			$return = $eurtorub;
		}
		if($type == "usd"){
			$return = $usdtorub;
		}

		return $return;
	}

	public function getFind($array,$type){

		$in_out = $array['in_out'];
		$plan_fact = $array['plan_fact'];
		$kassa = $array['kassa'];
		$state = $array['state'];
		$plan_data = explode(" — ",$array['plan_data']);
		$fact_data = explode(" — ",$array['fact_data']);
		$valut = $array['valut'];
		$dept = $array['dept'];
		$tp = "PROPERTY_";

		$arSort = [
			"PROPERTY_DATA" => "DESC"
		];

		$arFilter = [
			$find_in_out,
			"ACTIVE" => "Y",
		];

		if($type == "in"){
			$arFilter["IBLOCK_ID"] = IBLOCK_IN;
		}else{
			$arFilter["IBLOCK_ID"] = IBLOCK_OUT;
		}

		if(!$GLOBALS["USER"]->IsAdmin()){
			/* CHECK PERMISSION BY DEPT */
			$dept_perm = $this->getUserDept($GLOBALS["USER"]->GetID());
			if(!array_keys($dept_perm, REDACTOR_PERM_DEPT)){
				$arFilter["CREATED_BY"] = [$GLOBALS["USER"]->GetID()];
			}
		}

		if($in_out == "find_out"){
			if($plan_fact == "find_fact"){
				$arFilter[$tp."TYPE_STATUS"] = PARAM_OUT_FACT;
			}
			if($plan_fact == "find_plan"){
				$arFilter[$tp."TYPE_STATUS"] = PARAM_OUT_PLAN;
			}
		}else{
			if($plan_fact == "find_fact"){
				$arFilter[$tp."TYPE_STATUS"] = PARAM_IN_FACT;
			}
			if($plan_fact == "find_plan"){
				$arFilter[$tp."TYPE_STATUS"] = PARAM_IN_PLAN;
			}
		}

		if($kassa){
			$arFilter[$tp."KASSA.ID"] = $kassa;
		}
		if($state){
			$arFilter[$tp."STATE.ID"] = $state;
		}

		if(count($plan_data)>1){
			$arFilter[">=".$tp."DATA"] = date('Y-m-d',strtotime($plan_data[0]));
			$arFilter["<=".$tp."DATA"] = date('Y-m-d',strtotime($plan_data[1]));
		}

		if(count($fact_data)>1){
			$arFilter[">=".$tp."FACT_DATA"] = date('Y-m-d',strtotime($fact_data[0]));
			$arFilter["<=".$tp."FACT_DATA"] = date('Y-m-d',strtotime($fact_data[1]));
		}
		if($valut){
			$arFilter["%".$tp."ORIGINAL"] = $valut;
		}
		if($dept){
			$arFilter[$tp."OTDEL"] = $dept;
		}

		$arSelect = [
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DATE_CREATE",
			"ACTIVE_TO",
			"CREATED_BY",
			"PROPERTY_*" 
		];
		$res = CIBlockElement::GetList($arSort,$arFilter,false,false,$arSelect);
		while($obj = $res->GetNextElement()){
			$ret[] = array_merge($obj->GetFields(), $obj->GetProperties());
		}

		return $ret;
	} 

	public function add($data){

		$in_out = $data['in_out'];
		$plan_fact = $data['plan_fact'];

		if($in_out == "in"){
			$in_out_id = IBLOCK_IN;
			if($plan_fact == "fact"){
				$props_v2["TYPE_STATUS"] = ["VALUE"=> 60];
			}
			if($plan_fact == "plan"){
				$props_v2["TYPE_STATUS"] = ["VALUE"=> 61];
			}
		}
		
		if($in_out == "out"){
			$in_out_id = IBLOCK_OUT;
			if($plan_fact == "fact"){
				$props_v2["TYPE_STATUS"] = ["VALUE"=> PARAM_OUT_FACT];
			}
			if($plan_fact == "plan"){
				$props_v2["TYPE_STATUS"] = ["VALUE"=> PARAM_OUT_PLAN];
			}
		}

		// create iblock
		$el = new CIBlockElement;
		$props = [
			"KOGO_OTSENIVALI" => $to_user_prop,
			"OTSENILI" => $get_users_prop,
			"KOMPETENTSII" => implode(",", $compitentions),
			"TEKH_POLE" => $compet,
		];
 
		$props_v2["SUM"] = $data['sum'];
		$props_v2["STATE"] = $data['state'];
		$props_v2["DATA"] = new \Bitrix\Main\Type\Date($data['date']);
		$props_v2["KASSA"] = $data['kassa'];

		$arArray = [
			"MODIFIED_BY"    => 1,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => $in_out_id,
			"PROPERTY_VALUES"=> $props_v2,
			"NAME"           => $data['comment'],
			"ACTIVE"         => "Y",
			//"ACTIVE_TO"		 => $_POST["input_dend"],
		];

		$ret = $el->Add($arArray);
		return $ret;
	}

	public function getEditById($id, $type){

		if($type == "in"){
			$arFilter['IBLOCK_ID'] = IBLOCK_IN;
		}else{
			$arFilter['IBLOCK_ID'] = IBLOCK_OUT;
		}

		$arSort = [ 
			"PROPERTY_DATA" => "ASC"
		];
 
		$arFilter = [
			"ID" => $id,
			//"IBLOCK_ID" => IBLOCK_OUT,
			"ACTIVE" => "Y",
		];

		$arSelect = [
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DATE_CREATE",
			"ACTIVE_TO",
			"CREATED_BY",
			"PROPERTY_*" 
		];
		$res = CIBlockElement::GetList($arSort,$arFilter,false,false,$arSelect);
		if($obj = $res->GetNextElement()){
			$ret = array_merge($obj->GetFields(), $obj->GetProperties());
		}

		return $ret;
	}

	public function edit($data,$type){

		if($type == "in"){
			$code = PARAM_IN_FACT;
		}else{
			$code = PARAM_OUT_FACT;
		}
		
		if(CIBlockElement::SetPropertyValueCode($data, "TYPE_STATUS", ["VALUE"=> $code]) &&
			CIBlockElement::SetPropertyValueCode($data, "FACT_DATA", new \Bitrix\Main\Type\Date(date('d.m.Y')))){
			return true;
		}
	}

	public function del($data){
		$el = new CIBlockElement;
		$dataEl = [
			"ACTIVE" => "N"
		];
		$e = $el->Update($data,$dataEl);
		return $e;
	}
	
	private function closeTaskId($id){
		$ct = new \Bitrix\Tasks\Item\Task($id);
		$ct->complete();
		return true;
	}

	public function getTaskIdClose($id){
		if($type == "in"){
			$iblock = IBLOCK_IN;
		}else{
			$iblock = IBLOCK_OUT;
		}
		$task_id = CIBlockElement::GetProperty($iblock,$id,["sort"=>"asc"],['CODE'=>'TASK_ID'])->Fetch()['VALUE'];
		if(isset($task_id)){
			$task = new \Bitrix\Tasks\Item\Task($task_id);
			$this->closeTaskId($task_id);
			return $task->getData();
		}else{
			return false;
		}
	}

	public function set($string){
		return $string;
	}
	
	public function startFinanceOut(){
		// create iblock
		$el = new CIBlockElement;
		$arArray = Array(
			"CREATED_BY"=> $GLOBALS["USER"]->GetID(),
			"IBLOCK_ID"=> LIST_FINANCE,
			"ACTIVE" => "Y",
			"NAME" => "_".date('Y-m-d_H:i:s')
		);
		$ret = $el->Add($arArray);
		print_r($ret);
		// settings for start
		$arErrorsTmp = [];
		$workflowTemplateId = LIST_FINANCE_TEMPLATE;
		$documentId = $ret;

		// start now
		$wfId = CBPDocument::StartWorkflow(
		   $workflowTemplateId,
		   ["lists", "BizprocDocument", $documentId],
		   array_merge([$arWorkflowParameters], ["TargetUser" => "user_".intval($GLOBALS["USER"]->GetID())]),
		   $arErrorsTmp
		);

		return LocalRedirect("/company/personal/bizproc/{$wfId}/?back_url=%2F"._U."%2F");
	}

	public function startLimits(){
		// create iblock
		$el = new CIBlockElement;
		$arArray = Array(
			"CREATED_BY"=> $GLOBALS["USER"]->GetID(),
			"IBLOCK_ID"=> LIST_FINANCE_LIMIT,
			"ACTIVE" => "Y",
			"NAME" => "_".date('Y-m-d_H:i:s')
		);
		$ret = $el->Add($arArray);
		print_r($ret);
		// settings for start
		$arErrorsTmp = [];
		$workflowTemplateId = LIST_FINANCE_LIMIT_TEMPLATE;
		$documentId = $ret;

		// start now
		$wfId = CBPDocument::StartWorkflow(
		   $workflowTemplateId,
		   ["lists", "BizprocDocument", $documentId],
		   array_merge([$arWorkflowParameters], ["TargetUser" => "user_".intval($GLOBALS["USER"]->GetID())]),
		   $arErrorsTmp
		);

		return LocalRedirect("/company/personal/bizproc/{$wfId}/?back_url=%2F"._U."%2F");
	}

	public function startFinanceIn(){
		// create iblock
		$el = new CIBlockElement;
		$arArray = Array(
			"CREATED_BY"=> $GLOBALS["USER"]->GetID(),
			"IBLOCK_ID"=> LIST_FINANCE_IN,
			"ACTIVE" => "Y",
			"NAME" => "_".date('Y-m-d_H:i:s')
		);
		$ret = $el->Add($arArray);
		print_r($ret);
		// settings for start
		$arErrorsTmp = [];
		$workflowTemplateId = LIST_FINANCE_IN_TEMPLATE;
		$documentId = $ret;

		// start now
		$wfId = CBPDocument::StartWorkflow(
		   $workflowTemplateId,
		   ["lists", "BizprocDocument", $documentId],
		   array_merge([$arWorkflowParameters], ["TargetUser" => "user_".intval($GLOBALS["USER"]->GetID())]),
		   $arErrorsTmp
		);

		return LocalRedirect("/company/personal/bizproc/{$wfId}/?back_url=%2F"._U."%2F");
	}

	public function sendMessage(int $fromUserId, int $toUserId, string $message)
	{
		if (!\Bitrix\Main\Loader::includeModule('im')){
			throw new \Bitrix\Main\LoaderException('Unable to load IM module');
		}

		$fields = [
			"TO_USER_ID" => $fromUserId,
			"FROM_USER_ID" => $toUserId,
			"MESSAGE_TYPE" => "S", 
			"NOTIFY_MODULE" => "im",
			"NOTIFY_MESSAGE" => $message,
		];

		$msg = new \CIMMessenger();
		if (!$msg->Add($fields)) {
			$e = $GLOBALS['APPLICATION']->GetException();
			throw new \Bitrix\Main\SystemException($e->GetString());
		}
		
		return true;
	}

	public function exportCSV($data,$type){
		
		$is_type = false;
		if($type == "plan"){
			$is_type = true;
		}
		
		$GLOBALS['APPLICATION']->RestartBuffer();
		Header("Content-Type: application/force-download");
		Header("Content-Type: application/octet-stream");
		Header("Content-Type: application/download");
		Header("Content-Disposition: attachment;filename=".$type."_export_finance_".date('d.m.Y_H:i:s').".csv");
		Header("Content-Transfer-Encoding: binary");

		$out = fopen('php://output', 'w');

		/* ДОХОДЫ */
		if(count((array)$data['IN'])>0){
			$exp_name = [
				"Планируемая дата оплаты",
				"Фактическая дата оплаты", 
				"Тип", 
				"Оригинал",
				"Сумма, ₽",
				"Касса",
				"Отдел",
				"Статья расхода",
				"Комментарий"
			];

			fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($out,["ДОХОДЫ"]);
			fputcsv($out,[]);
			fputcsv($out,$exp_name,";");

			
			if($is_type){
			
				foreach($data['IN'] as $val){

					if($val['TYPE_STATUS']['VALUE'] == "План"){

						$exp = [
							$val['DATA']['VALUE'],
							$val['FACT_DATA']['VALUE'],
							$val['TYPE_STATUS']['VALUE'],
							$this->getMoneyFormat($val['ORIGINAL']['VALUE']),
							$this->getMoneyFormat($val['SUM']['VALUE']),
							$val['KASSA']['VALUE'] ? CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME'] : CIBlockElement::GetByID(CIBlockElement::GetProperty(IBLOCK_OUT_STATE,$val['STATE']['VALUE'],$arSort,['CODE'=>'KASSA'])->GetNext()['VALUE'])->GetNext()['NAME'],
							$val['OTDEL']['VALUE'] ? CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME'] : '',
							$val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'],
							$val['NAME']
						];
						fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
						fputcsv($out,$exp,";");

					}

				}
				
			}else{

				foreach($data['IN'] as $val){

					if($val['TYPE_STATUS']['VALUE'] == "Факт"){

						$exp = [
							$val['DATA']['VALUE'],
							$val['FACT_DATA']['VALUE'],
							$val['TYPE_STATUS']['VALUE'],
							$this->getMoneyFormat($val['ORIGINAL']['VALUE']),
							$this->getMoneyFormat($val['SUM']['VALUE']),
							$val['KASSA']['VALUE'] ? CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME'] : CIBlockElement::GetByID(CIBlockElement::GetProperty(IBLOCK_OUT_STATE,$val['STATE']['VALUE'],$arSort,['CODE'=>'KASSA'])->GetNext()['VALUE'])->GetNext()['NAME'],
							$val['OTDEL']['VALUE'] ? CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME'] : '',
							$val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'],
							$val['NAME']
						];
						fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
						fputcsv($out,$exp,";");

					}

				}
			}

		}

		/* РАСХОДЫ */
		if(count((array)$data['OUT'])>0){
			
			$exp_name = [
				"Планируемая дата оплаты",
				"Фактическая дата оплаты", 
				"Тип", 
				"Оригинал",
				"Сумма, ₽",
				"Касса",
				"Отдел",
				"Статья расхода",
				"Комментарий"
			];

			fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($out,[]);
			fputcsv($out,["РАСХОДЫ"]);
			fputcsv($out,[]);
			fputcsv($out,$exp_name,";");

			if($is_type){

				foreach($data['OUT'] as $val){

					if($val['TYPE_STATUS']['VALUE'] == "План"){

						$exp = [
							$val['DATA']['VALUE'],
							$val['FACT_DATA']['VALUE'],
							$val['TYPE_STATUS']['VALUE'],
							$this->getMoneyFormat($val['ORIGINAL']['VALUE']),
							$this->getMoneyFormat($val['SUM']['VALUE']),
							$val['KASSA']['VALUE'] ? CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME'] : CIBlockElement::GetByID(CIBlockElement::GetProperty(IBLOCK_OUT_STATE,$val['STATE']['VALUE'],$arSort,['CODE'=>'KASSA'])->GetNext()['VALUE'])->GetNext()['NAME'],
							$val['OTDEL']['VALUE'] ? CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME'] : '',
							$val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'],
							$val['NAME']
						];
						fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
						fputcsv($out,$exp,";");
		
					}

				}
			
			}else{

				foreach($data['OUT'] as $val){

					if($val['TYPE_STATUS']['VALUE'] == "Факт"){

						$exp = [
							$val['DATA']['VALUE'],
							$val['FACT_DATA']['VALUE'],
							$val['TYPE_STATUS']['VALUE'],
							$this->getMoneyFormat($val['ORIGINAL']['VALUE']),
							$this->getMoneyFormat($val['SUM']['VALUE']),
							$val['KASSA']['VALUE'] ? CIBlockElement::GetByID($val['KASSA']['VALUE'])->GetNext()['NAME'] : CIBlockElement::GetByID(CIBlockElement::GetProperty(IBLOCK_OUT_STATE,$val['STATE']['VALUE'],$arSort,['CODE'=>'KASSA'])->GetNext()['VALUE'])->GetNext()['NAME'],
							$val['OTDEL']['VALUE'] ? CIBlockSection::GetByID($val['OTDEL']['VALUE'])->Fetch()['NAME'] : '',
							$val['STATE']['VALUE']=="-999" ? "незапланированная статья" : CIBlockElement::GetByID($val['STATE']['VALUE'])->GetNext()['NAME'],
							$val['NAME']
						];
						fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
						fputcsv($out,$exp,";");
		
					}

				}
			
			}

			fclose($out);
		}

		return true;
	}

	public static function strlenComment($cmt){
		$cnt = 100;
		if(mb_strlen($cmt)>=$cnt){
			$return = '<span title="'.$cmt.'">'.mb_substr($cmt,0,$cnt).'...</span>';
		}else{
			$return = $cmt;
		}

		return $return;
	}
	
	public function getUsersId($id){
		$filter = [
			"ACTIVE" => "Y",
		];
		
		$userId = CUser::GetByID($id)->Fetch();
		foreach($userId["UF_DEPARTMENT"] as $val){
			$filter["UF_DEPARTMENT"] = $val;
		}
		
		$user = CUser::GetList([$by="personal_country"], [$order="desc"], $filter);
		while($rr = $user->Fetch()){
			$gg[] = $rr["ID"];
		}
		
		return $gg;
		
	}
	
	public function getUserDept($id){
		$userId = CUser::GetByID($id)->Fetch();
		foreach($userId["UF_DEPARTMENT"] as $val){
			$dept[] = $val;
		}
		return $dept;
	}
	
	public function statsMain($array){
		
		foreach($array as $val_ras){
			if($val_ras['TYPE_STATUS']['VALUE_XML_ID'] == "FACT"){
				if($val_ras['KASSA'] && !empty($val_ras['KASSA']['VALUE'])){
					if($val_ras['OTDEL'] && !empty($val_ras['OTDEL']['VALUE'])){
						$ras[CIBlockElement::GetByID($val_ras['KASSA']['VALUE'])->Fetch()["NAME"]][CIBlockSection::GetByID($val_ras['OTDEL']['VALUE'])->Fetch()["NAME"]] += $val_ras['SUM']['VALUE'];
						//$ras[CIBlockSection::GetByID($val_ras['OTDEL']['VALUE'])->Fetch()["NAME"]] += $val_ras['SUM']['VALUE'];
					}
				}
			}
		}
		
		return $ras;
		
	}
	
	public function statsMainPlan($array){
		
		foreach($array as $val_ras){
			if($val_ras['TYPE_STATUS']['VALUE_XML_ID'] == "PLAN"){
				if($val_ras['KASSA'] && !empty($val_ras['KASSA']['VALUE'])){
					if($val_ras['OTDEL'] && !empty($val_ras['OTDEL']['VALUE'])){
						$ras[CIBlockElement::GetByID($val_ras['KASSA']['VALUE'])->Fetch()["NAME"]][CIBlockSection::GetByID($val_ras['OTDEL']['VALUE'])->Fetch()["NAME"]] += $val_ras['SUM']['VALUE'];
						//$ras[CIBlockSection::GetByID($val_ras['OTDEL']['VALUE'])->Fetch()["NAME"]] += $val_ras['SUM']['VALUE'];
					}
				}
			}
		}
		
		return $ras;
		
	}
}
