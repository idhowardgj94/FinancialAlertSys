<?php
include_once  './db_controller_unit.php';

class financialIndexPage {
	
	// 超過千位的數加逗號
	function thousandsplit($str) {
		if( (float)$str >= 1000 OR (float)$str <= -1000 ) {
			return number_format($str);
		}
		return $str;
	}

	// 印出table到網頁上
	function printFinancialIndexTable($cid) {
		$obj1 = new db_controller_unit;
		
		if( !empty($obj1->isExistedFinancialIndexData($cid)) ) {
		
			$financialIndexDataArray=$obj1->getComapnyFinancialIndexArray($cid);
			
			echo '<div id="demoDiv"><div id="demoGrid"><table id="demoTable"><colgroup><col id="demoTableCol1"></colgroup><thead><tr>';
			for($i=0; $i<count($financialIndexDataArray[0]); $i++) {
					echo '<th><span id="demoHdr'. ($i+1) .'">'. $financialIndexDataArray[0][$i] .'</span></th>';
			}
			echo '</tr></thead><tbody>';
			
			for($i=1; $i<count($financialIndexDataArray); $i++) {
				echo '<tr>';
				for($j=0; $j<count($financialIndexDataArray[0]); $j++) {
					// title class
					if($i===1 OR $i===11 
					OR $i===16 OR $i===21 
					OR $i===24)
						echo '<td class="finacial_title">'.$financialIndexDataArray[$i][$j].'</td>';
					else {
						if($j===0) { echo '<td class="g_title">'.$financialIndexDataArray[$i][$j].'</td>'; }
						else if((float)$financialIndexDataArray[$i][$j]<0 AND $financialIndexDataArray[$i][$j]!='-') { echo '<td><font color="red">'.$this->thousandsplit($financialIndexDataArray[$i][$j]).'</font></td>'; }
						else { echo '<td>'.$this->thousandsplit($financialIndexDataArray[$i][$j]).'</td>'; }
					}
				}
				echo '</tr>';
			}
			
			echo '</tbody></table></div></div>';
		}
		else
			echo '<br>no data';
	}
}


?>