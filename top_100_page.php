<?php
include 'db_controller_unit.php';

class top_100_page {

	function thousandsplit($f)
	{
		if( $f >= 1000 OR $f <= -1000 ) {
			return number_format($f);
		}
		return $f;
	}

	function getTop100TitleName() {
		define('LONG_STRING', 16);
		$dname ="page_title_name.xml"; // xml檔名
		
		//建立XML操作物件
		$doc = new DOMDocument();
		$doc->load($dname);
		
		$nodes = $doc->getElementsByTagName('top100_title');
		
		$k=0;
		foreach ($nodes as $node) {
			if( strpos($node->nodeValue, "(") !== false ) { // 有單位的title name 加入換行
				$index = strpos($node->nodeValue, "(");
				$tem_title_name = substr($node->nodeValue,0,$index).'<br>'.substr($node->nodeValue,$index);
				$title_name_list[$k] = $tem_title_name;
			} else if ( strlen($node->nodeValue) >= LONG_STRING ) { // 過長的title name 加入換行
				$index = strlen($node->nodeValue) / 2;
				$tem_title_name = substr($node->nodeValue,0,$index).'<br>'.substr($node->nodeValue,$index);
				$title_name_list[$k] = $tem_title_name;
			}
			else {
				$title_name_list[$k] = $node->nodeValue;
			}
			$k++;
		}
		
		return $title_name_list;
	}

	function printValueatRisktable($year) {
		$obj1 = new db_controller_unit;
		
		$top100_financial_info = $obj1->getTop100FinancialInfo($year);
		
		if(!empty($top100_financial_info)) {
			echo '<div id="demoDiv"><div id="demoGrid"><table id="demoTable"><colgroup><col id="demoTableCol1"></colgroup><thead><tr>';
			
			$title_name = $this->getTop100TitleName();
			
			for($i=0; $i<count($title_name); $i++) {
				echo "<th><span id=demoHdr". $i .">". $title_name[$i] ."</span></th>";
			}
			
			echo '</tr></thead><tbody>';
			
			for ( $i=0; $i<count($top100_financial_info); $i++ ) {
				echo '<tr>';
				for ( $j=0; $j<count($top100_financial_info[0]); $j++ ) {	
					if ( $j<2 ) {
						echo '<td class="g_title">'.$top100_financial_info[$i][$j].'</td>';
					}
					else {
						if( preg_match( '/%/', $top100_financial_info[$i][$j] ) ) {
							echo '<td>'.$top100_financial_info[$i][$j].'</td>';
						}
						else {
							echo '<td>'. $this->thousandsplit($top100_financial_info[$i][$j]) .'</td>';
						}
					}
				}
				echo '</tr>';
			}
			echo '</tbody></table></div></div>';
		} else {
			echo '<br>no data.';
		}
	}

}

?>