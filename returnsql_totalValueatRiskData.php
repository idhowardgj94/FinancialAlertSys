<?php

	$name = $_GET['name'];
			
	// 取所有資料
	include("./db_controller_unit.php");

	$obj1 = new db_controller_unit;
	$total_value_at_risk_data = $obj1->getTotalValueatRiskforTrendChart($name);

	$obj1 = null;

	// return data
	echo json_encode($total_value_at_risk_data);
	
?>