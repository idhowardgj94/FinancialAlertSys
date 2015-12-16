<?php
/*
  計算台灣公司在該頁面的index
  
  若為別頁面則回傳頁面名稱
  若找不到資料則回傳-888
*/

// get 搜尋字串及當前頁面的status
$searchString = $_GET['search'];
$cstatus = $_GET['status'];

include 'db_controller_unit.php';

$obj1 = new db_controller_unit;
$dbn = $obj1->connect_DB();

// 設定index初始值
$index = -888;

// 找出欲搜尋的公司的status
$comtem = $dbn->query('SELECT `status` FROM `company_basic_information` WHERE `company_id` = "'. $searchString .'" OR `company_name` = "'. $searchString .'" OR `company_nickname` = "'. $searchString .'"');
if(!empty($comtem)) {
	$comtem_row=mysqli_fetch_row($comtem);
	if(!empty($comtem_row)) {
		// 判斷欲搜尋的公司是否在該status頁面下, 若是則計算index
		if($comtem_row[0] != null AND $comtem_row[0]==$cstatus) {
			// 找出該status頁面下所有公司list並計算index
			$comlist = $dbn->query('SELECT `company_id`,  `company_name`,  `company_nickname` FROM `company_basic_information` WHERE `status` = "'. $comtem_row[0] .'" ORDER BY `company_id` ASC');
			if(!empty($comlist)) {
				for($i=0; $i<mysqli_num_rows($comlist); $i++) {
					$comlist_row=mysqli_fetch_row($comlist);
					if(!empty($comlist_row)) {
						if( $comlist_row[0] == $searchString OR $comlist_row[1] == $searchString OR $comlist_row[2] == $searchString ) {
							$index = $i;
							break;
						}
					}
				}
			}
		}
		else {
			switch($comtem_row[0]) {
				case '上市':
					$index = -1;
					break;
				case '上櫃':
					$index = -2;
					break;
				case '興櫃':
					$index = -3;
					break;
				case '公開發行':
					$index = -4;
					break;
				case '下市櫃':
					$index = -5;
					break;
			}
		}
	}
}

$dbn = null;

echo $index;

?>