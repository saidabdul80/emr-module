<?php

header('Content-type: application/json');
require '../vendor/autoload.php';
require 'conn.php';
require 'Prepare/FitbitPrepare.php';
require 'Fhir/MapToFhir.php';
$param = array();
parse_str($_SERVER['QUERY_STRING'], $param);

use \Carbon\Carbon;
use Prepare\FitbitPrepare;
require_once("../../../../globals.php");
use Fhir\MapToFhir;
use GuzzleHttp\Client;
use OpenEMR\Services\PatientService;
$pService = new PatientService;

$id = $param['id']??"";
$type = $param['type']??"";
$startDate = $param['startDate']??"";
$endDate =   $param['endDate']??"";
$wearable = $param['wearable']??"";
$pid = $param['pid']??"";

$result = $conn->query("SELECT * FROM `pghd_tokens` where `id`= $id");
$tokens = json_decode(json_encode($result->fetch_assoc()));

$result = $conn->query("SELECT * FROM `pghd_auth` where `id`= 1");
$fhir_base_url = json_decode(json_encode($result->fetch_assoc()))?->base_url;

$result = $pService->getOne($pid);
$patient = $result->getData()[0];

//checking last data date 
//however this has some limitations, like it can't resolve gap between dates if there is.
$result = $conn->query("SELECT * FROM `pghd_observation` where `subject`='$tokens->pid' and `name`='$type' order by effective desc limit 1");
if($result->num_rows>0){
    $observations = json_decode(json_encode($result->fetch_assoc()));
    $startDate = Carbon::parse($observations->effective)->addDay();
    /* if(Carbon::parse($startDate)->isBefore(Carbon::parse($observations))){
        $endDate = Carbon::parse($observations->effective)->subDay();
    }else{
    } */
}

$result = $conn->query("SELECT * FROM `pghd_wearable_apis` where `category`='$wearable' and `name`='$type'");
$apis = json_decode(json_encode($result->fetch_assoc()));
$keys = array(
    "startDate" =>$startDate,
    "endDate" =>$endDate,
    "userID" =>$tokens->user_id
);
foreach($keys as $key=>$value){
    $apis->endpoint =  str_replace($key,$value,$apis->endpoint);
}

$responseMessageFormat =[
    "error"=>false,
    "message"=>""
   ];

$http =  new Client([
    'headers' => [
        'Accept' => 'application/json',
        "Authorization"=>"Bearer $tokens->access_token"
    ]
]);
//echo $apis->endpoint;
try{
    $response = $http->get($apis->endpoint);
    $response = $response->getBody();    
}catch(\GuzzleHttp\Exception\RequestException $e){
    $statusCode = $e->getResponse()->getStatusCode();
    $body = $e->getResponse()->getBody();
    $responseMessageFormat['error'] = true;
    $responseMessageFormat['message'] = $body;
    echo json_encode($responseMessageFormat);
    exit();
}

if (strpos($apis->endpoint, 'fitbit.com') !== false) {             
    
    try{
    $prepare = new FitbitPrepare($response, $conn,$patient);                     
    $fhir = new MapToFhir($fhir_base_url,$patient,$prepare->{$type}('https://fitbit.com'),'fitbit', $type);
    $fhir->createPatienIfNotExists($patient);
    $fhir->mapToObservation($patient); 
    }catch(\Exception $e){
        $responseMessageFormat["error"] = true;
        $responseMessageFormat["message"] = $e->getMessage(); 
    }
    //post t        
} elseif (strpos($apis->endpoint, 'googleapis.com') !== false) {
    //$error_response = json_decode($response, true);
    //$error = $error_response['error'];
    //$code = $error['code'];
    //$message = $error['message'];
    //$status = $error['status'];
    // Handle the Google Fit error as needed
} else {
    // Handle errors for other APIs or unknown endpoints
}




echo json_encode($responseMessageFormat);
?>