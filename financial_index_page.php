<?php
include './db_controller_unit.php';

class financial_index_page {
	
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
		
			$financial_index_data=$obj1->getComapnyFinancialIndex($cid);
			
			echo '<div id="demoDiv"><div id="demoGrid"><table id="demoTable"><colgroup><col id="demoTableCol1"></colgroup><thead><tr>';
			for($i=0; $i<count($financial_index_data[0]); $i++) {
					echo '<th><span id="demoHdr'. ($i+1) .'">'. $financial_index_data[0][$i] .'</span></th>';
			}
			echo '</tr></thead><tbody>';
			
			for($i=1; $i<count($financial_index_data); $i++) {
				echo '<tr>';
				for($j=0; $j<count($financial_index_data[0]); $j++) {
					// title class
					if($i===1 OR $i===11 
					OR $i===16 OR $i===21 
					OR $i===24)
						echo '<td class="finacial_title">'.$financial_index_data[$i][$j].'</td>';
					else {
						if($j===0) { echo '<td class="g_title">'.$financial_index_data[$i][$j].'</td>'; }
						else if((float)$financial_index_data[$i][$j]<0 AND $financial_index_data[$i][$j]!='-') { echo '<td><font color="red">'.$this->thousandsplit($financial_index_data[$i][$j]).'</font></td>'; }
						else { echo '<td>'.$this->thousandsplit($financial_index_data[$i][$j]).'</td>'; }
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