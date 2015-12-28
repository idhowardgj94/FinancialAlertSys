<?php
	// 連結資料庫
	include("./db_controller_unit.php");
	
	//台灣還中國公司分類 中國=china
	$c = $_GET['class'];

	// --取得公司資料
	$company_id = $_GET['id'];
	
	$obj1 = new db_controller_unit;

	$value_at_risk_chart_xy_axis = $obj1->getValueatRiskforTrendChart($c, $company_id);	

	$obj1 = null;
	
	// return data
	echo json_encode($value_at_risk_chart_xy_axis);
?>