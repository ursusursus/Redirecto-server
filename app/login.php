<?php 
require_once "index.php";

function login() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	
	$email = $array ["email"];
	$password = $array ["password"];
	
	// $token = sha1 ( uniqid () );
	$hashedPassword = sha1 ( $password );
	

	$pdo = getDatabase ();

	$sql = "SELECT id, email, directory_number FROM redirecto_user WHERE email = :email AND password = :password;";
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "email", $email );
	$statement->bindParam ( "password", $hashedPassword );
	$statement->execute ();
	$rows = $statement->fetchAll ( \PDO::FETCH_OBJ );
	
	//
	if ($statement->rowCount () != 1) {
		echo error ( ERROR_CODE_BAD_LOGIN_OR_PASSWORD, ERROR_MSG_BAD_LOGIN_OR_PASSWORD );

	} else {
		// Generate token
		$token = sha1 ( uniqid () );
		$isAdmin = isAdmin($pdo, $rows[0]->id);

		// Update token in database
		$sql2 = "UPDATE redirecto_user SET token = :token WHERE id = :id;";
		$statement2 = $pdo->prepare ( $sql2 );
		$statement2->bindParam ( "token", $token );
		$statement2->bindParam ( "id", $rows[0]->id );
		$statement2->execute ();

		// Echo response
		$responseArray = array ();
		$responseArray ["token"] = $token;
		$responseArray ["email"] = $rows[0]->email;
		$responseArray ["is_admin"] = $isAdmin;
		$responseArray ["directory_number"] = $rows[0]->directory_number;
		echo success ( $responseArray );
	}
	
	// Clean up
	// $pdo->commit();
	// $pdo = null;
}
 ?>