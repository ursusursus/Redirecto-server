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
require "app/change_coef_settings.php";
require "app/admin/get_all_users.php";
require "app/admin/add_user.php";
require "app/admin/add_room.php";
require "app/admin/remove_room.php";
require "app/admin/new_fingerprints.php";
require "app/admin/get_rooms_and_aps.php";

\Slim\Slim::registerAutoloader ();
$app = new \Slim\Slim ();




/******************************
********** CONSTANTS **********
*******************************/
define ( MAX_RSSI, -110 );
define ( VOIP_REDIRECT_URL, "http://ns.cnl.sk/forward.php");
define ( SHARED_SECRET, "VoIPr3d1r3ct0r");
define ( MINIMAL_ACC_COEFICIENT, 30);
define ( DEFAULT_ACC_COEFICIENT, 50);

define ( ERROR_CODE_BAD_LOGIN_OR_PASSWORD, - 1234 );
define ( ERROR_MSG_BAD_LOGIN_OR_PASSWORD, "Nesprávne meno alebo heslo" );
define ( ERROR_CODE_INVALID_TOKEN, - 1235 );
define ( ERROR_MSG_INVALID_TOKEN, "Neplatný token" );
define ( ERROR_CODE_DATABASE_ERROR, - 1236 );
define ( ERROR_MSG_DATABASE_ERROR, "Databázová chyba" );
define ( ERROR_CODE_UNAUTHORIZED_ACCESS, - 1237 );
define ( ERROR_MSG_UNAUTHORIZED_ACCESS, "Nepovolený prístup" );
define ( ERROR_CODE_DUPLICATE, - 1238 );
define ( ERROR_MSG_DUPLICATE, "Už existuje" );
define ( ERROR_CODE_ROOM_NOT_CHANGED_ERROR, -1239 );
define ( ERROR_MSG_ROOM_NOT_CHANGED_ERROR, "Miestnosť sa nezmenila" );
define ( ERROR_CODE_REDIRECT_FAILED, -1240) ;
define ( ERROR_MSG_REDIRECT_FAILED, "Presmerovanie zlyhalo" );
define ( ERROR_CODE_COEFICIENT_TOO_LARGE, -1241 );
define ( ERROR_MSG_COEFICIENT_TOO_LARGE, "Koeficient je príliš veľký. Nepresmerujem" );


// !!! KEEP SYNCED WITH DATABASE COLUMN NAMES AT ALL TIMES !!!
// IF THIS CHANGES (or a typo was made) YOU NEED TO RECREATE THE
// WHOLE DATABASE, AS IT NEEDS TO BE DROPPED
$ACCEPTED_BSSIDs = array(
	"00:26:cb:4d:78:a1",
	"00:26:cb:4d:78:ae",
	"00:26:cb:4e:18:d1",
	"00:26:cb:4e:19:11",
	"00:26:cb:4e:19:1e",
	"00:26:cb:4e:19:f1",
	"00:26:cb:4e:19:fe",
	"00:26:cb:4e:1b:01",
	"00:26:cb:4e:38:b1",
	"00:26:cb:4e:38:be",
	"00:26:cb:9f:96:01",
	"00:26:cb:9f:96:0e",
	"00:26:cb:9f:ac:91",
	"00:26:cb:a0:8f:c1",
	"00:26:cb:a0:93:f1",
	"00:26:cb:a0:93:fe",
	"00:26:cb:a0:a1:b1",
	"00:26:cb:a0:a1:be",
	"00:26:cb:a0:a8:51",
	"00:26:cb:a0:e5:51",
	"00:26:cb:a0:e6:01",
	"00:26:cb:a0:e6:0e",
	"58:bc:27:5c:ba:91",
	"58:bc:27:5c:ba:9e");
// !!!




/******************************
************* API *************
*******************************/

/* $app->get ( "/test_redirect", function () use($app) {
	$redirectSuccess = redirectVoipCalls(1, 28);
	if(!$redirectSuccess) {
		echo error ( ERROR_CODE_REDIRECT_FAILED, ERROR_MSG_REDIRECT_FAILED );
	}
}); */

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
* {"token":"abc123", "room_id":2}
*/
$app->post ( "/force_localize", function () use($app) {
	forceLocalize();
});

/**
* CHANGE COEFICIENT SETTINGS
* {"token":"abc123", "coef_setting":99}
*/
$app->post ( "/change_coef_settings", function () use($app) {
	changeCoeficientSettings();
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
 * {"token":"abc123", "name":"A117", "floor":"5. poschodie", "phone_number":"1234"} 
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

/**
 * GET ROOMS AND APS 
 * {"token":"abc123", "room_id":1} 
 *
 * -- ADMIN ONLY
 */
$app->post ( "/get_rooms_and_aps", function () use($app) {
	getRoomsAndAPs();
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

function isBssidAccepted($filteredBssid) {
	global $ACCEPTED_BSSIDs;
	foreach ($ACCEPTED_BSSIDs as $bssid) {
		// if($ssid == $filteredSsid) {
		if (strcasecmp($bssid, $filteredBssid) == 0) {
			return true;
		}
	}
	return false;
}

function redirectVoipCalls($userId, $roomId) {
	//
	$pdo = getDatabase();

	// Get user directory number
	$sql1 = "SELECT directory_number FROM redirecto_user WHERE id = :id";

	$statement1 = $pdo->prepare ( $sql1 );
	$statement1->bindParam ( "id", $userId );
	$statement1->execute ();
	
	$rows1 = $statement1->fetchAll ( \PDO::FETCH_OBJ );
	if($statement1->rowCount () != 1) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
		return;
	}
	$directoryNumber = $rows1 [0]->directory_number;
	

	// Get room phone number
	$sql2 = "SELECT phone_number FROM redirecto_room WHERE id = :id";

	$statement2 = $pdo->prepare ( $sql2 );
	$statement2->bindParam ( "id", $roomId );
	$statement2->execute ();
	
	$rows2 = $statement2->fetchAll ( \PDO::FETCH_OBJ );
	if($statement2->rowCount () != 1) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
		return;
	}
	$phoneNumber = $rows2 [0]->phone_number;


	// Call script that actually redirects calls inside call manager
	$timestamp=time();
	// $dn="24"; //klapka 7077
	// $phoneNumber="2553"; //kam presmerovat

	$hashedString = SHARED_SECRET . ';timestamp=' . $timestamp .';dn=' . $directoryNumber . ';forward=' . $phoneNumber . ';' . SHARED_SECRET;
	$hash = hash('sha512',$hashedString);

	$url = VOIP_REDIRECT_URL . "?timestamp=$timestamp&dn=$directoryNumber&forward=$phoneNumber&hash=$hash";

	$fp=fopen($url,"rb");
	$response = stream_get_contents($fp);
	fclose($fp);	

	return $response == "OK";
}

?>