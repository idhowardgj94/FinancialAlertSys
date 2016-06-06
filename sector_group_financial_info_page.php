<?php
include 'valueatRiskPage.php';

class sector_group_financial_info_page extends valueatRiskPage {

	// 超過1000以上的數會加千位逗號
	function thousandsplit($f)
	{
		if( $f >= 1000 OR $f <= -1000 ) {
			return number_format($f);
		}
		return $f;
	}

	// 印出table在螢幕上
	function printValueatRisktable($class, $pageID) {
		$obj1 = new db_controller_unit;
		$pageID = $this->toSectorGroupName($class, $pageID);
		
		// 取得資料
		if($pageID)
			$financialInfoArray = $obj1->getSectorGroupFinancialInfoList($class, $pageID);
		
		if (!empty($financialInfoArray)) {		
			echo "<div id='demoDiv'><div id='demoGrid'><table id='demoTable'><colgroup><col id='demoTableCol1'></colgroup><thead><tr>";
			for( $j=0; $j<count($financialInfoArray[0]); $j++ ) {
				echo '<th><span id="demoHdr'.$j.'">'.$financialInfoArray[0][$j].'</span></th>';
			}
			echo "</tr></thead><tbody>";
			
			$isVaR = 1;
			
			for($a=1; $a<count($financialInfoArray); $a++) {
				echo "<tr>";
				
				if($financialInfoArray[$a][0] === '產業總風險值' OR $financialInfoArray[$a][0] === '集團總風險值') $isVaR = 2;
				if($financialInfoArray[$a][0] === '總資產') $isVaR = 0;
				
				for($b=0; $b<count($financialInfoArray[0]); $b++) {
					
					//$tem = explode( "%", $financialInfo[$a][$b] );
					if($isVaR!=0) {
						if ( $b === 0 AND $isVaR === 2 ) {
							echo '<td class="g_title"><a href="#" onclick="window.open('."'drawsectorgroup.php?id=".$pageID."'".');">'.$financialInfoArray[$a][$b].'</a></td>';
						}
						else if ( $b === 0 ) {
							echo '<td class="g_title">'.$financialInfoArray[$a][$b].'</td>';
						}
						else if( $this->checkValueatRisk($financialInfoArray[$a][$b]) > 0 AND $b >= 1 ) {
							if($this->checkValueatRisk($financialInfoArray[$a][$b])>1)
								echo '<td class="g_hRisk">'.$financialInfoArray[$a][$b].'</td>'; // 高風險值背景class
							else
								echo '<td class="g_lRisk">'.$financialInfoArray[$a][$b].'</td>'; // 中風險值背景class
						}
						else {
							echo '<td>'.$financialInfoArray[$a][$b].'</td>';
						}
					}
					else {
						if ( $b === 0 ) {
							echo '<td class="g_title2">'.$financialInfoArray[$a][$b].'</td>';
						}
						else {
							if ( (float)$financialInfoArray[$a][$b] < 0 ) {
								echo '<td class="g_body2"><font color="red">'. $this->thousandsplit($financialInfoArray[$a][$b]) .'</font></td>'; 
							}
							else {
								echo '<td class="g_body2">'. $this->thousandsplit($financialInfoArray[$a][$b]) .'</td>'; 
							}
						}
					}
				}
				echo "</tr>";
			}
		
			echo '</tbody></table></div></div>';
		
		} else {
			echo "<br>no data.";
		}
		
	}

	// 將產業 企業集團代號轉換成實際的名稱
	function toSectorGroupName($class, $str)
	{
		$dname ="sectorgroupname.xml"; // xml檔名
		
		//建立XML操作物件
		$doc = new DOMDocument();
		$doc->load($dname);
		
		$nodes = $doc->getElementsByTagName($class);
		
		$k=0;
		foreach ($nodes as $node) {
			$eng_abbreviation = $nodes->item($k)->getAttribute('eng_abbreviation');
			if($str==$eng_abbreviation) {
				$name = $node->nodeValue;
				return $name;
			}
			$k++;
		}
		
		return null;
	}
}

?>