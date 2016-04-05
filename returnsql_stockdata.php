<?php
	// 連結資料庫
	include("./db_controller_unit.php");
	
	$company_id = $_GET['id'];
	$obj1 = new db_controller_unit;

	// --取得指定公司的資料
	$stock_chart_xy_axis = $obj1->getStockforTrendChart($company_id);

	$obj1 = null;
	
	// return data
	echo json_encode($stock_chart_xy_axis);
?>