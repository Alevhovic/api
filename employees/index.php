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
				
				$name = $_GET["name"];

				$qry = $db->prepare("SELECT * FROM employees WHERE name = :name");
				$qry->bindValue(':name', $name, PDO::PARAM_STR);
				$qry->execute();
				$ans = $qry->fetchAll();

		    	$infos = array_map(function($dbentry) { 

		    	return array(
		    		'name'     => $dbentry['name'],
		            'salary' => $dbentry['salary'],
		            'seniority'  => $dbentry['seniority']); 
				}, $ans);

				$qry2 = $db->prepare("SELECT name FROM projets WHERE employee = :employeeName");
				$qry2->bindValue(':employeeName', $name, PDO::PARAM_STR);
				$qry2->execute();
				$ans2 = $qry2->fetchAll();

		    	$projets = array_map(function($dbentry2) { 

		    	return array(
		    		'name'     => $dbentry2['name']); 
				}, $ans2);

		    	echo json_encode(array('infos' => $infos, 'projets' => $projets));

			} else {

				$qry = $db->prepare("SELECT * FROM employees");
				$qry->execute();
				$ans = $qry->fetchAll();
		    	$infos = array_map(function($dbentry) { 

		    	return array(
		    		'name'     => $dbentry['name'],
		            'salary' => $dbentry['salary'],
		            'seniority'  => $dbentry['seniority']); 
				}, $ans);

		    	echo json_encode(array('infos' => $infos));
			}

			break;

		case '2':

			if (empty($_POST["name"])) {
				echo json_encode(array('status' => 'Error : Name value is missing'));
			}

			if (empty($_POST["salary"])) {
				echo json_encode(array('status' => 'Error : Salary value is missing'));
			}

			if (empty($_POST["seniority"])) {
				echo json_encode(array('status' => 'Error : Seniority value is missing'));
			}

			if (empty($_POST)) {
				echo json_encode(array('status' => 'Error : Invalid Formulary'));
			}
			
			if (!empty($_POST["name"]) && !empty($_POST["salary"]) && !empty($_POST["seniority"])) {

				$name        = htmlspecialchars($_POST["name"]);
				$salary      = htmlspecialchars($_POST["salary"]);
				$seniority   = htmlspecialchars($_POST["seniority"]);

				$check = $db->prepare("SELECT * FROM employees WHERE name = :name");
				$check->bindValue(':name', $name, PDO::PARAM_STR);
				$check->execute();
				$resultCheck = $check->fetch();

				if ($resultCheck["name"] == $name) {

					echo json_encode(array('status' => 'Error : NAME already use !'));

				} else {

					$qry = $db->prepare("INSERT INTO employees (name, salary, seniority) VALUES (:name, :salary, :seniority)");

					$qry->bindValue(':name', $name, PDO::PARAM_STR);
					$qry->bindValue(':salary', $salary, PDO::PARAM_STR);
					$qry->bindValue(':seniority', $seniority, PDO::PARAM_STR);

					$qry->execute();

					echo json_encode(array('status' => 'Success : POST'));
				}				

			} else {
				
				echo json_encode(array('status' => 'Error : Invalid parameters'));
			}
			break;

		case '3':
			
			if (!empty($_GET["name"])) {

				$name = htmlspecialchars($_GET["name"]);

				if (!empty($_GET["salary"]) && !empty($_GET["seniority"])) { $case2 = "1"; } // ALL Parameters Complete					

				elseif (!empty($_GET["salary"]) && empty($_GET["seniority"])) { $case2 = "2"; } // Salary BUT NOT Seniority				

				elseif (!empty($_GET["seniority"]) && empty($_GET["salary"])) { $case2 = "3"; } // Seniority BUT NOT Salary

				elseif (empty($_GET["seniority"]) && empty($_GET["salary"])) { $case2 = "4"; } // ALL Empty

				else { echo json_encode(array('status' => 'Error : Parameters does not exist')); }

				switch ($case2) {
					
					case '1':

						$salary = htmlspecialchars($_GET["salary"]);
						$seniority = htmlspecialchars($_GET["seniority"]);

						$qry = $db->prepare("UPDATE employees SET salary = :salary, seniority = :seniority WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':salary', $salary, PDO::PARAM_STR);
						$qry->bindValue(':seniority', $seniority, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - salary, seniority'));
						break;

					case '2':

						$salary = htmlspecialchars($_GET["salary"]);

						$qry = $db->prepare("UPDATE employees SET salary = :salary WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':salary', $salary, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - salary'));
						break;

					case '3':
						
						$seniority = htmlspecialchars($_GET["seniority"]);

						$qry = $db->prepare("UPDATE employees SET seniority = :seniority WHERE name = :name");
						$qry->bindValue(':name', $name, PDO::PARAM_STR);
						$qry->bindValue(':seniority', $seniority, PDO::PARAM_STR);
						$qry->execute();
						echo json_encode(array('status' => 'Success : PUT - seniority'));
						break;

					case '4':

						echo json_encode(array('status' => 'Error : No parameters'));
						break;
					
					default:

						echo json_encode(array('status' => 'Error : No Case define'));

					break;
				}

			} else {

				echo json_encode(array('status' => 'Error : Invalid parameters'));
			}

			break;

		case '4':

			if (!empty($_GET["name"])) {

				$name = $_GET["name"];

				$check = $db->prepare("SELECT * FROM employees WHERE name = :name");
				$check->bindValue(':name', $name, PDO::PARAM_STR);
				$check->execute();
				$resultCheck = $check->fetch();

				if (!$resultCheck) {
					
					echo json_encode(array('status' => 'Error : DELETE - Name doesn\'t match'));

				} else {

					$qry = $db->prepare("DELETE FROM employees WHERE name = :name");
					$qry->bindValue(':name', $name, PDO::PARAM_STR);
					$qry->execute();

					$qry2 = $db->prepare("DELETE FROM projets WHERE employee = :name");
					$qry2->bindValue(':name', $name, PDO::PARAM_STR);
					$qry2->execute();

					echo json_encode(array('status' => 'Sucess : DELETE'));
				}			

			}else {

				echo json_encode(array('status' => 'Error : DELETE - Missing infos'));
			}
			break;		
		
		default:
			# code...
			break;
	}

?>