<?php 
require_once "index.php";

function changeCoeficientSettings() {
	$array = json_decode ( file_get_contents ( 'php://input' ), true );
	$token = $array ["token"];
	$newCoeficientSetting = $array ["coef_setting"];

	//
	$pdo = getDatabase();

	// Get user id from token
	$userId = isTokenValid ( $pdo, $token );
	if ($userId == - 1) {
		echo error ( ERROR_CODE_INVALID_TOKEN, ERROR_MSG_INVALID_TOKEN );
		return;
	}

	if($newCoeficientSetting < MINIMAL_ACC_COEFICIENT) {
		$newCoeficientSetting = MINIMAL_ACC_COEFICIENT;
	}

	// Update coef setting
	$sql = "UPDATE redirecto_user SET acc_coef=:acc_coef WHERE id=:user_id;";

	$statement = $pdo->prepare ( $sql );
	$statement->bindParam("acc_coef", $newCoeficientSetting);
	$statement->bindParam("user_id", $userId);
	$statement->execute ();	

	$rowCount = $statement->rowCount ();
	if ($rowCount <= 0) {
		echo error ( ERROR_CODE_DATABASE_ERROR, ERROR_MSG_DATABASE_ERROR );
		return;
	}

	echo success(true);

}
 ?>