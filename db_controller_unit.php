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
	function colseDB($conn) {
		mysqli_close ( $conn );
	}
	// 上傳 insert 資料
	function insertData($tablename, $value) {
		$dbn = $this->connect_DB ();
		$sql = 'INSERT INTO `' . $tablename . '` ' . $value . '';
		// echo $sql;
		$retval = $dbn->query ( $sql );
		if (! $retval) {
			$ret=( 'Could not insert data: ' . $dbn->error );
			$this->colseDB($dbn);
			return $ret;
		}
		
		// echo "Entered data successfully\n";
		$this->colseDB ( $dbn );
		return true;
	}
	function deleteData($tableName, $condition){
		$sqlQuery = "DELETE FROM `$tablename` WHERE ";
		while ( list ( , $key ) = each ( $condition ) ) {
			list ( , $value ) = each ( $condition );
			if ($firstFlag) {
				$sqlQuery .= "`$key`= \"$value\" ";
				$firstFlag = false;
			} else
				$sqlQuery .= "AND `$key`= \"$value\" ";
		}
		$dbn = $this->connect_DB();
		$retval = $dbn -> query($sqlQuery);
		if (! $retval) {
			$ret= 'Could not DELETE data: ' . $dbn->error;
			echo $ret;
			$this->colseDB ( $dbn );
			return $ret;
			// die("資料表名稱：$tablename\n 改動屬性：$colname");
			// die( "updateData");
		}
		
		// echo "Entered data successfully\n";
		$this->colseDB ( $dbn );
		return true;
		
	}
	/**
	 * 修改資料
	 * 
	 * @param 資料表名  $tablename  	
	 * @param 修改的欄位名稱  $colname
	 * @param 值 $value
	 * @param WHERE，要做好字串處理 $condition
	 *        	
	 *        	
	 */
	function updateData($tablename, $colname, $value, $condition) {
		$sql = 'UPDATE `' . $tablename . '` SET `' . $colname . '`=' . $value . ' WHERE ' . $condition;
		//echo $sql.'<br>';
		$dbn = $this->connect_DB ();
		$retval = $dbn->query ( $sql );
		if (! $retval) {
			$ret= 'Could not update data: ' . $dbn->error;
			$dbn->close();
			return $ret;
			// die("資料表名稱：$tablename\n 改動屬性：$colname");
			// die( "updateData");
		}
		
		// echo "Entered data successfully\n";
		$dbn->close();
		return true;
	}
	/**
	 * 取得資料庫搜尋結果
	 * <p>$attrabute如果只放*，則回傳所有資料
	 * 若只放 count(小寫），則回傳資料總筆數<br>
	 * 若放sum（小寫），在放一個欄位名稱，則回傳該欄位總筆數
	 * </p>
	 * @param database con $dbn
	 * @param tablename 表格名稱，字串        	
	 * @param AttributeArray 要搜尋的欄位，陣列，第一個值使用 "＊"表示全部
	 * @param condition 選擇條件，array格式：key, value
	 *        	
	 */
	function getDatawithCondition(&$dbn, $tablename, $AttributeArray, $condition) {
		$sqlQuery = "SELECT ";
		if (strcmp ( $AttributeArray [0], "*" ) == 0)
			$sqlQuery .= "*";
		else if(strcmp($AttributeArray[0], "count")==0){
			$sqlQuery.="COUNT(*)";
		}
		else if(strcmp($AttributeArray[0], "sum")==0){
			$sqlQuery.="SUM($AttributeArray[1])";
		}
		else {
			$firstFlag = true;
			foreach ( $AttributeArray as $attrName ) {
				if ($firstFlag) {
					$sqlQuery .= "`$attrName`";
					$firstFlag = false;
				} else
					$sqlQuery .= ", `$attrName`";
			}
		}
		$sqlQuery .= " FROM `$tablename` WHERE ";
		$firstFlag = true;
		while ( list ( , $key ) = each ( $condition ) ) {
			list ( , $value ) = each ( $condition );
			if ($firstFlag) {
				$sqlQuery .= "`$key`= \"$value\" ";
				$firstFlag = false;
			} else
				$sqlQuery .= "AND `$key`= \"$value\" ";
		}
		$dbn = $this->connect_DB ();
		$result = $dbn->query ( $sql );
		
		if (! $result) {
			echo  "Invalid query:" . mysql_error ();
			return null;
		}
		return $result;
	}
	/**
	 * 檢查該id的公司 在財務指標頁面下 是否存在
	 */
	function isExistedFinancialIndexData($input_str) {
		
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
		//mysqli_close ( $dbn );
		
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
	function getSectorGroupFinancialInfoList($class, $pageID) {
		
		// 連接資料庫
		$dbn = $this->connect_DB ();
		
		// 取出該 產業 企業集團 有資料的季別列表
		$seasonQueryResult = $dbn->query ( 'SELECT `season`
		FROM `sector_group_financial_information`
		WHERE `name` = "' . $pageID . '"
		ORDER BY `season` DESC' );
		
		// 處理季別資料
		$seasonCount = 0; // seasom count
		$temSeason = ""; // seasom query (逗點分隔）
		
		if (! empty ( $seasonQueryResult )) {
			for($i = 0; $i < mysqli_num_rows ( $seasonQueryResult ); $i ++) {
				$seasonRow = mysqli_fetch_row ( $seasonQueryResult );
				if (! empty ( $seasonRow )) {
					if ($temSeason !== "")
						$temSeason .= ", ";
					$seasonList [$seasonCount] = $seasonRow [0];
					$seasonCount = $seasonCount + 1;
					$temSeason .= " '" . $seasonRow [0] . "'";
				}
			}
		}
		
		// 儲存季別資料
		$financialInfoDataArray [0] [0] = $pageID;
		
		for($i = 0; $i < count ( $seasonList ); $i ++) {
			$financialInfoDataArray [0] [1 + $i] = $seasonList [$i];
		}
		
		// 取得對應 產業 企業集團 的公司 tem_season季別 的風險值資料
		$temSeason = " (" . $temSeason . ")";
		
		$companyDataQueryResult = $dbn->query ( 'SELECT `company_basic_information`.`company_id`, `company_basic_information`.`company_nickname`, `company_financial_information`.`season`, `company_financial_information`.`value_at_risk`
							FROM `company_basic_information`, `company_financial_information`
							WHERE `company_basic_information`.`company_id` = `company_financial_information`.`company_id` AND `company_basic_information`.`' . $class . '` = "' . $pageID . '" AND `company_financial_information`.`season` IN ' . $temSeason . '
							ORDER BY `company_basic_information`.`company_id` ASC, `company_financial_information`.`season` DESC' );
		
		$seasonDataCount = 0;
		
		$rowCount = 0;
		$colCount = 0;
		$currentCompany = - 1;
		$Result = $companyDataQueryResult;
		
		// 儲存該 產業 企業集團 下的公司資料
		if (! empty ( $Result )) {
			for($i = 0; $i < mysqli_num_rows ( $Result ); $i ++) {
				$dataRow = mysqli_fetch_row ( $Result );
				if (! empty ( $dataRow )) {
					if ($currentCompany !== $dataRow [0]) {
						// 已經沒有現在這一家公司的資料，將此家公司剩餘season全部補零
						if ($currentCompany != - 1) {
							for($i = $seasonDataCount; $i < count ( $seasonList ); $i ++) {
								$financialInfoDataArray [$rowCount] [$colCount] = "-";
								$colCount = $colCount + 1;
							}
						}
						
						$rowCount = $rowCount + 1;
						$colCount = 0;
						
						$financialInfoDataArray [$rowCount] [$colCount] = $dataRow [0] . " " . $dataRow [1];
						
						$colCount = 1;
						$currentCompany = $dataRow [0];
						$seasonDataCount = 0;
					}
					
					do {
						if ($dataRow [2] === $seasonList [$seasonDataCount] and $dataRow [3] !== null) {
							$financialInfoDataArray [$rowCount] [$colCount] = $this->convertValueatRisk ( $dataRow [3] );
							$colCount = $colCount + 1;
							
							$seasonDataCount = $seasonDataCount + 1;
							break;
						} else if ($dataRow [2] === $seasonList [$seasonDataCount] and $dataRow [3] === null) {
							$financialInfoDataArray [$rowCount] [$colCount] = "-";
							$colCount = $colCount + 1;
							
							$seasonDataCount = $seasonDataCount + 1;
							break;
						} else {
							$financialInfoDataArray [$rowCount] [$colCount] = "-";
							$colCount = $colCount + 1;
							$seasonDataCount = $seasonDataCount + 1;
						}
					} while ( 1 );
				}
			}
			for($i = $seasonDataCount; $i < count ( $seasonList ); $i ++) {
				$financialInfoDataArray [$rowCount] [$colCount] = "-";
				$colCount = $colCount + 1;
			} // 剩下的全部補空資料（最後一筆的）
		} else {
			echo "沒有資料";
		}
		
		// 取得對應 產業 企業集團 的財務資料
		$sectorGroupDataQueryResult = $dbn->query ( 'SELECT *
							FROM `sector_group_financial_information`
							WHERE `name` = "' . $pageID . '"
							ORDER BY `season` DESC' );
		$Result = $sectorGroupDataQueryResult;
		
		// 該 產業 企業集團 的資料
		if (! empty ( $Result )) {
			for($i = 0; $i < mysqli_num_rows ( $Result ); $i ++) {
				$dataRow = mysqli_fetch_row ( $Result );
				if (! empty ( $dataRow )) {
					for($j = 0; $j < count ( $dataRow ); $j ++) {
						$sectorGroupInfoArray [$i] [$j] = $dataRow [$j];
					}
				}
			}
		}
		
		// 根據class選擇固定的title名
		if ($class == 'sector') {
			$dataTitte = [ 
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
			$dataTitte = [ 
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
		
		$rowCount = count ( $financialInfoDataArray );
		
		define ( "VALUEATRISK_INDEX", 2 );
		define ( "PERCENT_INDEX", 4 );
		define ( "DECIMAL_DOT2", 7 );
		
		for($k = 2; $k < count ( $sectorGroupInfoArray [0] ); $k ++) {
			// K是$sector_group_info的第二維度（行）
			// L是第一維度（列）
			$colCount = 0;
			$financialInfoDataArray [$rowCount] [$colCount] = $dataTitte [$k - 2]; // 標題名稱
			$colCount = $colCount + 1;
			
			for($l = 0; $l < count ( $sectorGroupInfoArray ); $l ++) {
				
				if ($k === VALUEATRISK_INDEX)
					$financialInfoDataArray [$rowCount] [$colCount] = $this->convertValueatRisk ( $sectorGroupInfoArray [$l] [$k] );
				else if ($k === PERCENT_INDEX)
					$financialInfoDataArray [$rowCount] [$colCount] = sprintf ( "%1\$.2f", $sectorGroupInfoArray [$l] [$k] * 100 ) . "%";
				else if ($k === DECIMAL_DOT2)
					$financialInfoDataArray [$rowCount] [$colCount] = sprintf ( "%1\$.2f", $sectorGroupInfoArray [$l] [$k] );
				else
					$financialInfoDataArray [$rowCount] [$colCount] = $sectorGroupInfoArray [$l] [$k];
				
				$colCount = $colCount + 1;
			} // 這邊是在做資料轉置
			$rowCount = $rowCount + 1;
		}
		
		$financialInfoDataArray [$rowCount] [0] = $dataTitte [count ( $dataTitte ) - 1];
		
		for($m = 0; $m < count ( $sectorGroupInfoArray ); $m ++) {
			// '營業內創現金流量(千元)' = '營業活動之淨現金流入(千元)'+'投資活動之淨現金流入(千元)',
			$financialInfoDataArray [$rowCount] [$m + 1] = $sectorGroupInfoArray [$m] [count ( $sectorGroupInfoArray [0] ) - 3] + $sectorGroupInfoArray [$m] [count ( $sectorGroupInfoArray [0] ) - 4];
		} // 最後總合的資料
		
		mysqli_close ( $dbn );
		return $financialInfoDataArray;
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
	function getTop100FinancialInfoArray($year) {
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
	function getComapnyFinancialIndexArray($cid) {
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