<?php session_start(); ?>

<meta http-equiv="Content-Type" content="test/html"; charset="utf-8" />
<?php 
function check_input($str){
	//如果輸入不是數字，則處理成合法字串存入globals['dbn']
	if (get_magic_quotes_gpc()){
		//檢查溢出字元有沒有加反鈄線
		$str = stripslashes($str);
		//將溢出字元的反斜線去掉
	}
	
	if(!is_numeric($str)){
	
	//不是數字的話，則加上單引號
		$str = "'" . mysqli_real_escape_string($GLOBALS['dbn'], $str) . "'";
	}
	return $str;
}

include("mysqlconnect.php");
//引入此php檔

//get user input username and password
$username = check_input($_POST['id']);
$pw = check_input($_POST['pw']);

//check if id and pw is blink or not
if($username !== null && $pw !==null){
	/*在php之中，由於沒有強調變數型態，所以如果使用== or !=
	 * 比較時將不會考慮其型態，亦即，0==null為ture
	 * 若型態亦要考慮，則需使用=== or !==*/
	$sqlstr = "SELECT * FROM member_table WHERE username = " .$username. " AND pw =" .$pw. "";
	$result = $dbn->query($sqlstr);
}else{
	echo '使用者未輸入帳號密碼';
	echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
}

//check MySQL資料庫裡有無使用者帳戶
if(!empty($result) AND $user_account=mysqli_fetch_row($result)){
	$_SESSION['username'] = $username;
	//input username to session to identify user
	
	echo '登入成功';
	echo '<meta http-equiv=REFRESH CONTENT=1;url=index.php>';
	//echo '<meta http-equiv=REFRESH CONTENT=1;url=index.php>';
	//return to index( index will show butten)
}
else{
	echo '登入失敗';
	echo '<meta http-equiv=REFRESH CONTENT=1; url=login.php>';
}
?>