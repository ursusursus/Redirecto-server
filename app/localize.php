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
	global $ACCEPTED_BSSIDs;
	foreach ($ACCEPTED_BSSIDs as $bssid) {
		$apValuesArray[$bssid] = MAX_RSSI;
	}

	// Override accepted SSIDs with measured values
	foreach($fingerprint as $ap) {
		if(isBssidAccepted($ap["bssid"])) {
			$apValuesArray[strtolower($ap["bssid"])] = $ap["rssi"];
		}
	}

	// Assemble select clause
	$sql = "SELECT id, room_id, sqrt(";

	$index = 0;
	foreach ($apValuesArray as $bssid => $rssi) {
		$sql = $sql . "power(abs({$rssi}-`ap_{$bssid}`),2)";
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

	// LOG
	$sqlLog = "INSERT INTO redirecto_log (row_id, coeficient, calculated_room_id, query) VALUES (:row_id, :coeficient, :calculated_room_id, :query);";
	$statement2 = $pdo->prepare ( $sqlLog );
	$statement2->bindParam ( "row_id", $rows[0]->id );
	$statement2->bindParam ( "coeficient", $rows[0]->coeficient );
	$statement2->bindParam ( "calculated_room_id", $rows[0]->room_id );
	$statement2->bindParam ( "query", $sql );
	$statement2->execute ();
	// END LOG

	// Get users defined accuracy coeficient setting
	$sqlCoef = "SELECT acc_coef FROM redirecto_user WHERE id = :user_id";
	$statement3 = $pdo->prepare( $sqlCoef );
	$statement3->bindParam ( "user_id", $userId);
	$statement3->execute ();
	$coefs = $statement3->fetchAll ( \PDO::FETCH_OBJ );
	
	$calculatedRoomId = $rows[0]->room_id;
	$calculatedCoeficient = $rows[0]->coeficient;
	$userSavedCoeficient = $coefs[0]->acc_coef;


	// Figure out whether to redirect is needed
	if($calculatedCoeficient >= $userSavedCoeficient) {
		echo error( ERROR_CODE_COEFICIENT_TOO_LARGE, ERROR_MSG_COEFICIENT_TOO_LARGE );
		return;
	}

	// If room changed
	if($calculatedRoomId != $lastRoomId) {

		// Go redirect VoIP calls
		$redirectSuccess = redirectVoipCalls($userId, $calculatedRoomId);
		if(!$redirectSuccess) {
			echo error ( ERROR_CODE_REDIRECT_FAILED, ERROR_MSG_REDIRECT_FAILED );
			return;
		}

	}

	// Query room details
	$sqlRoom = "SELECT id, name, floor FROM redirecto_room WHERE id=:id";
	$statement4 = $pdo->prepare($sqlRoom);
	$statement4->bindParam("id", $calculatedRoomId);
	if(!$statement4->execute()) {
		echo error( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR);
		return;
	}

	$rooms = $statement4->fetchAll(\PDO::FETCH_OBJ);
	echo success(
		array(
			"calculated_room_id" => $rooms[0]->id,
			"calculated_room_name" => $rooms[0]->name,
			"calculated_room_desc" => $rooms[0]->floor
			)
		);
		

}
 ?>