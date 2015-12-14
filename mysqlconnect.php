<?php

$dbn = new mysqli(
		'localhost',
		'root',
		'1234',
		'financial_schema'
		);
$dbn->set_charset('utf-8');
// Check connection
/*if ($dbn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";*/
?>
