
<?php
session_start ();
// session_destroy();
?>
<html>
<head>
<link href="Grid.css" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet" type="text/css">
		<link href="table.css" rel="stylesheet" type="text/css">
<meta http-equiv="content-type" content="text/html" charset="utf-8">
<!-- 讓瀏覽器知道編碼使用utf-8 -->
<title>財務演算預警系統</title>
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<!-- inlude jquery libary(in google) -->
<script type="text/javascript">
		$(function(){
				$("ul.navigation > li:has(ul) > a").append('<div class="arrow-bottom"></div>');
				$("ul.navigation > li ul li:has(ul) > a").append('<div class="arrow-right"></div>');
				
			});
		//此表示方式為當DOM被載入後，執行此function。
	</script>

</head>
<body>

	<?php
	if (isset ( $_SESSION ['username'] )) {
		?>
		<div id="nav">
			<?php include './menu_list.php';?>
		</div>
		<?php include 'explanation.html';?>
	<?php
	} else {
		echo '您無權觀看此頁面！';
		session_destroy ();
		echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
		// 重新導向到login.php
	}
	?>
	
</body>
</html>
