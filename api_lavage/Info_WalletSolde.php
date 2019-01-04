<?php
/*
*
* Script (KyberLabs)
*
*/

include './include/DbHandler.php';
$db = new DbHandler();

	if($_SERVER['REQUEST_METHOD']=='GET'){
		
		$code_connexion  = $_GET['code_connexion'];
		
		$response = array();
		//$response["error"] = false;

		$response = $db->getWalletSolde($code_connexion);

			if ($response != NULL){
       			echo json_encode( array("info_walletSolde"=>$response));
    		} 

	}
	
?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
