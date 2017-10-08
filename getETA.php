<?php
	include 'database.php';

	$bus_stop_id = $_POST['bus_stop_id'];
	$bus_id = $_POST['bus_id'];
	$route_id = $_POST['route_id'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT b.route_id, b.bus_service_no, eta FROM etav2 e, bus_route b WHERE b.bus_id = e.bus_id AND b.route_id = e.route_id AND e.bus_id = ? AND e.route_id = ? AND bus_stop_id = ? AND e.eta > ? AND e.time = (SELECT MAX( t.time ) FROM etav2 t WHERE t.bus_id = ? AND t.route_id = ? ) ORDER BY e.time DESC");	
	// $stmt = $mysqli->prepare("SELECT r.route_id, r.bus_service_no, GROUP_CONCAT(e.eta) as eta FROM (SELECT * FROM (SELECT * FROM eta WHERE bus_stop_id = ? ORDER BY time DESC) as arrival_time GROUP BY bus_id) e, route r WHERE r.route_id = e.route_id AND e.eta > ? GROUP BY r.route_id");
	$stmt->bind_param("iiisii", $bus_id, $route_id, $bus_stop_id, getTime(), $bus_id, $route_id);
	$stmt->execute();

	$result = $stmt->get_result();

	$arr = array();
	$currentTime = round(microtime(true));
	// echo $currentTime;

	while ($obj = $result->fetch_array(MYSQLI_ASSOC)) {
		$obj['eta'] = processEta($currentTime, $obj['eta']);
		array_push($arr, $obj);
	}

	$stmt->close();
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

		return $timediff."m";
	}
?>
