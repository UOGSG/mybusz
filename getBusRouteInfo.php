<?php
	include 'database.php';

	$bus_id = $_POST['bus_id'];
	$bus_no = $_POST['bus_service_no'];

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT r.route_id FROM bus b, route r, bus_route br WHERE br.bus_id = b.bus_id AND br.route_id = r.route_id AND b.bus_id = ? AND br.bus_service_no = ?");
	$stmt->bind_param("is", $bus_id, $bus_no);
	$stmt->execute();

	$result = $stmt->get_result();

	$arr = array();

	while ($row = $result->fetch_array(MYSQLI_NUM)) {
		foreach ($row as $r) {
			$stmt = $mysqli->prepare("SELECT r.route_id, bs.name FROM route r, bus_stop bs, route_bus_stop rbs WHERE rbs.route_id = r.route_id AND rbs.bus_stop_id = bs.bus_stop_id AND r.route_id = ? ORDER BY bs.bus_stop_id DESC LIMIT 1");
			$stmt->bind_param("i", $r);
			$stmt->execute();

			$result2 = $stmt->get_result();

			while ($obj = $result2->fetch_object()) {
				array_push($arr, $obj);
			}
		}
	}

	$stmt->close();
	$mysqli->close();

	if($arr!=NULL)
		echo json_encode($arr);
	else
		response("400", "No bus route found");
?>