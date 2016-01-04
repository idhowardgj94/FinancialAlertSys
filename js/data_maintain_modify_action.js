// show no data input
function showNoDataInput()
{
	alert('You have to input company data.');
}

// 提示使用者確定是否修改資料
function showConfirmModifyMessage()
{
	// 若確定則呼叫修改資料function
	if(confirm("確定修改資料？"))
	{
		modify_financial_data(modifyClass);
	}
}

// 呼叫對應的修改資料function
function modify_financial_data(e)
{
	switch(e)
	{
		case CBASIC_INFO:
		case CHINA_CBASIC_INFO:
			modify_cbasic_info();
			break;
		case CFINANCIAL_INFO:
		case CHINA_CFINANCIAL_INFO:
			modify_cfinancial_info();
			break;
		case CFINANCIAL_INDEX:
		case SECTOR_INFO:
		case GROUP_INFO:
		case TOP100_DATA:
			modify_single_cfinancial_info();
			break;
		case CRISIS_DATE:
			modify_crisis_date();
			break;
		default:
	}
}

/*
	將字串轉成上傳格式的字串
*/
function toUpdateString(str) {
	if(str!='null')
		str = '"'+str+'"';
	
	return str;
}


// 檢查使用者是否修改公司基本資料
function modify_cbasic_info()
{
	var company_id = document.getElementById(modifyClass+"_id").innerHTML;
	if(company_id!='') {
		// taiwan or china company for different modifyDataClass and table_class
		var modifyDataClass, table_class;
		switch(modifyClass) {
			case CBASIC_INFO:
				modifyDataClass = [ "company_name", "company_nickname", "status", "sector", "group"];
				table_class = CBASIC_INFO;
				break;
			case CHINA_CBASIC_INFO:
				modifyDataClass = [ "company_name", "company_nickname", "company_fullname", "status"];
				table_class = CHINA_CBASIC_INFO;
				break;
		}
		
		var index = 0; // data index
		
		// get all input but not button
		// if user has input value
		// modify this data
		$( "#"+modifyClass+" :input" ).not( "input[type=button]" ).each(function(){
			var input = $(this); // This is the jquery object of the input, do what you will
			if( input.val()!='' && input.val()!='#' ) {
				modify_data(table_class, modifyDataClass[index], input.val(), company_id);
			}
			index++;
		});
		
	}
	else
	{
		showNoDataInput(); // if company_id is null show message
	}
}


// 檢查使用者是否修改公司財務資料
function modify_cfinancial_info()
{
	var company_id = document.getElementById(modifyClass+"_id").innerHTML;
	var season = document.getElementById(modifyClass+"_season").innerHTML;
	
	if(company_id!='' && season!='') {		
		// taiwan or china company for different modifyDataClass and table_class
		var modifyDataClass, table_class;
		switch(modifyClass) {
		case CFINANCIAL_INFO:
			modifyDataClass = [ "value_at_risk", "stock", "cashflow_operating", "cashflow_investment", "proceed_fm_newIssue"];
			table_class = CFINANCIAL_INFO;
			break;
		case CHINA_CFINANCIAL_INFO:
			modifyDataClass = [ "value_at_risk", "cashflow_operating", "cashflow_investment", "proceed_fm_newIssue"];
			table_class = CHINA_CFINANCIAL_INFO;
			break;
		}
		
		var index = 0; // data index
		
		// get all input but not button
		// if user has input value
		// modify this data
		$( "#"+modifyClass+" :input" ).not( "input[type=button]" ).each(function(){
			var input = $(this); // This is the jquery object of the input, do what you will
			if( input.val()!='' && input.val()!='#' ) {
				modify_data(table_class, modifyDataClass[index], input.val(), company_id, season);
			}
			index++;
		});
	}
	else {
		showNoDataInput();
	}
}

// 修改單一格財務資料 sector or group or financial_index or top100
function modify_single_cfinancial_info()
{
	// modify condition1 and condition2
	var condition1, table_class, condition_time;
	switch(modifyClass) {
		case CFINANCIAL_INDEX:
			condition1 = document.getElementById(modifyClass+"_id").innerHTML;
			table_class = CFINANCIAL_INDEX;
			condition_time = document.getElementById(modifyClass+"_season").innerHTML;
			break;
		case TOP100_DATA:
			var top100_clist = document.getElementById("top100_company_list");
			condition1 = top100_clist.options[top100_clist.selectedIndex].value;
			table_class = TOP100_DATA;
			condition_time = document.getElementById("selected_top100_year").value;
			break;
		default:
			condition1 = document.getElementById(modifyClass+"_name").innerHTML;
			table_class = SECTOR_GROUP_INFO;
			condition_time = document.getElementById(modifyClass+"_season").innerHTML;
	}

	// get update_class
	var update_class_name = document.getElementById("selected_"+modifyClass);
	var update_class = update_class_name.options[update_class_name.selectedIndex].value;
	
	// 將 update_class 轉成對應的 col_name
	var update_col_name = getColumnName(update_class);
	
	var value = document.getElementById(modifyClass+"_value").innerHTML;
	if(value!='') {
		var value_input = document.getElementById(modifyClass+"_value_input").value;
		if(value_input!='')
			modify_data(table_class, update_col_name, value_input, condition1, condition_time);
	} else {
		showNoDataInput();
	}
}

// 檢查使用者是否修改危機發生年月資料
function modify_crisis_date()
{
	var crisis_clist = document.getElementById("crisis_date_list");
	var company_id = crisis_clist.options[crisis_clist.selectedIndex].value;
	
	var year_value = document.getElementById("crisis_date_value_year").value;
	var update_year_value = document.getElementById("crisis_date_value_input_year").value;
	if(year_value!='' && update_year_value!='') {
		var update_season_value = document.getElementById("crisis_date_value_input_season").value;
		update_value = update_year_value + "." + update_season_value; // 1999.11
		modify_data(CRISIS_DATE, CRISIS_DATE, update_value, company_id);
	}
	else {
		showNoDataInput();
	}
}

/*
	修改資料
	table name, col name, value, condition
	table_class : 欲修改的資料分類
	update_class : 欲修改的資料schema的col_name
	update_value : user input value
	condition1 : 修改條件 company_id | name
	condition2 : 修改條件 season | year
*/
function modify_data(table_class, update_class, update_value, condition1, condition2) {
	if(table_class==CBASIC_INFO || table_class==CHINA_CBASIC_INFO || table_class==CRISIS_DATE)
		update_value = toUpdateString(update_value); // "string"
	
	var parameter_str = 'table_class=' + table_class + '&update_class=' + update_class + '&value=' + update_value;
	parameter_str += '&condition[]=' + condition1; // cid or name
	if(arguments.length==5)
		parameter_str += '&condition[]=' + condition2; // season or year
	
	$.ajax({
		url: './update_datamaintain.php?'+parameter_str,
		type: 'POST',
		async: false,
		success: function(msg){
			alert(msg);
		}
	});
}