<?php
require_once("../../../../../sites/default/sqlconf.php");
/* require  'Model.php';
$wearables =  Model::get("pghd_wearable",[],true);   
 */
 $conn= new mysqli($host,$login,$pass,$dbase);
 if($conn){
//  echo "connected";
 }else{
    //echo "not connected";
 }
?>