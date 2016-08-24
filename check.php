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
	

	return $str;
}

include("db_controller_unit.php");
$dbc_object = new db_controller_unit;
//引入此php檔

//get user input username and password
$username = check_input($_POST['id']);
$pw = check_input($_POST['pw']);
$result;
$dbn=null;
//check if id and pw is blink or not
if($username !== null && $pw !==null){
	/*在php之中，由於沒有強調變數型態，所以如果使用== or !=
	 * 比較時將不會考慮其型態，亦即，0==null為ture
	 * 若型態亦要考慮，則需使用=== or !==*/
	$dbn=null;
	$tablename="member_table";
	$AttributeArray[]="*";
	$condition=[];
	array_push($condition, "username", $username, "pw", $pw);
	$result=$dbc_object->getDatawithCondition($dbn, $tablename, $AttributeArray, $condition);
	
	//$sqlstr = "SELECT * FROM member_table WHERE username = " .$username. " AND pw =" .$pw. "";
	//$result = $dbn->query($sqlstr);
}else{
	echo '使用者未輸入帳號密碼';
	echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
}

//check MySQL資料庫裡有無使用者帳戶
if(!empty($result) AND $user_account=mysqli_fetch_row($result)){
	$_SESSION['username'] = $username;
	//input username to session to identify user
	
	echo '登入成功';
	$dbc_object->closeDB($dbn);
	echo '<meta http-equiv=REFRESH CONTENT=1;url=index.php>';
	//echo '<meta http-equiv=REFRESH CONTENT=1;url=index.php>';
	//return to index( index will show butten)
}
else{
	echo '登入失敗';
	//echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
}
?>