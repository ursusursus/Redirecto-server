<?php
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader ();
$app = new \Slim\Slim ();

define ( "ERROR_CODE_BAD_LOGIN_OR_PASSWORD", - 1234 );
define ( "ERROR_MSG_BAD_LOGIN_OR_PASSWORD", "Bad username or password" );
define ( "ERROR_CODE_INVALID_TOKEN", - 1235 );
define ( "ERROR_MSG_INVALID_TOKEN", "Invalid token" );
define ( "ERROR_CODE_DATABASE_ERROR", - 1236 );
define ( "ERROR_MSG_DATABASE_ERROR", "Database error" );
define ( "ERROR_CODE_UNAUTHORIZED_ACCESS", - 1237 );
define ( "ERROR_MSG_UNAUTHORIZED_ACCESS", "Unauthorized access" );
define ( "ERROR_CODE_DUPLICATE", - 1238 );
define ( "ERROR_MSG_DUPLICATE", "Already exists" );

/**
 * GET ALL ROOMS
 * {"token":"abc123"}
 */
$app->post ( "/get_all_rooms", function () use($app) {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	
	//
	$pdo = getDatabase ();
	
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}
	
	$sql = "SELECT id, name, floor, created_at, changed_at FROM redirecto_room ORDER BY name;";
	$statement = $pdo->prepare ( $sql );
	$statement->execute ();
	
	$rooms = $statement->fetchAll ( PDO::FETCH_OBJ );
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( $rooms );
	}
} );

/**
 * GET MY ROOMS
 * {"token":"abc123"}
 */
$app->post ( "/get_my_rooms", function () use($app) {
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
	
	// select redirecto_room.id, redirecto_room.name, redirecto_room.floor, redirecto_room.created_at, redirecto_room.changed_at
	// FROM redirecto_user INNER JOIN redirecto_user_room ON redirecto_user.id = redirecto_user_room.user_id INNER JOIN redirecto_room ON redirecto_room.id = redirecto_user_room.room_id
	// WHERE redirecto_user.id=1;
	$sql = "SELECT redirecto_room.id, redirecto_room.name, redirecto_room.floor, redirecto_room.created_at, redirecto_room.changed_at
				FROM redirecto_user INNER JOIN redirecto_user_room ON redirecto_user.id = redirecto_user_room.user_id INNER JOIN redirecto_room ON redirecto_room.id = redirecto_user_room.room_id
				WHERE redirecto_user.id=:user_id
				ORDER BY redirecto_room.name;";
	
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
} );

/**
 * ADD MY ROOM
 * {"token":"abc123", "room_id":2}
 */
$app->post ( "/add_my_room", function () use($app) {
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
} );

/**
 * REMOVE MY ROOM
 * {"token":"abc123", "room_id":2}
 */
$app->post ( "/remove_my_room", function () use($app) {
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
} );

/*
 * ADD ROOM {"token":"abc123", "name":"A117", "floor":"5. poschodie"} -- ADMIN ONLY
 */
$app->post ( "/add_room", function () use($app) {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$name = $array ["name"];
	$floor = $array ["floor"];
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
	$sql = "INSERT INTO redirecto_room (name, floor) VALUES (:name, :floor);";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "name", $name );
	$statement->bindParam ( "floor", $floor );
	
	//
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( true );
	}
} );

/*
 * REMOVE ROOM {"token":"abc123", "room_id":1} -- ADMIN ONLY
 */
$app->post ( "/remove_room", function () use($app) {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$roomId = $array ["room_id"];
	
	//
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
	$sql = "DELETE FROM redirecto_room WHERE id = :id;";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "id", $roomId );
	
	//
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( true );
	}
} );

$app->post ( "/localize_me", function () use($app) {
	// automaticky vypocita room
	// podla prijatych signalov
	echo "localize me";
} );

$app->post ( "/localize_me_manually", function () use($app) {
	// rucne nastavi room
	echo "localize me manually";
} );

/**
 * LOGIN
 * {"email":"admin@redirecto.sk", "password":"heslo"}
 */
$app->post ( "/login", function () use($app) {
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
} );

/**
 * LOGOUT
 * {"token":"abc123"}
 */
$app->post ( "/logout", function () use($app) {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	
	$pdo = getDatabase ();
	
	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}
	
	//
	$sql = "UPDATE redirecto_user SET token = NULL WHERE id = :id;";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "id", $userId );
	
	//
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( true );
	}
} );

/**
 * ADD USER
 * {"token":"abc123", "email":"user@redirecto.sk", "password":"heslo"}
 *
 * -- ADMIN ONLY
 */
$app->post ( "/add_user", function () use($app) {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$email = $array ["email"];
	$password = $array ["password"];
	
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
	$sql = "INSERT INTO redirecto_user (email, password, role) VALUES (:email, :password, 'regular');";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "email", $email );
	$statement->bindParam ( "password", $hashedPassword );
	
	//
	$statement->execute ();
	
	//
	if ($statement->rowCount () <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
	} else {
		echo success ( true );
	}
} );

/**
 * GET ALL USERS
 * {"token":"abc123"}
 *
 * -- ADMIN ONLY
 */
$app->post ( "/get_all_users", function () use($app) {
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
} );

$app->run ();
function getDatabase() {
	$config = include ("config.php");
	$hostname = $config ["hostname"];
	$dbname = $config ["dbname"];
	$username = $config ["username"];
	$password = $config ["password"];
	
	$pdo = new PDO ( "mysql:host=$hostname;dbname=$dbname", $username, $password );
	return $pdo;
}
function error($code, $message) {
	return '{"jsonrpc":"2.0", "error":{"code": ' . $code . ', "message": "' . $message . '"}}';
}
function success($array) {
	return '{"jsonrpc":"2.0", "result":' . json_encode ( $array ) . '}';
}
function isAdmin($pdo, $userId) {
	if ($userId == NULL) {
		return false;
	}
	
	$sql = "SELECT id FROM redirecto_user WHERE id = :id AND role = 'admin';";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "id", $userId );
	$statement->execute ();
	
	//
	$rows = $statement->fetchAll ( \PDO::FETCH_OBJ );
	
	//
	$rowCount = $statement->rowCount ();
	return $rowCount == 1;
}
function isTokenValid($pdo, $token) {
	if ($token == NULL) {
		return - 1;
	}
	
	$sql = "SELECT id FROM redirecto_user WHERE token = :token;";
	
	//
	$statement = $pdo->prepare ( $sql );
	$statement->bindParam ( "token", $token );
	$statement->execute ();
	
	//
	$rows = $statement->fetchAll ( \PDO::FETCH_OBJ );
	
	//
	$rowCount = $statement->rowCount ();
	if ($rowCount != 1) {
		return - 1;
	} else {
		return $rows [0]->id;
	}
}

?>