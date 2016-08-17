/**
 * 此javascript必須與foolprove一同被載入
 */
const FINANCIAL_INDEX_LINK = "cfinancialIndex";
const STRINGLENTH = 20;
function changeColor() {
	document.getElementById("menu_financialIndex").style.background = "#333";
}
//http://whereiswelly.tw/?p=407
/*function Trim(InputString)
{
  //去除字串左右兩邊的空白
  return InputString.replace(/^\s+|\s+$/g, "");
 
  //使用方式為replace(要被換掉的字串,要取代的字串)
  //這裡面我們搭配使用了Regular Expression的方式 /搜尋的字串/g 來搜尋"要被換掉的字串"
}*/
//search 輸入字串的公司是否存有資料後 跳轉頁面
function jumpPage(cid) {
	$.ajax({
		url: './returnsql_companyId.php?input=' + cid,
		type: 'POST',
		async: false,
		success: function(msg){
			if(msg)
				window.open(FINANCIAL_INDEX_LINK+".php?cid="+msg ,'_self');
			else
				alert(cid+'公司資料不存在');
		}
	});
}

function searchCompanyFiancialIndex(e){
	// serchInput 當作變數丟到 jumpPage
	alert('hello, ');
	// 取消表單提交 ex: submit
	e.preventDefault();
	// do somthing with inputs
	var status = 0;
	var cid = Trim(document.getElementById('serchInput').value);
	if(cid==''){
		cid = Trim(document.getElementById('searchInputAdv').value);
		status++;
	}
	alert(cid);
	//var pattern_isdigit =/^[0-9]*$/; //是否全部數字的正則式
	//var pattern_isChinese =/^[\u4e00-\u9fa5]+$/;//只能是漢字
	if(cid.length>STRINGLENTH){
		alert("您輸入的字串長度異常，請重新輸入！");
		if(status==0)
			document.getElementById('serchInput').value='';
		else
			document.getElementById('searchInputAdv').value='';
		return false;
	}
	else if(isdigit(cid)==false && isChinese(cid)==false){
		alert("您輸入的搜尋異常！本搜尋功能只提供ID或公司名稱搜尋！");
		if(status==0)
			document.getElementById('serchInput').value='';
		else
			document.getElementById('searchInputAdv').value='';
		return false;
	}
	jumpPage(cid);

	return false;
}

//對應下拉式選單選擇的status產生正確的公司選單
function buildCompanyList(index) {
	$.ajax({
		url: './returnsql_companylist.php?status=' + index,
		type: 'POST',
		async: false,
		success: function(msg){
		
		var ctr=1;
		var cnamelist = [];
		document.CompanyListForm.CompanyList.selectedIndex=0; 
		document.CompanyListForm.CompanyList.options[0]=new Option("請選擇公司...","");
		
		var com_list = $.parseJSON(msg);
		
		for (var i=0;i<com_list[0].length;i++) {
			cnamelist.push(com_list[0][i] + " " + com_list[1][i]);
		}
		
		// 產生公司選單
		for(i = 0; i< cnamelist.length; i++) {
			document.CompanyListForm.CompanyList.options[ctr]=new Option(cnamelist[i],FINANCIAL_INDEX_LINK+".php?cid="+com_list[0][i]);
			ctr=ctr+1;
		}
		}
	});

}



// http://whereiswelly.tw/?p=407
function Trim(InputString)
{
  //去除字串左右兩邊的空白
  return InputString.replace(/^\s+|\s+$/g, "");
 
  //使用方式為replace(要被換掉的字串,要取代的字串)
  //這裡面我們搭配使用了Regular Expression的方式 /搜尋的字串/g 來搜尋"要被換掉的字串"
}

// search 輸入字串的公司是否存有資料後 跳轉頁面
function jumpPage(cid) {
	$.ajax({
		url: './returnsql_companyId.php?input=' + cid,
		type: 'POST',
		async: false,
		success: function(msg){
			if(msg)
				window.open(FINANCIAL_INDEX_LINK+".php?cid="+msg ,'_self');
			else
				alert(cid+'公司資料不存在');
		}
	});
}