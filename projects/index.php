<?php
	
	require ("../includes/db.php");
	require ("../includes/jsonheader.php");

	if ($_SERVER["REQUEST_METHOD"] == "GET") {

		$case = "1";

	} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {	

		$case = "2";

	} elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {

		$case = "3";

	} elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {

		$case = "4";

	} else {

		echo json_encode(array('status' => 'Error : Missing Request Infos'));
	}

	switch ($case) {

		case '1':

			if (!empty($_GET["name"])) {

			    $qry = $db->prepare('SELECT * from projets WHERE name=?');
			    $qry->execute(array($_GET["name"]));
			    $ans = $qry->fetchAll();
			    $infos = array_map(function($dbentry) { 

			    	return array(
			    		'name'     	 => $dbentry['name'],
			            'proposedby' => $dbentry['proposedby'],
			            'employee'   => $dbentry['employee'],
			            'budget'  	 => $dbentry['budget']); 
					}, $ans);

			    echo json_encode(array('infos' => $infos));

			} else {		

			    $qry = $db->query('SELECT * FROM projets');
			    $ans = $qry->fetchAll();
			    $projets = array_map(function($dbentry) { 

			    	return array( 

			    		'name'     	 => $dbentry['name'],
			    		'proposedby' => $dbentry['proposedby'],
			    		'employee'   => $dbentry['employee'],
			    		'budget'  	 => $dbentry['budget']); 
					}, $ans);

		    	echo json_encode(array('projets' => $projets));
			} 

			break;

		case '2':

			if (empty($_POST["name"])) {
				echo json_encode(array('status' => 'Error : Name value is missing'));
			}

			if (empty($_POST["proposedby"])) {
				echo json_encode(array('status' => 'Error : Proposedby value is missing'));
			}

			if (empty($_POST["employee"])) {
				echo json_encode(array('status' => 'Error : Employee value is missing'));
			}

			if (empty($_POST["budget"])) {
				echo json_encode(array('status' => 'Error : Budget value is missing'));
			}

			if (!empty($_POST["name"]) && !empty($_POST["proposedby"]) && !empty($_POST["employee"]) && !empty($_POST["budget"])) {

				$name       = htmlspecialchars($_POST["name"]);
				$proposedby = htmlspecialchars($_POST["proposedby"]);
				$employee   = htmlspecialchars($_POST["employee"]);
				$budget     = htmlspecialchars($_POST["budget"]);

				$check = $db->prepare("SELECT * FROM projets WHERE name = :name");
				$check->bindValue(':name', $name, PDO::PARAM_STR);
				$check->execute();
				$resultCheck = $check->fetch();

				if ($resultCheck["name"] == $name) {

					echo json_encode(array('status' => 'Error : NAME already use !'));

				} else {

					$qry = $db->prepare("INSERT INTO projets (name, proposedby, employee, budget) VALUES (:name, :proposedby, :employee, :budget)");
					$qry->bindValue(':name', $name, PDO::PARAM_STR);
					$qry->bindValue(':proposedby', $proposedby, PDO::PARAM_STR);
					$qry->bindValue(':employee', $employee, PDO::PARAM_STR);
					$qry->bindValue(':budget', $budget, PDO::PARAM_STR);
					$qry->execute();	

					echo json_encode(array('status' => 'Success : POST'));
				}				

			} else {
				
				echo json_encode(array('status' => 'Error : Invalid parameters'));
			}

			break;

		case '3':

			if (!empty($_GET["name"])) {

				$name = $_GET["name"];

				if (!empty($_GET["proposedby"])     && !empty($_GET["employee"])  && !empty($_GET["budget"]))    { $case2 = "1"; } // ALL Parameters

				elseif (!empty($_GET["proposedby"]) && !empty($_GET["employee"])  && empty($_GET["budget"]))     { $case2 = "2"; } // Proposed, Employee / Budget

				elseif (!empty($_GET["proposedby"]) && !empty($_GET["budget"])    && empty($_GET["employee"]))   { $case2 = "3"; } // Proposed, Budget   / Employee

				elseif (!empty($_GET["employee"])   && !empty($_GET["budget"])    && empty($_GET["proposedby"])) { $case2 = "4"; } // Employee, Budget   / Proposed

				elseif (!empty($_GET["proposedby"]) && empty($_GET["employee"])   && empty($_GET["budget"]))     { $case2 = "5"; } // Proposed / Employee, Budget

				elseif (!empty($_GET["employee"])   && empty($_GET["proposedby"]) && empty($_GET["budget"])) 	 { $case2 = "6"; } // Employee / Proposed, Budget

				elseif (!empty($_GET["budget"])     && empty($_GET["proposedby"]) && empty($_GET["employee"]))   { $case2 = "7"; } // Budget,  / Proposed, Employee

				elseif (empty($_GET["proposedby"])  && empty($_GET["employee"])   && empty($_GET["budget"]))     { $case2 = "8"; } // ALL Empty

				else { }

				switch ($case2) {

					case '1':

						$employee = htmlspecialchars($_GET["employee"]);
						$proposedby = htmlspecialchars($_GET["proposedby"]);
						$budget = htmlspecialchars($_GET["budget"]);

						$qry = $db->prepare("UPDATE projets SET proposedby = :proposedby, employee = :employee, budget = :budget WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':employee', $employee, PDO::PARAM_STR);
						$qry->bindValue(':proposedby', $proposedby, PDO::PARAM_STR);
						$qry->bindValue(':budget', $budget, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - proposedby, employee, budget'));
						break;

					case '2':

						$employee = htmlspecialchars($_GET["employee"]);
						$proposedby = htmlspecialchars($_GET["proposedby"]);

						$qry = $db->prepare("UPDATE projets SET proposedby = :proposedby, employee = :employee WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':employee', $employee, PDO::PARAM_STR);
						$qry->bindValue(':proposedby', $proposedby, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - proposedby, employee'));
						break;

					case '3':

						$budget = htmlspecialchars($_GET["budget"]);
						$proposedby = htmlspecialchars($_GET["proposedby"]);

						$qry = $db->prepare("UPDATE projets SET proposedby = :proposedby, budget = :budget WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':budget', $budget, PDO::PARAM_STR);
						$qry->bindValue(':proposedby', $proposedby, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - proposedby, budget'));						
						break;

					case '4':

						$employee = htmlspecialchars($_GET["employee"]);
						$budget = htmlspecialchars($_GET["budget"]);

						$qry = $db->prepare("UPDATE projets SET budget = :budget, employee = :employee WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':employee', $employee, PDO::PARAM_STR);
						$qry->bindValue(':budget', $budget, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - budget, employee'));
						break;

					case '5':

						$proposedby = htmlspecialchars($_GET["proposedby"]);

						$qry = $db->prepare("UPDATE projets SET proposedby = :proposedby WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':proposedby', $proposedby, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - proposedby'));						
						break;

					case '6':

						$employee = htmlspecialchars($_GET["employee"]);

						$qry = $db->prepare("UPDATE projets SET employee = :employee WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':employee', $employee, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - employee'));
						break;

					case '7':

						$budget = htmlspecialchars($_GET["budget"]);

						$qry = $db->prepare("UPDATE projets SET budget = :budget WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':budget', $budget, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - budget'));						
						break;

					case '8':

						echo json_encode(array('status' => 'Error : PUT - Parameters are missing.'));
						break;
					
					default:

						echo json_encode(array('status' => 'Error : PUT - Parameters are missing.'));

					break;
				}

			} else {

				echo json_encode(array('status' => 'Error : PUT - Parameters are missing.'));
			}
			break;

		case '4':

			if (!empty($_GET["name"])) {

				$name = $_GET["name"];

				$check = $db->prepare("SELECT * FROM projets WHERE name = :name");
				$check->bindValue(':name', $name, PDO::PARAM_STR);
				$check->execute();
				$resultCheck = $check->fetch();
				
				$employee = $resultCheck["employee"];

				if (!$resultCheck) {
					
					echo json_encode(array('status' => 'Error : DELETE - Name doesn\'t match'));

				} else {

					$qry = $db->prepare("DELETE FROM projets WHERE name = :name");
					$qry->bindValue(':name', $name, PDO::PARAM_STR);
					$qry->execute();
					echo json_encode(array('status' => 'Sucess : DELETE'));
				}			

			} else {

				echo json_encode(array('status' => 'Error : DELETE - Missing parameters'));
			}
			
			break;
		
		default:

			echo json_encode(array('status' => 'Error : Missing Case Infos'));

		break;
	}

?>