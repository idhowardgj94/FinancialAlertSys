<!--
資料維護 頁面
-->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php session_start(); ?>
<html>
	<head>

		<title>財務演算預警系統</title>
 		<meta name="description" content="HTML-based table with fixed headers, fixed footers, fixed left columns, row selection, sorting and more. Open source.">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
				<script type="text/javascript" src="js/foolprove.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="js/data_maintain_list_action.js"></script>
		<script type="text/javascript" src="js/data_maintain_show_action.js"></script>
		<script type="text/javascript" src="js/data_maintain_modify_action.js"></script>
		<script type="text/javascript" src="js/data_maintain_insert_action.js"></script>
		<style type="text/css">
		#maintain_modify {
			overflow:auto;
			background-color: #fff;
			border-width:0px;
		}
		
		#maintain_insert {
			overflow:auto;
			background-color: #fff;
			border-width:0px;
		}
		
		div {
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
		
		div TD {
			border: 1px #333 solid;
		}
		
		</style>
		
	</head>
	<body onload="addOriginalSelectList()">
	
	<?php
	include 'checkAdminAccount.php';
	
	if( isset($_SESSION['username']) AND isAdmin($_SESSION['username']) )
	{?>
	<h1>資料維護</h1>
	
	<!--
		選擇要修改的資料種類
	-->
	<div id="maintainform">
	<form id="maintain_selected_action">
		<input type="radio" value="maintain_modify" name="maintain_selected" checked> 修改
		<input type="radio" value="maintain_insert" name="maintain_selected"> 新增
	</form>
	<br>
	<form id="maintainform_modify">
	欲修改資料:<br>
	<select name="uploaddata" onChange="listchange(this.options[this.selectedIndex].value);">
		<option value="cbasic_info">公司基本資料</option>
　		<option value="cfinancial_info">公司風險值/股價/現金流量資料</option>
　		<option value="cfinancial_index">公司財務指標資料</option>
		<option value="crisis_date">公司危機發生日</option>
		<option value="sector_info">產業風險資料</option>
		<option value="group_info">集團風險資料</option>
		<option value="top100_data">上市櫃百大競爭力資料</option>
		<option value="china_cbasic_info">中國公司基本資料</option>
		<option value="china_cfinancial_info">中國公司風險值/現金流量資料</option>
	</select><br/><br/>
	</form>
	
	<form id="maintainform_insert" style="display:none;">
	欲新增資料:<br>
	<select name="insertdata" onChange="listchange(this.options[this.selectedIndex].value);">
		<option value="cbasic_info">公司基本資料</option>
　		<option value="cfinancial_info">公司風險值資料</option>
		<option value="crisis_date">公司危機發生日</option>
		<option value="china_cbasic_info">中國公司基本資料</option>
		<option value="china_cfinancial_info">中國公司風險值資料</option>
	</select><br/><br/>
	</form>
	
	</div>

	<!--
		修改單一資料區塊
	-->
	<?php include './data_maintain_page_modify.html'; ?>
	
	<!--
		新增單筆資料區塊
	-->
	<?php include './data_maintain_page_insert.html'; ?>
	
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