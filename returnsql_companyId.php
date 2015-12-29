<?php
/*
  檢查 input string 是否為財務指標頁面有資料的公司
*/

$str = $_GET['input'];

include './db_controller_unit.php';

$obj1 = new db_controller_unit;
$cid = $obj1->isExistedFinancialIndexData($str);

echo $cid;

?>