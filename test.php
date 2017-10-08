<?php
	include 'database.php';

	$bus_id = 1;
	$route_id = 1;
	$imei = "1234568758";
	$latitude = "1.59261";
	$longitude = "103.64709";
	$speed = "50";

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("INSERT INTO location_data(bus_id, route_id, imei, latitude, longitude, speed, time) VALUES(?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iisssss", $bus_id, $route_id, $imei, $latitude, $longitude, $speed, getTime());
	$result = $stmt->execute();
	$stmt->close();
	$mysqli->close();

	if ($result)
		response("200", "Location data updated");
	else
		response("400", "Unable to update location data");

	function response($code, $message) {
		$response = array(
			'code' => $code,
			'msg' => $message
			);

		echo json_encode($response);
	}
?>