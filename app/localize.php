<?php 
require_once "index.php";

function localize() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$lastRoomId = $array ["last_room_id"];
	$fingerprint = $array ["fingerprint"];

	//
	$pdo = getDatabase();

	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}

	// Prefill array with defaults
	$apValuesArray = array();
	global $ACCEPTED_SSIDs;
	foreach ($ACCEPTED_SSIDs as $ssid) {
		$apValuesArray[$ssid] = MAX_RSSI;
	}

	// Override accepted SSIDs with measured values
	foreach($fingerprint as $ap) {
		if(isSsidAccepted($ap["ssid"])) {
			$apValuesArray[strtolower($ap["ssid"])] = $ap["rssi"];
		}
	}

	// Assemble select clause
	$sql = "SELECT id, room_id, sqrt(";

	$index = 0;
	foreach ($apValuesArray as $ssid => $rssi) {
		$sql = $sql . "power(abs({$rssi}-ap_{$ssid}),2)";
		if($index++ < count($apValuesArray) - 1) {
			$sql = $sql . "+\n";
		}
	}

	$sql = $sql . ") as coeficient FROM redirecto_fingerprint ORDER BY coeficient ASC LIMIT 1;";

	// Execute query
	$statement = $pdo->prepare ( $sql );
	$statement->execute ();	
	$rows = $statement->fetchAll ( \PDO::FETCH_OBJ );

	$rowCount = $statement->rowCount ();
	if ($rowCount <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
		return;
	} 

	// Figure out wether to redirect is needed
	$calculatedRoomId = $rows[0]->room_id;

	// LOG
	$sqlLog = "INSERT INTO redirecto_log (row_id, coeficient, calculated_room_id, query) VALUES (:row_id, :coeficient, :calculated_room_id, :query);";
	$statement2 = $pdo->prepare ( $sqlLog );
	$statement2->bindParam ( "row_id", $rows[0]->id );
	$statement2->bindParam ( "coeficient", $rows[0]->coeficient );
	$statement2->bindParam ( "calculated_room_id", $rows[0]->room_id );
	$statement2->bindParam ( "query", $sql );
	$statement2->execute ();
	// END LOG

	// if($calculatedRoomId != $lastRoomId) {
		// Its a change!

		$redirectSuccess = redirectVoipCalls($userId, $calculatedRoomId);
		if(!$redirectSuccess) {
			echo error ( ERROR_CODE_REDIRECT_FAILED, ERROR_MSG_REDIRECT_FAILED );

		} else {
			echo success(
				array(
					"calculated_room_id" => $calculatedRoomId
					)
				);
		}

	/* } else {
		echo error ( ERROR_CODE_ROOM_NOT_CHANGED_ERROR, ERROR_MSG_ROOM_NOT_CHANGED_ERROR );
	} */

}
 ?>