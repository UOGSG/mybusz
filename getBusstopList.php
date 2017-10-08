<?php
	include 'database.php';

	$route = $_POST["route"];
	$bus_id = $_POST["bus_id"];
		
	//echo "<h1>Hello " . $_GET["route"] . "</h1>";

	$mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$stmt = $mysqli->prepare("SELECT a.latitude, a.longitude FROM location_datav2 a WHERE a.route_id =? AND a.bus_id =? AND a.time > (? - INTERVAL 3600 SECOND) AND a.time = ( SELECT MAX( v.time ) FROM location_datav2 v WHERE v.route_id =? AND v.bus_id =? )"); 
	//$stmt = $mysqli->prepare("SELECT b.bus_stop_id, b.name, b.latitude, b.longitude, 0 AS Distance FROM bus_stop b, route_bus_stop r WHERE b.bus_stop_id = r.bus_stop_id and r.route_id = ? "); 
		
	$stmt->bind_param("iisii", $route, $bus_id, getTime(), $route, $bus_id);
	$stmt->execute();

	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$lat = $row["latitude"];
			$long = $row["longitude"];
		}
		$stmt->close();
		
		//now get the nearest bus stop from the latest location 
		$stmt = $mysqli->prepare("SELECT b.bus_stop_id , ( 6371 * acos( cos( radians(?) ) * cos( radians( b.latitude ) ) * cos( radians( b.longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( b.latitude ) ) ) ) AS distance FROM bus_stop b, route_bus_stop r WHERE b.bus_stop_id = r.bus_stop_id AND r.route_id =? HAVING distance < 0.5 ORDER BY distance LIMIT 1");
		
		$stmt->bind_param("sssi", $lat, $long, $lat, $route);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		if ($result->num_rows > 0) {			
			while($row = $result->fetch_assoc()) {
				$bus_stop_id = $row["bus_stop_id"];					
			}		
		}
		else	
			$bus_stop_id = 0;		
		
		$stmt->close();	
	}
	else {
		$bus_stop_id = 0;
		$stmt->close();
	}
		
	//now get the list of bus stop
	$stmt = $mysqli->prepare("SELECT b.bus_stop_id, b.name, b.latitude, b.longitude, 0 AS Distance FROM bus_stop b, route_bus_stop r WHERE b.bus_stop_id = r.bus_stop_id and r.route_id = ? AND b.bus_stop_id > ?"); 
	$stmt->bind_param("ii", $route, $bus_stop_id);
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
		response("400", "No Bus stop list found");	
?>