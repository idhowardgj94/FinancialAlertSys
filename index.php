<?php session_start(); ?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html"; charset="utf-8">
	<title>財務演算預警系統</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script type="text/javascript">
		
	</script>
	<style type="text/css">
	BODY{
		background:linear-gradient(top,#888,#eee);
		background:-moz-linear-gradient(top,#888,#eee);
		background:-webkit-linear-gradient(top,#888,#eee);
	}
	#nav {
		height: 37px;
		width: 100%;
	}
	</style>
	<?php 
	if(isset($_SESSION['username'])){
	?>
		<div id="nav">
			<?php include './menu_list.php';?>
		</div>
	<?php 
	}else{
		echo '您無權觀看此頁面！';
		session_destroy();
		echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
	}		
	?>
</head>
</html>
