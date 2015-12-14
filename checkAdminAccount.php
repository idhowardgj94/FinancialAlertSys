<?php
include ("mysqlconnect.php");
function isAdmin($username) {
	$sqlstr = 'SELECT `group` FROM `member_table` WHERE `username` = ' . $username . ' ';
	$result = $GLOBALS ['dbn']->query ( $sqlstr );
	
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