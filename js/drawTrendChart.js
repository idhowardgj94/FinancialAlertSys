/**
 * 繪製風險值&股價趨勢圖相關函式 
 */
// 取得畫公司風險值趨勢圖所需資料
function drawCompanyData(company_id, company_name, c) {
	$.ajax({
		url: './returnsql_companydata.php?id=' + company_id + '&class=' + c,
		type: 'POST',
		async: false,
		success: function(msg){
			var company_data = $.parseJSON(msg);
			if (company_data[0].length > 1)
			{
				chart_title = company_id + " " + company_name;
				drawCompanyLineChart(company_data, chart_title, "valueAtRisk");
			}
		}
	});
}
//取得畫公司股價趨勢圖所需資料
function drawStockData(company_id, company_name) {
	$.ajax({
		url: './returnsql_stockdata.php?id=' + company_id,
		type: 'POST',
		async: false,
		success: function(stock_season){
			var stock_data = $.parseJSON(stock_season);
			if (stock_data[0].length > 1) {
				chart_title = company_id + " " + company_name;
				drawCompanyLineChart(stock_data, chart_title, "stock");
			}
		}
	});
}

// 取得畫公司現金流量趨勢圖所需資料
function drawCashflowData(company_id, company_name, c) {
	$.ajax({
		url: './returnsql_cashflowdata.php?id=' + company_id + '&class=' + c,
		type: 'POST',
		async: false,
		success: function(cashflow_season){
			var cashflow_data = $.parseJSON(cashflow_season);
			if (cashflow_data[0].length > 1) {
				chart_title = company_id + " " + company_name;
				drawCompanyColumnChart(cashflow_data, chart_title);
			}
		}
	});
}

// 取得畫總風險值趨勢圖所需資料
function drawTotalVaRTrendChart(sectorgroup_name) {
	$.ajax({
		url: './returnsql_totalValueatRiskData.php?name=' + sectorgroup_name,
		type: 'POST',
		async: false,
		success: function(totalValueatRisk_season){
			var totalValueatRisk_data = $.parseJSON(totalValueatRisk_season);
			if (totalValueatRisk_data[0].length > 1) {
				drawCompanyLineChart(totalValueatRisk_data, sectorgroup_name, "valueAtRisk");
			}
		}
	});
}

// 取得畫產業現金流量趨勢圖所需資料
function drawSectorGroupCashflowData(sectorgroup_name) {
	$.ajax({
		url: './returnsql_sectorCashflowdata.php?name=' + sectorgroup_name,
		type: 'POST',
		async: false,
		success: function(cashflow_season){
			var cashflow_data = $.parseJSON(cashflow_season);
			if (cashflow_data[0].length > 1) {
				drawCompanyColumnChart(cashflow_data, sectorgroup_name);
			}
		}
	});
}


//bar圖
function drawCompanyColumnChart(dataforchart, chart_title) {
	var screen_w = document.documentElement.clientWidth * 0.9;
	var screen_h = document.documentElement.clientHeight * 0.45;
	
	var graphData = new google.visualization.DataTable();
	//https://developers.google.com/chart/interactive/docs/datatables_dataviews
	graphData.addColumn('string', 'time');
	graphData.addColumn('number', '現金流量');
	
	var options = { 
		title: chart_title,
		isStacked: true,
		height: screen_h,
		width: screen_w,
		chartArea: {
			backgroundColor: 'fff'
		},
		crosshair: {
			color: '#000',
			trigger: 'both' //both, focus, selection
		},		
	};
	
	for(var i = 0; i < dataforchart[0].length; ++i) {
		graphData.addRows([[ dataforchart[0][i], parseFloat(dataforchart[1][i]) ]]);
	}
	
	// 畫Columnchart到div#cashFlow
	var company_chart = new google.visualization.ColumnChart(document.getElementById("cashFlow"));
	company_chart.draw(graphData, options);
}

//line chart
function drawCompanyLineChart(dataforchart, chart_title, chart_type) {
	// 根據螢幕調大小
	var screen_w = document.documentElement.clientWidth * 0.9;
	var screen_h = document.documentElement.clientHeight * 0.45;
	
	//var dataforcolorlist = [0.35, 0.15, 0.5];
	var dataforcolorlist = [30, 10, 60];
	
	// chart 背景顏色
	//var colorlist = ["#fff", "yellow", "red"];
	var colorlist = ["red", "yellow", "#fff"];
	
	if(chart_type=="stock")
		colorlist = ["#fff", "#fff", "#fff"];

	// 資料建置
	var graphData = new google.visualization.DataTable();
	//https://developers.google.com/chart/interactive/docs/datatables_dataviews
	graphData.addColumn('string', 'time');
	
	// chart gui 參數
	var options = { 
		title: chart_title,
		isStacked: true,
		height: screen_h,
		width: screen_w,
		vAxis: {
			/* minValue: 0,
			maxValue: 1,
			format: '#,###%' */
			minValue: 0,
			maxValue: 100,
		},
		chartArea: {
			backgroundColor: 'fff'
		},
		crosshair: {
		  color: '#000',
		  trigger: 'both' //both, focus, selection
		},
		series: {
			0: {
				lineWidth: 3, //暫定
				type: 'line'
			},
			1: {
				lineWidth: 0,
				type: 'area',
				visibleInLegend: false,
				enableInteractivity: false,
				color: colorlist[0]
			},
			2: {
				lineWidth: 0,
				type: 'area',
				visibleInLegend: false,
				enableInteractivity: false,
				color: colorlist[1]
			},
			3: {
				lineWidth: 0,
				type: 'area',
				visibleInLegend: false,
				enableInteractivity: false,
				color: colorlist[2]
			}
		}
	};
	
	// 風險值與股價不同處
	if (chart_type == "valueAtRisk") {
		//graphData.addColumn('number', '風險值');
		graphData.addColumn('number', '信用評分');
	} else {
		graphData.addColumn('number', '股價');
		
		options['vAxis'] = {};
		
		var temdata = dataforchart[1][0] + 1;
		dataforcolorlist = [temdata, temdata, temdata];
	}
	graphData.addColumn('number', 'color band 1');
	graphData.addColumn('number', 'color band 2');
	graphData.addColumn('number', 'color band 3');

	for(var i = 0; i < dataforchart[0].length; ++i) {
		graphData.addRows([[ dataforchart[0][i], parseFloat(dataforchart[1][i]), dataforcolorlist[0], dataforcolorlist[1], dataforcolorlist[2] ]]);
	}
	
	// 畫combochart到div#drawcompanyresult
	var company_chart = new google.visualization.ComboChart(document.getElementById(chart_type));
	company_chart.draw(graphData, options);
}