const COMPANY_DIV_HEIGHT = 31;
const STRINGLENTH = 20;
$(function(){
	$("ul.navigation > li:has(ul) > a").append('<div class="arrow-bottom"></div>');
	$("ul.navigation > li ul li:has(ul) > a").append('<div class="arrow-right"></div>');
});


function searchCompanyValueatRisk(e, status){
	// 幫 a.abgne_gotoheader 加上 click 事件
	//$('a.abgne_gotoheader').click(function(){
		// offset 以px計 一間公司31px高
		// 取消表單提交 ex: submit
		e.preventDefault();
		// do somthing with inputs
		var searchString = document.getElementById('serchInput').value;


		searchString = Trim(searchString);
		//檢查長度
		if(lengthcheck(searchString, STRINGLENTH)){
			alert("您輸入的字串長度異常，請重新輸入！");
			document.getElementById('serchInput').value='';
			return false;
		}
		else if(isdigit(searchString)==false && isChinese(searchString)==false){
			alert("您輸入的搜尋異常！本搜尋功能只提供ID或公司名稱搜尋！");
			document.getElementById('serchInput').value='';
		if(searchString.length>20){
			alert("您輸入的字串長度異長，請重新輸入！");
			document.getElementById('searchInput').value='';
			return false;
		}
		else if(pattern_isdigit.test(searchString)==false && pattern_isChinese.test(searchString)==false){
			alert("您輸入的搜尋異常！本搜尋功能只提供ID或公司名稱搜尋！");
			document.getElementById('searchInput').value='';
			return false;
		}
		searchCompany(searchString, status);
		//var jumpindex = parseInt(index) * 31;
		//$('div.g_Body').scrollTop(jumpindex);
		
		return false;
	//});
}

function searchChinaCompanyValueatRisk(e) {
	// 取消表單提交 ex: submit
	e.preventDefault();
	// do somthing with inputs
	var searchString = document.getElementById('serchInput').value;
	searchString = Trim(searchString);
	searchChinaCompany(searchString);
	return false;
}

function changeColor() {
	document.getElementById("menu_risk_at_value").style.background = "#333";
}

function searchCompany(searchString, status) {
	$.ajax({
		url: './countCompanyindex.php?search=' + searchString + '&status=' + status,
		type: 'POST',
		async: false,
		success: function(cindex){
			var index = parseInt(cindex);
			// 同頁面會回傳 index
			if(index>=0) {
				var jumpindex = parseInt(cindex) * COMPANY_DIV_HEIGHT;
				$('div.g_Body').scrollTop(jumpindex);
			}
			// 不同頁面則告知頁面名及跳轉頁面
			else {
				switch(index) {
					case -1:
						alert(searchString+'公司在上市分類');
						document.location.href="listedtsec.php?search="+searchString;
						break;
					case -2:
						alert(searchString+'公司在上櫃分類');
						document.location.href="listedotcc.php?search="+searchString;
						break;
					case -3:
						alert(searchString+'公司在興櫃分類');
						document.location.href="listedces.php?search="+searchString;
						break;
					case -4:
						alert(searchString+'公司在公開發行分類');
						document.location.href="gopublicc.php?search="+searchString;
						break;
					case -5:
						alert(searchString+'公司在下市櫃分類');
						document.location.href="cdelisting.php?search="+searchString;
						break;
					default:
						alert(searchString+'公司不存在');
				}
			}
		}
	});
	//alert("end searchcompany222	");
}

function searchChinaCompany(searchString) {
	$.ajax({
		url: './countChinaCompanyindex.php?search=' + searchString,
		type: 'POST',
		async: false,
		success: function(cindex){
			var index = parseInt(cindex);
			if(index>=0) {
				var jumpindex = parseInt(cindex) * COMPANY_DIV_HEIGHT;
				$('div.g_Body').scrollTop(jumpindex);
			}
			else
				alert(searchString+'公司不存在');
		}
	});
}

