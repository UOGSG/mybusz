<?php
	include 'database.php';

	$lat = $_POST['lat'];
	$lng = $_POST['lng'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	//$stmt = $mysqli->prepare("SELECT *, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance FROM bus_stop");
	$stmt = $mysqli->prepare("SELECT *, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance FROM bus_stop HAVING distance < 1 ORDER BY distance");
	$stmt->bind_param("sss", $lat, $lng, $lat);
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
		response("400", "No nearby bus stop found");
?>