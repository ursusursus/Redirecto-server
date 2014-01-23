<?php 
require_once "index.php";

function forceLocalize() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$desiredRoomId = $array ["room_id"];

	//
	$pdo = getDatabase();

	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}

	//
	redirectVoipCalls($desiredRoomId);
	echo success(
		array(
			"calculated_room_id" => $desiredRoomId
			)
		);
}
 ?>