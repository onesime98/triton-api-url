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

 if($_SERVER['REQUEST_METHOD']=='POST'){ 
	
    require_once('dbConnect.php');

    // details de la transaction
	$customer = "229".$_POST['customer']; // numero du client qui veut recharger
 	$amount = $_POST['amount']; // montant de la recharge
        $customerbd = $_POST['customer']; // numero du client conforme à la base de données

	if(is_numeric($amount)){

        if(!empty($customer) AND !empty($amount)){

            $sql = "SELECT numero_telephone FROM triton_utilisateur WHERE numero_telephone = '$customerbd' AND etat = '1' ";
            $result = mysqli_query($con,$sql);
            $check = mysqli_fetch_array($result);

            if(isset($check)){

		addLogEvent("Vérification des informations effectuées : $customer");

                $commande = array(
                    "commande" => array(
                        "invoice" => array(
                            "items" => array(
                                array(
                                    "name" => "Recharge TritonPay",
                                    "quantity" => 1,
                                    "unit_price" => "".$amount,
                                    "total_price" => "".$amount
                                )
                            ),
                            "total_amount" => "".$amount,
                            "devise" => "xof",
                            "description" => "Recharge TritonPay",
                            "customer" => "".$customer
                        ),
                        "store" => array(
                            "name" => "Triton",
                            "website_url" => "https://www.triton-group.net/"
                        ),
                        "actions" => array(
                            "cancel_url" => "https://www.triton-group.net/",
                            "return_url" => "https://www.triton-group.net/",
                            "callback_url" => "https://www.triton-group.net/"
                        ),
                        "custom_data" => array(
                            "rubrique" => "valeur_de_la_rubrique"
                        )
                    )
                );
                try{
                    // Create a POST request
                    $res = $client->request('POST', $request_url, array(
                        'base_uri' => ''.$request_url,
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json',
                            'Authorization' => 'Bearer '.$authorization,
                            'Apikey' => ''.$apikey,
                        ],
                        'json' => $commande,
                        'verify' => false,
                        'exceptions' => false
                    ));

                    switch ($res->getStatusCode()){
                        case "200":
                            $response = json_decode($res->getBody(), true);
                            $response["customer"] = "".$customer;

                            echo json_encode($response);
                            break;
                        default:
                            $response = array(
                                "response_code"=>"01",
                                "token"=>"",
                                "response_text"=>"Une erreur est est survenue lors de l'envoi de la requete.",
                                "description"=>"",
                                "customdata"=>"",
                                "customer"=>"".$customer
                            );
                            echo json_encode($response);
                            break;
                    }

                }catch (Exception $e){
                    $response = array(
                        "response_code"=>"01",
                        "token"=>"",
                        "response_text"=>"Une erreur est est survenue lors de l'envoi de la requete.",
                        "description"=>"",
                        "customdata"=>"",
                        "customer"=>"".$customer
                    );
                    echo json_encode($response);
                }
            }else{
                $response = array(
                    "response_code"=>"01",
                    "token"=>"",
                    "response_text"=>"Numero non reconnu",
                    "description"=>"",
                    "customdata"=>"",
                    "customer"=>"".$customer
                );
                echo json_encode($response);
            }
        } else{
            $response = array(
                "response_code"=>"01",
                "token"=>"",
                "response_text"=>"Numero ou Montant incorrect",
                "description"=>"",
                "customdata"=>"",
                "customer"=>"".$customer
            );
            echo json_encode($response);
        }
    }else{
        $response = array(
            "response_code"=>"01",
            "token"=>"",
            "response_text"=>"Erreur au niveau du numero!",
            "description"=>"",
            "customdata"=>"",
            "customer"=>"".$customer
        );
        echo json_encode($response);
    }

 }