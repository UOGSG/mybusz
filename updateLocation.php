<?php
	include 'database.php';

	$bus_id = $_POST['bus_id'];
	$route_id = $_POST['route_id'];
	$imei = $_POST['imei'];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$speed = $_POST['speed'];

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
?>