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
	$sql = "SELECT id, room_id, min(sqrt(";

	$index = 0;
	foreach ($apValuesArray as $ssid => $rssi) {
		$sql = $sql . "power(abs({$rssi}-ap_{$ssid}),2)";
		if($index++ < count($apValuesArray) - 1) {
			$sql = $sql . "+\n";
		}
	}

	$sql = $sql . ")) as coeficient FROM redirecto_fingerprint;";

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

	// LOGINSERT INTO redirecto_log (column_id, coeficient, calculated_room_id, sql) VALUES (:column_id, :coeficient, :calculated_room_id, :sql);
	$sqlLog = "";
	$statement2 = $pdo->prepare ( $sqlLog );
	$statement2->bindParam ( "column_id", $rows[0]->id );
	$statement2->bindParam ( "coeficient", $rows[0]->coeficient );
	$statement2->bindParam ( "calculated_room_id", $rows[0]->room_id );
	$statement2->bindParam ( "sql", $sql );
	$statement2->execute ();	
	// END LOG

	// if($calculatedRoomId != $lastRoomId) {
		// Its a change!
		redirectVoipCalls($calculatedRoomId);
		echo success(
			array(
				"calculated_room_id" => $calculatedRoomId
				)
			);
	/* } else {
		echo error ( ERROR_CODE_ROOM_NOT_CHANGED_ERROR, ERROR_MSG_ROOM_NOT_CHANGED_ERROR );
	} */

}
 ?>