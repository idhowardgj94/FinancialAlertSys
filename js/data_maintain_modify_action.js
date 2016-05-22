// show no data input
function showNoDataInput() {
	alert('You have to input company data.');
}

// 提示使用者確定是否修改資料
function showConfirmModifyMessage() {
	// 若確定則呼叫修改資料function
	if (confirm("確定修改資料？")) {
		modify_financial_data(modifiedClass);
	}
}

// 呼叫對應的修改資料function
function modify_financial_data(e) {
	switch (e) {
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
 * 將字串轉成上傳格式的字串
 */
function toUpdateString(str) {
	if (str != 'null')
		str = '"' + str + '"';

	return str;
}

// 檢查使用者是否修改公司基本資料
function modify_cbasic_info() {
	var company_id = document.getElementById(modifiedClass + "_id").innerHTML;
	if (company_id != '') {
		// taiwan or china company for different setOfField and tableName
		var setOfField, tableName;
		switch (modifiedClass) {
		case CBASIC_INFO:
			setOfField = [ "company_name", "company_nickname", "status",
					"sector", "group" ];
			tableName = CBASIC_INFO;
			break;
		case CHINA_CBASIC_INFO:
			 setOfField= [ "company_name", "company_nickname",
					"company_fullname", "status" ];
			tableName = CHINA_CBASIC_INFO;
			break;
		}

		var index = 0; // data index

		// get all input but not button
		// if user has input value
		// modify this data
		// ----------------以下實作防呆功能

		// --------------------------
		var index = 0;
		const CBASIC_INFO_NAME_INPUT = "cbasic_info_name_input";
		const CBASIC_INFO_NICKNAME_INPUT = "cbasic_info_nickname_input";
		$("#" + modifiedClass + " :input").not("input[type=button]").each(
				function() {
					var elementmname = $("#" + modifiedClass + " :input").not(
							"input[type=button]")[index].id;
					//alert(elementmname);
					 //alert(CBASIC_INFO_NAME_INPUT);
					 //alert(CBASIC_INFO_NICKNAME_INPUT);
					var input = $(this); // This is the jquery object of the
											// input, do what you will
					var modify_state=true;
					if (input.val() != '' && input.val() != '#') {
						switch (elementmname) {
						case CBASIC_INFO_NAME_INPUT:
							if (!isChinese(input)||!lengthcheck(input.val(), MAXSTRINGLENGTH)) {
								alert("公司名稱輸入非法，將不會進行更新");
								modify_state=false;
							}
							// alert("is it ok");
							break;
						case CBASIC_INFO_NICKNAME_INPUT:
							if (!isChinese(input)||!lengthcheck(input.val(), MAXSTRINGLENGTH)) {
								alert("公司暱稱輸入非法，將不會進行更新");
								modify_state=false;
							}
							break;
						}
						if(modify_state)
							modify_data(tableName, setOfField[index], input
								.val(), company_id);
					}
					index++;
				});

	} else {
		showNoDataInput(); // if company_id is null show message
	}
}

// 檢查使用者是否修改公司財務資料
function modify_cfinancial_info() {
	var company_id = document.getElementById(modifiedClass + "_id").innerHTML;
	var season = document.getElementById(modifiedClass + "_season").innerHTML;

	if (company_id != '' && season != '') {
		// taiwan or china company for different setOfField and tableName
		var setOfField, tableName;
		switch (modifiedClass) {
		case CFINANCIAL_INFO:
			setOfField = [ "value_at_risk", "stock", "cashflow_operating",
					"cashflow_investment", "proceed_fm_newIssue" ];
			tableName = CFINANCIAL_INFO;
			break;
		case CHINA_CFINANCIAL_INFO:
			setOfField = [ "value_at_risk", "cashflow_operating",
					"cashflow_investment", "proceed_fm_newIssue" ];
			tableName = CHINA_CFINANCIAL_INFO;
			break;
		}

		var index = 0; // data index

		// get all input but not button
		// if user has input value
		// modify this data
		$("#" + modifiedClass + " :input").not("input[type=button]").each(
				function() {
					var input = $(this); // This is the jquery object of the
											// input, do what you will
					if (input.val() != '' && input.val() != '#') {
						if(isdigit(input.val())&&lengthcheck(input.val(), MAXSTRINGLENGTH))
							modify_data(tableName, setOfField[index], input
								.val(), company_id, season);
						else
							alert("輸入非法，將不會進行更新");
					}
					index++;
				});
	} else {
		showNoDataInput();
	}
}

// 修改單一格財務資料 sector or group or financial_index or top100
function modify_single_cfinancial_info() {
	// modify condition1 and condition2
	var condition1, tableName, condition_time;
	switch (modifiedClass) {
	case CFINANCIAL_INDEX:
		condition1 = document.getElementById(modifiedClass + "_id").innerHTML;
		tableName = CFINANCIAL_INDEX;
		condition_time = document.getElementById(modifiedClass + "_season").innerHTML;
		break;
	case TOP100_DATA:
		var top100_clist = document.getElementById("top100_company_list");
		condition1 = top100_clist.options[top100_clist.selectedIndex].value;
		tableName = TOP100_DATA;
		condition_time = document.getElementById("selected_top100_year").value;
		break;
	default:
		condition1 = document.getElementById(modifiedClass + "_name").innerHTML;
	tableName = SECTOR_GROUP_INFO;
		condition_time = document.getElementById(modifiedClass + "_season").innerHTML;
	}

	// get update_class
	var update_class_name = document.getElementById("selected_" + modifiedClass);
	var update_class = update_class_name.options[update_class_name.selectedIndex].value;

	// 將 update_class 轉成對應的 col_name
	var update_col_name = getColumnName(update_class);

	var value = document.getElementById(modifiedClass + "_value").innerHTML;
	if (value != '') {
		var value_input = document.getElementById(modifiedClass + "_value_input").value;
		//alert(value_input);
		if (value_input != ''&& isdigit(value_input)&&lengthcheck(value_input, MAXSTRINGLENGTH))
			modify_data(tableName, update_col_name, value_input, condition1,
					condition_time);
		else
			alert("輸入非法！將不會進行更新");
	} else {
		showNoDataInput();
	}
}

// 檢查使用者是否修改危機發生年月資料
function modify_crisis_date() {
	var crisis_clist = document.getElementById("crisis_date_list");
	var company_id = crisis_clist.options[crisis_clist.selectedIndex].value;

	var year_value = document.getElementById("crisis_date_value_year").value;
	var update_year_value = document
			.getElementById("crisis_date_value_input_year").value;
	if (year_value != '' && update_year_value != '') {
		if(isdigit(update_year_value)&&lengthcheck(update_year_value, YEAR)){
		var update_season_value = document
				.getElementById("crisis_date_value_input_season").value;
		update_value = update_year_value + "." + update_season_value; // 1999.11
		modify_data(CRISIS_DATE, CRISIS_DATE, update_value, company_id);
		}
		else
			alert("年份資料輸入非法");
	
	} else {
		showNoDataInput();
	}
}

/*
 * 修改資料 table name, col name, value, condition tableName : 欲修改的資料分類
 * update_class : 欲修改的資料schema的col_name update_value : user input value
 * condition1 : 修改條件 company_id | name condition2 : 修改條件 season | year
 */
function modify_data(tableName, update_class, update_value, condition1,
		condition2) {
	if (tableName == CBASIC_INFO || tableName == CHINA_CBASIC_INFO
			|| tableName == CRISIS_DATE)
		update_value = toUpdateString(update_value); // "string"

	var parameter_str = 'table_class=' + tableName + '&update_class='
			+ update_class + '&value=' + update_value;
	parameter_str += '&condition[]=' + condition1; // cid or name
	if (arguments.length == 5)
		parameter_str += '&condition[]=' + condition2; // season or year

	$.ajax({
		url : './update_datamaintain.php?' + parameter_str,
		type : 'POST',
		async : false,
		success : function(msg) {
			alert(msg);// 印出sql query
		}
	});
}