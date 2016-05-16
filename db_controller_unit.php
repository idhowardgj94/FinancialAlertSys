<?php
define ( "FINANCIAL_TITLE", "financial_title" );
define ( "FINANCIAL_DATA_TITLE", "financial_data_title" );
define ( "TITLE_NAME_INDEX", 0 );
define ( "TITLE_NUMBER_INDEX", 1 );
class db_controller_unit {
	// return 連接好的資料庫
	// dbn : return connected_database
	function connect_DB() {
		// 設定連接DB名稱
		$dbn = new mysqli ( 'localhost', // 主機位置
			'root', // 帳號
			'1234', // 密碼
			'financial_schema_test' ); // 資料庫名稱
		                      
		// 設定編碼
		$dbn->set_charset ( 'utf8' );
		return $dbn;
	}
	function colseDB($conn){
		mysql_close($conn);
	}
	// 上傳 insert 資料
	function insertData($tablename, $value) {
		$dbn = $this->connect_DB();
		$sql = 'INSERT INTO `' . $tablename . '` ' . $value . '';
		echo $sql;
		$retval = mysql_query( $sql, $conn );
		if(! $retval ) {
			die('Could not enter data: ' . mysql_error());
		}
		 
		echo "Entered data successfully\n";
		$this->colseDB($dbn);
	}
	
	// 修改 update 資料
	function updateData($tablename, $colname, $value, $condition) {
		$sql = 'UPDATE `' . $tablename . '` SET `' . $colname . '`=' . $value . ' WHERE ' . $condition;
		echo $sql;
		$dbn = $this->connect_DB();
		$retval = mysql_query( $sql, $conn );
		if(! $retval ) {
			die('Could not enter data: ' . mysql_error());
		}
			
		echo "Entered data successfully\n";
		$this->colseDB($dbn);
	}
	//取得資料(未完成）
	function GetDatawithCondition($tablename, $AttributeArray, $condition){
		
		$sqlQuery = 'SELECT `' . $AttributeArray . '` FROM `' .$tablename . '`WHERE ' . $condition;
		$dbn = $this->connect_DB();
		$result = mysql_query($sqlQuery);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		return $result;
	}
	/**
	 * 檢查該id的公司 在財務指標頁面下 是否存在
	 */
	function isExistedFinancialIndexData($input_str) {
		// 為什麼這裡要先呼叫
		$dbn = $this->connect_DB ();
		
		$temdata = $dbn->query ( 'SELECT DISTINCT a.`company_id` FROM `company_basic_information` a, `financial_index_all` b
		WHERE ( a.`company_id` = "' . $input_str . '" OR a.`company_name` = "' . $input_str . '" OR a.`company_nickname` = "' . $input_str . '" )
		AND b.`company_id` = a.`company_id` AND a.`status` != "下市櫃" ' );
		
		if (! empty ( $temdata )) {
			$temdata_row = mysqli_fetch_row ( $temdata );
			if (! empty ( $temdata_row ))
				return $temdata_row [0];
			else
				return null;
		}
	}
	
	/**
	 * 將風險值轉換成顯示在頁面上的格式
	 * ex:
	 * 0.99 -> 99%
	 * （為什麼這會在db_controller_unit？
	 */
	function convertValueatRisk($input) {
		// $value_at_risk = sprintf("%1\$.2f" ,$input*100) . "%";
		
		// 100 - ( 風險值乘以100 開根號再乘10 )
		$tem_value_at_risk = 100 - (sqrt ( ( float ) $input * 100 ) * 10);
		$value_at_risk = round ( ( float ) $tem_value_at_risk, 2 );
		
		/*
		 * if($value_at_risk==0)
		 * $value_at_risk = 0.00;
		 */
		
		return ( float ) $value_at_risk;
	}
	
	/*
	 * 將風險值轉換成趨勢圖需要的格式
	 * 取小數點後四位
	 */
	function convertTrendChartValueatRisk($input) {
		// $value_at_risk = round((float)$input, 4);
		$tem_value_at_risk = 100 - (sqrt ( ( float ) $input * 100 ) * 10);
		$value_at_risk = round ( ( float ) $tem_value_at_risk, 2 );
		
		return $value_at_risk;
	}
	
	/*
	 * 取得台灣公司風險值資料
	 * status : page ID
	 * ValueatRiskData : return array
	 * company_id company_name value_at_risk
	 *
	 */
	function getCompanyValueatRiskData($status) {
		$dbn = $this->connect_DB ();
		
		// 根據status取得對應的季別
		if ($status === "上市" or $status === "上櫃")
			$season_all = $dbn->query ( 'SELECT DISTINCT `season` FROM `company_financial_information` ORDER BY `season` DESC' );
		else if ($status === "公開發行" or $status === "興櫃")
			// why _____2?
			$season_all = $dbn->query ( 'SELECT DISTINCT `season` FROM `company_financial_information` WHERE `season` LIKE "_____2" OR `season` LIKE "_____4" ORDER BY `season` DESC' );
		else
			$season_all = $dbn->query ( 'SELECT DISTINCT `company_financial_information`.`season`
									FROM `company_financial_information`, `company_basic_information`
									WHERE `company_financial_information`.`company_id` = `company_basic_information`.`company_id` AND `company_basic_information`.`status` = "下市櫃"
									ORDER BY `season` DESC' );
			
			// season數量
		$season_num = 0;
		
		if (! empty ( $season_all )) {
			for($i = 0; $i < mysqli_num_rows ( $season_all ); $i ++) {
				$season_all_row = mysqli_fetch_row ( $season_all );
				if (! empty ( $season_all_row )) {
					$season_list [$season_num] = $season_all_row [0];
					$season_num = $season_num + 1;
				}
			}
		}
		
		$ValueatRiskData [0] [0] = "公司代號";
		$ValueatRiskData [0] [1] = $status . "公司";
		
		for($i = 0; $i < count ( $season_list ); $i ++) {
			$ValueatRiskData [0] [2 + $i] = $season_list [$i];
		}
		
		$array_index = 0;
		
		$row_num = 1;
		$col_num = 0;
		
		// 根據status取得公司風險值
		if ($status === "上市" or $status === "上櫃") {
			$datatem = $dbn->query ( 'SELECT `company_basic_information`.`company_id`, `company_basic_information`.`company_nickname`, `company_financial_information`.`season`, `company_financial_information`.`value_at_risk`
							FROM `company_basic_information`, `company_financial_information`
							WHERE `company_basic_information`.`company_id` = `company_financial_information`.`company_id` AND `company_basic_information`.`status` = "' . $status . '"
							ORDER BY `company_basic_information`.`company_id` ASC, `company_financial_information`.`season` DESC' );
		} else if ($status === "公開發行" or $status === "興櫃") {
			$datatem = $dbn->query ( 'SELECT `company_basic_information`.`company_id`, `company_basic_information`.`company_nickname`, `company_financial_information`.`season`, `company_financial_information`.`value_at_risk`
							FROM `company_basic_information`, `company_financial_information`
							WHERE `company_basic_information`.`company_id` = `company_financial_information`.`company_id` AND `company_basic_information`.`status` = "' . $status . '" AND ( `company_financial_information`.`season` LIKE "_____2" OR `company_financial_information`.`season` LIKE "_____4" )
							ORDER BY `company_basic_information`.`company_id` ASC, `company_financial_information`.`season` DESC' );
		} else { // 下市櫃
			$datatem = $dbn->query ( 'SELECT `company_basic_information`.`company_id`, `company_basic_information`.`company_nickname`, `company_financial_information`.`season`, `company_financial_information`.`value_at_risk`
							FROM `company_basic_information`, `company_financial_information`
							WHERE `company_basic_information`.`company_id` = `company_financial_information`.`company_id` AND `company_basic_information`.`status` = "' . $status . '"
							ORDER BY `company_basic_information`.`company_id` ASC, `company_financial_information`.`season` DESC' );
		}
		
		// 將公司風險值對應季別排成array
		if (! empty ( $datatem )) {
			for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
				$data_row = mysqli_fetch_row ( $datatem );
				if (! empty ( $data_row )) {
					if ($i === 0) {
						$ValueatRiskData [$row_num] [$col_num] = $data_row [0];
						$ValueatRiskData [$row_num] [$col_num + 1] = $data_row [1];
						
						$col_num = 2;
						$company_now = $data_row [0];
					} else {
						if ($company_now !== $data_row [0]) {
							for($i = $array_index; $i < count ( $season_list ); $i ++) {
								$ValueatRiskData [$row_num] [$col_num] = "-";
								$col_num = $col_num + 1;
							}
							
							$row_num = $row_num + 1;
							$col_num = 0;
							
							$ValueatRiskData [$row_num] [$col_num] = $data_row [0];
							$ValueatRiskData [$row_num] [$col_num + 1] = $data_row [1];
							
							$col_num = 2;
							$company_now = $data_row [0];
							$array_index = 0;
						}
					}
					
					do {
						if ($data_row [2] === $season_list [$array_index] and $data_row [3] !== null) {
							$ValueatRiskData [$row_num] [$col_num] = $this->convertValueatRisk ( $data_row [3] );
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
							break;
						} else if ($data_row [2] === $season_list [$array_index] and $data_row [3] === null) {
							$ValueatRiskData [$row_num] [$col_num] = "-";
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
							break;
						} else {
							$ValueatRiskData [$row_num] [$col_num] = "-";
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
						}
					} while ( 1 );
				}
			}
			
			for($i = $array_index; $i < count ( $season_list ); $i ++) {
				$ValueatRiskData [$row_num] [$col_num] = "-";
				$col_num = $col_num + 1;
			}
		} else {
			echo "沒有資料";
		}
		
		// colse dbn
		mysqli_close ( $dbn );
		
		// 回傳儲存好的風險值array
		return $ValueatRiskData;
	}
	
	/*
	 * 取得中國公司風險值資料
	 * ValueatRiskData : return array
	 * company_id company_name value_at_risk
	 */
	function getChinaCompanyValueatRiskData() {
		$dbn = $this->connect_DB ();
		
		// 取得季別
		$season_all = $dbn->query ( 'SELECT DISTINCT `season`
					FROM `china_company_financial_information` ORDER BY `season` DESC' );
		$season_num = 0;
		
		if (! empty ( $season_all )) {
			for($i = 0; $i < mysqli_num_rows ( $season_all ); $i ++) {
				$season_all_row = mysqli_fetch_row ( $season_all );
				if (! empty ( $season_all_row )) {
					$season_list [$season_num] = $season_all_row [0];
					$season_num = $season_num + 1;
				}
			}
		}
		
		$ValueatRiskData [0] [0] = "公司代號";
		$ValueatRiskData [0] [1] = "中國公司";
		
		for($i = 0; $i < count ( $season_list ); $i ++) {
			$ValueatRiskData [0] [2 + $i] = $season_list [$i];
		}
		
		$array_index = 0;
		
		$row_num = 1;
		$col_num = 0;
		
		// 取得中國公司風險值
		$datatem = $dbn->query ( 'SELECT `china_company_basic_information`.`company_id`, `china_company_basic_information`.`company_nickname`, `china_company_financial_information`.`season`, `china_company_financial_information`.`value_at_risk`
							FROM `china_company_basic_information`, `china_company_financial_information`
							WHERE `china_company_basic_information`.`company_id` = `china_company_financial_information`.`company_id` AND `china_company_basic_information`.`status` = "T"
							ORDER BY `china_company_basic_information`.`company_id` ASC, `china_company_financial_information`.`season` DESC' );
		
		// 對應季別排序公司風險值資料
		if (! empty ( $datatem )) {
			for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
				$data_row = mysqli_fetch_row ( $datatem );
				if (! empty ( $data_row )) {
					if ($i === 0) {
						$ValueatRiskData [$row_num] [$col_num] = $data_row [0];
						$ValueatRiskData [$row_num] [$col_num + 1] = $data_row [1];
						
						$col_num = 2;
						$company_now = $data_row [0];
					} else {
						if ($company_now !== $data_row [0]) {
							for($i = $array_index; $i < count ( $season_list ); $i ++) {
								$ValueatRiskData [$row_num] [$col_num] = "-";
								$col_num = $col_num + 1;
							}
							
							$row_num = $row_num + 1;
							$col_num = 0;
							
							$ValueatRiskData [$row_num] [$col_num] = $data_row [0];
							$ValueatRiskData [$row_num] [$col_num + 1] = $data_row [1];
							
							$col_num = 2;
							$company_now = $data_row [0];
							$array_index = 0;
						}
					}
					
					do {
						if ($data_row [2] === $season_list [$array_index] and $data_row [3] != null) {
							$ValueatRiskData [$row_num] [$col_num] = $this->convertValueatRisk ( $data_row [3] );
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
							break;
						} else if ($data_row [2] === $season_list [$array_index] and $data_row [3] == null) {
							$ValueatRiskData [$row_num] [$col_num] = "-";
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
							break;
						} else {
							$ValueatRiskData [$row_num] [$col_num] = "-";
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
						}
					} while ( 1 == 1 );
				}
			}
			
			for($i = $array_index; $i < count ( $season_list ); $i ++) {
				$ValueatRiskData [$row_num] [$col_num] = "-";
				$col_num = $col_num + 1;
			}
		} else {
			echo "沒有資料";
		}
		
		mysqli_close ( $dbn );
		return $ValueatRiskData;
	}
	
	/**
	 * 取得風險值趨勢圖資料
	 * c : taiwan or china
	 * company_id : company's id
	 * start_year : 開始顯示的季別
	 * value_at_risk_date : 季別資料
	 * value_at_risk : 風險值資料
	 *
	 * value_at_risk_chart_xy_axis : return array
	 * value_at_risk_date
	 * value_at_risk
	 */
	function getValueatRiskforTrendChart($c, $company_id) {
		$dbn = $this->connect_DB ();
		
		if ($c === 'taiwan')
			$table_name = 'company_financial_information';
		else if ($c === 'china')
			$table_name = 'china_company_financial_information';
			
			// 根據company_id取得風險值趨勢圖所需資料
		if (isset ( $table_name )) {
			$company_value_at_risk = $dbn->query ( 'SELECT `season`, `value_at_risk`
			FROM `' . $table_name . '`
			WHERE `company_id` = ' . $company_id . ' ORDER BY `season` ' );
		}
		
		$number_of_data = 0;
		
		// 要顯示的資料起始年份
		$start_year = 2009;
		
		// 排序趨勢圖所需資料
		if ($company_value_at_risk) {
			for($i = 0; $i < mysqli_num_rows ( $company_value_at_risk ); $i ++) {
				$company_value_at_risk_row = mysqli_fetch_row ( $company_value_at_risk );
				$tem_date = str_split ( $company_value_at_risk_row [0], 4 );
				
				if ($company_value_at_risk_row [1] !== null and ( int ) $tem_date [0] >= $start_year) {
					$value_at_risk [$number_of_data] = $this->convertTrendChartValueatRisk ( $company_value_at_risk_row [1] );
					$value_at_risk_date [$number_of_data] = $company_value_at_risk_row [0];
					
					$number_of_data = $number_of_data + 1;
				}
			}
		}
		
		$value_at_risk_chart_xy_axis = array (
				$value_at_risk_date,
				$value_at_risk 
		);
		
		// disconnect database
		$dbn = null;
		
		return $value_at_risk_chart_xy_axis;
	}
	
	/*
	 * 取得股價趨勢圖資料
	 * company_id : company's id
	 * stock_date : 季別資料
	 * stock_price : 股價資料
	 *
	 * stock_chart_xy_axis : return array
	 * stock_date
	 * stock_price
	 */
	function getStockforTrendChart($company_id) {
		$dbn = $this->connect_DB ();
		
		// 取得指定公司的股價趨勢圖所需資料
		$stock_data = $dbn->query ( 'SELECT `season`, `stock`
		FROM `company_financial_information`
		WHERE `company_id` = ' . $company_id . ' ORDER BY `season`' );
		
		$number_of_data = 0;
		
		// 排序資料
		for($i = 0; $i < mysqli_num_rows ( $stock_data ); $i ++) {
			
			$stock_data_row = mysqli_fetch_row ( $stock_data );
			
			if (( float ) $stock_data_row [1] > 0) {
				$stock_date [$number_of_data] = $stock_data_row [0];
				$stock_price [$number_of_data] = ( float ) $stock_data_row [1];
				
				$number_of_data = $number_of_data + 1;
			}
		}
		
		$stock_chart_xy_axis = array (
				$stock_date,
				$stock_price 
		);
		
		// disconnect database
		$dbn = null;
		
		return $stock_chart_xy_axis;
	}
	
	/*
	 * 取得現金流量趨勢圖資料
	 * c : taiwan or china
	 * company_id : company's id
	 * start_year : 開始顯示的季別
	 * cashflow_date : 季別資料
	 * cashflow_price : 現金流量資料
	 *
	 * cashflow_chart_xy_axis : return array
	 * cashflow_date
	 * cashflow_price
	 */
	function getCashflowforTrendChart($c, $company_id) {
		$dbn = $this->connect_DB ();
		
		if ($c === 'taiwan')
			$table_name = 'company_financial_information';
		else if ($c === 'china')
			$table_name = 'china_company_financial_information';
			
			// 取得對應公司所需的現金流量趨勢圖資料
		if (isset ( $table_name )) {
			$cashflow_data = $dbn->query ( 'SELECT `season`, `cashflow_operating`, `cashflow_investment`, `proceed_fm_newIssue`
			FROM `' . $table_name . '`
			WHERE `company_id` = ' . $company_id . '  ORDER BY `season` ' );
		}
		
		$number_of_data = 0;
		
		// 資料顯示起始年份
		$start_year = 2009;
		
		// 排序資料
		if ($cashflow_data) {
			for($i = 0; $i < mysqli_num_rows ( $cashflow_data ); $i ++) {
				
				$cashflow_data_row = mysqli_fetch_row ( $cashflow_data );
				$tem_date_year = str_split ( $cashflow_data_row [0], 4 );
				
				if ($cashflow_data_row [1] !== null and $cashflow_data_row [2] !== null and $cashflow_data_row [3] !== null and ( int ) $tem_date_year [0] >= $start_year) {
					$cashflow_date [$number_of_data] = $cashflow_data_row [0];
					$cashflow_price [$number_of_data] = ( int ) $cashflow_data_row [1] + ( int ) $cashflow_data_row [2] + ( int ) $cashflow_data_row [3];
					
					$number_of_data = $number_of_data + 1;
				}
			}
		}
		
		$cashflow_chart_xy_axis = array (
				$cashflow_date,
				$cashflow_price 
		);
		
		// disconnect database
		$dbn = null;
		
		return $cashflow_chart_xy_axis;
	}
	
	/*
	 * 取得產業與企業集團財務資料
	 * class : sector or group
	 * pageID : 產業與企業集團名稱
	 * financialInfoData : return array
	 * company_id company_name value_at_risk
	 * sector_group_data
	 */
	function getSectorGroupFinancialInfo($class, $pageID) {
		
		// 連接資料庫
		$dbn = $this->connect_DB ();
		
		// 取出該 產業 企業集團 有資料的季別列表
		$season_all = $dbn->query ( 'SELECT `season`
		FROM `sector_group_financial_information`
		WHERE `name` = "' . $pageID . '"
		ORDER BY `season` DESC' );
		
		$season_num = 0;
		$tem_season = "";
		
		if (! empty ( $season_all )) {
			for($i = 0; $i < mysqli_num_rows ( $season_all ); $i ++) {
				$season_all_row = mysqli_fetch_row ( $season_all );
				if (! empty ( $season_all_row )) {
					if ($tem_season !== "")
						$tem_season .= ", ";
					$season_list [$season_num] = $season_all_row [0];
					$season_num = $season_num + 1;
					$tem_season .= " '" . $season_all_row [0] . "'";
				}
			}
		}
		
		// 取得對應 產業 企業集團 的公司 tem_season季別 的風險值資料
		$tem_season = " (" . $tem_season . ")";
		
		$company_datatem = $dbn->query ( 'SELECT `company_basic_information`.`company_id`, `company_basic_information`.`company_nickname`, `company_financial_information`.`season`, `company_financial_information`.`value_at_risk`
							FROM `company_basic_information`, `company_financial_information`
							WHERE `company_basic_information`.`company_id` = `company_financial_information`.`company_id` AND `company_basic_information`.`' . $class . '` = "' . $pageID . '" AND `company_financial_information`.`season` IN ' . $tem_season . '
							ORDER BY `company_basic_information`.`company_id` ASC, `company_financial_information`.`season` DESC' );
		
		// 取得對應 產業 企業集團 的財務資料
		$sector_group_datatem = $dbn->query ( 'SELECT *
							FROM `sector_group_financial_information`
							WHERE `name` = "' . $pageID . '"
							ORDER BY `season` DESC' );
		
		// 儲存季別資料
		$financialInfoData [0] [0] = $pageID;
		
		for($i = 0; $i < count ( $season_list ); $i ++) {
			$financialInfoData [0] [1 + $i] = $season_list [$i];
		}
		
		$array_index = 0;
		
		$row_num = 1;
		$col_num = 0;
		
		$datatem = $company_datatem;
		
		// 儲存該 產業 企業集團 下的公司資料
		if (! empty ( $datatem )) {
			for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
				$data_row = mysqli_fetch_row ( $datatem );
				if (! empty ( $data_row )) {
					if ($i === 0) {
						$financialInfoData [$row_num] [$col_num] = $data_row [0] . " " . $data_row [1];
						
						$col_num = 1;
						$company_now = $data_row [0];
					} else {
						if ($company_now !== $data_row [0]) {
							for($i = $array_index; $i < count ( $season_list ); $i ++) {
								$financialInfoData [$row_num] [$col_num] = "-";
								$col_num = $col_num + 1;
							}
							
							$row_num = $row_num + 1;
							$col_num = 0;
							
							$financialInfoData [$row_num] [$col_num] = $data_row [0] . " " . $data_row [1];
							
							$col_num = 1;
							$company_now = $data_row [0];
							$array_index = 0;
						}
					}
					
					do {
						if ($data_row [2] === $season_list [$array_index] and $data_row [3] !== null) {
							$financialInfoData [$row_num] [$col_num] = $this->convertValueatRisk ( $data_row [3] );
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
							break;
						} else if ($data_row [2] === $season_list [$array_index] and $data_row [3] === null) {
							$financialInfoData [$row_num] [$col_num] = "-";
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
							break;
						} else {
							$financialInfoData [$row_num] [$col_num] = "-";
							$col_num = $col_num + 1;
							
							$array_index = $array_index + 1;
						}
					} while ( 1 );
				}
			}
			
			for($i = $array_index; $i < count ( $season_list ); $i ++) {
				$financialInfoData [$row_num] [$col_num] = "-";
				$col_num = $col_num + 1;
			}
		} else {
			echo "沒有資料";
		}
		
		$datatem = $sector_group_datatem;
		
		// 該 產業 企業集團 的資料
		if (! empty ( $datatem )) {
			for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
				$data_row = mysqli_fetch_row ( $datatem );
				if (! empty ( $data_row )) {
					for($j = 0; $j < count ( $data_row ); $j ++) {
						$sector_group_info [$i] [$j] = $data_row [$j];
					}
				}
			}
		}
		
		// 根據class選擇固定的title名
		if ($class == 'sector') {
			$datatitle = [ 
					'產業總風險值',
					'總資產',
					'資產負債比',
					'產業營業收入(千元)',
					'產業稅後獲利(千元)',
					'產業每股盈餘(元)',
					'營業活動之淨現金流入(千元)',
					'投資活動之淨現金流入(千元)',
					'籌資活動之淨現金流入(千元)',
					'現金增資(千元)',
					'營業內創現金流量(千元)' 
			];
		} else {
			$datatitle = [ 
					'集團總風險值',
					'總資產',
					'資產負債比',
					'集團營業收入(千元)',
					'集團稅後獲利(千元)',
					'集團每股盈餘(元)',
					'營業活動之淨現金流入(千元)',
					'投資活動之淨現金流入(千元)',
					'籌資活動之淨現金流入(千元)',
					'現金增資(千元)',
					'營業內創現金流量(千元)' 
			];
		}
		
		$row_num = count ( $financialInfoData );
		
		define ( "VALUEATRISK_INDEX", 2 );
		define ( "PERCENT_INDEX", 4 );
		define ( "DECIMAL_DOT2", 7 );
		
		for($k = 2; $k < count ( $sector_group_info [0] ); $k ++) {
			
			$col_num = 0;
			$financialInfoData [$row_num] [$col_num] = $datatitle [$k - 2];
			$col_num = $col_num + 1;
			
			for($l = 0; $l < count ( $sector_group_info ); $l ++) {
				
				if ($k === VALUEATRISK_INDEX)
					$financialInfoData [$row_num] [$col_num] = $this->convertValueatRisk ( $sector_group_info [$l] [$k] );
				else if ($k === PERCENT_INDEX)
					$financialInfoData [$row_num] [$col_num] = sprintf ( "%1\$.2f", $sector_group_info [$l] [$k] * 100 ) . "%";
				else if ($k === DECIMAL_DOT2)
					$financialInfoData [$row_num] [$col_num] = sprintf ( "%1\$.2f", $sector_group_info [$l] [$k] );
				else
					$financialInfoData [$row_num] [$col_num] = $sector_group_info [$l] [$k];
				
				$col_num = $col_num + 1;
			}
			$row_num = $row_num + 1;
		}
		
		$financialInfoData [$row_num] [0] = $datatitle [count ( $datatitle ) - 1];
		
		for($m = 0; $m < count ( $sector_group_info ); $m ++) {
			$financialInfoData [$row_num] [$m + 1] = $sector_group_info [$m] [count ( $sector_group_info [0] ) - 3] + $sector_group_info [$m] [count ( $sector_group_info [0] ) - 4];
		}
		
		mysqli_close ( $dbn );
		return $financialInfoData;
	}
	/*
	 * 取得總風險值趨勢圖資料
	 * name : sector or group name
	 * total_risk_at_value_date : 季別資料
	 * total_risk_at_value : 總風險值資料
	 *
	 * total_risk_at_value_list : return array
	 * total_risk_at_value_date
	 * total_risk_at_value
	 */
	function getTotalValueatRiskforTrendChart($name) {
		// 連接資料庫
		$dbn = $this->connect_DB ();
		
		// 取得對應產業的總風險值趨勢圖所需資料
		$datatem = $dbn->query ( 'SELECT `season`, `total_value_at_risk`
		FROM `sector_group_financial_information`
		WHERE `name` = "' . $name . '" ORDER BY `season` ' );
		
		$number_of_data = 0;
		
		// 儲存趨勢圖資料
		for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
			$datatem_row = mysqli_fetch_row ( $datatem );
			if ($datatem_row [1] !== null) {
				$total_risk_at_value_date [$number_of_data] = $datatem_row [0];
				$total_risk_at_value [$number_of_data] = $this->convertTrendChartValueatRisk ( $datatem_row [1] );
				
				$number_of_data = $number_of_data + 1;
			}
		}
		
		$total_risk_at_value_list = array (
				$total_risk_at_value_date,
				$total_risk_at_value 
		);
		
		// disconnect database
		$dbn = null;
		
		return $total_risk_at_value_list;
	}
	
	/*
	 * 取得產業與企業集團現金流量趨勢圖資料
	 * name : sector or group name
	 * cashflow_date : 季別資料
	 * cashflow : 現金流量資料
	 *
	 * cashflow_list : return array
	 * cashflow_date
	 * cashflow
	 */
	function getSectorGroupCashflowforTrendChart($name) {
		// 連接資料庫
		$dbn = $this->connect_DB ();
		
		// 取得對應產業的總風險值趨勢圖所需資料
		$datatem = $dbn->query ( 'SELECT `season`, `cashflow_operating`, `cashflow_investment`, `proceed_fm_newIssue`
		FROM `sector_group_financial_information`
		WHERE `name` = "' . $name . '" ORDER BY `season` ' );
		
		$number_of_data = 0;
		
		// 儲存趨勢圖資料
		for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
			$datatem_row = mysqli_fetch_row ( $datatem );
			if ($datatem_row [1] !== null and $datatem_row [2] !== null and $datatem_row [3] !== null) {
				$cashflow_date [$number_of_data] = $datatem_row [0];
				$cashflow [$number_of_data] = ( int ) $datatem_row [1] + ( int ) $datatem_row [2] + ( int ) $datatem_row [3];
				
				$number_of_data = $number_of_data + 1;
			}
		}
		
		$cashflow_list = array (
				$cashflow_date,
				$cashflow 
		);
		
		// disconnect database
		$dbn = null;
		
		return $cashflow_list;
	}
	
	/*
	 * 取得前百大資料season
	 * year : 年份
	 * return season : 對應季別
	 * 回傳：該年最新季別
	 */
	function getTop100Season($year) {
		// 連接資料庫
		$dbn = $this->connect_DB ();
		
		$seasontem = $dbn->query ( 'SELECT DISTINCT `season` FROM `top_100_company` WHERE `season` LIKE "' . $year . '%" ORDER BY `season` ASC' );
		if (! empty ( $seasontem )) {
			$seasontem_row = mysqli_fetch_row ( $seasontem );
			$season = $seasontem_row [0];
			return $season;
		}
		
		return null;
	}
	/*
	 * 取得前百大公司資料
	 * season : 欲取得資料季別
	 * top100_financial_info : return array
	 * comapny_id comapny_name top_100_info
	 */
	function getTop100FinancialInfo($year) {
		// 連接資料庫
		$dbn = $this->connect_DB ();
		
		$season = $this->getTop100Season ( $year );
		
		// 取得對應season的前百大公司資料
		if ($season) {
			$datatem = $dbn->query ( 'SELECT a.`company_id`, b.`company_nickname`, c.`value_at_risk`, a.`total_assets`, a.`net_sales`, a.`net_income`, d.`gross_margin`, d.`operating_income`, d.`eps`, a.`leverage`, d.`roa`, d.`roe`, a.`scores`, a.`rank`
								FROM `top_100_company` a, `company_basic_information` b, `company_financial_information` c, `financial_index_all` d
								WHERE a.`season` = "' . $season . '" AND a.`company_id` = b.`company_id` AND a.`company_id` = d.`company_id` AND d.`season` = "' . $season . '" AND a.`company_id` = c.`company_id` AND c.`season` = "' . $season . '"
								ORDER BY a.`rank` ASC' );
		}
		$row_num = 0;
		
		define ( "VALUEATRISK_INDEX", 2 );
		define ( "PERCENT_INDEX", 9 );
		define ( "SCORE_INDEX", 12 );
		
		$percent_decimal_dot2 = array (
				6,
				7,
				10,
				11 
		);
		
		// 排序資料
		if (! empty ( $datatem )) {
			for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
				$datatem_row = mysqli_fetch_row ( $datatem );
				$col_num = 0;
				if (! empty ( $datatem_row )) {
					for($j = 0; $j < count ( $datatem_row ); $j ++) {
						
						if ($datatem_row [$j] != null) {
							if ($j === VALUEATRISK_INDEX)
								$top100_financial_info [$row_num] [$col_num] = $this->convertValueatRisk ( $datatem_row [$j] );
							else if ($j === PERCENT_INDEX)
								$top100_financial_info [$row_num] [$col_num] = sprintf ( "%1\$.2f", $datatem_row [$j] * 100 ) . "%";
							else if (array_search ( $j, $percent_decimal_dot2 ) !== false) // $j==6 OR $j==7 OR $j==10 OR $j==11
								$top100_financial_info [$row_num] [$col_num] = sprintf ( "%1\$.2f", $datatem_row [$j] ) . "%";
							else if ($j === SCORE_INDEX)
								$top100_financial_info [$row_num] [$col_num] = sprintf ( "%1\$.0f", $datatem_row [$j] );
							else
								$top100_financial_info [$row_num] [$col_num] = $datatem_row [$j];
							
							$col_num ++;
						} else {
							$top100_financial_info [$row_num] [$col_num] = "-";
							$col_num ++;
						}
					}
					$row_num ++;
				}
			}
		}
		
		return $top100_financial_info;
	}
	
	/*
	 * 取得財務指標title名稱
	 */
	function getFinancialTitleName($class) {
		$dname = "page_title_name.xml"; // xml檔名
		                                
		// 建立XML操作物件
		$doc = new DOMDocument ();
		$doc->load ( $dname );
		
		$nodes = $doc->getElementsByTagName ( $class );
		
		$k = 0;
		foreach ( $nodes as $node ) {
			switch ($class) {
				case FINANCIAL_TITLE :
					$title_number = $nodes->item ( $k )->getAttribute ( 'number' );
					$title_name_list [$k] [TITLE_NAME_INDEX] = $node->nodeValue;
					$title_name_list [$k] [TITLE_NUMBER_INDEX] = $title_number;
					break;
				case FINANCIAL_DATA_TITLE :
					$title_name_list [$k] = $node->nodeValue;
					break;
			}
			
			$k ++;
		}
		
		return $title_name_list;
	}
	
	/*
	 * 取得財務指標資料
	 */
	function getComapnyFinancialIndex($cid) {
		define ( "FIRST_FINANCIAL_INDEX", 3 );
		define ( "LAST_FINANCIAL_INDEX", 21 );
		
		// 連接資料庫
		$dbn = $this->connect_DB ();
		$season_all = $dbn->query ( 'SELECT DISTINCT `season` FROM `financial_index_all` ORDER BY `season` DESC' );
		
		// 取財務指標資料中全部季別資料
		$season_num = 0;
		if (! empty ( $season_all )) {
			// 最新一季季別
			$season_all_row = mysqli_fetch_row ( $season_all );
			$season_list [$season_num] = $season_all_row [0];
			$season_num ++;
			
			// 同一年的季別只取最新的顯示
			for($i = 1; $i < mysqli_num_rows ( $season_all ); $i ++) {
				$season_all_row = mysqli_fetch_row ( $season_all );
				$year = str_split ( $season_all_row [0], 4 );
				// strpos：找到substring第一次出現的起始位置
				if (strpos ( $season_list [$season_num - 1], $year [0] ) === false) {
					$season_list [$season_num] = $season_all_row [0];
					$season_num ++;
				}
			}
			
			// season, season, season, ...
			$tem_season = "";
			// tem_season: seaon的資料排列（把season_list中的季別使用逗點串在一起
			for($i = 0; $i < count ( $season_list ); $i ++) {
				if ($tem_season !== "")
					$tem_season .= ", ";
				$tem_season .= " '" . $season_list [$i] . "'";
			}
		}
		
		// 取得對應公司的財務指標資料
		$datatem = $dbn->query ( 'SELECT a.`season`, a.`company_id`, b.`company_nickname`, a.`gross_margin`, a.`operating_income`, a.`pretax_income`, a.`ps_sales`, a.`ps_operating_income`, a.`ps_pre_tax_income`, a.`roe`, a.`roa`, a.`eps`, a.`current`, a.`acid_test`, a.`liabilities`, a.`times_interest_earne`, a.`aoverr_and_noverr_turnover`, a.`inventory_turnover`, a.`fixed_asset_turnover`, a.`total_asset_turnover`, a.`debt_over_equity_ratio`, a.`liabilities_to_assets_ratio`, a.`cashflow_operating`, a.`cashflow_investment`, a.`cashflow_financing`, a.`proceed_fm_newIssue`
							FROM `financial_index_all` a, `company_basic_information` b
							WHERE a.`company_id` = "' . $cid . '" AND a.`company_id` = b.`company_id` AND a.`season` IN (' . $tem_season . ')
							ORDER BY a.`season` DESC' );
		
		$row_num = 0;
		
		// 排序資料 將資料儲存到 tem_financial_index_data
		if (! empty ( $datatem )) {
			for($i = 0; $i < mysqli_num_rows ( $datatem ); $i ++) {
				$datatem_row = mysqli_fetch_row ( $datatem );
				$col_num = 0;
				
				if (! empty ( $datatem_row )) {
					for($j = 0; $j < count ( $datatem_row ); $j ++) {
						// 3到21項取到小數後兩位
						if ($datatem_row [$j] != null) {
							if ($j >= FIRST_FINANCIAL_INDEX and $j <= LAST_FINANCIAL_INDEX) // 財務指標取到小數後兩位
								$tem_financial_index_data [$row_num] [$col_num] = sprintf ( "%1\$.2f", $datatem_row [$j] );
							else
								$tem_financial_index_data [$row_num] [$col_num] = $datatem_row [$j];
							
							$col_num ++;
						} else {
							$tem_financial_index_data [$row_num] [$col_num] = "-";
							$col_num ++;
						}
					}
					$row_num ++;
				}
			}
		}
		
		// 計算營業加理財現金流量（index: 26)
		$col_num = count ( $tem_financial_index_data [0] );
		for($i = 0; $i < count ( $tem_financial_index_data ); $i ++) {
			if ($tem_financial_index_data [$i] [$col_num - 3] !== '-' and $tem_financial_index_data [$i] [$col_num - 4] !== '-')
				$tem_financial_index_data [$i] [$col_num] = $tem_financial_index_data [$i] [$col_num - 3] + $tem_financial_index_data [$i] [$col_num - 4];
			else
				$tem_financial_index_data [$i] [$col_num] = '-';
		}
		
		$row_num = 0;
		// 將 tem_financial_index_data 排序 儲存到 financial_index_data
		// 儲存公司代號跟名稱
		$financial_index_data [$row_num] [0] = $tem_financial_index_data [0] [1] . " " . $tem_financial_index_data [0] [2];
		
		// 儲存季別資料（把season抓出來放在第0列…（直轉橫）
		for($i = 0; $i < count ( $tem_financial_index_data ); $i ++)
			$financial_index_data [$row_num] [$i + 1] = $tem_financial_index_data [$i] [0];
		$row_num ++;
		
		// 儲存對應季別的財務指標資料
		// $financial_title = [['獲利能力',9], ['償債能力',4], ['經營能力',4], ['資本結構',2], ['現金流量',5]];
		// title name
		$financial_title = $this->getFinancialTitleName ( FINANCIAL_TITLE );
		$datatitle = $this->getFinancialTitleName ( FINANCIAL_DATA_TITLE );
		
		$index = 0;
		$col_num = 3;
		
		for($i = 0; $i < count ( $financial_title ); $i ++) {
			$index = $i;
			
			// 獲利能力 到 現金流量 title
			$financial_index_data [$row_num] [0] = $financial_title [$index] [TITLE_NAME_INDEX];
			for($j = 0; $j < count ( $tem_financial_index_data ); $j ++)
				$financial_index_data [$row_num] [$j + 1] = "";
			
			$row_num ++;
			
			// 儲存各指標數值 到 financial_index_data
			for($k = 0; $k < $financial_title [$index] [TITLE_NUMBER_INDEX]; $k ++) {
				$financial_index_data [$row_num] [0] = $datatitle [$k + ($col_num - 3)];
				for($l = 0; $l < count ( $tem_financial_index_data ); $l ++) {
					$financial_index_data [$row_num] [1 + $l] = $tem_financial_index_data [$l] [$col_num + $k];
				}
				$row_num ++;
			}
			
			$col_num = $col_num + $financial_title [$index] [TITLE_NUMBER_INDEX];
		}
		
		return $financial_index_data;
	}
}