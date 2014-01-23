<?php 
require_once "index.php";

function addMyRoom() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$roomId = $array ["room_id"];
	
	// Get database
	$pdo = getDatabase ();
	
	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}
	
	// Kontrola na duplikaty
	$sql1 = "SELECT id FROM redirecto_user_room WHERE user_id = :user_id AND room_id = :room_id;";
	
	$statement1 = $pdo->prepare ( $sql1 );
	$statement1->bindParam ( "user_id", $userId );
	$statement1->bindParam ( "room_id", $roomId );
	$statement1->execute ();
	
	//
	if ($statement1->rowCount () != 0) {
		echo error ( ERROR_CODE_DUPLICATE, ERROR_MSG_DUPLICATE );
		return;
	}
	
	// Vlozit novu miestnost medzi svoje
	$sql2 = "INSERT INTO redirecto_user_room (user_id, room_id) VALUES (:user_id, :room_id);";
	
	$statement2 = $pdo->prepare ( $sql2 );
	$statement2->bindParam ( "user_id", $userId );
	$statement2->bindParam ( "room_id", $roomId );
	$statement2->execute ();
	
	//
	if ($statement2->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
		return;
	}

	// Odpovedat s prave vlozenym roomom
	$sql3 = "SELECT id, name, floor, created_at, changed_at FROM redirecto_room WHERE id = :id;";
	
	$statement3 = $pdo->prepare ( $sql3 );
	$statement3->bindParam ( "id", $roomId );
	$statement3->execute ();
	$rooms = $statement3->fetchAll ( PDO::FETCH_OBJ );
	
	//
	if ($statement3->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
		return;
	} else {
		echo success( $rooms[0] );
	}
}
 ?>