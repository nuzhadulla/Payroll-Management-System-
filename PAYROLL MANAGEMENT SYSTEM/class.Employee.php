<?php
class employee{

//Change Field Name & Form Variable as per your DB and Form

	function doInsertNewEmployee($objArray){
		global $objCommon,$global_config;
		
		$i=0;
		$objArray['employeedate'];
		
		$updateData[$i]["Field"]	 = "name";
		$updateData[$i++]["Value"]	 = $objArray['name'];
		
		$updateData[$i]["Field"]	 = "code";
		$updateData[$i++]["Value"]	 = $objArray['code'];
		
		if(checkEmptyDate($objArray['join_date'])){
			$updateData[$i]["Field"]	 = "join_date";
			$updateData[$i++]["Value"]	 = dateFormat($objArray['join_date']);
		}
		$updateData[$i]["Field"]	 = "designation";
		$updateData[$i++]["Value"]	 = $objArray['designation'];
		
		$updateData[$i]["Field"]	 = "salary";
		$updateData[$i++]["Value"]	 = $objArray['salary'];

		$updateData[$i]["Field"]	 = "gross_salary";
		$updateData[$i++]["Value"]	 = $objArray['gross_salary'];
				
		$updateData[$i]["Field"]	 = "email_official";
		$updateData[$i++]["Value"]	 = $objArray['email_official'];
		
		$updateData[$i]["Field"]	 = "email_nonofficial";
		$updateData[$i++]["Value"]	 = $objArray['email_nonofficial'];
				
		$updateData[$i]["Field"]	 = "date_of_birth";
		$updateData[$i++]["Value"]	 =  $objArray['employeeyear'].'-'.$objArray['employeemonth'].'-'.$objArray['employeedate'];
		
		$updateData[$i]["Field"]	 = "gender";
		$updateData[$i++]["Value"]	 = $objArray['gender'];

		$updateData[$i]["Field"]	 = "marital_status";
		$updateData[$i++]["Value"]	 = $objArray['marital_status'];

		$updateData[$i]["Field"]	 = "qualification";
		$updateData[$i++]["Value"]	 = $objArray['qualification'];
		
		$updateData[$i]["Field"]	 = "mobile_no";
		$updateData[$i++]["Value"]	 = $objArray['mobile_no'];

		$updateData[$i]["Field"]	 = "phone_no";
		$updateData[$i++]["Value"]	 = $objArray['phone_no'];

		$updateData[$i]["Field"]	 = "blood_group";
		$updateData[$i++]["Value"]	 = $objArray['blood_group'];

		$updateData[$i]["Field"]	 = "communication_address";
		$updateData[$i++]["Value"]	 = $objArray['communication_address'];
	
		$updateData[$i]["Field"]	 = "permanant_address";
		$updateData[$i++]["Value"]	 = $objArray['permanant_address'];

		$updateData[$i]["Field"]	 = "passport_no";
		$updateData[$i++]["Value"]	 = $objArray['passport_no'];
		
		if(checkEmptyDate($objArray['passport_expiry_date'])){
			$updateData[$i]["Field"]	 = "passport_expiry_date";
			$updateData[$i++]["Value"]	 = dateFormat($objArray['passport_expiry_date']);
		}
	
		$updateData[$i]["Field"]	 = "pan_card_no";
		$updateData[$i++]["Value"]	 = $objArray['pan_card_no'];

		$updateData[$i]["Field"]	 = "photo";
		$updateData[$i++]["Value"]	 = $objArray['photo'];

		$updateData[$i]["Field"]	 = "bank_acc_no";
		$updateData[$i++]["Value"]	 = $objArray['bank_acc_no'];
		
		$updateData[$i]["Field"]	 = "previous_exp";
		$updateData[$i++]["Value"]	 = $objArray['previous_exp'];
		if($objArray['previous_exp']=='yes'){
		$updateData[$i]["Field"]	 = "previous_exp_years";
		$updateData[$i++]["Value"]	 = $objArray['previous_exp_years'];
		}
		$updateData[$i]["Field"]	 = "previous_exp_months";
		$updateData[$i++]["Value"]	 = $objArray['previous_exp_months'];
		
		$updateData[$i]["Field"]	 = "company_name";
		$updateData[$i++]["Value"]	 = $objArray['company_name'];

		$updateData[$i]["Field"]	 = "last_drawn_salary";
		$updateData[$i++]["Value"]	 = $objArray['last_drawn_salary'];
		
		$updateData[$i]["Field"]	 = "added_date";
		$updateData[$i++]["Value"]	 = date('Y-m-d H:i:s');
						
		$updateData[$i]["Field"]	 = "tds_deduction";
		$updateData[$i++]["Value"]	 = $objArray['tds_deduction'];
		
		$updateData[$i]["Field"]	 = "pftype";
		$updateData[$i++]["Value"]	 = $objArray['pftype'];
				
		$updateData[$i]["Field"]	 = "pfamount";
		$updateData[$i++]["Value"]	 = $objArray['pfamount'];

		$updateData[$i]["Field"]	 = "pfnumber";
		$updateData[$i++]["Value"]	 = $objArray['pfnumber'];
		
		$updateData[$i]["Field"]	 = "note";
		$updateData[$i++]["Value"]	 = $objArray['note'];
		
		$updateData[$i]["Field"]	 = "first_appraisal";
		$updateData[$i++]["Value"]	 = $objArray['first_appraisal'];
		
		$updateData[$i]["Field"]	 = "guardian_name";
		$updateData[$i++]["Value"]	 = $objArray['guardian_name'];
		
		$updateData[$i]["Field"]	 = "esiamount";
		$updateData[$i++]["Value"]	 = $objArray['esi_amount'];
		
		$insertID = $objCommon->doInsert(EMPLOYEE,$updateData);
		if($insertID){
		  $this->insertEmployeeSalary($insertID,$objArray['salary']);
		}
		return $insertID;
	}
	function doUpdateNewEmployee($objDetails){
		global $objCommon,$global_config;
		$updateData['id'] 						=	$objDetails["id"];
		$updateData['emp_name'] 				=	$objDetails["name"];
		$updateData['emp_code'] 				=	$objDetails["code"];

		if(checkEmptyDate($objDetails['join_date'])){
			$updateData['emp_join_date'] 			=	dateFormat($objDetails['join_date']);
		}
		
		$updateData['emp_designation'] 			= 	$objDetails["designation"];
		$updateData['emp_email_official']		=	$objDetails["email_official"];
		$updateData['emp_email_nonofficial'] 	=	$objDetails["email_nonofficial"];

		$updateData['emp_date_of_birth']		=	$objDetails['employeeyear'].'-'.$objDetails['employeemonth'].'-'.$objDetails['employeedate'];
		$updateData['emp_gender'] 				=	$objDetails["gender"];
		$updateData['emp_marital_status'] 		=	$objDetails["marital_status"];
		$updateData['emp_qualification'] 		=	$objDetails["qualification"];
		$updateData['emp_mobile_no'] 			= 	$objDetails["mobile_no"];
		$updateData['emp_phone_no']				=	$objDetails["phone_no"];
		$updateData['emp_blood_group']			=	$objDetails["blood_group"];		
		
		$updateData['emp_communication_address']=	$objDetails["communication_address"];
		$updateData['emp_permanant_address'] 	=	$objDetails["permanant_address"];
		$updateData['emp_passport_no'] 			=	$objDetails["passport_no"];
		
		if(checkEmptyDate($objDetails['passport_expiry_date'])){
		$updateData['emp_passport_expiry_date'] = 	dateFormat($objDetails['passport_expiry_date']);
		}
		$updateData['emp_pan_card_no']			=	$objDetails["pan_card_no"];
		if($objDetails["photo"]){
		$updateData['emp_photo']				=	$objDetails["photo"];
		}
		$updateData['emp_bank_acc_no'] 			=	$objDetails["bank_acc_no"];
		$updateData['emp_added_date'] 			=	date('Y-m-d H:i:s');
		$updateData['emp_previous_exp'] 		=	$objDetails["previous_exp"];
		$updateData['emp_company_name'] 		=	$objDetails["company_name"];
		$updateData['emp_last_drawn_salary']	=	$objDetails["last_drawn_salary"];
		$updateData['emp_notice_period']		=	$objDetails["notice_period"];
		if(checkEmptyDate($objDetails['releaving_date'])){
		$updateData['emp_releaving_date']		=	dateFormat($objDetails['releaving_date']);
		}
		$updateData['emp_releaving_reason']		=	$objDetails["releaving_reason"];
		$updateData['emp_rehire']				=	$objDetails["rehire"];
		$updateData['emp_status']				=	$objDetails["status"];
				
		if($objDetails['previous_exp']=='yes'){
			$updateData['emp_previous_exp_years']	=	$objDetails["previous_exp_years"];
			$updateData['emp_previous_exp_months']	=	$objDetails["previous_exp_months"];
		}else{
			$updateData['emp_previous_exp_years']	=	0;
			$updateData['emp_previous_exp_months']	=	0;
		}
		
		$updateData['emp_tds_deduction']			=	$objDetails["tds_deduction"];
		$updateData['emp_pftype']					=	$objDetails["pftype"];
		$updateData['emp_pfamount']					=	$objDetails["pfamount"];
		$updateData['emp_pfnumber']					=	$objDetails["pfnumber"];
		$updateData['emp_note']						=	$objDetails["note"];
		$updateData['emp_first_appraisal']			=	$objDetails["first_appraisal"];
		$updateData['emp_guardian_name']			=	$objDetails["guardian_name"];
		$updateData['emp_esiamount']				=	$objDetails["esiamount"];		
		
		$WhereClause = " Where id='".$updateData['id']."'";
		$res = $objCommon->UpdateInfoToDB($updateData,'emp_',EMPLOYEE,$WhereClause);

		/** Added code for  Salary Update :: START */
		if ($updateData['id'] && $objDetails['salary'] > 0) {
			$sql = "SELECT current_salary FROM ".APPRAISAL." WHERE emp_id = '".(int)$updateData['id']."' Order by id DESC limit 0,1";
			$res = $objCommon->getSelectQuery($sql);	
			if ($res[0]['current_salary'] != $objDetails["salary"]) {
				$updateData1['emp_salary']	=	$objDetails["salary"];
				$WhereClause1 				=	" Where id='".$updateData['id']."'";
				$res = $objCommon->UpdateInfoToDB($updateData1,'emp_',EMPLOYEE,$WhereClause1);
				$this->insertEmployeeSalary($updateData['id'], $objDetails['salary']);
			}
		}
		/** Added code for  Salary Update :: END */
		return $res;
	}

	function doGetEmployees($strWhereCon='',$limit='',$strField='')
	{
		global $objCommon,$global_config;

		if ($limit) {
			$sql = "SELECT * FROM ".EMPLOYEE." WHERE $strWhereCon ORDER BY name ASC $limit";
		} else {
			if($strField=='')
				$sql = "SELECT * FROM ".EMPLOYEE." WHERE $strWhereCon ORDER BY name ASC";
			else
				$sql = "SELECT id,name,join_date,code,pan_card_no,tds_deduction FROM ".EMPLOYEE." WHERE $strWhereCon ORDER BY name ASC";	
		}
		
		$res = $objCommon->getSelectQuery($sql);
		return $res;
	}
	
}
?>