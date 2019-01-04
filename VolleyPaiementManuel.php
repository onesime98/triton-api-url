<?php
/*
*
* API URL // Paiement Manuel (KyberLabs)
*
*/

date_default_timezone_set('Africa/Porto-Novo');

$today = date("Y-m-d H:i:s"); 

    if($_SERVER['REQUEST_METHOD']=='POST'){

        require_once('dbConnect.php');
     
        $code_centre = $_POST['code_centre'];
        $type_service = $_POST['type_service'];
        $appreciation = $_POST['appreciation'];
        $numero_telephone = $_POST['numero_telephone'];
         
        //Récupération du solde du wallet
        $sql = "SELECT solde FROM triton_wallet_utilisateur WHERE id_utilisateur = '$numero_telephone' AND etat = '1' ";
        $result = mysqli_query($con,$sql);
        $check = mysqli_fetch_array($result);
        $solde = $check[0];

        //Récupération du montant du service et son id
        $sql2 = "SELECT montant, id_service FROM triton_service WHERE nom = '$type_service' AND etat = '1' ";
        $result2 = mysqli_query($con,$sql2);
        $check2 = mysqli_fetch_array($result2);
        $lavage_montant = $check2[0];
        $id_service = $check2[1];

        //Récupération id centre de lavage
        $sql3 = "SELECT id_centre_lavage FROM triton_centre_lavage WHERE code_centre = '$code_centre' AND etat = '1' ";
        $result3 = mysqli_query($con,$sql3);
        $check3 = mysqli_fetch_array($result3);
        $id_centre_lavage = $check3[0];

        //Récupération pourcentage centre de lavage
        $sql4 = "SELECT pourcentage FROM triton_centre_lavage_pourcentage WHERE id_wallet_centre_lavage = '$id_centre_lavage' AND id_service = '$id_service' AND etat = '1' ";
        $result4 = mysqli_query($con,$sql4);
        $check4 = mysqli_fetch_array($result4);
        $pourcentage = $check4[0];     

        //Récupération solde lavage
        $sql5 = "SELECT solde FROM triton_wallet_centre_lavage WHERE id_wallet_centre_lavage = '$id_centre_lavage' AND etat = '1' ";
        $result5 = mysqli_query($con,$sql5);
        $check5 = mysqli_fetch_array($result5);
        $solde_centre_lavage = $check5[0];


        if($lavage_montant > $solde){
            $message="Votre solde est insuffisant pour payer ce lavage.";
            echo utf8_decode($message); 
        }else{

     
            if(!empty($code_centre) AND !empty($type_service) AND !empty($appreciation) AND !empty($numero_telephone)){

                    $reste_wallet = $solde - $lavage_montant;

                    $recu_centre_lavage = ($lavage_montant * $pourcentage) / 100;

                    $nouveau_solde_centre_lavage = $solde_centre_lavage + $recu_centre_lavage;

                    // Réservation de l'utilisateur
                    $sql = "INSERT INTO triton_wallet_operation (id_service, id_wallet_utilisateur, solde_avant_utilisateur, montant_operation, solde_apres_utilisateur, id_wallet_centre_lavage, solde_avant_centre_lavage, solde_recu_centre_lavage, solde_apres_centre_lavage, appreciation, cree_le, modifier_le, etat) 
                             VALUES ('$id_service', '$numero_telephone', '$solde', '$lavage_montant', '$reste_wallet', '$id_centre_lavage', '$solde_centre_lavage', '$recu_centre_lavage', '$nouveau_solde_centre_lavage', '$appreciation', '$today', '$today', 1)";

                    $sql2 = "UPDATE triton_wallet_utilisateur SET solde ='$reste_wallet' WHERE id_wallet_utilisateur ='$numero_telephone'";

                    $sql3 = "UPDATE triton_wallet_centre_lavage SET solde ='$nouveau_solde_centre_lavage' WHERE id_wallet_centre_lavage = '$id_centre_lavage'";


                    if(mysqli_query($con,$sql) AND mysqli_query($con,$sql2) AND mysqli_query($con,$sql3)){

                        $message="Paiement effectuée avec succès. Vous avez désormais $reste_wallet FCFA dans votre wallet.";
                        echo utf8_decode($message);

                    }else{
                        echo "Erreur dans le code du centre de lavage !";
                    }
                    
                    
                    }else{
                        $message="Veuillez remplir tous les champs !";
                        echo $message;
                    }
            }
    
    }else{
        echo 'error';
    }
