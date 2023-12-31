<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();
$resArr['data'] = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

$user_id = "";

if(isset($_POST['USER_ID'])){
 $user_id = mysqli_real_escape_string($conn,$_POST["USER_ID"]);   
}

if(isset($_GET['USER_ID'])){
 $user_id = mysqli_real_escape_string($conn,$_GET["USER_ID"]);   
}

$select_sql = "SELECT * FROM allbankcards WHERE user_id='$user_id' AND bank_card_primary='true' ";
$select_query = mysqli_query($conn,$select_sql);

if(mysqli_num_rows($select_query) <= 0){
    $resArr['status_code'] = "404";
}else{
    $resArr['status_code'] = "success";
    
    $res_data = mysqli_fetch_assoc($select_query);
 
    $index['c_beneficiary'] = $res_data['beneficiary_name'];
    $index['c_bank_name'] = $res_data['bank_name'];
    $index['c_bank_account'] = $res_data['bank_account'];
    $index['c_bank_ifsc_code'] = $res_data['bank_ifsc_code'];
    
    array_push($resArr['data'], $index);
}

  mysqli_close($conn);

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($resArr);
}
?>