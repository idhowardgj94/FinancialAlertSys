// show no data
function showNoDataReturn()
{
	alert('No data.');
}

// 檢查是否為空值
function checkNull(str)
{
	if(str==null)
		return "null";
	else
		return str;
}

// 將str轉換成col name
function getColumnName(str)
{
	var xmlDoc;
	var dname = 'dictionary.xml'; // xml檔名

	// 讀取xml檔
	try {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', dname, false);
		xmlhttp.setRequestHeader('Content-Type', 'text/xml');
		xmlhttp.send('');
		xmlDoc = xmlhttp.responseXML;
	} catch (e) {
		try {
			xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		} catch (e) {
			console.error(e.message);
		}
	}
	  
	// get all word elements
	var wordNodes = xmlDoc.getElementsByTagName("word");
	
	// 判斷wordname 與 str是否相等
	// 若相等傳回word內的value(colName)
	for(var i=0; i<wordNodes.length; i++) {
		var wordName = xmlDoc.getElementsByTagName("word")[i].getAttribute("name");
		if(str==wordName) {
			var colName = xmlDoc.getElementsByTagName("word")[i].innerHTML;
			return colName;
		}
	}
	
	// 找不到相等值 回傳null
	return null;
}

// 使用者按下輸入按鈕時觸發對應function修改表格資料
function formSubmit()
{
	alert("in form submit");
	//alert(modifyclass);
	switch(modifyClass)
	{
		case CBASIC_INFO:
		case CHINA_CBASIC_INFO:
			change_cbasic_info();
			break;
		case CFINANCIAL_INFO:
		case CHINA_CFINANCIAL_INFO:
			change_cfinancial_info();
			break;
		case CFINANCIAL_INDEX:
			change_cfinancial_index();
			break;
		case SECTOR_INFO:
		case GROUP_INFO:
			change_sector_group_info();
			break;
		case TOP100_DATA:
			change_top100_data();
			break;
		default:
	}
}

// 透過php連接sql取得需要顯示的資料
function get_needed_data(classes, condition)
{
	alert("in get_needed_data");
	// 組成丟進php取資料的字串
	var parameter_str='data_class='+classes;
	if(condition) {
		for(var i=0; i<condition.length; i++)
			parameter_str+='&condition[]='+condition[i];
	}

	var return_data;
	$.ajax({ // 取得選取分類的資料的資料
		url: './returnsql_datamaintain.php?'+parameter_str,
		type: 'POST',
		async: false,
		success: function(msg){
			var returnsql_data = $.parseJSON(msg);
			if(returnsql_data!=null){ // 若有資料則將資料設給return_data
				alert("return data:"+returnsql_data);
				return_data = returnsql_data;
			}
			else { // 無資料則提示沒有資料回傳
				//alert("no data");
				showNoDataReturn();
			}
		}
	});
	return return_data;
}

// 修改公司基本資料
function change_cbasic_info()
{
	// 取得使用者輸入公司ID
	var company_id = document.getElementById('cid_input_value').value;
	
	var condition =[];
	condition.push(company_id);
	
	// get 公司基本資料
	var company_basic_data = get_needed_data(modifyClass, condition);
	if(company_basic_data) { // 若有資料傳回則顯示在螢幕上
		document.getElementById(modifyClass).style.display = "block";
		document.getElementById(modifyClass+"_id").innerHTML = company_id;
		var displayBlock = document.getElementById(modifyClass).getElementsByTagName("p");
		for(var i=0; i<displayBlock.length; i++)
			displayBlock[i].innerHTML = checkNull(company_basic_data[i+1]);
	}

}

// 修改公司風險值 股價 現金流量
function change_cfinancial_info()
{
	// 取得使用者輸入公司ID season
	var company_id = document.getElementById("cid_input_value").value;	
	var year = document.getElementById("season_input_year").value;
	
	var e = document.getElementById("season_input_value");
	var season = e.options[e.selectedIndex].value;
	
	var company_season = year + season;
	
	var condition =[];
	condition.push(company_id);
	condition.push(company_season);
	
	// 判斷是否為中國公司
	var classes;
	switch(modifyClass) {
		case CFINANCIAL_INFO:
			classes = CBASIC_INFO;
			break;
		case CHINA_CFINANCIAL_INFO:
			classes = CHINA_CBASIC_INFO;
			break;
	}
	
	// get 公司基本資料
	var company_basic_data = get_needed_data(classes, condition);
	
	// get 公司財務資料
	var company_financial_data = get_needed_data(modifyClass, condition);
	if(company_basic_data && company_financial_data) { // 若有資料傳回則顯示在螢幕上
		document.getElementById(modifyClass).style.display = "block";
		/*
			taiwan
				company_basic_data : comapny_id company_name comapny_nickname status sector group
				company_financial_data : comapny_id season value_at_risk stock cashflow_operating cashflow_investment proceed_fm_newIssue
			china
				company_basic_data : comapny_id company_name comapny_nickname comapny_fullname status
				company_financial_data : comapny_id season value_at_risk cashflow_operating cashflow_investment proceed_fm_newIssue
		*/
		document.getElementById(modifyClass+"_id").innerHTML = company_id;
		document.getElementById(modifyClass+"_season").innerHTML = company_season;
		
		var needed_data =[];
		
		const COMPANY_NICKNAME_INDEX = 2;
		const STATUS_INDEX = 3;
		const SECTOR_INDEX = 4;
		const GROUP_INDEX = 5;
		const CHINA_COMPANY_NAME_INDEX = 1;
		const VALUE_AT_RISK_INDEX = 2;
		const STOCK_INDEX = 3;
		const CASHFLOW_OPERATING_INDEX = 4;
		const CASHFLOW_INVESTMENT_INDEX = 5;
		const PROCEED_FM_NEWISSUE_INDEX = 6;
		const CHINA_VALUE_AT_RISK_INDEX = 2;
		const CHINA_CASHFLOW_OPERATING_INDEX = 3;
		const CHINA_CASHFLOW_INVESTMENT_INDEX = 4;
		const CHINA_PROCEED_FM_NEWISSUE_INDEX = 5;
		
		// 設定需要顯示的資料index 基本資料
		switch(modifyClass) {
		case CFINANCIAL_INFO:
			var needed_data_index = [COMPANY_NICKNAME_INDEX, STATUS_INDEX, SECTOR_INDEX, GROUP_INDEX];
			break;
		case CHINA_CFINANCIAL_INFO:
			var needed_data_index = [CHINA_COMPANY_NAME_INDEX];
			break;
		}
		
		for(var i=0; i<company_basic_data.length; i++) { // 將需要顯示的資料push進needed_data
			if(needed_data_index.indexOf(i)!=-1)
				needed_data.push(company_basic_data[i]);
		}
		
		// 設定需要顯示的資料index 財務資料
		switch(modifyClass) {
		case CFINANCIAL_INFO:
			needed_data_index = [VALUE_AT_RISK_INDEX, STOCK_INDEX, CASHFLOW_OPERATING_INDEX, CASHFLOW_INVESTMENT_INDEX, PROCEED_FM_NEWISSUE_INDEX];
			break;
		case CHINA_CFINANCIAL_INFO:
			needed_data_index = [CHINA_VALUE_AT_RISK_INDEX, CHINA_CASHFLOW_OPERATING_INDEX, CHINA_CASHFLOW_INVESTMENT_INDEX, CHINA_PROCEED_FM_NEWISSUE_INDEX];
			break;
		}

		for(var i=0; i<company_financial_data.length; i++) { // 將需要顯示的資料push進needed_data
			if(needed_data_index.indexOf(i)!=-1)
				needed_data.push(company_financial_data[i]);
		}
		
		// 將needed_data顯示在螢幕上
		var displayBlock = document.getElementById(modifyClass).getElementsByTagName("p");
		for(var i=0; i<displayBlock.length; i++)
			displayBlock[i].innerHTML = checkNull(needed_data[i]);
	}
}

// 修改公司財務指標
function change_cfinancial_index()
{
	// 取得使用者輸入公司ID season
	var company_id = document.getElementById("cid_input_value").value;
	var year = document.getElementById("season_input_year").value;
	var e = document.getElementById("season_input_value");
	var season = e.options[e.selectedIndex].value;
	
	var condition =[];
	condition.push(company_id);
	condition.push(season);
	
	// get 公司基本資料
	var company_basic_data = get_needed_data(CBASIC_INFO, condition);
	if(company_basic_data) { // 若有資料傳回則顯示在螢幕上
		document.getElementById(modifyClass).style.display = "block";
	
		document.getElementById(modifyClass+"_id").innerHTML = company_id;
		document.getElementById(modifyClass+"_season").innerHTML = year + season;
		
		const COMAPNY_NICKNAME_INDEX = 2;
		const STATUS_INDEX = 3;
		
		document.getElementById("cfinancial_index_name").innerHTML = checkNull(company_basic_data[COMAPNY_NICKNAME_INDEX]);
		document.getElementById("cfinancial_index_status").innerHTML = checkNull(company_basic_data[STATUS_INDEX]);
	}
}

// 修改產業 企業集團資訊
function change_sector_group_info()
{
	// 判斷是產業 or 企業集團
	var classes;
	switch(modifyClass) {
		case SECTOR_INFO:
			classes = 'sector';
			break;
		case GROUP_INFO:
			classes = 'group';
			break;
	}
	
	if(classes) {
		// 將使用者選擇的名稱與季別顯示在螢幕上
		document.getElementById(modifyClass).style.display = "block";
		var e = document.getElementById("selected_"+classes);
		var sector_name = e.options[e.selectedIndex].value;
		
		var year = document.getElementById("season_input_year").value;
		var k = document.getElementById("season_input_value");
		var season = k.options[k.selectedIndex].value;
		
		document.getElementById(modifyClass+"_name").innerHTML = sector_name;
		document.getElementById(modifyClass+"_season").innerHTML = year + season;
	}
}


// 產生該年份的前百大公司清單
function change_top100_data()
{
	// get 使用者選擇的年份
	var e = document.getElementById("selected_top100_year");
	var year = e.options[e.selectedIndex].value;
	document.getElementById("top100_year").innerHTML = year;
	
	var condition =[];
	condition.push(year);
	
	// 取得該年份的前百大公司名單
	var company_list = get_needed_data('top100_companylist', condition);
	if(company_list) { // 產生公司名單的下拉式選單
		document.getElementById("top100_data").style.display = "block";
		
		const COMPANY_ID_INDEX=0;
		const COMPANY_NAME_INDEX=1;
		var option_company_list = [];
		
		var top100_company_list = document.getElementById("top100_company_list");
		top100_company_list.options.length = 0;
		top100_company_list.selectedIndex=0;
		
		for (var i=0;i<company_list[COMPANY_ID_INDEX].length;i++) {
			option_company_list.push(company_list[COMPANY_ID_INDEX][i] + " " + company_list[COMPANY_NAME_INDEX][i]);
		}
		
		var new_option;
		// 產生公司選單
		for(i = 0; i< option_company_list.length; i++) {
			new_option = new Option(option_company_list[i],company_list[COMPANY_ID_INDEX][i]);
			top100_company_list.options.add(new_option);
		}
	}
}

// 產生有危機發生日的公司清單
function addSelectCrisisCompany()
{
	var company_list = get_needed_data('crisis_companylist');
	if(company_list) {
		var option_company_list = [];
		const COMPANY_ID_INDEX=0;
		const COMPANY_NAME_INDEX=1;

		var crisis_date_company_list = document.getElementById("crisis_date_list");
		crisis_date_company_list.selectedIndex=0;
				
		for (var i=0;i<company_list[COMPANY_ID_INDEX].length;i++) {
			option_company_list.push(company_list[COMPANY_ID_INDEX][i] + " " + company_list[COMPANY_NAME_INDEX][i]);
		}
		
		var new_option;
		// 產生公司選單
		for(i = 0; i< option_company_list.length; i++) {
			new_option = new Option(option_company_list[i],company_list[COMPANY_ID_INDEX][i]);
			crisis_date_company_list.options.add(new_option);
		}
	}

	change_crisis_date(crisis_date_company_list.options[0].value);
}

// 修改危機發生日頁面資訊
function change_crisis_date(company_id)
{
	var condition =[];
	condition.push(company_id);
	
	// get 該公司ID的危機發生日
	var crisis_date = get_needed_data(CRISIS_DATE, condition);
	if(crisis_date) {
		const YEAR_INDEX = 0;
		const SEASON_INDEX = 1;

		crisis_date = crisis_date.split(".");
		document.getElementById("crisis_date_value_year").innerHTML = checkNull(crisis_date[YEAR_INDEX]);
		document.getElementById("crisis_date_value_season").innerHTML = checkNull(crisis_date[SEASON_INDEX]);
	}
}

/*
	change selected financial info
	change the info diaplay
	
	selectFinancialInfo : 選擇的需顯示資料
	
	CFINANCIAL_INDEX 財務指標資料
	TOP100_DATA	前百大財務資料
	SECTOR_INFO GROUP_INFO 產業 企業集團財務資料
*/
function change_select_financial_info(selectFinancialInfo) {
	// 將選擇的需顯示資料名稱print在頁面上
	document.getElementById(modifyClass+'_original').innerHTML = selectFinancialInfo;
	document.getElementById(modifyClass+'_new').innerHTML = selectFinancialInfo;
	
	var condition1, classes, condition_time;
	// 根據modifyClass讀取需要的資料
	switch(modifyClass) {
		case CFINANCIAL_INDEX: // 財務指標
			condition1 = document.getElementById(modifyClass+"_id").innerHTML;
			classes = CFINANCIAL_INDEX;
			condition_time = document.getElementById(modifyClass+"_season").innerHTML;
			break;
		case TOP100_DATA: // 前百大
			// 取得使用者選擇的公司ID
			var top100_clist = document.getElementById("top100_company_list");
			condition1 = top100_clist.options[top100_clist.selectedIndex].value;
			classes = TOP100_DATA;
			condition_time = document.getElementById("selected_top100_year").value;
			break;
		default: // 產業 企業集團
			condition1 = document.getElementById(modifyClass+"_name").innerHTML;
			classes = SECTOR_GROUP_INFO;
			condition_time = document.getElementById(modifyClass+"_season").innerHTML;
	}

	// 將資料種類中文名稱轉換成col name
	var col_name = getColumnName(selectFinancialInfo);
	
	var condition =[];
	condition.push(condition1); // c_id or name
	condition.push(condition_time); // season or year
	condition.push(col_name);

	// 用上述資料取得對應的財務資料
	var financial_data_value = get_needed_data(classes, condition);
	if(financial_data_value) {
		document.getElementById(modifyClass+"_value").innerHTML = checkNull(financial_data_value); // 若有值回傳則print在頁面上
	}
}

// 清空使用者上次的輸入顯示在頁面上的資訊
function clean_info() {
	// 清空div內的span 及 p
	$( "#"+modifyClass ).find( "span" ).empty();
	$( "#"+modifyClass ).find( "p" ).empty();
	
	// 若 modifyClass 的div內有下拉式選單 將selectedIndex設為0
	if(modifyClass==CFINANCIAL_INDEX || modifyClass==SECTOR_INFO || modifyClass==GROUP_INFO || modifyClass==TOP100_DATA)
		document.getElementById("selected_"+modifyClass).selectedIndex = "0";
}


// 產生初始select選單
function addOriginalSelectList() {
	addSectorGroupSelectList("selected_sector", "sector");
	addSectorGroupSelectList("cbasic_info_sector_input", "sector");
	addSectorGroupSelectList("cbasic_info_insert_sector_input", "sector");
	addSectorGroupSelectList("selected_group", "group");
	addSectorGroupSelectList("cbasic_info_group_input", "group");
	addSectorGroupSelectList("cbasic_info_insert_group_input", "group");
}

// 產生產業 企業集團select選單
// id : select id
// classes : sector or group
function addSectorGroupSelectList(id, classes) {
	var xmlDoc;
	var dname = 'sectorgroupname.xml'; // xml檔名

	// 讀取xml檔
	try {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', dname, false);
		xmlhttp.setRequestHeader('Content-Type', 'text/xml');
		xmlhttp.send('');
		xmlDoc = xmlhttp.responseXML;
	} catch (e) {
		try {
			xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		} catch (e) {
			console.error(e.message);
		}
	}
	  
	// get all elements
	var nodes = xmlDoc.getElementsByTagName(classes);
	
	//先用getElementById取得select的id
	var select = document.getElementById(id);
	select.selectedIndex = 0;
	
	// 將所有node產生成option
	// 加進select清單
	for(var i=0; i<nodes.length; i++) {
		var colName = xmlDoc.getElementsByTagName(classes)[i].innerHTML;
		var new_option = new Option(colName, colName);
		select.options.add(new_option);
	}
	
	switch(id) {
		case "cbasic_info_sector_input":
		case "cbasic_info_insert_sector_input":
			var new_option = new Option("其他", "null");
			select.options.add(new_option);
			break;
		case "cbasic_info_group_input":
		case "cbasic_info_insert_group_input":
			var new_option = new Option("無", "null");
			select.options.add(new_option);
			break;
	}
}