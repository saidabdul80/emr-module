<?php
header('Content-type: application/json');
require '../vendor/autoload.php';
require 'conn.php';
$param = array();
parse_str($_SERVER['QUERY_STRING'], $param);

use \Carbon\Carbon;
$date = date('Y-m-d h:i:s');
$currentDateTime = Carbon::parse($date);

$pid = '';
$object = false;
try {

    if (isset($param['pid'])) {
        $patient_id = $param['pid'];
        $name = $param['name'];
        $i = 1;
        
        while ($i < 10) {
            $i += 1;                      
            $result = $conn->query("SELECT * FROM `pghd_tokens` where `pid`='$patient_id' and `name` = '$name' ");
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                
                $dateTime = Carbon::parse($data['updated_at']);
                
                $isToday = $dateTime->isToday();                
                $isSameHour = $currentDateTime->hour === $dateTime->hour;
                if ($isToday && $isSameHour) {
                    $object = $data;
                    break;
                }
            }
           sleep(6);
        }
    }
    echo json_encode($object);
} catch (\Exception $e) {
    echo false;
}
