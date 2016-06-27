<?php
include 'db_controller_unit.php';

$dbc_object = new db_controller_unit;

// 檢查該id資料是否存在於資料庫
// 依class區分table_name
// 存在 回傳 1
// 不存在 回傳 0
function checkCompany($class, $cid) {
	// check 該公司資料是否存在
	
	$dbn = $GLOBALS [ 'dbc_object' ]->connect_DB();
	
	switch($class) {
		case TAIWAN:
		case CBASIC_INFO:
		case CFINANCIAL_INFO:
			$table_name = "company_basic_information";
			break;
		case CHINA:
		case CHINA_CBASIC_INFO:
		case CHINA_CFINANCIAL_INFO:
			$table_name = "china_company_basic_information";
			break;
		/*case CRISIS_DATE:
			$table_name = "company_financial_crisis";
			break;*/
		default:
	}
	
	if(isset($table_name))
		$tem_companydata = $dbn->query('SELECT * FROM `'. $table_name .'` WHERE `company_id`="'. $cid .'"');

	if(!empty($tem_companydata)) {
		$companydata=mysqli_fetch_row($tem_companydata);
		$GLOBALS [ 'dbc_object' ]->closeDB($dbn);
		if(!empty($companydata))
			return 1;
		else
			return 0;
	}
}
/** 
 * 查循該季別資料是否存在資料庫中
 * @param season: 上傳的資料季別
 * @param table: 要查循的資料表*/
function checkSeasonExist($season, $table){
	global $dbc_object;
	$askData="*";
	$condition[]="season";
	$condition[]=$season;
	$return=$dbc_object->getDatawithCondition($table, $askData, $condition);
	if($return)
		return true;
	return false;
}
// 檢查該筆財務資料是否存在
function checkFinancialInfo($class, $cid, $season) {
	// c : taiwan or china
	
	// check 該id x season資料是否存在
	// if 存在 : return 1
	// else : return 0
	
	$dbn = $GLOBALS [ 'dbc_object' ]->connect_DB();
	
	switch($class) {
		case TAIWAN:
		case CFINANCIAL_INFO:
			$table_name = "company_financial_information";
			break;
		case CHINA:
		case CHINA_CFINANCIAL_INFO:
			$table_name = "china_company_financial_information";
			break;
		default:
	}
	
	if(isset($table_name))
		$tem_companydata = $dbn->query('SELECT * FROM `'. $table_name .'` WHERE `company_id`="'. $cid .'" AND `season`="'. $season .'"');
	if(!empty($tem_companydata)) {
		$companydata=mysqli_fetch_row($tem_companydata);
		if(!empty($companydata))
			return 1;
		else
			return 0;
	}
}
/**
 * 跳出確認視窗
 * 問題：php無法非同步直到使用者按下確認才繼續執行
 * 可能解法：
 * jquery，但要大改架構…
 * */
function confirm($String){
	echo '<script type="text/javascript">';
	echo 'var check = confirm('.$String.')';
	echo 'document.cookie="check="+check';
	echo '</script>'; 
	$check=$_COOKIE['check'];
	if($check=="true")
		return true;
	else 
		return false;
	return true;
}

?>