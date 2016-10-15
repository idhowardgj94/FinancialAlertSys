<?php
include ("db_controller_unit.php");
$dbc_object = new db_controller_unit;
function isAdmin($username) {
	global $dbc_object;
	$tablename=array("member_table");
	$condition=array("username", $username);
	//$sqlstr = 'SELECT `group` FROM `member_table` WHERE `username` = ' . $username . ' ';
	//$result = $GLOBALS ['dbn']->query ( $sqlstr );
	$result=$dbc_object->getDatawithCondition($dbn, $tablename, "*", $condition);
	$dbc_object->closeDB($dbn);
	if (! empty ( $result ) and $user_group = mysqli_fetch_row ( $result )) {
		if ($user_group [0]) // admin 帳號
			return true;
		else
			return false;
	} else {
		return false;
	}
}
?>