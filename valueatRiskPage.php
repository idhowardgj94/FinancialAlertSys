<?php
include_once 'db_controller_unit.php';
class valueatRiskPage {
	var $companyList;
	var $id;
	// 	判斷風險值是否為高 中風險
	// 高 > 50.00 （改為30？
	// 中 > 35.00 （改為40？ 因為之前換過公式？
	/**
	 * 回傳值：-1表示無風險值，表示高風險值，1表示中風險值，0表示正常*
	 */
	function checkValueatRisk($valueatRisk) {
		$highRisk = 30.00;
		$mediumRisk = 40.00;
		if ($valueatRisk === '-')
			return - 1; // no risk value
		if ($valueatRisk <= $highRisk)
			return 2;
		else if ($valueatRisk <= $mediumRisk)
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
	 * dataList : 待整理的資料
	 * pagrID : 頁面分類
	 * newDataList : return array
	 */
	function sortData($dataList, $pageID) {
		
		// 不須整理直接回傳
		if ($pageID === '興櫃' or $pageID === '公開發行' or $pageID === '中國')
			return $dataList;
		else if ($pageID === '下市櫃') { // 下市櫃頁面加上危機發生年月
			$obj1 = new db_controller_unit ();
			$dbn = $obj1->connect_DB ();
			
			for($i = 0; $i < count ( $dataList ); $i ++) {
				
				$newIndex = 0;
				for($j = 0; $j < count ( $dataList [0] ); $j ++) {
					if ($i === 0) {
						if ($j === 1) {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							$newDataList [$i] [$newIndex + 1] = "危機發生年/月";
							$newIndex = $newIndex + 2;
						} else {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							$newIndex = $newIndex + 1;
						}
					} else {
						if ($j === 1) {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							
							$crisisDdate = $dbn->query ( 'SELECT * FROM `company_financial_crisis` WHERE `company_id` = "' . $dataList [$i] [0] . '"' );
							if (! empty ( $crisisDate )) {
								$crisisDateRow = mysqli_fetch_row ( $crisisDate );
								if (! empty ( $crisisDateRow ))
									$newDataList [$i] [$newIndex + 1] = $crisisDateRow [1];
								else
									$newDataList [$i] [$newIndex + 1] = "NULL";
							} else {
								$newDataList [$i] [$newIndex + 1] = "NULL";
							}
							$newIndex = $newIndex + 2;
						} else {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							$newIndex = $newIndex + 1;
						}
					}
				}
			}
			
			return $newDataList;
		} else { // 上市上櫃頁面加上三年預警率
			for($i = 0; $i < count ( $dataList ); $i ++) {
				
				$newIndex = 0;
				for($j = 0; $j < count ( $dataList [0] ); $j ++) {
					if ($i === 0) {
						if ($j === 1) {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							$newDataList [$i] [$newIndex + 1] = "三年預警率";
							$newIndex = $newIndex + 2;
						} else {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							$newIndex = $newIndex + 1;
						}
					} else {
						if ($j === 1) {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							
							$highRisk = 0;
							$valueatRiskNum = 0;
							for($k = 1; $k < 13; $k ++) {
								// $tem = explode( "%", $dataList[$i][$j+$k] );
								
								/*
								 * if ( $dataList[$i][$j+$k] != "-" )
								 * $valueatRiskNum++;
								 */
								if (strcasecmp ( $dataList [$i] [$j + $k], "-" ))
									$valueatRiskNum++;
									// if ( $tem[0] > 50.00 )
									// $highrisk++;
								if ($this->checkValueatRisk ( $dataList [$i] [$j + $k] ) === 2)
									$highRisk ++;
							}
							if ($valueatRiskNum)
								$wariningRate = ($highRisk / $valueatRiskNum) * 100;
							else
								$wariningRate = 0;
							
							$newDataList [$i] [$newIndex + 1] = sprintf ( "%.0f", $wariningRate ) . "% (" . $highRisk . '/' . $valueatRiskNum . ')';
							
							$newIndex = $newIndex + 2;
						} else {
							$newDataList [$i] [$newIndex] = $dataList [$i] [$j];
							$newIndex = $newIndex + 1;
						}
					}
				}
			}
			
			return $newDataList;
		}
	}
}