<?php
require_once "index.php";

function addRoom() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$name = $array ["name"];
	$floor = $array ["floor"];
	$phoneNumber = $array ["phone_number"];
	
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
	
	//
	$sql = "INSERT INTO redirecto_room (name, floor, phone_number) VALUES (:name, :floor, :phone_number);";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "name", $name );
	$statement->bindParam ( "floor", $floor );
	$statement->bindParam ( "phone_number", $phoneNumber );
	
	//
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( true );
	}
}
 ?>