<!-------------------------
	趨勢圖顯示頁面
------------------------->
<?php session_start(); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type="text/javascript"
	src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/drawTrendChart.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript"
	src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
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
BODY {
	background: linear-gradient(top, #888, #eee);
	background: -moz-linear-gradient(top, #888, #eee);
	background: -webkit-linear-gradient(top, #888, #eee);
}
</style>
</head>
	
	<?php
	if (isset ( $_SESSION ['username'] )) {
		$company_id = $_GET ['id'];
		$classification = $_GET ['class'];
		
		// 取所有資料
		include 'db_controller_unit.php';
		$obj1 = new db_controller_unit ();
		$dbn = $obj1->connect_DB ();
		
		if (isset ( $company_id )) {
			$datatem = $dbn->query ( 'SELECT * FROM `company_basic_information` WHERE `company_id` = "' . $company_id . '" ' );
			$data_row = mysqli_fetch_row ( $datatem );
		}
		?>
		<script type="text/javascript">	
			// 讀取Google API結束後執行loadVisual()
			loadAPI('http://www.google.com/jsapi?callback=loadVisual');

			// Google API讀取繪圖所需Packages，並執行loadPage()
			function loadVisual() {
				google.load('visualization', '1', {'packages':['corechart'], "callback" : loadPage});
			}
			
			// 依公司代號及名稱取得資料並開始繪圖
			function loadPage() {
				const TAIWAN = 'taiwan';
				var company_id = <?php echo $company_id; ?>;
				var company_name = "<?php echo $data_row[2]; ?>";
				var classification = "<?php echo $classification; ?>";
				
				<?php
		$dbn = null;
		?>
				
				drawCompanyData(company_id, company_name, TAIWAN); 
				
				if(classification != "publicoffer") {
					drawStockData(company_id, company_name);
				} else {
					drawCashflowData(company_id, company_name, TAIWAN);
				}
				
			}
		</script>
<div id="valueAtRisk"></div>
<!--</br>-->
<div id="stock"></div>
<div id="cashFlow"></div>
<br>

<input onclick="window.close();" value="關閉視窗" type="button">
		<?php
	} else {
		echo '您無權限觀看此頁面!';
		session_destroy ();
		echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
	}
	?>
	</body>
</html>