<!--
財務指標頁面
-->
<?php session_start(); ?>
<html>
<head>
<title>財務演算預警系統</title>
<meta name="description"
	content="HTML-based table with fixed headers, fixed footers, fixed left columns, row selection, sorting and more. Open source.">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<link href="Grid.css" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700"
	rel="stylesheet" type="text/css">
<link href="table.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/component.css" />
<script src="js/modernizr.custom.js"></script>
<script type="text/javascript" src="js/financial_index_page_action.js"></script>
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="Grid.js"></script>
<script src="js/classie.js"></script>
<script type="text/javascript">
			$(function(){
				$("ul.navigation > li:has(ul) > a").append('<div class="arrow-bottom"></div>');
				$("ul.navigation > li ul li:has(ul) > a").append('<div class="arrow-right"></div>');
			});
		</script>
</head>
<body onload="changeColor()">
<?php
if (isset ( $_SESSION ['username'] )) {
	?>
<div id="nav">	
<?php
	include './menu_list.php';
	?>					
	<form>
			<span style="margin-top: 5px; margin-right: 5px; float: right;"> <input
				style="border-radius: 5px;" size="5" id="serchInput"></input> <a
				href="#" class="abgne_gotoheader"><input type="submit"
					onclick="searchCompanyFiancialIndex(event);" size="5" value="搜尋"></a>
			</span> <a href="#" id="showLeft"><input type="button" size="5"
				value="進階"></a>
		</form>
	</div>

	<div>
		<?php
	include 'cfinancial_index_list.html';
	include 'financial_index_page.php';
	if (isset ( $_GET ['cid'] )) {
		$cid = $_GET ['cid'];
		
		$object = new financial_index_page ();
		$object->printFinancialIndexTable ( $cid );
	}
	
	?>
				<script type="text/javascript">
			(function(window, document, undefined) {
				"use strict";
				
				//document.getElementById("demoDiv").style.margin = '0px 0px 40px 0%';
				//document.getElementById("demoDiv").style.float = 'right';
				//document.getElementById('demoDiv').style.width = document.documentElement.clientWidth;
				
				//根據螢幕調大小
				
				var h = document.documentElement.clientHeight;
				var new_h = h-43;
				
				document.getElementById("demoDiv").style.height=new_h+'px';
				
				var gridColSortTypes = ["string", "number", "number", "number", "number", "number", "number", "number", "number", "number"], 
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
		<script>
			var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),//這裡是幹麻的
				showLeft = document.getElementById( 'showLeft' ),
				
				body = document.body;

			showLeft.onclick = function() {
				classie.toggle( this, 'active' );
				classie.toggle( menuLeft, 'cbp-spmenu-open' );
				
			};
		</script>
	</div>
<?php
} else {
	echo '您無權限觀看此頁面!';
	session_destroy ();
	echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
}
?>
</body>

</xhtml>