<?php
namespace Prepare;

class FitbitPrepare
{   
    private $data;
    private $conn;    
    private $emr_data;
    private $patient;
    public function __construct($data, $connx, $patient){
        $this->data =json_decode($data);
        $this->conn =  $connx;                
        $this->patient = $patient;
        $this->emr_data = "INSERT INTO pghd_observation (`identifier`,`subject`,`code`,`value`,`effective`,`device`,`name`) VALUES ";     
    }

    private function code($type)
    {
        $code = array(
            "heartrate" => [
                'system' => 'http://snomed.info/sct',
                'code' => 'SNOMED CT-46680005',
                'display' => 'Heart rate'
            ],
            "sleep" => [
                'system' => 'http://snomed.info/sct',
                'code' => 'SNOMED CT-46680005',
                'display' => 'Heart rate'
            ],
        );
        return $code[$type];
    }

    public function sleep($system){        
        $array = [];   
      
        foreach($this->data->sleep as $sleep){
            $value = round($sleep->duration/3600000,2);
            $date = $sleep->dateOfSleep;
            $code = $this->code('sleep');
            $array[] = [
                "value"=> $value,
                "unit"=>'hrs',
                "system"=> $system,
                "issued"=>$date,
                "code"=>$code
            ];              
            $uuid = $this->patient['uuid'];
            $pid = $this->patient['id'];
            $ncode = json_encode($code);
           $this->emr_data .=" ('$uuid',$pid,'$ncode',$value,'$date','fitbit','sleep'), ";
        }
        $this->emr_data = substr(rtrim($this->emr_data), 0, -1);        
        $run = $this->conn->query($this->emr_data);           
        return $array; 
    }
    
    public function heartrate(){
        
    }
}
    
?>