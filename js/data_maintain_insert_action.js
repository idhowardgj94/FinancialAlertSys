// 提示使用者確定是否新增資料
function showConfirmInsertMessage()
{
	// 若確定則呼叫新增資料function
	if(confirm("確定新增資料？"))
	{
		insertFinancialInfo(insertionType);
	}
}

// 新增單筆財務資料
function insertFinancialInfo(insertionType)
{
	var insertionList = [];
	var isInsertion = 1;
	var tableName;
	if(insertionType==CRISIS_DATE)
		tableName = "crisis_date_info_insert";
	else
		tableName = insertionType+"_insert";

	
	// 取得所有input輸入丟進insertdata陣列中
	$( "#"+tableName+" :input" ).not( "input[type=button]" ).each(function(){
		var input = $(this); // This is the jquery object of the input, do what you will
		if( input.val()!='' && input.val()!='#') {
			//alert(input.val());
			if(!lengthcheck(input.val(), MAXSTRINGLENGTH)){
				alert("長度異常！");
				isInsertion = 0;
			}
			else
				insertionList.push( input.val());
		} else { // 若其中有無輸入的input格將flag改成0
			isInsertion = 0;
		}
	});
	
	// 需全部input格都有輸入值才會執行insert function
	if( isInsertion )
		insertDatatoDB(insertionType, insertionList);
	else
		showNoDataInput();
}

/*
	新增資料
	tableName, value[]
	tableName : 欲新增的資料分類
	value[] : user input value
			  insert data 前 k 個資料值
			  table_class
			  
*/
function insertDatatoDB(tableName, selectedInsertItem) {
	var parameter_str = 'tableName=' + tableName + '&selectedInsertItem[]=' + selectedInsertItem[0];
	for(var i=1; i<selectedInsertItem.length; i++)
		parameter_str += '&selectedInsertItem[]=' + selectedInsertItem[i];

	$.ajax({
		url: './insert_datamaintain.php?'+parameter_str,
		type: 'POST',
		async: false,
		success: function(msg){
			alert(msg);
		}
	});
}