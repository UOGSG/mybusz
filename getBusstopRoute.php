<?php
	include 'database.php';

	$route = $_POST["route"];	
		
	//echo "<h1>Hello " . $_GET["route"] . "</h1>";

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT b.bus_stop_id, b.name, b.latitude, b.longitude, 0 AS Distance FROM bus_stop b, route_bus_stop r WHERE b.bus_stop_id = r.bus_stop_id and r.route_id = ? "); 
	
	$stmt->bind_param("i", $route);
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