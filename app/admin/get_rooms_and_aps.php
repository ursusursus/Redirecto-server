<?php 
require_once "index.php";

function getRoomsAndAPs() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];

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

	$sql = "SELECT id, name, floor, phone_number, created_at, changed_at FROM redirecto_room ORDER BY name;";

	$statement = $pdo->prepare ( $sql );
	$statement->execute ();
	
	$rooms = $statement->fetchAll ( PDO::FETCH_OBJ );
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		global $ACCEPTED_BSSIDs;
		echo success ( array(
			"rooms" => $rooms,
			"aps" => $ACCEPTED_BSSIDs
			) 
		);
	}
}
 ?>