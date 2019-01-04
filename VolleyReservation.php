<?php
/*
*
* API URL // Reservation (KyberLabs)
*
*/

date_default_timezone_set('Africa/Porto-Novo');

$today = date("Y-m-d H:i:s"); 


    if($_SERVER['REQUEST_METHOD']=='POST'){

        require_once('dbConnect.php');
     
        $numero_telephone = $_POST['numero_telephone'];
        $lieu_lavage = $_POST['lieu_lavage'];
        $date_lavage = $_POST['date_lavage'];
        $heure_lavage = $_POST['heure_lavage'];
         
        //Récupération du solde du wallet
        $sql = "SELECT solde FROM triton_wallet_utilisateur WHERE id_utilisateur = '$numero_telephone' AND etat = '1' ";
        $result = mysqli_query($con,$sql);
        $check = mysqli_fetch_array($result);
        $solde = $check[0];

        //Récupération du montant du service et son id
        $sql2 = "SELECT montant, id_service FROM triton_service WHERE id_service = '3' AND etat = '1' ";
        $result2 = mysqli_query($con,$sql2);
        $check2 = mysqli_fetch_array($result2);
        $lavage_montant = $check2[0];
        $id_service = $check2[1];


        if($lavage_montant > $solde){
            $message="Votre solde est insuffisant pour réserver le lavage.";
            echo utf8_decode($message); 
        }else{
     
     
            if(!empty($numero_telephone) AND !empty($lieu_lavage) AND !empty($date_lavage) AND !empty($heure_lavage)){

                    $reste_wallet = $solde - $lavage_montant;

                    // Réservation de l'utilisateur
                    $sql3 = "INSERT INTO triton_reservation (id_wallet_utilisateur, id_service, lieu_lavage, date_lavage, heure_lavage, cree_le, modifier_le, etat) 
                             VALUES ('$numero_telephone', '$id_service', '$lieu_lavage', '$date_lavage', '$heure_lavage', '$today', '$today', 1)";
                    
                    $sql4 = "INSERT INTO triton_wallet_operation (id_service, id_wallet_utilisateur, solde_avant_utilisateur, montant_operation, solde_apres_utilisateur, cree_le, modifier_le, etat) 
                             VALUES ('$id_service', '$numero_telephone', '$solde', '$lavage_montant', '$reste_wallet', '$today', '$today', 1)";
                  
                    $sql5 = "UPDATE triton_wallet_utilisateur SET solde ='$reste_wallet' WHERE id_wallet_utilisateur ='$numero_telephone'";

                    if(mysqli_query($con,$sql3) AND mysqli_query($con,$sql4) AND mysqli_query($con,$sql5)){

                        $message="Réservation effectuée avec succès. Vous avez désormais $reste_wallet CFA dans votre wallet.";
                        echo utf8_decode($message);

                    }else{
                        echo "Erreur serveur";
                    }
                    
                    
                    }else{
                        $message="Veuillez remplir tous les champs !";
                        echo utf8_decode($message);
                    }
            }

    }else{
        echo 'error';
    }