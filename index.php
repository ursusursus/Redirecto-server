<?php
require "Slim/Slim.php";
require "app/login.php";
require "app/logout.php";
require "app/localize.php";
require "app/force_localize.php";
require "app/get_all_rooms.php";
require "app/get_my_rooms.php";
require "app/add_my_room.php";
require "app/remove_my_room.php";
require "app/admin/get_all_users.php";
require "app/admin/add_user.php";
require "app/admin/add_room.php";
require "app/admin/remove_room.php";
require "app/admin/new_fingerprints.php";

\Slim\Slim::registerAutoloader ();
$app = new \Slim\Slim ();

/******************************
********** CONSTANTS **********
*******************************/
define ( "MAX_RSSI", -110 );
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

// !!! Keep synced with database columns at all times !!!
$ACCEPTED_SSIDs = array("anton", "bashawell", "dlink", 
		"fonseka", "gbvideo", "herkel", 
		"megs", "mike_sk", "nikolka", 
		"tomiwifi", "upc179993");





/******************************
************* API *************
*******************************/

/**
 * LOGIN
 * {"email":"admin@redirecto.sk", "password":"heslo"}
 */
$app->post ( "/login", function () use($app) {
	login();
});

/**
 * LOGOUT
 * {"token":"abc123"}
 */
$app->post ( "/logout", function () use($app) {
	logout();
});

/**
 * GET ALL ROOMS
 * {"token":"abc123"}
 */
$app->post ( "/get_all_rooms", function () use($app) {
	getAllRooms();
});

/**
 * GET MY ROOMS
 * {"token":"abc123"}
 */
$app->post ( "/get_my_rooms", function () use($app) {
	getMyRooms();
});

/**
 * ADD MY ROOM
 * {"token":"abc123", "room_id":2}
 */
$app->post ( "/add_my_room", function () use($app) {
	addMyRoom();
});

/**
 * REMOVE MY ROOM
 * {"token":"abc123", "room_id":2}
 */
$app->post ( "/remove_my_room", function () use($app) {
	removeMyRoom();
});

/**
* LOCALIZE
* {
*	"token":"abc123", 
*	"last_room_id":25, 
*	"fingerprint":[
*		{"ssid":"gbvideo", "rssi":40},
*		{"ssid":"bashawell", "rssi":30}
* 	]
* }
*/
$app->post ( "/localize", function () use($app) {
	localize();
});

/**
* FORCE LOCALIZE
*
*/
$app->post ( "/force_localize", function () use($app) {
	forceLocalize();
});

/**
 * GET ALL USERS
 * {"token":"abc123"}
 *
 * -- ADMIN ONLY
 */
$app->post ( "/get_all_users", function () use($app) {
	getAllUsers();
});

/**
 * ADD USER
 * {"token":"abc123", "email":"user@redirecto.sk", "password":"heslo"}
 *
 * -- ADMIN ONLY
 */
$app->post ( "/add_user", function () use($app) {
	addUser();
});

/**
 * ADD ROOM 
 * {"token":"abc123", "name":"A117", "floor":"5. poschodie"} 
 *
 * -- ADMIN ONLY
 */
$app->post ( "/add_room", function () use($app) {
	addRoom();
});

/**
 * REMOVE ROOM 
 * {"token":"abc123", "room_id":1} 
 *
 * -- ADMIN ONLY
 */
$app->post ( "/remove_room", function () use($app) {
	removeRoom();
});

/**
* NEW FINGERPRINTS
* {
*	"token":"abc123", 
*	"room_id":1, 
*	"fingerprints":[
*		[{"ssid":"anton", "rssi":40}, {"ssid":"bashawell", "rssi":30}], 
*		[{"ssid":"anton", "rssi":41}, {"ssid":"bashawell", "rssi":31}],
*		[{"ssid":"anton", "rssi":42}, {"ssid":"bashawell", "rssi":32}]
*	]
* }
*
* -- ADMIN ONLY
*/
$app->post ( "/new_fingerprints", function () use($app) {
	newFingerprints();
});

$app->run ();





/******************************
********** HELPERS ************
*******************************/

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

function isSsidAccepted($filteredSsid) {
	global $ACCEPTED_SSIDs;
	foreach ($ACCEPTED_SSIDs as $ssid) {
		if($ssid == $filteredSsid) {
			return true;
		}
	}
	return false;
}

function redirectVoipCalls($roomId) {
	//
}

?>