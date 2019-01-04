<?php
/*
*
* API URL // Login Laveur (KyberLabs)
*
*/	

date_default_timezone_set('Africa/Porto-Novo');

 	if($_SERVER['REQUEST_METHOD']=='POST'){
	 
 		$code_connexion = $_POST['code_connexion'];
 
 		require_once('../dbConnect.php');
 
 		$sql = "SELECT code_connexion FROM triton_centre_lavage WHERE code_connexion = '$code_connexion' AND etat = '1' ";
	 
 		$result = mysqli_query($con,$sql);
 
		$check = mysqli_fetch_array($result);
		$code_connexion = $check[0];

 
 		if(isset($check)){
 			$statut="sucess";
 			echo $code_connexion."#".$statut;
 		}else{
	  		$message="Vérifier votre code de connexion !";
 			echo utf8_decode($message);
 		}
 
 	}
