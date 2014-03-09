<?php 
require_once "index.php";

function newFingerprints() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$roomId = $array ["room_id"];
	$fingerprints = $array ["fingerprints"];

	//
	$pdo = getDatabase ();
	
	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}
	
	// Is admin?
	if (! isAdmin ( $pdo, $userId )) {
		echo error ( ERROR_CODE_UNAUTHORIZED_ACCESS, ERROR_MSG_UNAUTHORIZED_ACCESS );
		return;
	}

	$errorCount = 0;

	// Assemble insert statements
	foreach($fingerprints as $fingerprint) {
		// Example	
		// INSERT INTO redirecto_fingerprint (ap_anton, ap_bashawell, room_id) 
		// VALUES (50, 50, 25);
		$sql = "INSERT INTO redirecto_fingerprint (";

		// Assemble column names
		for($i = 0; $i < count($fingerprint); $i++) {
			$ap = $fingerprint[$i];
			if(isBssidAccepted($ap["bssid"])) {
				$sql = $sql . "`ap_" . $ap["bssid"] . "`,"; 
			}
		}

		$sql = $sql . "room_id) VALUES (";

		// Assemble column values
		for($i = 0; $i < count($fingerprint); $i++) {
			$ap = $fingerprint[$i];
			if(isBssidAccepted($ap["bssid"])) {
				$sql = $sql . $ap["rssi"] . ","; 
			}
		}

		$sql = $sql . "$roomId);";


		// Execute query
		$statement = $pdo->prepare ( $sql );
		$statement->execute ();
		if ($statement->rowCount () <= 0) {
			$errorCount++;
		}
	}

	if($errorCount > 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( true );
	}
}
 ?>