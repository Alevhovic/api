<?php

	try {

	    $db = new PDO('mysql:host=localhost;dbname=datatest;charset=utf8', 'root', '');

	} catch (PDOException $e) {

	    print "Erreur !: " . $e->getMessage() . "<br/>";
	    die();

	}

?>