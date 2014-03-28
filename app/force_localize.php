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

	// Go redirect VoIP calls
	$redirectSuccess = redirectVoipCalls($userId, $desiredRoomId);
	if(!$redirectSuccess) {
		echo error ( ERROR_CODE_REDIRECT_FAILED, ERROR_MSG_REDIRECT_FAILED );
		return;
	}

	// Query room details
	$sql = "SELECT id, name, floor FROM redirecto_room WHERE id=:id";
	$statement = $pdo->prepare($sql);
	$statement->bindParam("id", $desiredRoomId);
	if(!$statement->execute()) {
		echo error( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR);
		return;
	}

	$rooms = $statement->fetchAll(\PDO::FETCH_OBJ);
	echo success(
		array(
			"calculated_room_id" => $rooms[0]->id,
			"calculated_room_name" => $rooms[0]->name,
			"calculated_room_desc" => $rooms[0]->floor
			)
		);	

}
 ?>