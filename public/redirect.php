<?php
require '../vendor/autoload.php';
require 'HttpRequest.php';
require 'conn.php';

$state = explode('_', urldecode(base64_decode($_GET['state'])));
$code = $_GET['code'];
$patient_id = $state[0];
$wearable = $state[1];
$sql = "SELECT * FROM pghd_wearable WHERE wearable = '$wearable'";
$res = json_decode(json_encode($conn->query($sql)->fetch_assoc()));
$http = new HttpRequest($res->client_id, $res->secrete_key, $wearable);
$endpoints = json_decode($res->endpoints);
$token = json_decode($http->getAccessToken($endpoints->urltoken, $code));

/* $response = $provider->get('https://graph.facebook.com/v12.0/me?access_token=' . $accessToken);
 $userInfo = $response->getBody(); */
$date = date('Y-m-d h:i:s');
try {
    $result = $conn->query("SELECT * FROM pghd_tokens where pid='$patient_id' AND `name` = '$wearable'");
    if ($result->num_rows < 1) {
        $sql = "INSERT INTO `pghd_tokens`(pid,scope,access_token,`name`,refresh_token,user_id,token_type,created_at,updated_at) VALUES ('$patient_id','$token->scope','$token->access_token','$wearable','$token->refresh_token','$token->user_id','$token->token_type','$date','$date')";
        $run = $conn->query($sql);
    } else {  
        $stmt = $conn->query("UPDATE `pghd_tokens` SET `access_token`='$token->access_token', `refresh_token` = '$token->refresh_token', `scope`='$token->scope', `updated_at`='$date' WHERE pid= '$patient_id' and `name` = '$wearable' ");
        if(!$stmt){
            echo 'error';
            exit;
        }
    }
    
} catch (\Exception $e) {
    echo $e->getMessage();
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        localStorage.setItem('processed', '<?= $date ?>')
        showAlert()
        async function showAlert(){
            await Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 3500
            })
            window.close();
        }
    </script>
</body>

</html>