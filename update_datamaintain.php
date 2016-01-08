<?php
/*
	update Data for single cell value

	update_class : col_name
	value : update_value
	condition : cid or name | season or year
	condition_str : last combined condition string
*/
function modifyConditionData($modify_class) {
	$update_class = $_GET['update_class'];
	$update_value = $_GET['value'];
	
	$condition = $_GET['condition']; // cid or name | season or year

	$table_name = getTableName($modify_class);
	
	// 判斷 condition1 的種類
	switch( $modify_class ) {
		case SECTOR_GROUP_INFO:
			$condition1_col_name = 'name';
			break;
		case TOP100_DATA: // top100用 year 找出對應當年份的季別
			$condition1_col_name = 'company_id';
			$condition[1] = $GLOBALS [ 'dbc_object' ]->getTop100Season($condition[1]);
			$table_name=isTop100Info($update_class); // 確認要修改的項目是財務指標還是前百大資料
			break;
		default:
			$condition1_col_name = 'company_id';
			break;
	}

	// company_id = "1234" AND season = "2013Q2"
	// name = "1234" AND season = "2013Q2"
	// condition_str : 組成欲修改資料的條件
	$condition_str = '`'. $condition1_col_name .'` = "' . $condition[0] . '"';
	if(count($condition)>1)
		$condition_str .= ' AND `season`= "'. $condition[1] .'"';
	
	// 修改資料
	if($table_name)
		$GLOBALS [ 'dbc_object' ]->updateData($table_name, $update_class, $update_value, $condition_str);
}

function getTableName($modify_class) {
	$table_name=null;
	switch($modify_class) {
		case CBASIC_INFO:
			$table_name='company_basic_information';
			break;
		case CRISIS_DATE:
			$table_name='company_financial_crisis';
			break;
		case CFINANCIAL_INFO:
			$table_name='company_financial_information';
			break;
		case CHINA_CBASIC_INFO:
			$table_name='china_company_basic_information';
			break;
		case CHINA_CFINANCIAL_INFO:
			$table_name='china_company_financial_information';
			break;
		case CFINANCIAL_INDEX:
			$table_name='financial_index_all';
			break;
		case SECTOR_GROUP_INFO:
			$table_name='sector_group_financial_information';
			break;
		default:
	}
	return $table_name;
}

// 判斷欲修改的top 100財務資料是否為財務指標
function isTop100Info($update_class) {
	$is_top100 = 1;
	
	if($update_class==='gross_margin' OR 
	$update_class==='operating_income' OR 
	$update_class==='eps' OR 
	$update_class==='roa' OR 
	$update_class==='roe')
		$is_top100 = 0;
		
	if($is_top100)
		$table_name = 'top_100_company';
	else
		$table_name = 'financial_index_all';
		
	return $table_name;
}


include 'data_maintain_action.php';

$class = $_GET['table_class'];
modifyConditionData($class);

?>