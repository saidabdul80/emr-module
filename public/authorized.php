<?php
require_once("../../../../globals.php");
require '../vendor/autoload.php';
use League\OAuth2\Client\Provider\GenericProvider;
session_start();
$patientId = $_SESSION['pid']== 0? $_GET['pid']:$_SESSION['pid'];
$wearablex = $_GET["wtype"];
$secrete_key = "";
$client_id = "";
$urlAuthorize ="";
$urlAccessToken ="";
$wearables = $_SESSION['wearables'];
foreach($wearables as $wearable){

    if($wearable->wearable == $wearablex){    
        $endpoint = json_decode($wearable->endpoints);
        $secrete_key = $wearable->secrete_key;
        $client_id = $wearable->client_id;
        $urlAuthorize = $endpoint->urlauthorize;
        $urlAccessToken = $endpoint->urltoken;
        break;
    }
}

$provider = new GenericProvider([
    'clientId'                => $client_id,
    'clientSecret'            => $secrete_key,
    'redirectUri'             => "http://localhost/emr/openemr-7.0.0/pghd_redirect",
    'urlAuthorize'            => $urlAuthorize,
    'urlAccessToken'          => $urlAccessToken,
    'urlResourceOwnerDetails' => 'https://api.fitbit.com/1.2/user/-/sleep/date/2020-01-01.json',
    'use_bearer_authorization'=>true,   
]);

$patientId = $_SESSION['pid'] ?? 0;

$authorizationUrl = $provider->getAuthorizationUrl([
    'state'=> base64_encode(urlencode($patientId.'_'.$_GET['wtype']))
]);

$authorizationUrl= $authorizationUrl.'&'.$state.'&scope=heartrate+sleep+activity';
/* if($patientId == 0){
    echo "please login to patient portal";
    die();
} */
header('Location: ' . $authorizationUrl);
?>
