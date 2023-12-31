<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include '../../../api/security/config.php';
include '../../../api/security/access_codes.php';
include 'manage-payment-records.php';
require_once("../../../api/services/send-notification-to-admin.php");

$resArr = array();
$resArr['data'] = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

if(isset($_POST['USER_ID']) && isset($_POST['AMOUNT'])){
  $pay_user_id = mysqli_real_escape_string($conn,$_POST["USER_ID"]);
  $pay_amount = mysqli_real_escape_string($conn,$_POST["AMOUNT"]);
  $pay_utr_code= mysqli_real_escape_string($conn,$_POST["UTR_CODE"]);
}

if(isset($_GET['USER_ID']) && isset($_GET['AMOUNT'])){
  $pay_user_id = mysqli_real_escape_string($conn,$_GET["USER_ID"]);
  $recharge_amount = mysqli_real_escape_string($conn,$_GET["AMOUNT"]);  
  $pay_utr_code= mysqli_real_escape_string($conn,$_GET["UTR_CODE"]);
}

$resArr['status_code'] = "404"; 

if($pay_utr_code==""){
    echo 'invalid param';
    return;
}

// initialize payment object
$paymentObj = new ManagePayments($conn,$pay_user_id,$recharge_amount,$pay_utr_code);

$pgVarsResponse = $paymentObj -> getPGVars();
$pgVarsJsonArr = json_decode($pgVarsResponse,true);

$bp_token = $pgVarsJsonArr['pg_token'];

// check if utr code passed or not
$referenceResponse = $paymentObj -> checkReferenceNum();

$referenceJsonArr = json_decode($referenceResponse,true);

if($referenceJsonArr['response_code']=="success"){
  $resArr['payment_details'] = $referenceJsonArr['data'];  

  $paymentResponse = $paymentObj -> makeRechargeSuccess();
  $resArr['status_code'] = $paymentResponse;
  sendNotification('New Recharge!','Automatic recharge of Rs.'.$recharge_amount.' using ZEEPay',$messageToken);
}else{
  
  $validateResponse = $paymentObj -> validateNewPayment($bp_token);
  $validateJsonArr = json_decode($validateResponse,true);
  $resArr['extra_data'] = $validateResponse;
  
  if($validateJsonArr['response_code']=="success"){
    $referenceResponse = $paymentObj -> checkReferenceNum();  
    
    $referenceJsonArr = json_decode($referenceResponse,true);
    $resArr['temp_data'] = $referenceJsonArr; 
        
    if($referenceJsonArr['response_code']=="success"){
      $resArr['payment_details'] = $referenceJsonArr['data'];  
    
      $paymentResponse = $paymentObj -> makeRechargeSuccess();
      $resArr['status_code'] = $paymentResponse;
      sendNotification('New Recharge!','Automatic recharge of Rs.'.$recharge_amount.' using ZEEPay',$messageToken);
    }
  }

}

echo json_encode($resArr);
}