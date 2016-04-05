<?php
	// 連結資料庫
	include("./db_controller_unit.php");
	
	$sectorgroup_name = $_GET['name'];
	$obj1 = new db_controller_unit;

	// --取得指定公司的資料
	$cashflow_chart_xy_axis = $obj1->getSectorGroupCashflowforTrendChart($sectorgroup_name);

	$obj1 = null;
	
	// return data
	echo json_encode($cashflow_chart_xy_axis);
?>