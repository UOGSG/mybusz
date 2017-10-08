<?php
	include 'database.php';

	$bus_stop_id = $_POST['bus_stop_id'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	//$stmt = $mysqli->prepare("SELECT br.route_id, br.bus_service_no, GROUP_CONCAT(e.eta) as eta FROM (SELECT * FROM (SELECT * FROM eta WHERE bus_stop_id = ? ORDER BY time DESC) as arrival_time GROUP BY bus_id) e, bus_route br WHERE br.bus_id = e.bus_id AND br.route_id = e.route_id AND e.eta > ? AND time > (SELECT MAX(time) - INTERVAL 30 SECOND FROM eta) GROUP BY br.route_id");
	// $stmt = $mysqli->prepare("SELECT r.route_id, r.bus_service_no, GROUP_CONCAT(e.eta) as eta FROM (SELECT * FROM (SELECT * FROM eta WHERE bus_stop_id = ? ORDER BY time DESC) as arrival_time GROUP BY bus_id) e, route r WHERE r.route_id = e.route_id AND e.eta > ? GROUP BY r.route_id");
	//$stmt->bind_param("is", $bus_stop_id, getTime());
	//$stmt->execute();

	//$result = $stmt->get_result();

	//$arr = array();
	//$currentTime = round(microtime(true));
	// echo $currentTime;

	//while ($obj = $result->fetch_array(MYSQLI_ASSOC)) {
	//	$obj['eta'] = processEta($currentTime, $obj['eta']);
	//	array_push($arr, $obj);
	//}

	//$stmt->close();

	//$stmt1 = $mysqli->prepare("SELECT br.route_id, br.bus_service_no, GROUP_CONCAT(e.eta) as eta FROM (SELECT * FROM (SELECT * FROM etav2 WHERE bus_stop_id = ? ORDER BY time DESC) as arrival_time GROUP BY bus_id) e, bus_route br WHERE br.bus_id = e.bus_id AND br.route_id = e.route_id AND e.eta > ? AND time > (SELECT MAX(time) - INTERVAL 30 SECOND FROM etav2) GROUP BY br.route_id ORDER BY eta");
	// $stmt = $mysqli->prepare("SELECT r.route_id, r.bus_service_no, GROUP_CONCAT(e.eta) as eta FROM (SELECT * FROM (SELECT * FROM eta WHERE bus_stop_id = ? ORDER BY time DESC) as arrival_time GROUP BY bus_id) e, route r WHERE r.route_id = e.route_id AND e.eta > ? GROUP BY r.route_id");
	$stmt1 = $mysqli->prepare("SELECT e.route_id, br.bus_service_no, GROUP_CONCAT(DISTINCT e.eta) AS eta FROM etav2 e, bus_route br WHERE e.bus_stop_id =? AND e.eta > ? AND e.time > (SELECT MAX( time ) - INTERVAL 30 SECOND FROM etav2 v WHERE v.bus_id = e.bus_id AND v.route_id = e.route_id ) GROUP BY e.route_id, br.bus_service_no ORDER BY eta DESC" );

	$stmt1->bind_param("is", $bus_stop_id, getTime());
	$stmt1->execute();

	$result1 = $stmt1->get_result();

	$arr = array();
	$currentTime1 = round(microtime(true));
	// echo $currentTime;

	while ($obj1 = $result1->fetch_array(MYSQLI_ASSOC)) {
		$obj1['eta'] = processEta($currentTime1, $obj1['eta']);
		array_push($arr, $obj1);
	}

	$stmt1->close();
	$mysqli->close();


	if($arr!=NULL)
		echo json_encode($arr);
	else
		response("400", "No bus service found");



	function processEta($t1, $etas) {
		$etaList = explode(",", $etas);

		for ($i = 0; $i < count($etaList); $i++) {
			// $etaList[$i] = getRelativeTime($t1, strtotime($etaList[$i]));
			$etaList[$i] = array(
			    "time" => $etaList[$i],
			    "relative_time" => getRelativeTime($t1, strtotime($etaList[$i]))
			);
		}

		return $etaList;
	}

	function getRelativeTime($t1, $t2) {
		$timediff = round(($t2-$t1)/60);

		return $timediff;
	}
?>
