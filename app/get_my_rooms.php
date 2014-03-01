<?php 
require_once "index.php";

function getMyRooms() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	
	// Get database
	$pdo = getDatabase ();
	
	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}
	
	$sql = "SELECT redirecto_room.id, redirecto_room.name, redirecto_room.floor, redirecto_room.phone_number, redirecto_room.created_at, redirecto_room.changed_at
				FROM redirecto_user INNER JOIN redirecto_user_room ON redirecto_user.id = redirecto_user_room.user_id INNER JOIN redirecto_room ON redirecto_room.id = redirecto_user_room.room_id
				WHERE redirecto_user.id=:user_id
				ORDER BY redirecto_room.name;";
	
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "user_id", $userId );
	$statement->execute ();
	
	$rooms = $statement->fetchAll ( PDO::FETCH_OBJ );
	
	//
	//if ($statement->rowCount () <= 0) {
	//	echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	// } else {
		echo success ( $rooms );
	// }
}
 ?>