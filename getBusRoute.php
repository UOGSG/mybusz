<?php
	include 'database.php';

	$route_id = $_POST['route_id'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT * FROM route WHERE route_id = ?");
	$stmt->bind_param("i", $route_id);
	$stmt->execute();

	$arr = array();

	$result = $stmt->get_result();

	while ($obj = $result->fetch_object()) {
		array_push($arr, $obj);
	}

	$stmt->close();
	$mysqli->close();
	// $arr = array ("busStopList" => $arr);

	if($arr!=NULL)
		echo json_encode($arr[0]);
	else
		response("400", "No bus route found");
?>