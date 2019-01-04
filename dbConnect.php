<?php
/*
*
* Connexion à la base de données (KyberLabs)
*
*/
 define('HOST','localhost');
 define('USER','u345668690_trito');
 define('PASS','triton_inc@');
 define('DB','u345668690_trito');
 
 $con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');
