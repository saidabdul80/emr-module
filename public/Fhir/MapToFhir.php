<?php

namespace Fhir;
require 'MapToFhirInterface.php';

use Carbon\Carbon;
use Fhir\MapToFhirInterface;
use GuzzleHttp\Client;
use Observable;

class MapToFhir implements MapToFhirInterface
{
    private $responses;
    private $observation;    
    private $wearable;
    private $patient_id;
    private $http;
    private $fhir_base_url;
    private $res;
    private $resCode;
    public function __construct($fhir_base_url,$patient, $response, $wearable = "fitbit")
    {
        $this->responses = $response;        
        $this->wearable = $wearable;
        $this->fhir_base_url  = $fhir_base_url;
        $this->res;
        $this->resCode = 0;
        $this->http =  new  \GuzzleHttp\Client([
            'headers' => [
                'Accept' => 'application/json',
                'base_uri' =>str_replace(' ','',$fhir_base_url)
            ]
        ]);
    }
    public function makeRequest($route,$method){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->fhir_base_url.$route,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,        
        CURLOPT_USERAGENT=> 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',         
        CURLOPT_HTTPHEADER => array(            
            "Accept: application/fhir+json",
            "Content-Type: application/fhir+json",
        ),
        ));        
        $this->resCode = curl_getinfo($curl);
        $this->res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);         
    }

    public function makeRequestPost($route,$data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->fhir_base_url.$route,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,        
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,        
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_POST => 1,
            CURLOPT_VERBOSE => true,
            CURLOPT_HTTPHEADER => array(            
                "Accept: application/fhir+json",
                "Content-Type: application/fhir+json",
            ),
            CURLOPT_HEADER => true, // include response headers
        ));        
        $this->resCode = curl_getinfo($curl);
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $this->res = json_decode(substr($response, $header_size));
        $err = curl_error($curl);
        curl_close($curl);  
    }
    public function createPatienIfNotExists($patient)
    {
        
        $this->makeRequest('Patient?identifier='. $patient['uuid'], 'GET');        
        
        if ($this->resCode ==404 or $this->res->resourceType =='OperationOutcome') {
            // Assume $patient is an array representing the OpenEMR patient record
            $fhir_patient = [
                'resourceType' => 'Patient',
                'id'=>$patient['uuid'],
                'identifier' => [
                    'value' => $patient['uuid'],                    
                ],                                   
                'name' => 
                    [
                        "name" =>$patient['lname']. ' '.$patient['fname'],
                        'family' => $patient['lname'],
                        'given' => $patient['fname'],
                    ],                
                'telecom' =>[
                    'system' => 'phone',
                    'value' => $patient['phone_home'],
                    'use' => 'home',
                ],
                'gender' => $patient['sex'] == 'Male' ? 'male' : 'female',
                'birthDate' => $patient['DOB'],
                'address' => array(
                    array(
                        'line' => array($patient['street']),
                        'city' => $patient['city'],
                        'state' => $patient['state'],
                        'postalCode' => $patient['postal_code'],
                        'country' => $patient['country'],
                        'type' => 'postal',
                    ),
                )
                ];

            $this->makeRequestPost('Patient',json_encode($fhir_patient));            
        }
    }

  

    public function mapToObservation($patient)
    {
        $status = 'final';        
        //$data = [];             
        foreach($this->responses as $response){
            $data= [
                'resourceType' => 'Observation',
                'status' => $status,
                "valueQuantity"=>[
                    'value' => $response['value'],
                    'unit' => $response['unit'],
                    'system' => $response['system']
                ],
                "subject" => [
                    "reference" => "Patient?identifier=" . $patient['uuid']
                ],
                "code" => $response['code'],
                "device" => [
                    "type" =>
                    ["text" => $this->wearable]
                ],          
                "effectiveDateTime"=>Carbon::parse($response['issued'])->format('Y-m-d'),
                "issued" =>Carbon::parse($response['issued'])->format('Y-m-d')
            ];
            $this->makeRequestPost('Observation/',json_encode($data));        
            sleep(2);
        }
        //$observation = new Observation($status, $code, $valueQuantities, $issued, $subject, $device);        
        
        echo json_encode($this->res);
    }
}

class Observation
{
    public $status;
    public $code;
    public $valueQuantity;
    public $issued;
    public $subject;
    public $device;
    public $resourceType;
    public function __construct($status, $code, $valueQuantity, $issued, $subject, $device)
    {
        $this->resourceType = "Observation";
        $this->status = $status;
        $this->code = $code;
        $this->valueQuantity = $valueQuantity;
        $this->issued = $issued;
        $this->subject = $subject;
        $this->device = $device;
    }
}
