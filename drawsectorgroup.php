<!-------------------------
	產業&集團趨勢圖顯示頁面
------------------------->
<?php session_start(); ?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="js/drawTrendChart.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
		<script type="text/javascript">
			// 依連結取得JavaScript之API
			function loadAPI(url) {
				var script = document.createElement('script');
				script.src = url;
				script.type = 'text/javascript';
				document.getElementsByTagName("head")[0].appendChild(script);
			}
		</script>
		<style type="text/css">
			BODY
			{
				background:linear-gradient(top,#888,#eee);
				background:-moz-linear-gradient(top,#888,#eee);
				background:-webkit-linear-gradient(top,#888,#eee);
			}
		</style>
	</head>
	<body>
	<?php
	if( isset($_SESSION['username']) )
	{?>
	
		<script type="text/javascript">	
			// 讀取Google API結束後執行loadVisual()
			loadAPI('http://www.google.com/jsapi?callback=loadVisual');

			// Google API讀取繪圖所需Packages，並執行loadPage()
			function loadVisual() {
				google.load('visualization', '1', {'packages':['corechart'], "callback" : loadPage});
			}
			
			// 依公司代號及名稱取得資料並開始繪圖
			function loadPage() {
				var chart_id = "<?php echo $_GET['id']; ?>";
				drawTotalVaRTrendChart(chart_id);
				drawSectorGroupCashflowData(chart_id);
			}
		</script>
		
		<div id="valueAtRisk"></div>
		<div id="cashFlow"></div>
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