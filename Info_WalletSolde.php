<?php
/*
*
* Script (KyberLabs)
*
*/

include './include/DbHandler.php';
$db = new DbHandler();

	if($_SERVER['REQUEST_METHOD']=='GET'){
		
		$numero_telephone  = $_GET['numero_telephone'];
		
		$response = array();
		//$response["error"] = false;

		$response = $db->getWalletSolde($numero_telephone);

			if ($response != NULL){
       			echo json_encode( array("info_walletSolde"=>$response));
    		} 

	}
	
?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
