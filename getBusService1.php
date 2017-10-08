<?php
	include 'database.php';

	$bus_stop_id = $_POST['bus_stop_id'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT r.route_id, r.bus_service_no, GROUP_CONCAT(e.eta) as eta FROM (SELECT route_id, MAX(eta) as eta FROM eta WHERE bus_stop_id = ? GROUP BY bus_id ORDER BY eta DESC) e, route r WHERE r.route_id = e.route_id AND e.eta > ? GROUP BY r.route_id");
	$stmt->bind_param("is", $bus_stop_id, getTime());
	// $stmt = $mysqli->prepare("SELECT r.route_id, e.bus_id, bs.bus_stop_id, r.bus_service_no, e.eta FROM route r, bus_stop bs, route_bus_stop rbs, eta e WHERE r.route_id = rbs.route_id AND bs.bus_stop_id = rbs.bus_stop_id AND e.bus_stop_id = bs.bus_stop_id AND bs.bus_stop_id = ? GROUP BY bus_id ORDER BY eta DESC");
	// $stmt->bind_param("i", $bus_stop_id);
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

	if($arr!=NULL)
		echo json_encode($arr);
	else
		response("400", "No bus service found");


	function processEta($t1, $etas) {
		$etaList = explode(",", $etas);

		for ($i = 0; $i < count($etaList); $i++) {
			$etaList[$i] = getRelativeTime($t1, strtotime($etaList[$i]));
		}

		return $etaList;
	}

	function getRelativeTime($t1, $t2) {
		$timediff = round(($t2-$t1)/60);

		return $timediff."m";
	}
?>