const CBASIC_INFO = 'cbasic_info';
const CFINANCIAL_INFO = 'cfinancial_info';
const CFINANCIAL_INDEX = 'cfinancial_index';
const SECTOR_INFO = 'sector_info';
const GROUP_INFO = 'group_info';
const TOP100_DATA = 'top100_data';
const CHINA_CBASIC_INFO = 'china_cbasic_info';
const CHINA_CFINANCIAL_INFO = 'china_cfinancial_info';
const CRISIS_DATE = 'crisis_date';//where?

const SECTOR_GROUP_INFO = 'sector_group_info';//where

const UPDATE = 'update';
const INSERT = 'insert';
const MAXSTRINGLENGTH=20;
const YEAR=4;

var modifiedClass = CBASIC_INFO;
var insertClass = CBASIC_INFO;
var selectedModifyAction = UPDATE;

/*
	選擇修改或新增資料按鈕時觸發
	顯示對應的區塊要求使用者輸入資料
*/
$(document).ready(function(){
	//maintain_select_action：第一個panel（選擇新增／修改、要修改的資料）
	$('#maintain_selected_action').change(function(){
		selected_value = $("input[name='maintain_selected']:checked").val();
		// 修改
		if( selected_value == 'maintain_modify' ) {
			selectedModifyAction = UPDATE;
			document.getElementById("maintainform_modify").style.display = "block";//in data_maintain_page.php
			document.getElementById("maintainform_insert").style.display = "none";// in data_maintain_page.php
			document.getElementById("maintain_insert").style.display = "none";
			document.getElementById(selected_value).style.display = "block";//where?
		} // 新增
		else {
			selectedModifyAction = INSERT;
			document.getElementById("maintainform_modify").style.display = "none";
			document.getElementById("maintainform_insert").style.display = "block";
			document.getElementById("maintain_modify").style.display = "none";
			document.getElementById(selected_value).style.display = "block";
		}
		
	});
});

// 關掉上一個顯示選單
function disableLastBlock(e)
{
	// 若使用者在update 修改模式下
	if(selectedModifyAction==UPDATE) {
		switch(e)
		{
			case CBASIC_INFO:
			case CHINA_CBASIC_INFO:
				document.getElementById("cid_input").style.display = "none";
				document.getElementById(e).style.display = "none";
				break;
			case CFINANCIAL_INFO:
			case CHINA_CFINANCIAL_INFO:
			case CFINANCIAL_INDEX:
				document.getElementById("cid_input").style.display = "none";
				document.getElementById("season_input").style.display = "none";
				document.getElementById(e).style.display = "none";
				break;
			case CRISIS_DATE:
				document.getElementById("crisis_date_info").style.display = "none";
				document.getElementById("input_data").style.display = "block";
				break;
			case SECTOR_INFO:
				document.getElementById("sector_input").style.display = "none";
				document.getElementById("season_input").style.display = "none";
				document.getElementById(e).style.display = "none";
				break;
			case GROUP_INFO:					
				document.getElementById("group_input").style.display = "none";
				document.getElementById("season_input").style.display = "none";
				document.getElementById(e).style.display = "none";
				break;
			case TOP100_DATA:
				document.getElementById("top100_season_input").style.display = "none";
				document.getElementById(e).style.display = "none";
				break;
			default:
		}
	} else {
		if(e==CRISIS_DATE)
			document.getElementById("crisis_date_info_insert").style.display = "none";
		else
			document.getElementById(e+"_insert").style.display = "none";
	}//重覆block會有問題？
}

// 選單改變時觸發
function listchange(e)
{
	// 若使用者在update 修改模式下
	if(selectedModifyAction==UPDATE) {
		switch(e)
		{
			case CBASIC_INFO:
			case CHINA_CBASIC_INFO:
				disableLastBlock(modifiedClass);
				document.getElementById("cid_input").style.display = "block";
				modifiedClass = e;
				break;
			case CFINANCIAL_INFO:
			case CHINA_CFINANCIAL_INFO:
			case CFINANCIAL_INDEX:
				disableLastBlock(modifiedClass);
				document.getElementById("cid_input").style.display = "block";
				document.getElementById("season_input").style.display = "block";
				modifiedClass = e;
				break;
			case CRISIS_DATE:
				disableLastBlock(modifiedClass);
				addSelectCrisisCompany();
				document.getElementById("input_data").style.display = "none";
				document.getElementById("crisis_date_info").style.display = "block";
				modifiedClass = e;
				break;
			case SECTOR_INFO:
				disableLastBlock(modifiedClass);
				document.getElementById("sector_input").style.display = "block";
				document.getElementById("season_input").style.display = "block";
				modifiedClass = e;
				break;
			case GROUP_INFO:
				disableLastBlock(modifiedClass);
				document.getElementById("group_input").style.display = "block";
				document.getElementById("season_input").style.display = "block";
				modifiedClass = e;
				break;
			case TOP100_DATA:
				disableLastBlock(modifiedClass);
				document.getElementById("top100_season_input").style.display = "block";
				modifiedClass = e;
				break;
			default:
		}
	} else {
		disableLastBlock(insertClass);
		if(e==CRISIS_DATE)
			document.getElementById("crisis_date_info_insert").style.display = "block";
		else
			document.getElementById(e+"_insert").style.display = "block";
		insertClass = e;
	}
}