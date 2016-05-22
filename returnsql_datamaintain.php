<?php

// 取得公司基本資料
function getCompanyBasicInfo() {
	$condition = $_GET['condition'];
	$cid = $condition[0];
	
	switch( $GLOBALS [ 'return_data_class' ] ) {
		case CBASIC_INFO:
			$table_name = 'company_basic_information';
			break;
		case CHINA_CBASIC_INFO:
			$table_name = 'china_company_basic_information';
			break;
		default:
	}
	
	if(isset($table_name)) {
		$temdata = $GLOBALS [ 'dbn' ]->query('SELECT * FROM `'. $table_name .'` 
			WHERE `company_id` = "'. $cid .'"');
	}
	
	if(!empty($temdata)) {
		$data_row=mysqli_fetch_row($temdata);
		echo json_encode($data_row);
	}
}

// 取得公司財務資料
function getComapnyFinancialInfo() {
	$condition = $_GET['condition'];
	$cid = $condition[0];
	$cseason = $condition[1];
	
	switch( $GLOBALS [ 'return_data_class' ] ) {
		case CFINANCIAL_INFO:
			$table_name = 'company_financial_information';
			break;
		case CHINA_CFINANCIAL_INFO:
			$table_name = 'china_company_financial_information';
			break;
		default:
	}

	if(isset($table_name)) {
		$temdata = $GLOBALS [ 'dbn' ]->query('SELECT *
			FROM `'. $table_name .'`
			WHERE `company_id` = "'. $cid .'" AND `season` = "'. $cseason .'"');
	}
	
	if(!empty($temdata)) {
		$data_row=mysqli_fetch_row($temdata);
		echo json_encode($data_row);
	}
}

// 取得前百大公司清單
function getTop100CompanyList() {
	$condition = $_GET['condition'];
	$cyear = $condition[0];
		
	// get season
	$temdata = $GLOBALS [ 'dbn' ]->query('SELECT DISTINCT `season`
			FROM `top_100_company`
			WHERE `season` LIKE "'. $cyear .'__"');
	if(!empty($temdata)) {
		$data_row=mysqli_fetch_row($temdata);
		$cseason = $data_row[0];
	}
		
	$temdata = $GLOBALS [ 'dbn' ]->query('SELECT DISTINCT a.`company_id`, a.`company_nickname` 
		FROM `company_basic_information` a, `top_100_company` b 
		WHERE a.`company_id` = b.`company_id` AND b.`season` = "'. $cseason .'" ORDER BY b.`rank` ASC');
		
	$data_num = 0;

	if(!empty($temdata)) {
		for ($i=0; $i<mysqli_num_rows($temdata); $i++) {
			$temdata_row=mysqli_fetch_row($temdata);
			$com_id[$data_num] = $temdata_row[0];
			$com_name[$data_num] = $temdata_row[1];
			$data_num++;
		}
	}

	$com_list = array(
		$com_id, $com_name
	);
	
	echo json_encode($com_list);
}

// 取得前百大財務資料
function getTop100InfoValue() {
	$condition = $_GET['condition'];
	$cid = $condition[0];
	$cyear = $condition[1];
	$col_title = $condition[2];
	$is_top100 = 1;
	
	// get season
	$temdata = $GLOBALS [ 'dbn' ]->query('SELECT DISTINCT `season`
			FROM `top_100_company`
			WHERE `season` LIKE "'. $cyear .'__"');
	if(!empty($temdata)) {
		$data_row=mysqli_fetch_row($temdata);
		$cseason = $data_row[0];
	}

	if($col_title!==null) {
		if($col_title=='gross_margin' 
		OR $col_title=='operating_income' 
		OR $col_title=='eps' 
		OR $col_title=='roa' 
		OR $col_title=='roe')
			$is_top100 = 0;
			
		if($is_top100)
			$table_name = 'top_100_company';
		else
			$table_name = 'financial_index_all';
		
		$temdata = $GLOBALS [ 'dbn' ]->query('SELECT `'. $col_title .'`
			FROM `'. $table_name .'`
			WHERE `company_id`="'. $cid .'" AND `season` = "'. $cseason .'"');

		if(!empty($temdata)) {
			$data_row=mysqli_fetch_row($temdata);
			echo json_encode($data_row[0]);
		}
	}
}

// 取得財務指標資料
function getFinancialIndexValue() {
	$condition = $_GET['condition'];
	$cid = $condition[0];
	$cseason = $condition[1];
	$col_title = $condition[2];
	
	if($col_title!==null) {
		$temdata = $GLOBALS [ 'dbn' ]->query('SELECT `'. $col_title .'`
			FROM `financial_index_all`
			WHERE `company_id`="'. $cid .'" AND `season` = "'. $cseason .'"');

		if(!empty($temdata)) {
			$data_row=mysqli_fetch_row($temdata);
			echo json_encode($data_row[0]);
		}
	}
}

// 取得產業 企業集團財務資料
function getSectorGroupInfoValue() {
	$condition = $_GET['condition'];
	$name = $condition[0];
	$season = $condition[1];
	$col_title = $condition[2];

	if($col_title!==null) {
		$temdata = $GLOBALS [ 'dbn' ]->query('SELECT `'. $col_title .'`
			FROM `sector_group_financial_information`
			WHERE `name`="'. $name .'" AND `season` = "'. $season .'"');

		if(!empty($temdata)) {
			$data_row=mysqli_fetch_row($temdata);
			echo json_encode($data_row[0]);
		}
	}
}

// 取得危機發生日公司清單
function getCrisisCompanyList() {
	$temdata = $GLOBALS [ 'dbn' ]->query('SELECT DISTINCT a.`company_id`, a.`company_nickname` 
		FROM `company_basic_information` a, `company_financial_crisis` b 
		WHERE a.`company_id` = b.`company_id` ORDER BY a.`company_id` ASC');
		
	$data_num = 0;

	if(!empty($temdata)) {
		for ($i=0; $i<mysqli_num_rows($temdata); $i++) {
			$temdata_row=mysqli_fetch_row($temdata);
			$com_id[$data_num] = $temdata_row[0];
			$com_name[$data_num] = $temdata_row[1];
			$data_num++;
		}
	}

	$com_list = array(
		$com_id, $com_name
	);
	
	echo json_encode($com_list);
}

// 取得公司危機發生日資料
function getCrisisDateValue() {
	$condition = $_GET['condition'];
	$cid = $condition[0];
	
	$temdata = $GLOBALS [ 'dbn' ]->query('SELECT `crisis_date`
			FROM `company_financial_crisis`
			WHERE `company_id` = "'. $cid .'"');
	if(!empty($temdata)) {
		$data_row=mysqli_fetch_row($temdata);
		echo json_encode($data_row[0]);
	}
}

include 'data_maintain_action.php';
include_once 'constant_definition.php';
$dbn = $dbc_object->connect_DB();
$return_data_class = $_GET['data_class'];
// 根據return_data_class呼叫對應取資料function
switch($return_data_class) {
	case CBASIC_INFO:
	case CHINA_CBASIC_INFO:
		getCompanyBasicInfo();
		break;
	case CFINANCIAL_INFO:
	case CHINA_CFINANCIAL_INFO:
		getComapnyFinancialInfo();
		break;
	case TOP100_DATA:
		getTop100InfoValue();
		break;
	case CFINANCIAL_INDEX:
		getFinancialIndexValue();
		break;
	case SECTOR_GROUP_INFO:
		getSectorGroupInfoValue();
		break;
	case CRISIS_DATE:
		getCrisisDateValue();
		break;
	case TOP100_COMPANYLIST:
		getTop100CompanyList();
		break;
	case CRISIS_COMPANYLIST:
		getCrisisCompanyList();
		break;
	default:
}
	
mysqli_close($dbn);
//echo null;
?>