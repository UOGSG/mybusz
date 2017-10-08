<?php
	include 'database.php';

	$route_id = $_POST['route_id'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT bs.bus_stop_id, bs.name, bs.latitude, bs.longitude FROM bus_stop bs, route r, route_bus_stop rbs WHERE bs.bus_stop_id = rbs.bus_stop_id AND r.route_id = rbs.route_id AND r.route_id = ?");
	$stmt->bind_param("i", $route_id);
	$stmt->execute();

	$result = $stmt->get_result();

	$arr = array();
	while ($obj = $result->fetch_object()) {
		array_push($arr, $obj);
	}

	$stmt->close();
	$mysqli->close();

	// $arr = array ("busStopList" => $arr);

	if($arr!=NULL)
		echo json_encode($arr);
	else
		response("400", "No bus stop found");
?>