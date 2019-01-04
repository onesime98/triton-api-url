<?php
/*
*
* Script (KyberLabs)
*
*/

 
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

   

    /*******  Wallet Solde  *********/
        
    public function getWalletSolde($code_connexion){
    
        $stmt = $this->conn->prepare("SELECT triton_wallet_centre_lavage.solde, triton_centre_lavage.nom
                                      FROM triton_wallet_centre_lavage, triton_centre_lavage
                                      WHERE triton_wallet_centre_lavage.id_centre_lavage = triton_centre_lavage.id_centre_lavage
                                      AND triton_centre_lavage.code_connexion='$code_connexion' 
                                      AND triton_wallet_centre_lavage.etat = '1'
                                      AND triton_centre_lavage.etat = '1' ");       
        $listDepot=array();
        
            if ($stmt->execute()) {
                $stmt->bind_result($solde, $nom);
                $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                while($stmt->fetch() ){
                    array_push($listDepot,array(      
                        'solde'=>$solde,
                        'nom'=> utf8_decode($nom)
                    )
                );
            }                               
                
                   $stmt->close();
                
                   return $listDepot;
                
            }else{
              return NULL;
            }

            }else{
              return NULL;
            }
 
    }

    /*******  Wallet Opération  *********/
        
    public function getWalletOperationHistorique($code_connexion){
    
        $stmt = $this->conn->prepare("SELECT triton_service.nom, triton_wallet_operation.solde_recu_centre_lavage, triton_wallet_operation.appreciation, triton_wallet_operation.cree_le
                                      FROM triton_service, triton_wallet_operation, triton_wallet_centre_lavage, triton_centre_lavage 
                                      WHERE triton_service.id_service = triton_wallet_operation.id_service 
                                      AND triton_wallet_operation.id_wallet_centre_lavage = triton_wallet_centre_lavage.id_wallet_centre_lavage 
                                      AND triton_centre_lavage.id_centre_lavage = triton_wallet_centre_lavage.id_wallet_centre_lavage 
                                      AND triton_centre_lavage.code_connexion='$code_connexion' 
                                      AND triton_service.etat = '1'
                                      AND triton_wallet_operation.etat = '1'
                                      AND triton_wallet_centre_lavage.etat = '1'
                                      AND triton_centre_lavage.etat = '1' ORDER BY triton_wallet_operation.id_wallet_operation DESC ");       
        $listDepot=array();
        
            if ($stmt->execute()) {
                $stmt->bind_result($nom_service, $solde_recu, $appreciation, $cree_le);
                $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                while($stmt->fetch() ){
                    array_push($listDepot,array(      
                        'nom'=> utf8_decode($nom_service),
                        'solde_recu_centre_lavage'=>$solde_recu,
                        'appreciation'=> utf8_decode($appreciation),
                        'cree_le'=>$cree_le
                    )
                );
            }                               
                
                   $stmt->close();
                
                   return $listDepot;
                
            }else{
              return NULL;
            }

            }else{
              return NULL;
            }
 
    }

		
	
	
}
?>
