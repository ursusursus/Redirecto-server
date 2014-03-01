<?php 
function getAllRooms() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	
	//
	$pdo = getDatabase ();
	
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == -1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}
	
	$sql = "SELECT id, name, floor, phone_number, created_at, changed_at
			FROM redirecto_room
			WHERE id NOT IN (
				SELECT redirecto_room.id
				FROM redirecto_user 
				JOIN redirecto_user_room ON redirecto_user.id = redirecto_user_room.user_id
				JOIN redirecto_room ON redirecto_room.id = redirecto_user_room.room_id
				WHERE redirecto_user.id = :user_id)";

	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "user_id", $userId );
	$statement->execute ();
	
	$rooms = $statement->fetchAll ( PDO::FETCH_OBJ );
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( $rooms );
	}
}
 ?>