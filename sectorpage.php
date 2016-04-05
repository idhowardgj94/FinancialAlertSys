<!--
	產業風險值頁面
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php session_start(); ?>
<html>
	<head>
		<title>財務演算預警系統</title>
 		<meta name="description" content="HTML-based table with fixed headers, fixed footers, fixed left columns, row selection, sorting and more. Open source.">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<link href="Grid.css" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet" type="text/css">
		<link href="table.css" rel="stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script type="text/javascript" src="Grid.js"></script>
		<script type="text/javascript" src="js/sectorgroup_page_action.js"></script>	
	</head>
	<body onload="changeColor('menu_sector')">
	<?php
	if( isset($_SESSION['username']) )
	{?>
		<div="container">
		<div id="nav">
			<?php include './menu_list.php' ?>
		</div>
		
		<?php
			define("SECTOR", "sector");//也許可以刪掉

			$index = $_GET['name'];
			include './sector_group_financial_info_page.php';

			$obj = new sector_group_financial_info_page;
			$obj->printValueatRisktable(SECTOR, $index);

			?>
		</div>
		<script type="text/javascript">
			
			(function(window, document, undefined) {
				"use strict";
						
				//根據螢幕調大小						
				var h = document.documentElement.clientHeight;
				var new_h = h-43;
						
				document.getElementById("demoDiv").style.height=new_h+'px';
						
				var gridColSortTypes = ["string", "number", "number", "number", "number", "number", "number", "number", "number", "number", "number", "number", "number", "number", "number"], 
					gridColAlign = [];
						
				var onResizeGrid = function(newWidth, newHeight) {
					var demoDivStyle = document.getElementById("demoDiv").style;
						demoDivStyle.width = newWidth + "px";
						demoDivStyle.height = newHeight + "px";
				};
						
				for (var i=0, col; col=gridColSortTypes[i]; i++) {
					gridColAlign[i] = (col === "number") ? "right" : "left";
				}
						
				var myGrid = new Grid("demoGrid", {
					srcType : "dom", 
					srcData : "demoTable", 
					colAlign : gridColAlign, 
					colBGColors : ["#fafafa"], 
					fixedCols : 1
				});
							
			})(this, this.document);
			
		</script>
		
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