<?php 
require_once "index.php";

function login() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	
	$email = $array ["email"];
	$password = $array ["password"];
	
	$token = sha1 ( uniqid () );
	$hashedPassword = sha1 ( $password );
	
	//
	$pdo = getDatabase ();
	$sql = "UPDATE redirecto_user SET token = :token WHERE email = :email AND password = :password;";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "token", $token );
	$statement->bindParam ( "email", $email );
	$statement->bindParam ( "password", $hashedPassword );
	
	//
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_BAD_LOGIN_OR_PASSWORD, ERROR_MSG_BAD_LOGIN_OR_PASSWORD );
	} else {
		$responseArray = array ();
		$responseArray ["token"] = $token;
		$responseArray ["email"] = $email;
		echo success ( $responseArray );
	}
	
	// Clean up
	// $pdo->commit();
	// $pdo = null;
}
 ?>