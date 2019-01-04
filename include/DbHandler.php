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

   

    /*******  Wallet Depot Historique  *********/
        
    public function getWalletDepotHistorique($numero_telephone){
    
        $stmt = $this->conn->prepare("SELECT solde_depot, cree_le 
                                      FROM triton_wallet_depot 
                                      WHERE id_wallet_utilisateur = '$numero_telephone' 
                                      AND etat = '1' ORDER BY id_wallet_depot DESC LIMIT 5");       
        $listDepot=array();
        
            if ($stmt->execute()) {
                $stmt->bind_result($solde_depot, $cree_le);
                $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                while($stmt->fetch() ){
                    array_push($listDepot,array(      
                        'solde_depot'=>$solde_depot,
                        'cree_le'=>$cree_le
                    )
                );
            }                               
                
                   $stmt->close();
                
                   return $listDepot;
                
            }else{
              return NULL;
            }
            }else {
              return NULL;
            }
 
    }

    /*******  Wallet Opération Historique  *********/
        
    public function getWalletOperationHistorique($numero_telephone){
    
        $stmt = $this->conn->prepare("SELECT triton_service.nom, triton_service.montant, triton_wallet_operation.cree_le 
                                      FROM triton_wallet_operation, triton_service 
                                      WHERE triton_wallet_operation.id_service = triton_service.id_service
                                      AND triton_wallet_operation.etat = '1'
                                      AND triton_service.etat = '1'
                                      AND id_wallet_utilisateur = '$numero_telephone' ORDER BY id_wallet_operation DESC LIMIT 5");       
        $listDepot=array();
        
            if ($stmt->execute()) {
                $stmt->bind_result($service, $montant, $cree_le);
                $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                while($stmt->fetch() ){
                    array_push($listDepot,array(      
                        'nom'=>$service,
                        'montant'=>$montant,
                        'cree_le'=>$cree_le
                    )
                );
            }                               
                
                   $stmt->close();
                
                   return $listDepot;
                
            }else{
              return NULL;
            }
            }else {
              return NULL;
            }
 
    }

    /*******  Wallet Soldee  *********/
        
    public function getWalletSolde($numero_telephone){
    
        $stmt = $this->conn->prepare("SELECT solde, triton_utilisateur.nom_prenom 
                                      FROM triton_wallet_utilisateur, triton_utilisateur 
                                      WHERE triton_wallet_utilisateur.id_wallet_utilisateur = triton_utilisateur.numero_telephone
                                      AND triton_wallet_utilisateur.etat = '1'
                                      AND triton_utilisateur.etat = '1'
                                      AND id_wallet_utilisateur = '$numero_telephone' ORDER BY id_wallet_utilisateur DESC LIMIT 5");       
        $listDepot=array();
        
            if ($stmt->execute()) {
                $stmt->bind_result($solde, $nom_prenom);
                $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                while($stmt->fetch() ){
                    array_push($listDepot,array(      
                        'solde'=>$solde,
                        'nom_prenom'=>utf8_decode($nom_prenom)
                    )
                );
            }                               
                
                   $stmt->close();
                
                   return $listDepot;
                
            }else{
              return NULL;
            }
            }else {
              return NULL;
            }
 
    }

		
	
	
}
?>
