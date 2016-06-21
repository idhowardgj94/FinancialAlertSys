/**
 * This javascript file is for data maintain upload page. made by NCU SML lab
 * 
 * latest update: 2016/4/26
 */
function checkSubmit() {
	var upload_year = document.getElementById("upload_year").value;
	if (upload_year != '')
		return confirm("確定上傳資料？");
	else {
		alert('必須輸入上傳年份');
		return false;
	}
}

function selectFinancialIndex(e) {
	if (e == 'cfinancial_index')
		document.getElementById('upload_file_more').style.display = 'block';
	else
		document.getElementById('upload_file_more').style.display = 'none';
}

/*
 * get Latest Season 讀取server時間判斷當前季別顯示在頁面上 1-3月 : 前一年Q4 selectedIndexindex = 3
 * 4-6月 : 當年Q1 selectedIndexindex = 0 7-9月 : 當年Q2 selectedIndexindex = 1 10-12月 :
 * 當年Q3 selectedIndexindex = 2
 */
function getLatestSeason() {

	$
			.ajax({
				type : 'POST',
				url : 'getServerTime.php',
				success : function(date) {
					var today = date.split("/");
					var year = today[0];
					var month = today[1];

					// 將月份轉成當前季別的index
					var season = Math.ceil(month / 3) - 1;

					// 換算成前一季度的index
					var season_selected_index = season - 1;
					if (season_selected_index < 0) { // 若index<0
														// 則預設的修改季別為前一年Q4
						year--;
						season_selected_index = 3;
					}

					// 修改頁面上顯示的季別
					document.getElementById("upload_year").value = year;
					document.uploadForm.upload_season.selectedIndex = season_selected_index;
				}
			});
}