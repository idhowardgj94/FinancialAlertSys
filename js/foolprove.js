/**
 * 有關防呆需要的判斷函式
 */
// http://whereiswelly.tw/?p=407
function Trim(InputString)
{
  //去除字串左右兩邊的空白
  return InputString.replace(/^\s+|\s+$/g, "");
 
  //使用方式為replace(要被換掉的字串,要取代的字串)
  //這裡面我們搭配使用了Regular Expression的方式 /搜尋的字串/g 來搜尋"要被換掉的字串"
}
function isdigit(InputString){
	var pattern_isdigit =/^[0-9]+$/; //是否全部數字的正則式
	return pattern_isdigit.test(searchString);
}
function isChinese(InputString){
	var pattern_isChinese =/^[\u4e00-\u9fa5]+$/;//只能是漢字
	return  pattern_isChinese.test(searchString);
}