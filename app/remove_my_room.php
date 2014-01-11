<?php
require_once "index.php";

function removeMyRoom() {
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
	
	$sql = "DELETE FROM redirecto_user_room WHERE user_id = :user_id AND room_id = :room_id;";
	
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "user_id", $userId );
	$statement->bindParam ( "room_id", $roomId );
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		// echo success ( true );
		echo success( $roomId );
	}
}
 ?>