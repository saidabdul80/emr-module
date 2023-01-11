<?php
header('Content-type: application/json');

require_once("../../../../globals.php");

$param = array();
parse_str($_SERVER['QUERY_STRING'],$param);

$source ='';
$from ='';
$to ='';
$byDate ='';
$pid ='';
if(isset($param['source'])){
    $source = $param['source'];
}
if(isset($param['from'])){
    $from = $param['from'];
}
if(isset($param['to'])){
    $to = $param['to'];
}
if(isset($param['byDate'])){
    $byDate = $param['byDate'];
}
if(isset($param['pid'])){
    $pid = $param['pid'];
}

if($param['source']=='fitbit'){
    
    if($byDate == 1){
        $res = sqlStatement("SELECT * FROM pghd_observation WHERE effective >= ?  AND effective <= ? AND `subject` = ? ORDER BY effective ASC ",[$from,$to,$pid]);
        $data = [];
        while($row = sqlFetchArray($res)){
            $data[] = $row;
        }
        $sort_data =[];
        foreach($data as $key => $d){
            $sort_data[$d['name']][] = $d;
        }        
    }else{
        $res = sqlStatement("SELECT * FROM pghd_observation WHERE  `subject` = ? ORDER BY effective ASC ",[$pid]);
        $data = [];
        while($row = sqlFetchArray($res)){
            $data[] = $row;
        }
        $sort_data =[];
        foreach($data as $key => $d){
            if($d['name'] == 'Heart Rate'){
                $sort_data['heart_rate']["rmssd"][] = json_decode($d["value"])?->rmssd??0;    
                $sort_data['heart_rate']["coverage"][] = json_decode($d["value"])?->coverage??0;    
                $sort_data['heart_rate']["low_frequency"][] = json_decode($d["value"])?->low_frequency??0;    
                $sort_data['heart_rate']["high_frequency"][] = json_decode($d["value"])?->high_frequency??0;                                    
                $sort_data['heart_rate']["date"] = $d["effective"];                                    
            }else{
                $sort_data[$d['name']][] = $d;
            }
        }    
    }
    http_response_code(200);
    echo json_encode($sort_data);
   
}