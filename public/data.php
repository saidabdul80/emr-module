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
$type='Heart Rate';
$date='';
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

if(isset($param['type'])){
    $type = $param['type'];
}

if(isset($param['date'])){
    $date = $param['date'];
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
        if($type == 'Heart Rate'){
            if($date != ''){                
                $res = sqlStatement("SELECT * FROM pghd_observation WHERE  `subject` = ? AND `name`=? AND effective = ? ",[$pid, $type, $date]);
            }else{
                $res = sqlStatement("SELECT * FROM pghd_observation WHERE  `subject` = ? AND `name`=? ORDER BY effective DESC LIMIT 1",[$pid,$type]);
            }
            $data = [];
            $row = sqlFetchArray($res);          
            
            $sort_data =[];
            $obj = json_decode($row["value"]);    
            
            $sort_data['bpm'] = [];
            $sort_data['confidence'] = [];
            foreach($obj as $key => $obj) {
                $sort_data['bpm'][] = $obj->value->bpm ;
                $sort_data['dateTime'][] = date("Y-m-d H:i:s",strtotime($obj->dateTime)) ;
                $sort_data['confidence'][] = $obj->value->confidence;                
            }
            $sort_data["date"] =date('Y-m-d',strtotime($row["effective"]));                                                
            
        }
    }
    http_response_code(200);
    echo json_encode($sort_data);
   
}