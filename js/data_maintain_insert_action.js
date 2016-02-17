// 提示使用者確定是否新增資料
function showConfirmInsertMessage()
{
	// 若確定則呼叫新增資料function
	if(confirm("確定新增資料？"))
	{
		insert_financial_info(insertClass);
	}
}

// 新增單筆財務資料
function insert_financial_info(classes)
{
	var insertdata = [];
	var input_all = 1;
	
	if(classes==CRISIS_DATE)
		class_id = "crisis_date_info_insert";
	else
		class_id = classes+"_insert";

	var user_input;
	// 取得所有input輸入丟進insertdata陣列中
	$( "#"+class_id+" :input" ).not( "input[type=button]" ).each(function(){
		var input = $(this); // This is the jquery object of the input, do what you will
		if( input.val()!='' && input.val()!='#') {
			//alert(input.val());
			if(!lengthcheck(input.val(), MAXSTRINGLENGTH)){
				alert("長度異常！");
				input_all = 0;
			}
			else{
				user_input = input.val();
				insertdata.push( user_input );
			}
		} else { // 若其中有無輸入的input格將flag改成0
			input_all = 0;
		}
	});
	
	// 需全部input格都有輸入值才會執行insert function
	if( input_all )
		insert_data(classes, insertdata);
	else
		showNoDataInput();
}

/*
	新增資料
	table_class, value[]
	table_class : 欲新增的資料分類
	value[] : user input value
			  insert data 前 k 個資料值
*/
function insert_data(table_class, value) {
	var parameter_str = 'table_class=' + table_class + '&value[]=' + value[0];
	for(var i=1; i<value.length; i++)
		parameter_str += '&value[]=' + value[i];

	$.ajax({
		url: './insert_datamaintain.php?'+parameter_str,
		type: 'POST',
		async: false,
		success: function(msg){
			alert(msg);
		}
	});
}