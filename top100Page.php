<?php
include 'db_controller_unit.php';

class top100Page {

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
				$index = strpos($node->nodeValue, "(");//找到單位的第一個字元（(）出現的index
				$temTitleName = substr($node->nodeValue,0,$index).'<br>'.substr($node->nodeValue,$index);
				$titleNameList[$k] = $temTitleName;
			} else if ( strlen($node->nodeValue) >= LONG_STRING ) { // 過長的title name 加入換行
				$index = strlen($node->nodeValue) / 2;
				$temTitleName = substr($node->nodeValue,0,$index).'<br>'.substr($node->nodeValue,$index);
				$titleNameList[$k] = $temTitleName;
			}
			else {
				$titleNameList[$k] = $node->nodeValue;
			}
			$k++;
		}
		
		return $titleNameList;
	}

	function printValueatRisktable($year) {
		$obj1 = new db_controller_unit;
		
		$top100FinancialInfoArray = $obj1->getTop100FinancialInfoArray($year);
		
		if(!empty($top100FinancialInfoArray)) {
			echo '<div id="demoDiv"><div id="demoGrid"><table id="demoTable"><colgroup><col id="demoTableCol1"></colgroup><thead><tr>';
			
			$titleName = $this->getTop100TitleName();
			//到XML讀檔，將title讀出
			
			for($i=0; $i<count($titleName); $i++) {
				echo "<th><span id=demoHdr". $i .">". $titleName[$i] ."</span></th>";
				//th 表格title，字體為粗體
			}
			
			echo '</tr></thead><tbody>';
			
			for ( $i=0; $i<count($top100FinancialInfoArray); $i++ ) {
				echo '<tr>';
				for ( $j=0; $j<count($top100FinancialInfoArray[0]); $j++ ) {	
					if ( $j<2 ) {
						echo '<td class="g_title">'.$top100FinancialInfoArray[$i][$j].'</td>';
					}
					else {
						if( preg_match( '/%/', $top100FinancialInfoArray[$i][$j] ) ) {
							echo '<td>'.$top100FinancialInfoArray[$i][$j].'</td>';
						}
						else {
							echo '<td>'. $this->thousandsplit($top100FinancialInfoArray[$i][$j]) .'</td>';
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