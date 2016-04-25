<!--
資料上傳 頁面
-->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php session_start(); ?>
<html>
	<head>

		<title>財務演算預警系統</title>
 		<meta name="description" content="HTML-based table with fixed headers, fixed footers, fixed left columns, row selection, sorting and more. Open source.">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
		<script type="text/javascript", src="js/dataMaintainUploadAction.js"></script>
		<style type="text/css">
		#uploadform {
			display:block;
			margin-left:auto;
			margin-right:auto;
			background-color: #eee;
			border-width:1px;
			border-color:#999;
			border-style:solid;
			padding-top:5px;
			padding-right:30px;
			margin:auto;
			margin-bottom:10px;
		}
		
		</style>

	</head>
	<body onload="getLatestSeason();">

	<?php
	include 'checkAdminAccount.php';
	
	if( isset($_SESSION['username']) AND isAdmin($_SESSION['username']) )
	{
	?>
	
	<h1>資料上傳</h1>
	
	<div id="uploadform">
	
	<form name="uploadForm" enctype="multipart/form-data" action="up_page.php" method="post" onSubmit="return checkSubmit()">
	<!-- 顯示defalut最新一季 -->
	上傳檔案季別：<br>
	年份：
	<input id="upload_year" name="upload_year" size="10"></input><br>
	季別：
	<select name="upload_season">
		<option value="Q1">Q1</option>
		<option value="Q2">Q2</option>
		<option value="Q3">Q3</option>
		<option value="Q4">Q4</option>
	</select>
	
	<br><br>
	<!-- 選擇上傳資料類別 -->
	上傳資料類別：<br>
	<select name="selected_uploaddata" onChange="selectFinancialIndex(this.options[this.selectedIndex].value);">
　		<option value="cvalue_at_risk_tse_otc">公司風險值(上市/上櫃)</option>
		<option value="cvalue_at_risk_es_public">公司風險值(興櫃/公開發行)</option>
　		<option value="cfinancial_index">公司財務指標</option>
　		<option value="cstock">公司股價</option>
　		<option value="ccashflow">公司現金流量</option>
		<option value="sector_financial_info">產業風險資料</option>
		<option value="group_financial_info">企業集團風險資料</option>
		<option value="top_100_financial_info">上市櫃百大競爭力資料</option>
		<option value="china_cvalue_at_risk">中國公司風險值</option>
		<option value="china_ccashflow">中國公司現金流量</option>
	</select>
	<br/>
	
	接受以下格式檔案：.csv .xlsx .xls<br/>
	檔案大小不可超過10M。<br/>
	上傳前請確認檔案排序正確。<br/><br/>
	
	<!-- 選擇上傳檔案 -->
	請選擇檔案： <br>
	<input name="upload_file" id="upload_file" type="file"><br>
	<input name="upload_file_more" id="upload_file_more" type="file" style="display:none;"><br>
	<input type="submit" value="上傳文件">
	</form>
	</div>
	<br>
	<input onclick="window.close();" value="關閉視窗" type="button">

	<?php
	}
	else
	{
		echo '您無權限觀看此頁面!';
		session_destroy();
		echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
	}
	?>
	</body>
</html>