<?php
use League\OAuth2\Client\Provider\GenericProvider;
class HttpRequest
{
    private $client_id;
    private $secrete_key;    
    private $wearable_type;
    private $redirectUri;

    function __construct($client_id,$secrete_key,$wearable_type='fitbit')
    {
        $this->client_id = $client_id;
        $this->secrete_key = $secrete_key;
        $this->redirectUri = "http://localhost/emr/openemr-7.0.0/pghd_redirect";
        $this->wearable_type =$wearable_type ;
    }    

    public function getAccessToken($url,$code){        

          // your redirect url
        $client_id = $this->client_id; // your client id from api
        $client_secret = $this->secrete_key;
        $code = $code;
        // if api require client id and client secret in paramters then below other wise
        $data = "grant_type=authorization_code&code=".$code."&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=".$this->redirectUri;
        
        $headers = array(
                'Content-Type:application/x-www-form-urlencoded',
        );
        
        // if api requires base64 and into -H header than 
        
        //$data = "grant_type=authorization_code&code=".$_GET['code']."&redirect_uri=".$url;
        $base64 = base64_encode($client_id.":".$client_secret);
        
       $headers = array(
           'Content-Type:application/x-www-form-urlencoded',
            'Content-Type:application/json',
            'Authorization: Basic '. $base64
        );
        
        
        
        function curlPost($data,$headers, $url){
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);  //Post Fields
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $server_output = curl_exec ($ch);
            
            return $server_output;
            curl_close ($ch);       
        }

        $response = curlPost($data,$headers,$url);
        return $response;
    }

}
