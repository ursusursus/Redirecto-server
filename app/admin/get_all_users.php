<?php 
require_once "index.php";

function getAllUsers() {
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
	
	$hashedPassword = sha1 ( $password );
	
	//
	$sql = "SELECT * FROM redirecto_user";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->execute ();
	$users = $statement->fetchAll ( PDO::FETCH_OBJ );
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( $users );
	}
}
 ?>