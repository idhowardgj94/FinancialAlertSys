<html>
<head>

<script type="text/javascript">
function count(t)
{
	setTimeout("location.href='data_upload_page.php'",t);
}
			
</script>

<?php
set_time_limit(0);
include_once 'constant_definition.php';
include_once "Classes/PHPExcel.php";
include_once 'data_maintain_action.php';

header ( "Content-Type:text/html; charset=utf-8" );
// 因為有中文資料，所以要有這行

// 定義常數變數
define ( "FIRSTCOLUMN", 0 );
define ( "SECONDCOLUMN", 1 );
define ( "THIRDCOLUMN", 2 );
define ( "FOURTHCOLUMN", 3 );

// csv需將season轉格式!!!!!!!!!!!!!!!!!!
// csv檔 season 格式 = 201412 201406
// excel檔 season 格式 = 2014Q4 2014Q3



//使用者選擇上傳的文件分類
$uploadDataType = $_POST ['selected_uploaddata'];
//上傳的檔案
$uploadFile = $_FILES ['upload_file'] ['tmp_name'];
$uploadFileName = $_FILES ['upload_file'] ['name'];
$uploadFileSize = $_FILES ["upload_file"] ["size"];

if ($uploadFile) {
	$tem_upload_file_name = explode ( ".", $uploadFileName );
	$upload_file_name_extention = $tem_upload_file_name [count ( $tem_upload_file_name ) - 1];
	// 取得副檔名（count回傳陣列元素個數）
	
	if (!isAcceptableFormat($upload_file_name_extention)) {
		printError ( "檔案類型不符合規定" );
	}	
	                              
	// 檢查檔大小
	if (isFileMax($uploadFileSize)) {
		printError ( "對不起，你的檔容量大於規定" );
	}
	
	// 檢查file2是否正確上傳
	if ($uploadDataType === 'cfinancial_index') {
		$uploadFile2 = $_FILES ['upload_file_more'] ['tmp_name'];
		$uploadFile2Name = $_FILES ['upload_file'] ['name'];
		$uploadFile2Size = $_FILES ["upload_file"] ["size"];
		
		if ($uploadFile2) {
			$tem_upload_file_name = explode ( ".", $uploadFile2Name );
			$upload_file_name_extention = $tem_upload_file_name [count ( $tem_upload_file_name ) - 1];
			
			if (!isAcceptableFormat($upload_file_name_extention)) {
				printError ( "檔案類型不符合規定" );
			}
			
			if (isFileMax($uploadFile2Size)) {
				printError ( "對不起，你的檔容量大於規定" );
			}
		}
	}
	
	if (isset ( $_FILES ['upload_file'] ['error'] )) {
		if ($_FILES ['upload_file'] ['error'] === 0) {
			
			/* $dbc_object = new db_controller_unit; */
			
			// 上傳季別
			$upload_season = $_POST ['upload_year'] . $_POST ['upload_season'];
			
			echo '選擇上傳了 ' . $uploadDataType . ' 類型的檔案<br><br>';
			
			switch ($uploadDataType) {
				case 'cvalue_at_risk_tse_otc' : // 公司風險值(上市/上櫃)
				                               // uploadValueAtRisk(); sheet(0) 上市 sheet(1) 上櫃
					uploadValueAtRisk ( TAIWAN, tse, $uploadFile );
					uploadValueAtRisk ( TAIWAN, otc, $uploadFile );
					break;
				case 'cvalue_at_risk_es_public' : // 公司風險值(興櫃/公開發行)
				                                 // uploadValueAtRisk(); sheet(0) 興櫃 sheet(1) 公開發行
					uploadValueAtRisk ( TAIWAN, es, $uploadFile );
					uploadValueAtRisk ( TAIWAN, gopublic, $uploadFile );
					break;
				case 'cfinancial_index' : // 公司財務指標
					uploadFinancialIndex ( $uploadFile, $uploupload_f
					break;
				case 'cstock' : // 公司股價
				               // uploadStock($upload_file);
					uploadStockorCashflow ( TAIWAN, STOCK, $uploadFile );
					break;
				case 'ccashflow' : // 公司現金流量
				                  // uploadCashflow(TAIWAN, $upload_file);
					uploadStockorCashflow ( TAIWAN, CASHFLOW, $uploadFile );
					break;
				case 'sector_financial_info' : // 產業風險資料
					uploadSectorGroupFinancialInfo ( SECTOR, $uploadFile );
					break;
				case 'group_financial_info' : // 企業集團風險資料
					uploadSectorGroupFinancialInfo ( GROUP, $uploadFile );
					break;
				case 'top_100_financial_info' : // 上市櫃百大競爭力資料
					uploadTop100FinancialInfo ( $uploadFile );
					break;
				case 'china_cvalue_at_risk' : // 中國公司風險值
					uploadValueAtRisk ( CHINA, "T", $uploadFile );
					break;
				case 'china_ccashflow' : // 中國公司現金流量
				                        // uploadCashflow(CHINA, $upload_file);
					uploadStockorCashflow ( CHINA, CASHFLOW, $uploadFile );
					break;
			}
		}
	}
}

Echo "<p>你上傳了文件:";
echo $_FILES ['upload_file'] ['name'];
echo "<br>";
// 用戶端機器文件的原名稱。

Echo "文件的 MIME 類型為:";
echo $_FILES ['upload_file'] ['type'];
// 檔的 MIME 類型，需要流覽器提供該資訊的支援，例如“image/gif”。
echo "<br>";

Echo "上傳文件大小:";
echo $_FILES ['upload_file'] ['size'];
// 已上傳檔的大小，單位為位元組。
echo "<br>";

// Echo "檔上傳後被臨時儲存為:";
// echo $_FILES['upload_file']['tmp_name'];
// 檔被上傳後在服務端儲存的暫存檔案名。
// echo "<br>";

/*
 * $Erroe=$_FILES['upload_file']['error'];
 * switch($Erroe){
 * case 0:
 * Echo "上傳成功";
 * Echo "<Script Language='JavaScript'>count(3000);</Script>";
 * break;
 * case 1:
 * Echo "上傳的檔案超過了 php.ini 中 upload_max_filesize 選項限制的值.";
 * Echo "<Script Language='JavaScript'>count(5000);</Script>";
 * break;
 * case 2:
 * Echo "上傳檔案的大小超過了 HTML 表單中 MAX_FILE_SIZE 選項指定的值。";
 * Echo "<Script Language='JavaScript'>count(5000);</Script>";
 * break;
 * case 3:
 * Echo "檔案只有部分被上傳";
 * Echo "<Script Language='JavaScript'>count(5000);</Script>";
 * break;
 * case 4:
 * Echo "沒有檔案被上傳";
 * Echo "<Script Language='JavaScript'>count(5000);</Script>";
 * break;
 * }
 */

/**
 * 檢查檔案格式
 * @param
 * fileNameExtention：檔案副檔名
 * @return 
 * ture or false
 */
function isAcceptableFormat($fileNameExtention){
	if ($fileNameExtention != 'csv' and $fileNameExtention != 'xls' and $fileNameExtention != 'xlsx')
		return false;
	return true;
}

/**
 * 檢查檔案大小有沒有超過上限
 * 檔案大小常數：FILESIZEMAX
 * 定義在 constant_definition.php中
 * @param fileSize：檔案大小
 * @return true or false
 * */
function isFileMax($fileSize){
	if ($fileSize > FILESIZEMAX)
		return true;
	return false;
}
// 輸出error字串然後離開php腳本
function printError($str) {
	exit ( $str );
}

// 檢查season與輸入的字串是否一致
function checkUploadSeason($season) {
	if ($season === $GLOBALS ['upload_season'])
		return 1;
	else
		return 0;
}

// 讀取xls檔案
function loadExcelFile($file, $sheet) {
	//maybe should use static variable?
	// 讀xls檔案
	
	// 取得資料檔的型式
	$fileType = PHPExcel_IOFactory::identify ( $file );
	
	// 產生 Reader 物件
	$objReader = PHPExcel_IOFactory::createReader ( $fileType );
	
	// 產生 PHPExcel 物件來幫忙我們處理 Excel 檔案
	$objPHPExcel = $objReader->load ( $file );
	
	// 將活頁簿裏的第一張工作表設為要操作的工作表
	$objWorksheet = $objPHPExcel->getSheet ( $sheet );
	
	return $objWorksheet;
}

// 取得xls檔案的sheet名
function getSheetName($file) {
	// 讀取 sheet 名稱
	$sheetnames = $objReader->listWorksheetNames ( $file );
	return $sheetnames;
}

// 讀取csv檔案
function loadCsvFile($file) {
	// 讀取csv檔
	// $CSVfile_size = filesize($file);
	$fp = fopen ( $file, "r" );
	// $ROW = fgetcsv($fp, $CSVfile_size);
	/*
	 * while ( $ROW = fgetcsv($fp, $CSVfile_size) ) {
	 * echo $ROW[0];
	 * echo '<br>';
	 * }
	 */
	
	return $fp;
}

// 上傳風險值檔案
function uploadValueAtRisk($c, $status, $file) {
	// c : taiwan or china
	// file : 上傳的檔案
	// status : 公司狀態
	if ($status === tse or $status === es or $status === "T")
		$fp = loadExcelFile ( $file, 0 );
	else
		$fp = loadExcelFile ( $file, 1 );
	
	// line 1 : tile列 公司ID 公司名稱 季別
	// line 2 start : data列 公司ID 公司名稱 該季風險值
	
	// step 1 : checkCompany(cid)
	// step 2 : checkFinancialInfo(taiwan, cid, season) ? 修改該id x season資料風險值欄位 : 新增該id x season資料
	//          同時儲存公司ID LIST
	
	// loop step 1 2 until data[0] = ''
	
	// checkCompanyList(status, list)
	
	if($fp) {
		// 取得季別
		//設計是先column在row，跟一般習慣相反…
		$season = $fp->getCellByColumnAndRow(THIRDCOLUMN, 1)->getValue();
		
		// 檢查季別是否與使用者輸入的季別相同
		// 若相同則繼續上傳動作
		if(checkUploadSeason($season)) {
		
			// 取得原本status下的公司ID清單
			$old_company_list = getCompanyList($c, $status);
			$company_index = 0;
			
			if($c===TAIWAN)
				$table_name = COMPANYFINANCIALINFORMATION;
			else
				$table_name = CHINACOMPANYFINANCIALINFORMATION;

			// 若status不等於上市, 將原本該status下的公司狀態改為null
			if($status!=tse) {
				$condition = '`status`="'. $status .'"';
				$GLOBALS [ 'dbc_object' ]->updateData(COMPANYBASICINFORMATION, 'status', 'null', $condition);
			}
			
			// 取得資料列數
			$highestRow = $fp->getHighestRow();
			
			// 照row的順序讀取每一家公司資料
			//row start from 1, and column start from 0?
			for($row = 2; $row <= $highestRow; $row++) {
				//echo $row;
				if( $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue() === '' )
					break;

				// 讀取該公司資料
				$cid = $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue();
				$company_name = $fp->getCellByColumnAndRow(SECONDCOLUMN, $row)->getValue();
				$value_at_risk = checkNull($fp->getCellByColumnAndRow(THIRDCOLUMN, $row)->getCalculatedValue());
				
				// 檢查公司是否存在於資料庫 若存在 existedCompany = 1 反之 = 0
				$existedCompany = checkCompany($c, $cid);
				
				if($existedCompany) {
					// 檢查該id x season資料是否存在
					if( checkFinancialInfo($c, $cid, $season) ) {
						echo '公司代號' . $cid . ' 季別' . $season . '風險值資料已存在<br>';
						//之後要記得加上upload！
					}
					else {
						if($c===TAIWAN) {
							$insert_value = '(`company_id`, `season`, `value_at_risk`, `stock`, `cashflow_operating`, `cashflow_investment`, `proceed_fm_newIssue`) 
						VALUES ("'. $cid .'","'. $season .'", '. $value_at_risk .', null, null, null, null)';
						}
						else {
							$insertvalue = '(`company_id`, `season`, `value_at_risk`, `cashflow_operating`, `cashflow_investment`, `proceed_fm_newIssue`) 
						VALUES ("'. $cid .'","'. $season .'",'. $value_at_risk .', null, null, null)';
						}
						$GLOBALS [ 'dbc_object' ]->insertData($table_name, $insert_value);
					}

					
					// 將該公司狀態改為status
					$status_value = '"'. $status .'"';
					$condition = '`company_id`="'. $cid .'"';
					$GLOBALS [ 'dbc_object' ]->updateData(COMPANYBASICINFORMATION, 'status', $status_value, $condition);

					
					// search 公司id是否存在於old_company_list並刪除該element
					$index = array_search($cid, $old_company_list);
					if($index !== FALSE)
						unset( $old_company_list[$index]);
				} else {
					echo '公司ID'. $cid .'不存在於資料庫中<br>';
				}
			}
			
			echo '上季公司名單中<br>';
			$old_company_list = array_values($old_company_list);
			for($i=0; $i<count($old_company_list); $i++) {
				echo '公司ID' . $old_company_list[$i] . '<br>';
			}
			echo '不存在在這季風險值檔案中';
		
		} else
			printError('檔案內的季別與輸入的上傳季別不一致 取消上傳動作');
	}
}


/**
 * 取得該status下的公司清單
 * $c:台灣或中國（資料表不同）
 * $status：上市、上櫃、興櫃…
 * return：公司的清單排序（company id）
 */
function getCompanyList($c, $status) {
	$company_num = 0;
	
	if($c===TAIWAN)
		$table_name = 'company_basic_information';
	else
		$table_name = 'china_company_basic_information';
	
	$dbn = $GLOBALS [ 'dbc_object' ]->connect_DB();
	$tem_companydata = $dbn->query('SELECT `company_id` FROM `'. $table_name .'` WHERE `status`="'. $status .'" ORDER BY `company_id` ASC');
	if(!empty($tem_companydata)) {
		for($i=0; $i<mysqli_num_rows($tem_companydata); $i++) {
			$companydata=mysqli_fetch_row($tem_companydata);
			$company_list[$company_num] = $companydata[0];
			$company_num++;
		}
	}
	
	return $company_list;
}


/*
	上傳股價 現金流量資料
	c : taiwan or china
	class : stock or cashflow
	file : upload file (.csv)
*/
function uploadStockorCashflow($c, $class, $file) {
	// fp : 讀取好的檔案
	$fp = loadCsvFile($file);
	
	// line 1 : tile列 公司ID 公司名稱 季別 資料欄位
	// 股價 : 1欄
	// 現金流量 : 3欄
	
	// line 2 start : data列 公司ID 公司名稱 季別 該季資料
	// step 1 : checkFinancialInfo(c, cid, season) ? 修改該id x season資料資料欄位 : 不做任何事
	// 			日期轉換 20140630 -> 2014Q2
	// loop step 1 until row over
	
	// stock : checkStockDate(season)
	
	if($fp) {
		if($c===TAIWAN)
			$tablename = 'company_financial_information';
		else if($c===CHINA)
			$tablename = 'china_company_financial_information';
		
		$ROW = fgetcsv($fp); // title列（跳過）
		
		// 計算現金流量的index
		if($class===CASHFLOW)
			$cashflowColNameIndexArray = getCashflowIndexArray($ROW);
		
		while ( $ROW = fgetcsv($fp) ) {
			$cid = trim($ROW[FIRSTCOLUMN]);
			if( is_numeric($cid) ) {
				$date = $ROW[THIRDCOLUMN];
				
				// 將日期轉成季別格式
				$season = convertDate2Season($date);
				
				// 檢查季別是否與使用者輸入的季別相同
				// 若相同則繼續上傳動作
				if(checkUploadSeason($season)) {
					if( checkFinancialInfo($c, $cid, $season) ) { // 檢查資料庫中是否已有該筆idxseason資料
						$condition = '`company_id`="'. $cid .'" AND `season`="'. $season .'"';
						if($class===STOCK) { // 修改資料庫中該筆idxseason資料的股價欄位
							$stock = checkNull($ROW[FOURTHCOLUMN]);
							$GLOBALS [ 'dbc_object' ]->updateData($tablename, STOCK, $stock, $condition);
						} else if($class=CASHFLOW) { // 修改資料庫中該筆idxseason資料的現金流量欄位
							for($i=0; $i<count($cashflowColNameIndexArray); $i++) {
								$cashflow = checkNull($ROW[$cashflowColNameIndexArray[1][$i]]);
								$GLOBALS [ 'dbc_object' ]->updateData($tablename, $cashflowColNameIndexArray[0][$i], $cashflow, $condition);
							}
						}else
							printError('處理資料出現問題！');
					}
				} else
					printError('檔案內的季別與輸入的上傳季別不一致 取消上傳動作');
			}
		}
		
		if($class===STOCK) // 上傳股價日期對應季別的資料
			updateStockDate($date);
	}
}

// 計算現金流量對應的index
// 營業 : 0
// 投資 : 1
// 現金增資 : 2
// return cashflow_index
function getCashflowIndexArray($titleRow) {
	// cashflow的指標名稱
	$cashflowColname = array('cashflow_operating', 'cashflow_investment', 'proceed_fm_newIssue');
	
	// 預設的index
	$cashflowIndex = array( FOURTHCOLUMN, FOURTHCOLUMN+1, FOURTHCOLUMN+2);
	
	// 判斷 title_row 裡title對應的index並存在cashflow_index
	for($i=0; $i<count($titleRow); $i++) {
		if( trim($titleRow[$i]) === '來自營運之現金流量' )
			$cashflowIndex[0] = $i;
		
		if( trim($titleRow[$i]) === '投資活動之現金流量' )
			$cashflowIndex[1] = $i;
		
		if( trim($titleRow[$i]) === '現金增 〈減〉 資' )
			$cashflowIndex[2] = $i;
	}
	
	$cashflowColNameIndexArray = array( 
		$cashflowColname, $cashflowIndex
	);
	
	// 回傳colname對應的index陣列
	return $cashflowColNameIndexArray;
}

// 檢查股價日期是否存在
function updateStockDate($date) {
	$dbn = $GLOBALS [ 'dbc_object' ]->connect_DB();
	$season = convertDate2Season($date);
		
	$tem_stockdate = $dbn->query('SELECT * FROM `stock_date` WHERE `date`="'. $date .'" OR `season`="'. $season .'"');
	
	if(!empty($tem_stockdate)) {
		$tem_stockdate_row=mysqli_fetch_row($tem_stockdate);
		if(!empty($tem_stockdate_row)) {
			$condition = '`season`="'. $season .'"';
			$GLOBALS [ 'dbc_object' ]->updateData('stock_date', 'stock_date', $date, $condition);
		}
		else {
			$insertvalue = '(`season`, `date`) VALUES ( "'. $season .'", "'. $date .'")';
			$GLOBALS [ 'dbc_object' ]->insertData('stock_date', $insertvalue);
		}
	}
}

// 上傳產業 企業集團資料
function uploadSectorGroupFinancialInfo( $class, $file) {
	// class : sector or group
	// fp : 讀取好的檔案
	$fp = loadExcelFile($file, 0);
	
	// '#' 為 分隔線 代表下一個產業 企業集團 開始
	
	// line 1 : sector or group name (1,1)
	// line 2 : title列 公司ID 公司名稱 季別(3,2)
	// line 3 start : 公司data列 公司ID 公司名稱 風險值(不重要)
	//                儲存公司名稱list company_list
	// until data(n,1) = __總風險值
	
	// line n start : 產業 企業集團data列 : 財務指標名稱 該季該財務指標數值
	//                依順序 `total_value_at_risk`, `total_assets`, `debt_asset_ratio`, `net_sales`, 
	// 					`net_income`, `eps`, `cashflow_operating`, `cashflow_investment`, 
	//					`cashflow_financing`, `proceed_fm_newIssue`
	// 					最後一行為cashflow_operating + cashflow_investment不記入資料庫資料
	
	// start a = 1 to highestRow
	// step 1 : 紀錄 sector or group name (0,a+1) : name
	// step 2 : 紀錄 season(2, a+2) : season
	// step 3 : check 該 name x season 資料是否存在, 若存在則break以下動作
	// step 4 : 紀錄 公司ID : company_list[i] until (0, a)=__總風險值
	// step 5 : 取得(2,a)~(2,a+10)資料上傳到 name x season 資料 check (0,a)~(0,a+10)!=''or'#'
	// step 6 : checkSectorGroupCompanyList( class, name, company_list)
	// if (0,a) = '#' loop 以上動作step1~6 : until (0,a) = '' or 無下一列資料
	
	$data_num = 10;// 預設檔案排序為資料庫中schema排序 財務資料數為10種
	$isFirst = 0;
	
	if($fp) {
		// 取得季別
		$season = $fp->getCellByColumnAndRow(THIRDCOLUMN, 2)->getValue();

		// 檢查季別是否與使用者輸入的季別相同
		// 若相同則繼續上傳動作
		if(checkUploadSeason($season)) {
			$highestRow = $fp->getHighestRow();
			for($row = 1; $row <= $highestRow; $row++) {
				if($fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue() === '' // 若連續三行為空字串則跳出檔案
				AND $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row+1)->getValue() === '' 
				AND $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row+2)->getValue() === '')
						break;
				
				// row = 1 為檔案中第一家產業 企業集團
				// 其餘用'#'分隔
				if($row===1 OR $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue()==='#' ) {
					if($row>1) { // 判斷是檔案中第一家還是其他產業 企業集團
						$row++;
						$isFirst = 0;
					} else {
						$isFirst = 1;
					}
					
					// 取得產業 企業集團名稱
					$name = $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue();
					
					// 將row設為下一列
					$row++;
					
					// 若為檔案中第一家產業 企業集團則再跳過season列
					if($isFirst)
						$row++;
					
					// 判斷資料庫中是否已有該季資料 若沒有才上傳
					if( !checkSectorGroupFinancialInfo($name, $season) ) {
						// 將原本該產業 企業集團分類下的公司分類改為未分類
						$condition = '`'. $class .'`="'. $name .'"';
						$GLOBALS [ 'dbc_object' ]->updateData( 'company_basic_information', $class, 'null', $condition);
					
						// while 第一格不為總風險值 OR 無連續兩列為空白
						// 讀取該產業 企業集團分類下的公司代號並修改該公司分類
						while( strpos($fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue(), '總風險值') === false 
						AND $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue() !== '' 
						AND $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row+1)->getValue() !== '' ) {
							$cid = $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue();

							$condition = '`company_id`="'. $cid .'"';
							$update_value = '"'. $name .'"';
							$GLOBALS [ 'dbc_object' ]->updateData( 'company_basic_information', $class, $update_value, $condition);
							
							$row = $row + 1;
						}
						
						// 新增該季產業 企業集團財務資料
						$insertvalue = '(`name`, `season`, `total_value_at_risk`, `total_assets`, `debt_asset_ratio`, `net_sales`, `net_income`, `eps`, `cashflow_operating`, `cashflow_investment`, `cashflow_financing`, `proceed_fm_newIssue`) 
						VALUES ( "'. $name .'", "'. $season .'"';
						for($i=0; $i<$data_num; $i++) { // 預設檔案排序為資料庫中schema排序 財務資料數為10種
							if( $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row+$i)->getValue() !== '' OR $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row+$i)->getValue() !== '#' ) {
								$insertvalue .= ', '. checkNull($fp->getCellByColumnAndRow(THIRDCOLUMN, $row+$i)->getCalculatedValue());
							}
						}
						$insertvalue .= ')';

						$GLOBALS [ 'dbc_object' ]->insertData('sector_group_financial_information', $insertvalue);
					}
				}
				
			}
		} else
			printError('檔案內的季別與輸入的上傳季別不一致 取消上傳動作');
	}
}

// 上傳前百大財務資料
function uploadTop100FinancialInfo($file) {
	// 前百大公司資料
	$fp = loadExcelFile($file, 0);
	
	define("RANDCOLUMN", 14);
	
	if($fp) {
	
		// 讀取season
		$season = $fp->getCellByColumnAndRow(FIRSTCOLUMN, 1)->getValue();
		
		// 檢查季別是否與使用者輸入的季別相同
		// 若相同則繼續上傳動作
		if(checkUploadSeason($season)) {
			$highestRow = $fp->getHighestRow(); // get HighestRow mean number of last row
			
			// line 1 : season (0,1)
			// line 2 : title列 公司代號 公司名稱 風險值 資產總額(千元) 營業收入淨額(千元) 稅後淨利(千元) 毛利率 營益率 每股盈餘 每元資本營收槓桿 ROA ROE 競爭力總分 競爭力名次
			// line 3 start : 公司data列 公司ID 公司名稱 ry
			
			// step 1 : 檢查該season資料是否存在, 若存在則break以下動作
			// step 2 : insert top 100 company data
			// 			依順序`scores`, `rank`, `total_assets`, `net_sales`, `net_income`, `leverage`, `market_value`
			//			競爭力總分, 競爭力名次, 資產總額, 營業收入淨額, 稅後淨利, 每元資本營收槓桿, null
			//			其他資料為財務指標資料故不記(市值尚未確認先存null值)
			// until rank = 100 or 沒下一列資料
			
			$fileName = 'top100 variable.xlsx';
			$top100Variable = loadExcelFile($fileName, 0);
			
			if( !checkTop100Data($season) ) {
				for($row = 3; $row <= $highestRow; $row++) {
					if( $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue() === '' OR $fp->getCellByColumnAndRow(RANDCOLUMN, $row-1)->getValue() === '100' )
						break;

					// 讀取該公司資料
					$cid = $fp->getCellByColumnAndRow(FIRSTCOLUMN, $row)->getValue();
					
					// 讀取top100 variable檔案中儲存的indx
					// 依序取出對應的資料存到top_100_data陣列中
					$col = 1;
					while( $top100Variable->getCellByColumnAndRow($col, 1)->getValue() != '' ) {
						if( $top100Variable->getCellByColumnAndRow($col, 3)->getValue() != '' ) {
							$index = $top100Variable->getCellByColumnAndRow($col, 3)->getValue()-1;
							$top100Data[$index] = $fp->getCellByColumnAndRow($col, $row)->getValue();
						}
						$col++;
					}
					
					// insert Data
					// 將top_100_data陣列中排序的資料組成上傳字串
					$insertvalue = '(`company_id`, `season`, `scores`, `rank`, `total_assets`, `net_sales`, `net_income`, `leverage`, `market_value`) 
						VALUES ("'. $cid .'","'. $season .'"';
					for($i=0; $i<count($top100Data); $i++) {
						$insertvalue .= ', '. checkNull($top100Data[$i]);
					}
					$insertvalue .= ', null)';
					
					$GLOBALS [ 'dbc_object' ]->insertData('top_100_company', $insertvalue);
				}
			}
		} else
			printError('檔案內的季別與輸入的上傳季別不一致 取消上傳動作');
	}
}

// 上傳財務指標資料
function uploadFinancialIndex($file, $fileMore) {
	// financial_index_all
	
	// line 1 : title列 公司代號 公司名稱 季別 ry
	// line 2 start : 公司data列 公司ID 公司名稱 季別 ry
	
	// 讀取兩個檔案
	// 看哪個col數較多 設為file1 另一個為file2
	
	$tempFile = loadCsvFile($file);
	$tempFile2 = loadCsvFile($fileMore);
	
	if($tempFile && $tempFile2) {
		$tempFileRowArray = fgetcsv($tempFile);
		$tempFile2RowArray = fgetcsv($tempFile2);
		
		if( count($tempFileRowArray)>count($tempFile2RowArray) ) {
			$file1 = $tempFile;
			$file2 = $tempFile2;
		} else {
			$file1 = $tempFile2;
			$file2 = $tempFile;
		}
		
		$fileRow = fgetcsv($file1);
		$file2Row = fgetcsv($file2);

		// 計算所需col index  data[i]的i x file col數
		// (1, 13) (3, 12) (4, 10) ... (8, 3)
		
		$colIndexArray = getFinancialIndexArray($fileRow, 1);
		$colIndexMoreArray = getFinancialIndexArray($file2Row, 2);
		
		// for each row
		//    儲存file1資料至data[]
		//    while (file2 company_id < file1 company_id & file2 != null ) , file2 do fgetcsv
		//    if (相同) 將file2中需要的資料儲存在data[]
		//    else (file2 company_id > file1 company_id) 將data[]剩餘欄位設為null
		
		//    將data[]轉成字串sql
		//    上傳sql
		
		// loop until file1 無資料
	
		$dataNum = 0;
		$mysql_command = '';
		
		while ( $file1Row = fgetcsv($file1) ) {
			for($i=0; $i<count($colIndexArray); $i++)
				$financialIndexData[$dataNum][ $colIndexArray[$i][0] ] = $file1Row[ $colIndexArray[$i][1] ];
			
			$season = convertDate2Season($financialIndexData[$dataNum][1]);
			// 檢查季別是否與使用者輸入的季別相同
			// 若相同則繼續上傳動作
			if(checkUploadSeason($season)) {
				while ( $file2Row = fgetcsv($file2) ) {
					if( trim($file2Row[0]) >= trim($financialIndexData[$dataNum][0]) )
						break;
				}
				
				if( trim($file2Row[0]) === trim($financialIndexData[$dataNum][0]) ) {
					for($i=0; $i<count($colIndexMoreArray); $i++)
						$financialIndexData[$dataNum][ $colIndexMoreArray[$i][0] ] = $file2Row[ $colIndexMoreArray[$i][1] ];
				} else {
					for($i=0; $i<count($colIndexMoreArray); $i++)
						$financialIndexData[$dataNum][ $colIndexMoreArray[$i][0] ] = 'null';
				}
				
				$sql='';
				for( $i=0; $i<count($financialIndexData[$dataNum]); $i++ ) {
					if( $sql!='' )
						$sql .= ', ';
					
					if( $i===1 ) // season轉換格式
						$sql .= convertDate2Season($financialIndexData[$dataNum][$i]);
					else
						$sql .= checkNull($financialIndexData[$dataNum][$i]);
				}
				
				
				if($mysqlCommand!='')
					$mysqlCommand .= ', ';
				
				$mysqlCommand .= '( ' . $sql . ')';
				
				$dataNum++;
			} else
				printError('檔案內的季別與輸入的上傳季別不一致 取消上傳動作');
		
		}
		
		$insertValue = '(`company_id`, `season`, `gross_margin`, `operating_income`, `pretax_income`, `ps_sales`, `ps_operating_income`, `ps_pre_tax_income`, `roe`, `roa`, `eps`, `current`, `acid_test`, `liabilities`, `times_interest_earne`, `aoverr_and_noverr_turnover`, `inventory_turnover`, `fixed_asset_turnover`, `total_asset_turnover`, `debt_over_equity_ratio`, `liabilities_to_assets_ratio`, `cashflow_operating`, `cashflow_investment`, `cashflow_financing`, `proceed_fm_newIssue`, `total_equity`, `lt_liabilities`, `total_fixed_assets`, `lt_investment`, `interest_exp`, `total_liabilities`, `net_sales`, `pre_tax_income`, `change_in_cashflow`) VALUES
			' . $mysqlCommand . '<br>';
			
		$GLOBALS [ 'dbc_object' ]->insertData('financial_index_all', $insertValue);
	}
}

// 檢查該季產業 企業集團財務資料是否存在
function checkSectorGroupFinancialInfo($name, $season) {
	// name : sector or group name
	
	// check 該name x season資料是否存在
	// if 存在 : return 1
	// else : return 0
	$dbn = $GLOBALS [ 'dbc_object' ]->connect_DB();
		
	$tem_companydata = $dbn->query('SELECT * FROM `sector_group_financial_information` WHERE `name`="'. $name .'" AND `season`="'. $season .'"');
	
	if(!empty($tem_companydata)) {
		$companydata=mysqli_fetch_row($tem_companydata);
		if(!empty($companydata))
			return 1;
		else
			return 0;
	}	
}

// 檢查該季前百大資料是否存在
function checkTop100Data($season) {
	// 檢查該季別資料是否存在
	$dbn = $GLOBALS [ 'dbc_object' ]->connect_DB();
		
	$tem_companydata = $dbn->query('SELECT * FROM `top_100_company` WHERE `season`="'. $season .'"');
	if(!empty($tem_companydata)) {
		$companydata=mysqli_fetch_row($tem_companydata);
		if(!empty($companydata))
			return 1;
		else
			return 0;
	}
}

// 計算財務指標上傳檔案的index
// 根據financial_index variable financial_index_more variable
function getFinancialIndexArray($fileRow, $fileNum) {
	
	if( $fileNum === 1 )
		$fileName = 'financial_index variable.xlsx';
	else
		$fileName = 'financial_index_more variable.xlsx';

	$indexFile = loadExcelFile($fileName, 0);
	
	$highestColumm = $indexFile->getHighestColumn();
	$highestColumm = PHPExcel_Cell::columnIndexFromString($highestColumm);

	
	$index = 0;
	for($i=0; $i<count($fileRow); $i++) {
		for($j=0; $j<$highestColumm; $j++) {
			if( $fileRow[$i] === $indexFile->getCellByColumnAndRow($j, 2)->getValue() ) {
				$colIndex[$index][0] = $indexFile->getCellByColumnAndRow($j, 3)->getValue();
				$colIndex[$index][1] = $i;
				$index++;
				break;
			}
		}
	}

	return $colIndex;
}

// 將 date 轉成 季別格式
// ex : 20130930 -> 2013Q3
//      201312 -> 2013Q4
function convertDate2Season($date) {
	$tem_year = str_split($date, 4);
	$tem_month = str_split($tem_year[1], 2);
			
	$season = $tem_year[0] . 'Q' . $tem_month[0]/3;
	
	return $season;
}

// 檢查字串是否為空 是的話回傳null字串
function checkNull($str) {
	if(trim($str)!='-' AND trim($str)!='')
		return trim($str);
	else
		return 'null';
}

?>

</head>

</html>