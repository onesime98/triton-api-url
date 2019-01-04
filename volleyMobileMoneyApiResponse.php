<?php
/*
*
* API URL // Mobile Money API (KyberLabs)
*
*/

date_default_timezone_set('Africa/Porto-Novo');

// function log
function addLogEvent($event) {

    $time = date("D, d M Y H:i:s");
    $time = "[".$time."] ";
 
    $event = $time.$event."\n";
 
    file_put_contents("fichier.log", $event, FILE_APPEND);
}

// librairies utiles
require 'vendor/autoload.php';
// urls de l'api de eazypay
$request_url = "https://app.eazypay.org/pay/v01/straight/checkout-invoice/create";
$confirm_url = "https://app.eazypay.org/pay/v01/straight/checkout-invoice/confirm";
// cles d'authentification de Triton
$authorization = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZF9hcHAiOiI0IiwiaWRfYWJvbm5lIjoiNCIsImRhdGVjcmVhdGlvbl9hcHAiOiIyMDE4LTEwLTE3IDE2OjE4OjM5In0.gXTEU9MGHHK90Lwx5PP3Xz6mTiD3yu14K2pQbOOCAtI";
$apikey = "0XWI6BB012NPRSHM5";
// creation du client
$client = new \GuzzleHttp\Client();

$today = date("Y-m-d H:i:s"); 

if($_SERVER['REQUEST_METHOD']=='POST') {

     require_once('dbConnect.php');

    // details de la transaction
    $token = $_POST['token']; // token de la transaction
    $customer = "229".$_POST['customer']; // numero du client qui veut recharger
    $amount = $_POST['amount']; // montant de la recharge
    $customerbd = $_POST['customer']; // numero du client conforme à la base de données
     

    if(!empty($token)){
	
         addLogEvent("Token de la transaction reçue : $token");

         try{
            $res = $client->request('POST', $confirm_url."?invoiceToken=".$token, array(
                 'base_uri' => ''.$confirm_url,
                 'headers' => [
                     'Accept' => 'application/json',
                     'Content-type' => 'application/json',
                     'Authorization' => 'Bearer '.$authorization,
                     'Apikey' => ''.$apikey,
                 ],
                 'verify' => false,
                 'exceptions' => false
             ));

             switch ($res->getStatusCode()){
                 case "200":
                     
                     addLogEvent("Case 200 : $token");
                     
                     $response = json_decode($res->getBody(), true);

                     if($response["status"] == "completed"){

                         $sql = "SELECT numero_telephone FROM triton_utilisateur WHERE numero_telephone = '$customerbd' AND etat = '1' ";
                         $result = mysqli_query($con,$sql);
                         $check = mysqli_fetch_array($result);

                         if(isset($check)){
                                                         
                            $sql4 = "SELECT * FROM triton_wallet_utilisateur WHERE id_utilisateur = '$customerbd' ";
                            $result_ = mysqli_query($con,$sql4);
                            $check_ = mysqli_fetch_array($result_);
                            $solde_wallet= $check_[2];

                            $montant_apres = $solde_wallet + $amount;

                            $sql3 = "UPDATE triton_wallet_utilisateur SET solde = '$montant_apres' WHERE id_utilisateur = '$customerbd'";

                            $sql2 = "INSERT INTO triton_wallet_depot (id_wallet_utilisateur, solde_depot, solde_avant, solde_apres, cree_le, modifier_le, etat) VALUES 
                                    ('$customerbd','$amount', '$solde_wallet','$montant_apres', '$today', '$today', 1)";

                             if(mysqli_query($con,$sql2) AND mysqli_query($con,$sql3)){
                                echo "Compte rechargé avec succès";
                                addLogEvent("Compte rechargé avec succès : $token");
                             }else{
				echo "Erreur";
                             }
                         }
                     }

                     echo json_encode($response);
                     break;
                 default:
                     $response = array(
                         "response_code"=>"01",
                         "token"=>"",
                         "montant"=>"",
                         "response_text"=>"Une erreur est est survenue lors de l'envoi de la requete.",
                         "status"=>"",
                         "custom_data"=>"",
                         "operator_id"=>"",
                         "operator_name"=>""
                     );
                     echo json_encode($response);
                     break;
             }

         }catch (Exception $e){
             $response = array(
                 "response_code"=>"01",
                 "token"=>"",
                 "montant"=>"",
                 "response_text"=>"Une erreur est est survenue lors de l'envoi de la requete.",
                 "status"=>"",
                 "custom_data"=>"",
                 "operator_id"=>"",
                 "operator_name"=>""
             );
             echo json_encode($response);
         }
     }else{
         $response = array(
             "response_code"=>"01",
             "token"=>"",
             "montant"=>"",
             "response_text"=>"Token vide",
             "status"=>"",
             "custom_data"=>"",
             "operator_id"=>"",
             "operator_name"=>""
         );
         echo json_encode($response);
     }

 }

