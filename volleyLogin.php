<?php
/*
*
* API URL // Login (KyberLabs)
*
*/	
 	if($_SERVER['REQUEST_METHOD']=='POST'){
	 
 		$numero_telephone = $_POST['numero_telephone'];
 		$code = $_POST['code'];
 
 		require_once('dbConnect.php');
 
 		$sql = "SELECT numero_telephone, code FROM triton_utilisateur WHERE numero_telephone = '$numero_telephone' AND etat = '1' ";
	 
 		$result = mysqli_query($con,$sql);
 
		$check = mysqli_fetch_array($result);
		$code_hash = $check[1];

		if(password_verify($code, $code_hash) == true)
		   $numero= $check[0];
 
 		if(isset($check)){
 			$statut="sucess";
 			echo $numero."#".$statut;
 		}else{
	  		$message="Vérifier le numéro de téléphone et le code !";
 			echo utf8_decode($message);
 		}
 
 	}
