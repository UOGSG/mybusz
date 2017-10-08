<?php
	include 'database.php';


	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT * FROM `location_data` WHERE time > '2015-09-11' and time < '2015-09-13'");
	// $stmt->bind_param("ss", $imei, $date);
	$stmt->execute();

	$result = $stmt->get_result();

	$arr = array();
	while ($obj = $result->fetch_object()) {
		array_push($arr, $obj);
	}

	$stmt->close();
	$mysqli->close();

	if($arr!=NULL)
		echo json_encode($arr);
	else
		response("400", "No location data found");
?>
