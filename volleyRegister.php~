<?php
/*
*
* API URL // Register (KyberLabs)
*
*/

date_default_timezone_set('Africa/Porto-Novo');

$today = date("Y-m-d H:i:s"); 


 	if($_SERVER['REQUEST_METHOD']=='POST'){
	 
		$nom_prenom = $_POST['nom_prenom'];
		$numero_telephone = $_POST['numero_telephone'];
		$code = $_POST['code'];
		 
		 require_once('dbConnect.php');
		 
		 $sql = "SELECT * FROM triton_utilisateur WHERE numero_telephone = '$numero_telephone' AND etat = '1' ";
		 
		 $result = mysqli_query($con,$sql);
		 
		 $check = mysqli_fetch_array($result);
		 
		 if(isset($check)){
		 	$message="Ce numéro de téléphone est déjà utilisé !";
		 	echo $message; 
		 }else{
	 
	 
  			if(!empty($numero_telephone) AND !empty($nom_prenom) AND !empty($code)){
	 
				if(strlen($numero_telephone) == 8){

					$code_hash = password_hash($code, PASSWORD_DEFAULT);

					// Insertion de l'utilisateur
					$sql2 = "INSERT INTO triton_utilisateur (nom_prenom, numero_telephone, code, cree_le, modifier_le, etat) 
					         VALUES ('$nom_prenom', '$numero_telephone', '$code_hash', '$today', '$today', 1)";
				  
					if(mysqli_query($con,$sql2)){

						// Création du wallet
						$sql3 = "INSERT INTO triton_wallet_utilisateur (id_wallet_utilisateur, id_utilisateur, solde, cree_le, modifier_le, etat) 
					    	     VALUES ('$numero_telephone', '$numero_telephone', 0, '$today', '$today', 1)";

					    if(mysqli_query($con,$sql3)){
						$message="Inscription effectuée avec succès. Vous pouvez vous connecter.";
						echo $message;
						}

					}else{
                        echo "Erreur serveur";
					}
					
					}else{
						$message="Vérifier votre numéro de téléphone !";
						echo $message;
					} 
					
					}else{
						$message="Veuillez remplir tous les champs !";
						echo $message;
					}
			}

	}else{
	    echo 'error';
	}
