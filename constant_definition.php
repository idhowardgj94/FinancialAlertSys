<?php
// 定義常數變數
define("TAIWAN", "taiwan");
define("CHINA", "china");
define("tse", "上市");
define("otc", "上櫃");
define("es", "興櫃");
define("gopublic", "公開發行");
define("SECTOR", "sector");
define("GROUP", "group");
define("delisting", "下市櫃");
define("STOCK", "stock");
define("CASHFLOW", "cashflow");

define("CBASIC_INFO", "cbasic_info");
define("CFINANCIAL_INFO", "cfinancial_info");
define("CHINA_CBASIC_INFO", "china_cbasic_info");
define("CHINA_CFINANCIAL_INFO", "china_cfinancial_info");
define("CRISIS_DATE", "crisis_date_info");
define("CFINANCIAL_INDEX", "cfinancial_index");
define("TOP100_DATA", "top100_data");
define("SECTOR_GROUP_INFO", "sector_group_info");

define("TOP100_COMPANYLIST", "top100_companylist");
define("CRISIS_COMPANYLIST", "crisis_companylist");



/*
	insert result message
	no_company_data : 該公司代號的公司不存在
	existed_company_basic_data : 該筆公司基本資料已存在於資料庫中
	existed_value_at_risk_data : 該筆財務資料已存在於資料庫中
	existed_crisis_date : 該公司的危機發生日資料已存在於資料庫中
	insert_success : 成功新增
*/
define("NO_COMPANY_DATA", "no_company_data");
define("EXISTED_COMPANY_BASIC_DATA", "existed_company_basic_data");
define("EXISTED_VALUE_AT_RISK_DATA", "existed_value_at_risk_data");
define("EXISTED_CRISIS_DATE", "existed_crisis_date");
define("INSERT_SUCCESS", "insert_success");

/**以下為資料表名稱**/

define("COMPANYFINANCIALINFORMATION", "company_financial_information");
define("COMPANYBASICINFORMATION", "company_basic_information");
define("CHINACOMPANYFINANCIALINFORMATION", "china_company_financial_information")
?>