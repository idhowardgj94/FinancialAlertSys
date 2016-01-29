<?php
// 定義常數變數
define("COMPANY_ID_INDEX", 0);

// 上傳公司基本資料

// value[] : user input
// value[0] = company id

// step 1 : 檢查id是否存在於資料庫, 若已存在則跳出
// step 2 : 資料庫沒有該id的公司->可新增->組成新增字串
// step 3 : call db_controller_unit insertData()
function insertComapnyBasicData() {
	$insert_value = $_GET['value'];
	$company_id = $insert_value[COMPANY_ID_INDEX];
	
	// 檢查id是否存在於資料庫, 不存在則繼續新增資料
	if( !checkCompany( $GLOBALS [ 'insert_data_class' ], $company_id) ) {
		
		// 組成新增字串
		switch( $GLOBALS [ 'insert_data_class' ] ) {
			case CBASIC_INFO:
				$table_name = "company_basic_information";
				$insert_value_str = "(`company_id`, `company_name`, `company_nickname`, `status`, `sector`, `group`) VALUES ";
				break;
			case CHINA_CBASIC_INFO:
				$table_name = "china_company_basic_information";
				$insert_value_str = "(`company_id`, `company_name`, `company_nickname`, `company_fullname`, `status`) VALUES ";
				break;
		}
		
		if(isset($table_name)) { // 將insert_value組成 新增資料的data str
			$insert_value_str .= "(";
			$insert_value_tem = "";
			for( $i=0; $i<count($insert_value); $i++ ) {
				// value[0] , value[1], value[2]...
				if( $insert_value_tem !== "" )
					$insert_value_tem .= ", ";

				$insert_value_tem .= toInsertString($insert_value[$i]);
			}
			
			if( $GLOBALS [ 'insert_data_class' ] === "china_cbasic_info" )
				$insert_value_tem .= ", 'T'";

			$insert_value_str .= $insert_value_tem . ")";

			// 新增單筆公司基本資料
			$GLOBALS [ 'dbc_object' ]->insertData($table_name, $insert_value_str);
			
			// showInsertMessage(INSERT_SUCCESS);
		}

	} else {
		showInsertMessage(EXISTED_COMPANY_BASIC_DATA); // 該筆公司基本資料已存在於資料庫中
	}
}

// 上傳公司財務資料

// value[] : user input
// value[0] = company id
// value[1] + value[2] = season

// step 1 : 檢查id是否存在於資料庫, 若不存在則跳出
// step 2 : 檢查idxsason財務資料是否存在於資料庫, 若存在則跳出
// setp 3 : 資料庫沒有該idxsason財務資料->可新增->組成新增字串
// step 4 : call db_controller_unit insertData()
function insertComapnyFinancialData() {
	define("YEAR_INDEX", 1);
	define("SEASON_INDEX", 2);
	define("VALUE_AT_RISK_INDEX", 3);

	$insert_value = $_GET['value'];
	$company_id = $insert_value[COMPANY_ID_INDEX];
	$season = $insert_value[YEAR_INDEX] . $insert_value[SEASON_INDEX];
	
	// 檢查id是否存在於資料庫, 若不存在則跳出
	if( checkCompany( $GLOBALS [ 'insert_data_class' ], $company_id) ) {
		// 檢查idxsason財務資料是否存在於資料庫, 若存在則跳出
		if( !checkFinancialInfo( $GLOBALS [ 'insert_data_class' ], $company_id, $season ) ) {
			$value_at_risk = $insert_value[VALUE_AT_RISK_INDEX];
			
			// 組成新增字串
			switch( $GLOBALS [ 'insert_data_class' ] ) {
				case CFINANCIAL_INFO:
					$table_name = "company_financial_information";
					$insert_value_str = '(`company_id`, `season`, `value_at_risk`, `stock`, `cashflow_operating`, `cashflow_investment`, `proceed_fm_newIssue`) VALUES ("'. $company_id .'","'. $season .'", '. $value_at_risk .', null, null, null, null)';
					break;
				case CHINA_CFINANCIAL_INFO:
					$table_name = "china_company_financial_information";
					$insert_value_str = '(`company_id`, `season`, `value_at_risk`, `cashflow_operating`, `cashflow_investment`, `proceed_fm_newIssue`) VALUES ("'. $company_id .'","'. $season .'",'. $value_at_risk .', null, null, null)';
					break;
			}
			
			// 新增單筆公司財務資料
			$GLOBALS [ 'dbc_object' ]->insertData($table_name, $insert_value_str);
		
			// showInsertMessage(INSERT_SUCCESS);
		} else {
			showInsertMessage(EXISTED_VALUE_AT_RISK_DATA); // 該筆財務資料已存在於資料庫中
		}
	} else { // NO_COMPANY_DATA
		showInsertMessage(NO_COMPANY_DATA); // 該公司代號的公司不存在
	}
}

// 上傳危機發生日資料
// value[] : user input
// value[0] = company id

// step 1 : 檢查id是否有公司基本資料存在於資料庫, 若不存在則跳出
// step 2 : 檢查該id是否有危機發生日資料存在於資料庫, 若存在則跳出
// step 2 : 資料庫沒有該id資料->可新增->組成新增字串
// step 3 : call db_controller_unit insertData()
function insertComapnyCrisisDate() {
	define("YEAR_INDEX", 1);
	define("MONTH_INDEX", 2);
	
	$insert_value = $_GET['value'];
	$company_id = $insert_value[COMPANY_ID_INDEX];
	
	// 檢查id是否有公司基本資料存在於資料庫, 若不存在則跳出
	if( checkCompany( TAIWAN, $company_id) ) {
		// 檢查該id是否有危機發生日資料存在於資料庫, 若存在則跳出
		if( !checkCompany( $GLOBALS [ 'insert_data_class' ], $company_id) ) {
			$date_value = $insert_value[YEAR_INDEX] . "." . $insert_value[MONTH_INDEX];
			// 2014.11

			// 組成新增字串
			$table_name = "company_financial_crisis";
			$insert_value_str = '(`company_id`, `crisis_date`) VALUES ("'. $company_id .'","'. $date_value .'")';
			
			// 新增單筆公司危機發生日資料
			$GLOBALS [ 'dbc_object' ]->insertData($table_name, $insert_value_str);
			// showInsertMessage(INSERT_SUCCESS);
		} else {
			showInsertMessage(EXISTED_CRISIS_DATE); // 該公司的危機發生日資料已存在於資料庫中
		}
	} else {
		showInsertMessage(NO_COMPANY_DATA); // 該公司代號的公司不存在
	}
}

// 將string加上字串邊框
// 若值為null則不用加
function toInsertString($str) {
	if( $str !== "null" )
		return '"' . $str . '"';
	else
		return $str;
}

/*
	show insert result message
	no_company_data : 該公司代號的公司不存在
	existed_company_basic_data : 該筆公司基本資料已存在於資料庫中
	existed_value_at_risk_data : 該筆財務資料已存在於資料庫中
	existed_crisis_date : 該公司的危機發生日資料已存在於資料庫中
	insert_success : 成功新增
*/
function showInsertMessage($index) {
	$message = "";
	
	switch($index) {
		case NO_COMPANY_DATA:
			$message = "該公司代號的公司不存在";
			break;
		case EXISTED_COMPANY_BASIC_DATA:
			$message = "該筆公司基本資料已存在於資料庫中";
			break;
		case EXISTED_VALUE_AT_RISK_DATA:
			$message = "該筆財務資料已存在於資料庫中";
			break;
		case EXISTED_CRISIS_DATE:
			$message = "該公司的危機發生日資料已存在於資料庫中";
			break;
		case INSERT_SUCCESS:
			$message = "成功新增";
			break;
	}
	
	echo $message;
}


include 'data_maintain_action.php';

// insert 種類
$insert_data_class = $_GET['table_class'];

// 判斷insert_data_class呼叫對應的insert function
switch($insert_data_class) {
	case CBASIC_INFO:
	case CHINA_CBASIC_INFO:
		insertComapnyBasicData();
		break;
	case CFINANCIAL_INFO:
	case CHINA_CFINANCIAL_INFO:
		insertComapnyFinancialData();
		break;
	case CRISIS_DATE:
		insertComapnyCrisisDate();
		break;
	default:
}



?>