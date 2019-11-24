<?php
 
	require ("../includes/db.php");
	require ("../includes/jsonheader.php");

	if ($_SERVER["REQUEST_METHOD"] == "GET") {

		if (isset($_GET["name"])) {

			$name = htmlspecialchars($_GET["name"]);

			$qry = $db->prepare("SELECT * FROM clients WHERE name = :name");
			$qry->bindValue(':name', $name, PDO::PARAM_STR);
			$qry->execute();
			$ans = $qry->fetchAll();
			$infos = array_map(function($dbentry) {

					return  array(
		    			'name'    => $dbentry['name'],
		            	'contact' => $dbentry['contact']); 
					}, $ans);

		    $qry2 = $db->prepare('SELECT * FROM projets WHERE proposedby = :name');
		    $qry2->bindValue(':name', $name, PDO::PARAM_STR);
		    $qry2->execute();
		    $ans2 = $qry2->fetchAll();
		    $projets = array_map(function($dbentry2) { 

		    	return array(
		    		'name'     => $dbentry2['name'],
		    		'employee' => $dbentry2['employee'],
		            'budget'   => $dbentry2['budget']); 
				}, $ans2);

		    echo json_encode(array('infos' => $infos, 'projet(s)' => $projets));

		} else {		

		    $qry = $db->query('SELECT * FROM clients');
		    $ans = $qry->fetchAll();
		    $clients = array_map(function($dbentry) { 

		    	return array( 

		    		$dbentry['name'],
		    		$dbentry['contact']); 
				}, $ans);

	    	echo json_encode(array('clients' => $clients));
		} 
		
	} elseif ($_SERVER["REQUEST_METHOD"] == "POST" ) {

		if (!empty($_POST["name"])) {

			if (!empty($_POST["contact"])) {

				$name     = htmlspecialchars($_POST["name"]);
				$contact  = htmlspecialchars($_POST["contact"]);

				$qry = $db->prepare("INSERT INTO clients (name, contact) VALUES (:name, :contact)");
				$qry->bindValue(':name', $name, PDO::PARAM_STR);
				$qry->bindValue(':contact', $contact, PDO::PARAM_STR);
				$qry->execute();
				echo json_encode(array('status' => 'Success : POST'));
					
			} else {

				echo json_encode(array('status' => 'Error : CONTACT value'));
			}			

		} else {
			
			echo json_encode(array('status' => 'Error : NAME value'));
		}

	} elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {	

		if (!empty($_GET["name"])) {

			$name = htmlspecialchars($_GET["name"]);			

			if (!empty($_GET["contact"])) {
				
				$contact = htmlspecialchars($_GET["contact"]);

				$qry = $db->prepare("UPDATE clients SET contact = :contact WHERE name = :name");
				$qry->bindValue(':name', $name, PDO::PARAM_STR);
				$qry->bindValue(':contact', $contact, PDO::PARAM_STR);
				$qry->execute();
				echo json_encode(array('status' => 'Success : PUT - contact'));

			} else {

				echo json_encode(array('status' => 'Error : PUT - contact undefined'));
			}		
			
		} else {

			echo json_encode(array('status' => 'Error : PUT - name undefined'));
		}

	} elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {

		if (!empty($_GET["name"])) {

			$name = $_GET["name"];

			$check = $db->prepare("SELECT * FROM clients WHERE name = :name");
			$check->bindValue(':name', $name, PDO::PARAM_STR);
			$check->execute();
			$resultCheck = $check->fetch();

			if (!$resultCheck) {
				
				echo json_encode(array('status' => 'Error : DELETE - Name doesn\'t match'));

			} else {

				$qry = $db->prepare("DELETE FROM clients WHERE name = :name");
				$qry->bindValue(':name', $name, PDO::PARAM_STR);
				$qry->execute();

				$qry2 = $db->prepare("DELETE FROM projets WHERE proposedby = :name");
				$qry2->bindValue(':name', $name, PDO::PARAM_STR);
				$qry2->execute();

				echo json_encode(array('status' => 'Sucess : DELETE'));
			}			

		}else {

			echo json_encode(array('status' => 'Error : DELETE - Missing infos'));
		}

	} else {

		echo json_encode(array('status' => 'Error : Missing Infos'));
	}
?>