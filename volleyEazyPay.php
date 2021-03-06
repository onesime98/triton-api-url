<?php
/*
*
* API URL // Eazy Pay (KyberLabs)
*
*/

date_default_timezone_set('Africa/Porto-Novo');

$today = date("Y-m-d H:i:s"); 

 if($_SERVER['REQUEST_METHOD']=='POST'){ 
	
    require_once('dbConnect.php');

	$customer = $_POST['customer']; // numero du client qui a envoye les sous
 	$amount = $_POST['amount']; // montant de la recharge
 	$payplus_id = $_POST['payplus_id']; // id de la transaction payplus
 	$triton_id = null; // id de la transaction triton

    $report = array(
        'error' => false, // indique si il y a erreur : true = il y a erreur, false = il n'y a apas d'erreur
        'error_code' => '00', // code de l'erreur ; 00 = aucune erreur, 01 = probleme interne, 02 = numero inexistant, 03 = montant incorrect , 04 = empty data
        'error_reason' => '', // indique la raison de l'erreur
        'payplus_id' => $payplus_id, // indique l'id payplus de la transaction
        'triton_id' => $triton_id, // indique l'id triton de la transaction
        'amount'=> $amount, // indique le montant
        'customer'=> $customer // indique l'utilisateur
    ); // reporting du traitement

	if(is_numeric($amount)){

        if(!empty($customer) AND !empty($amount)){

            $sql = "SELECT numero_telephone FROM triton_utilisateur WHERE numero_telephone = '$customer' AND etat = '1' ";
            $result = mysqli_query($con,$sql);
            $check = mysqli_fetch_array($result);

            if(isset($check)){

                $sql4 = "SELECT * FROM triton_wallet_utilisateur WHERE id_utilisateur = '$customer' ";
                $result_ = mysqli_query($con,$sql4);
                $check_ = mysqli_fetch_array($result_);
                $solde_wallet= $check_[2];

                $montant_apres = $solde_wallet + $amount;

                $sql3 = "UPDATE triton_wallet_utilisateur SET solde = '$montant_apres' WHERE id_utilisateur = '$customer'";

                $sql2 = "INSERT INTO triton_wallet_depot (id_wallet_utilisateur, solde_depot, solde_avant, solde_apres, cree_le, modifier_le, etat) VALUES 
                                    ('$customer','$amount', '$solde_wallet','$montant_apres', '$today', '$today', 1)";

                // ici vous devez recuperer la valeur de l'id genere apres l'execution de la requete $sql2 est mettre cette valeur dans $triton_id
                // je vous conseille d'encapsuler les deux requetes dans une transaction

                if(mysqli_query($con,$sql2) AND mysqli_query($con,$sql3)){

                    $sqlID = "SELECT * FROM triton_wallet_depot WHERE id_wallet_utilisateur = '$customer' AND solde_apres = '$montant_apres' ";
                    $resultID = mysqli_query($con,$sqlID);
                    $checkID = mysqli_fetch_array($resultID);
                    $triton_id= $checkID[0];

                    $message="Votre compte à été bien crédité";
                    echo utf8_decode($message);

                    $report = array(
                        'error' => false, // indique si il y a erreur : true = il y a erreur, false = il n'y a apas d'erreur
                        'error_code' => '00', // code de l'erreur ; 00 = aucune erreur, 01 = probleme interne, 02 = numero inexistant, 03 = montant incorrect , 04 = empty data
                        'error_reason' => 'Success', // indique la raison de l'erreur
                        'payplus_id' => $payplus_id, // indique l'id payplus de la transaction
                        'triton_id' => $triton_id, // indique l'id triton de la transaction
                        'amount'=> $amount, // indique le montant
                        'customer'=> $customer // indique l'utilisateur
                    ); // reporting du traitement

                }else{
                    echo "Could not register";

                    $report = array(
                        'error' => true, // indique si il y a erreur : true = il y a erreur, false = il n'y a apas d'erreur
                        'error_code' => '01', // code de l'erreur ; 00 = aucune erreur, 01 = probleme interne, 02 = numero inexistant, 03 = montant incorrect , 04 = empty data
                        'error_reason' => 'fail : internal error', // indique la raison de l'erreur
                        'payplus_id' => $payplus_id, // indique l'id payplus de la transaction
                        'triton_id' => $triton_id, // indique l'id triton de la transaction
                        'amount'=> $amount, // indique le montant
                        'customer'=> $customer // indique l'utilisateur
                    ); // reporting du traitement

                }

            }else{

                echo "Numero non reconnu";

                $report = array(
                    'error' => true, // indique si il y a erreur : true = il y a erreur, false = il n'y a apas d'erreur
                    'error_code' => '02', // code de l'erreur ; 00 = aucune erreur, 01 = probleme interne, 02 = numero inexistant, 03 = montant incorrect , 04 = empty data
                    'error_reason' => 'fail : unknown customer', // indique la raison de l'erreur
                    'payplus_id' => $payplus_id, // indique l'id payplus de la transaction
                    'triton_id' => $triton_id, // indique l'id triton de la transaction
                    'amount'=> $amount, // indique le montant
                    'customer'=> $customer // indique l'utilisateur
                ); // reporting du traitement

            }

        } else{
            $message="Vérifier les différents Champs!";
            echo utf8_decode($message);

            $report = array(
                'error' => true, // indique si il y a erreur : true = il y a erreur, false = il n'y a apas d'erreur
                'error_code' => '04', // code de l'erreur ; 00 = aucune erreur, 01 = probleme interne, 02 = numero inexistant, 03 = montant incorrect , 04 = empty data
                'error_reason' => 'fail : empty data', // indique la raison de l'erreur
                'payplus_id' => $payplus_id, // indique l'id payplus de la transaction
                'triton_id' => $triton_id, // indique l'id triton de la transaction
                'amount'=> $amount, // indique le montant
                'customer'=> $customer // indique l'utilisateur
            ); // reporting du traitement

        }
    }else{

        $report = array(
            'error' => true, // indique si il y a erreur : true = il y a erreur, false = il n'y a apas d'erreur
            'error_code' => '03', // code de l'erreur ; 00 = aucune erreur, 01 = probleme interne, 02 = numero inexistant, 03 = montant incorrect , 04 = empty data
            'error_reason' => 'fail : incorrect amount', // indique la raison de l'erreur
            'payplus_id' => $payplus_id, // indique l'id payplus de la transaction
            'triton_id' => $triton_id, // indique l'id triton de la transaction
            'amount'=> $amount, // indique le montant
            'customer'=> $customer // indique l'utilisateur
        ); // reporting du traitement

    }

     $payplus_server_url =  "https://app.payplus.africa/payplus-api/v01/tritonpay/deposit_callback";
     BackgroundsendPostData($payplus_server_url, $report);
     // return json_encode($report); // pas necessaire, sauf si vous comptez en faire usage
 }


// function d'envoi de requete en backGround : basee sur cURL
function BackgroundsendPostData($url, array $post, $return = false, $headers = array()){
    $data = "";
    foreach ($post as $key => $row) {
        $row = urlencode($row); //fix the url encoding
        $key = urlencode($key); //fix the url encoding
        if ($data == "") {
            $data .= "$key=$row";
        } else {
            $data .= "&$key=$row";
        }
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    if(count($headers) > 0){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $dossier = "curl/" . gmdate("Y-m-d");
    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }
    $fileHandle = fopen($dossier . "/log-" . gmdate("H-i-s") . ".txt", "w+");

    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_STDERR, $fileHandle);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    if ($return == false) {
        return;
    } else {
        return $result;
    }
}


