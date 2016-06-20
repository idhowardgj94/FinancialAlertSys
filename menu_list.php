
<style type="text/css">
			/* 初始化 */
			*{
				margin:0px;
			}
			
			ul, li, a{
				margin: 0;
				padding: 0;
				font-size: 13px;
				text-decoration: none;
			}
			ul, li {
				list-style: none;
			}
			
			/* 選單 li 之樣式 */			
			ul.navigation li {
				position: relative;
				float: left;
				overflow: visible;
			}
			/* 選單 li 裡面連結之樣式 */
			ul.navigation li a{
				display: block;
				padding: 12px 20px;
				//舊
				background: #888;
				//background: #000088;
				color: #FFF;
			}
			/* 特定在第一層，以左邊灰線分隔 */
			ul.navigation > li > a{			
				border-left: 1px solid #CCC;
				border-right: 1px solid #CCC;
				//border-left: 1px solid #0044BB;
				//border-right: 1px solid #0044BB;
			}
			ul.navigation > li > a:hover{
				color: #666;
				background: #DDD;
				//color: #FFF;
				//background: #5555FF;
			}
			/* 特定在第一層 > 第二層或以後下拉部分之樣式 */
			ul.navigation ul{
				display: none;
				float: left;
				position: absolute;			
				left: 0;	
				margin: 0;
				z-index: 100;
			}
			/* 當第一層選單被觸發時，指定第二層顯示 */
			ul.navigation li:hover > ul{
				display: block;
			}			
			/* 特定在第二層或以後下拉部分 li 之樣式 */
			ul.navigation ul li {
				border: 1px solid #AAA;
				//border: 1px solid #FFF;
			}
			
			
			/* 特定在第二層或以後下拉部分 li （最後一項不要底線）之樣式 */
			ul.navigation ul li:last-child {
				border-bottom: 2px solid #AAA;
				//border: 1px solid #FFF;
			}
			
			
			/* 第二層或以後選單 li 之樣式 */
			ul.navigation ul a {
				width: 160px; /* 原本150 */
				padding: 10px 12px;	
				color: #666;		
				background: #EEE;
				//color: #000;		
				//background: #CCCCFF;
			}
			ul.navigation ul a:hover {		
				background: #CCC;
				color: #FFF;
				//background: #5555FF;
			}
			/* 第三層之後，上一層的選單觸發則顯示出來（皆為橫向拓展） */
			ul.navigation ul li:hover > ul{
				display: block;
				position: absolute;
				top: 0;				
				left: 100%;
			}
			/* 箭頭向下 */
			.arrow-bottom {
				display: inline-block;
				margin-left: 5px;
				border-top: 4px solid #FFF;
				border-right: 4px solid transparent;				
				border-left: 4px solid transparent;		
				width: 1px;
				height: 1px;
			}

			/* 箭頭向右 */
			.arrow-right {
				display: inline-block;
				margin-left: 12px;	
				border-top: 3px solid transparent;
				border-bottom: 3px solid transparent;
				border-left: 3px solid #666;		
				width: 1px;
				height: 1px;
			}
			
		</style>



<ul class="navigation">
	<li id="menu_risk_at_value"><a href="#">企業財務風險監控</a>
		<ul>
			<li><a href="#">台灣公司</a>
				<ul>
					<li><a href="listedtsec.php">上市</a></li>
					<li><a href="listedotcc.php">上櫃</a></li>
					<li><a href="listedces.php">興櫃</a></li>
					<li><a href="gopublicc.php">公開發行</a></li>
					<li><a href="cdelisting.php">下市下櫃</a></li>
				</ul></li>
			<li><a href="chinac.php">中國公司</a></li>
		</ul></li>

	<li id="menu_financialIndex"><a href="cfinancialIndex.php?cid=1101">企業財務指標</a></li>

	<li id="menu_sector"><a href="#">產業風險監控</a>
		<ul>
			<li><a href="sectorpage.php?name=TCS">電信系統</a></li>
			<li><a href="sectorpage.php?name=DRAM">DRAM</a></li>
			<li><a href="sectorpage.php?name=TFT-LCD">TFT-LCD</a></li>
			<li><a href="sectorpage.php?name=Foundry">晶圓代工</a></li>
			<li><a href="sectorpage.php?name=LED">LED</a></li>
			<li><a href="sectorpage.php?name=SolarEnergy">太陽能</a></li>
			<li><a href="sectorpage.php?name=Construction">建築業</a></li>
		</ul></li>

	<li id="menu_group"><a href="#">企業集團風險監控</a>
		<ul>
			<li><a href="grouppage.php?name=fpg">台塑集團</a></li>
			<li><a href="grouppage.php?name=foxconn">鴻海集團</a></li>
			<li><a href="grouppage.php?name=uni-president">統一集團</a></li>
			<li><a href="grouppage.php?name=acer">宏碁集團</a></li>
			<li><a href="grouppage.php?name=csc">中鋼集團</a></li>
			<li><a href="grouppage.php?name=feg">遠東集團</a></li>
			<li><a href="grouppage.php?name=tatung">大同集團</a></li>
			<li><a href="grouppage.php?name=TSMC">台積電集團</a></li>
			<li><a href="grouppage.php?name=ruentex">潤泰集團</a></li>
		</ul></li>

	<li id="menu_top100"><a href="#">上市櫃百大競爭力</a>
		<ul>
			<li><a href="ctop100.php?y=2015">2015Q1</a></li>
			<li><a href="#">2008 - 2014</a>
				<ul>
					<li><a href="ctop100.php?y=2014">2014Q4</a></li>
					<li><a href="ctop100.php?y=2013">2013Q4</a></li>
					<li><a href="ctop100.php?y=2012">2012Q4</a></li>
					<li><a href="ctop100.php?y=2011">2011Q4</a></li>
					<li><a href="ctop100.php?y=2010">2010Q4</a></li>
					<li><a href="ctop100.php?y=2009">2009Q4</a></li>
					<li><a href="ctop100.php?y=2008">2008Q4</a></li>
					<!--<li><a href="ctop100.php?y=2007">2007Q4</a></li>-->
				</ul></li>
		</ul></li>

	<!-- 國家風險值先拿掉
	
	<li><a href="#">國家風險值</a>
		<ul>
		<li><a href="tem.html">台灣</a></li>
		<li><a href="tem.html">中國</a></li>
		<li><a href="tem.html">日本</a></li>
		<li><a href="tem.html">韓國</a></li>
		<li><a href="tem.html">馬來西亞</a></li>
		<li><a href="tem.html">菲律賓</a></li>
		<li><a href="tem.html">泰國</a></li>
		<li><a href="tem.html">印度</a></li>
		</ul>
	</li>
	
	-->

	<li><a href="#"
		onclick="window.open('SystemExplanation.html', '系統說明', 
		  'alwaysRaised=yes, top=300, left=500, width=630, height=400, location=no');">系統說明</a>
		<ul>
			<li><a href="#"
				onclick="window.open('IndustryRiskExplanation.html', '產業風險監控系統系統說明', 
      'alwaysRaised=yes, top=300, left=500, width=400, height=200, location=no');">產業風險監控系統</a></li>
			<li><a href="#"
				onclick="window.open('GroupRiskExplanation.html', '企業集團風險監控系統', 
      'alwaysRaised=yes, top=300, left=500, width=400, height=200, location=no');">企業集團風險監控系統</a></li>
		</ul></li>
	<?php
	include 'checkAdminAccount.php';
	if (isAdmin ( $_SESSION ['username'] )) {
		?>
	<li><a href="#">系統維護</a>
		<ul>
			<li><a href="#" onclick="window.open('data_upload_page.php');">整季資料上傳</a></li>
			<li><a href="#" onclick="window.open('data_maintain_page.php');">單筆資料新增修改</a></li>
		</ul></li>
	<?php
	}
	?>
</ul>