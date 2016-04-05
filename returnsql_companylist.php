<?php
/*
  根據選擇的status回傳有財務指標資料的公司List
*/

$status_num = $_GET['status'];

include './db_controller_unit.php';

// 判斷選擇的status
switch ($status_num) {
	case 1:
		$status = '上市';
		break;
	case 2:
		$status = '上櫃';
		break;
	case 3:
		$status = '興櫃';
		break;
	case 4:
		$status = '公開發行';
		break;
	default:
		$status = '上市';
}

// 連結資料庫
$obj1 = new db_controller_unit;
$dbn = $obj1->connect_DB();

// 抓公司資料
$comtem = $dbn->query('SELECT DISTINCT a.`company_id`, a.`company_nickname` 
FROM `company_basic_information` a, `financial_index_all` b 
WHERE a.`status` = "'. $status .'" AND a.`company_id` = b.`company_id` ORDER BY a.`company_id` ASC');
$data_num = 0;

if(!empty($comtem)) {
	for ($i=0; $i<mysqli_num_rows($comtem); $i++) {
		$comtem_row=mysqli_fetch_row($comtem);
		$com_id[$data_num] = $comtem_row[0];
		$com_name[$data_num] = $comtem_row[1];
		$data_num++;
	}
}

$com_list = array(
	$com_id, $com_name
);

$obj1 = null;
$dbn = null;

echo json_encode($com_list);

?>