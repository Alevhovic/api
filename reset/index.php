<?php

	require ("../includes/db.php");
	require ("../includes/jsonheader.php");

    $db->exec("TRUNCATE TABLE clients");
	$db->exec("TRUNCATE TABLE employees");
	$db->exec("TRUNCATE TABLE projets");

	echo json_encode(array('status' => 'API : RESET Ok !'));

?>