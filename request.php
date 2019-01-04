<?php
require 'vendor/autoload.php';

// initialize Guzzle client
$client = new \GuzzleHttp\Client();

// urls
$request_url = "https://app.eazypay.org/pay/v01/straight/checkout-invoice/create";

// keys
$authorization = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZF9hcHAiOiI0IiwiaWRfYWJvbm5lIjoiNCIsImRhdGVjcmVhdGlvbl9hcHAiOiIyMDE4LTEwLTE3IDE2OjE4OjM5In0.gXTEU9MGHHK90Lwx5PP3Xz6mTiD3yu14K2pQbOOCAtI";
$apikey = "0XWI6BB012NPRSHM5";

// details de la recharge
$montant = 350; // montant que la personne veut recharger
$numero = "22997761182"; // le numero doit toujours etre dans le format 229xxxxxxxx
$commande = array(
    "commande" => array(
        "invoice" => array(
            "items" => array(
                array(
                    "name" => "Recharge TritonPay",
                    "quantity" => 1,
                    "unit_price" => "".$montant,
                    "total_price" => "".$montant
                )
            ),
            "total_amount" => "".$montant,
            "devise" => "xof",
            "description" => "Recharge TritonPay",
            "customer" => "".$numero
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
//$commande = json_encode($commande);


try{
    // Create a POST request
    $res = $client->request('POST', $request_url, array(
        'base_uri' => 'http://74.208.84.251:8221',
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
            echo print_r($response);

            // format de la reponse
            /*

            array(
                    "response_code"=>"",
                    "token"=>"",
                    "response_text"=>"".$e->getMessage(),
                    "description"=>"",
                    "customdata"=>""
                );

            si le champ response_code == "00" alors succes
            sinon echec

            si l'operation est un succes, tu envoies la valeur du champ "token" a l'application mobile

            */

            break;
        default:

            break;
    }

}catch (Exception $e){
    echo $e->getMessage();
}
