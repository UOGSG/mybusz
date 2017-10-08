<?php
	include 'database.php';

	    $mysqli = new mysqli(DBSERVER, DBUSER, DBPASSWORD, DBNAME);

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    
    print("Total Records : ");
    $stmt->close();
	$mysqli->close();
?>