<?php
include 'db_controller_unit.php';
class value_at_risk_page {
	var $companyList;
	var $id;
	// 判斷風險值是否為高 中風險
	// 高 > 50.00 （改為30？
	// 中 > 35.00 （改為40？ 因為之前換過公式？
	/**
	 * 回傳值：-1表示無風險值，表示高風險值，1表示中風險值，0表示正常*
	 */
	function checkValueatRisk($valueatRisk) {
		$high_risk = 30.00;
		$medium_risk = 40.00;
		if ($valueatRisk === '-')
			return - 1; // no risk value
		if ($valueatRisk <= $high_risk)
			return 2;
		else if ($valueatRisk <= $medium_risk)
			return 1;
		return 0;
	}
	
	/**
	 * print 表格在螢幕上
	 * 參數說明：
	 * c : china or taiwan
	 * pageID : 頁面分類
	 * ValueatRiskData : 風險值資料
	 * fixedCols : 固定的title數
	 */
	function printValueatRisktable($c, $pageID) {
		// new a db_controller_unit object
		$obj1 = new db_controller_unit ();
		
		// justify china or taiwan company
		if ($c == "china")
			$ValueatRiskData = $obj1->getChinaCompanyValueatRiskData ();
		else
			$ValueatRiskData = $obj1->getCompanyValueatRiskData ( $pageID );
			
			// justify how fixed col(title, not change, and always in left)
		if ($pageID === '上市' or $pageID === '上櫃' or $pageID === '下市櫃')
			$fixedCols = 3;
		else
			$fixedCols = 2;
			
			// if has data then
		if (! empty ( $ValueatRiskData )) {
			$ValueatRiskData = $this->sortData ( $ValueatRiskData, $pageID );
			
			echo "<div id='demoDiv'><div id='demoGrid'><table id='demoTable'><colgroup><col id='demoTableCol1'></colgroup><thead><tr>";
			// 印title
			for($j = 0; $j < count ( $ValueatRiskData [0] ); $j ++) {
				echo '<th><span id="demoHdr' . $j . '">' . $ValueatRiskData [0] [$j] . '</span></th>';
			}
			echo "</tr></thead><tbody>";
			// 印資料
			for($i = 1; $i < count ( $ValueatRiskData ); $i ++) {
				echo "<tr>";
				for($j = 0; $j < count ( $ValueatRiskData [0] ); $j ++) {
					if ($j === 0) {
						echo '<td class="g_title">' . $ValueatRiskData [$i] [$j] . '</td>';
					} else if ($j === 1) {
						if ($c === "china")
							echo '<td class="g_title"><a href="#" onclick="window.open(' . "'drawcn.php?id=" . $ValueatRiskData [$i] [$j - 1] . "'" . ');">' . $ValueatRiskData [$i] [$j] . '</a></td>';
						else if ($pageID == '下市櫃')
							echo '<td class="g_title">' . $ValueatRiskData [$i] [$j] . '</td>';
						else
							echo '<td class="g_title"><a href="#" onclick="window.open(' . "'draw.php?class=" . $this->id . "&id=" . $ValueatRiskData [$i] [$j - 1] . "'" . ');">' . $ValueatRiskData [$i] [$j] . '</a></td>';
					} else if ($j === 2 and $fixedCols === 3) {
						echo '<td class="g_title">' . $ValueatRiskData [$i] [$j] . '</td>';
					} else if ($this->checkValueatRisk ( $ValueatRiskData [$i] [$j] ) > 0 and $j >= $fixedCols) // 高風險值背景class
{
						if ($this->checkValueatRisk ( $ValueatRiskData [$i] [$j] ) > 1)
							echo '<td class="g_hRisk">' . $ValueatRiskData [$i] [$j] . '</td>'; // 高風險值背景class
						else
							echo '<td class="g_lRisk">' . $ValueatRiskData [$i] [$j] . '</td>'; // 中風險值背景class
					} else {
						echo '<td>' . $ValueatRiskData [$i] [$j] . '</td>';
					}
				}
				echo "</tr></tbody>";
			}
			echo "</table></div></div>";
		} else {
			echo "<br>no data";
		}
	}
	
	/*
	 * 整理風險值資料
	 * datalist : 待整理的資料
	 * pagrID : 頁面分類
	 * new_datalist : return array
	 */
	function sortData($datalist, $pageID) {
		
		// 不須整理直接回傳
		if ($pageID === '興櫃' or $pageID === '公開發行' or $pageID === '中國')
			return $datalist;
		else if ($pageID === '下市櫃') { // 下市櫃頁面加上危機發生年月
			$obj1 = new db_controller_unit ();
			$dbn = $obj1->connect_DB ();
			
			for($i = 0; $i < count ( $datalist ); $i ++) {
				
				$new_index = 0;
				for($j = 0; $j < count ( $datalist [0] ); $j ++) {
					if ($i === 0) {
						if ($j === 1) {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							$new_datalist [$i] [$new_index + 1] = "危機發生年/月";
							$new_index = $new_index + 2;
						} else {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							$new_index = $new_index + 1;
						}
					} else {
						if ($j === 1) {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							
							$crisis_date = $dbn->query ( 'SELECT * FROM `company_financial_crisis` WHERE `company_id` = "' . $datalist [$i] [0] . '"' );
							if (! empty ( $crisis_date )) {
								$crisis_date_row = mysqli_fetch_row ( $crisis_date );
								if (! empty ( $crisis_date_row ))
									$new_datalist [$i] [$new_index + 1] = $crisis_date_row [1];
								else
									$new_datalist [$i] [$new_index + 1] = "NULL";
							} else {
								$new_datalist [$i] [$new_index + 1] = "NULL";
							}
							$new_index = $new_index + 2;
						} else {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							$new_index = $new_index + 1;
						}
					}
				}
			}
			
			return $new_datalist;
		} else { // 上市上櫃頁面加上三年預警率
			for($i = 0; $i < count ( $datalist ); $i ++) {
				
				$new_index = 0;
				for($j = 0; $j < count ( $datalist [0] ); $j ++) {
					if ($i === 0) {
						if ($j === 1) {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							$new_datalist [$i] [$new_index + 1] = "三年預警率";
							$new_index = $new_index + 2;
						} else {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							$new_index = $new_index + 1;
						}
					} else {
						if ($j === 1) {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							
							$highrisk = 0;
							$value_at_risk_num = 0;
							for($k = 1; $k < 13; $k ++) {
								// $tem = explode( "%", $datalist[$i][$j+$k] );
								
								/*
								 * if ( $datalist[$i][$j+$k] != "-" )
								 * $value_at_risk_num++;
								 */
								if (strcasecmp ( $datalist [$i] [$j + $k], "-" ))
									$value_at_risk_num ++;
									// if ( $tem[0] > 50.00 )
									// $highrisk++;
								if ($this->checkValueatRisk ( $datalist [$i] [$j + $k] ) === 2)
									$highrisk ++;
							}
							if ($value_at_risk_num)
								$warining_rate = ($highrisk / $value_at_risk_num) * 100;
							else
								$warining_rate = 0;
							
							$new_datalist [$i] [$new_index + 1] = sprintf ( "%.0f", $warining_rate ) . "% (" . $highrisk . '/' . $value_at_risk_num . ')';
							
							$new_index = $new_index + 2;
						} else {
							$new_datalist [$i] [$new_index] = $datalist [$i] [$j];
							$new_index = $new_index + 1;
						}
					}
				}
			}
			
			return $new_datalist;
		}
	}
}