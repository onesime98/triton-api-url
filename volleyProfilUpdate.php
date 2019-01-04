<?php
/*
*
* API URL // Login (KyberLabs)
*
*/

date_default_timezone_set('Africa/Porto-Novo');

	
 	if($_SERVER['REQUEST_METHOD']=='POST'){
	 
 		$numero_telephone = $_POST['numero_telephone'];
 		$code = $_POST['code'];

 		$code_hash = password_hash($code, PASSWORD_DEFAULT);
 
 		require_once('dbConnect.php');
 
 		$sql = "UPDATE triton_utilisateur SET code = '$code_hash' WHERE $numero_telephone = '$numero_telephone'";
	 
 		$result = mysqli_query($con,$sql);
 
	  	$message="Votre code a été modifié avec succès !";
 		echo utf8_decode($message);
 		

 
 	}
