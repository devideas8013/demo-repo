<?php
header("Access-Control-Allow-Origin: *");

$SERVER_URL = $_SERVER['SERVER_NAME'];

if($SERVER_URL==""){
  echo "Server URL error";
  return;
}

define("ACCESS_SECURITY","true");
include '../../../api.'.$SERVER_URL.'/security/config.php';
include 'manage-payment-records.php';

// constants
date_default_timezone_set('Asia/Kolkata');
$temp_from_date = date('d-m-Y')." 12:00:00 AM";
$temp_to_date = date('d-m-Y')." 11:59:59 PM";

$pg_from_date = strtotime($temp_from_date)*1000;
$pg_to_date = strtotime($temp_to_date)*1000;

$resArr = array();
$resArr['data'] = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

if(isset($_POST['USER_ID']) && isset($_POST['ORDER_ID']) && isset($_POST['AMOUNT'])){
  $pay_user_id = mysqli_real_escape_string($conn,$_POST["USER_ID"]);
  $pay_amount = mysqli_real_escape_string($conn,$_POST["AMOUNT"]);
  $pay_order_id = mysqli_real_escape_string($conn,$_POST["ORDER_ID"]);
  $pay_utr_code= mysqli_real_escape_string($conn,$_POST["UTR_CODE"]);
  $payee_name= mysqli_real_escape_string($conn,$_POST["PAYEE_NAME"]);
}

if(isset($_GET['USER_ID']) && isset($_GET['ORDER_ID']) && isset($_GET['AMOUNT'])){
  $pay_user_id = mysqli_real_escape_string($conn,$_GET["USER_ID"]);
  $recharge_amount = mysqli_real_escape_string($conn,$_GET["AMOUNT"]);
  $pay_order_id = mysqli_real_escape_string($conn,$_GET["ORDER_ID"]);   
  $pay_utr_code= mysqli_real_escape_string($conn,$_GET["UTR_CODE"]);
  $payee_name= mysqli_real_escape_string($conn,$_GET["PAYEE_NAME"]);
}

$resArr['status_code'] = "404"; 

if($pay_utr_code==""){
    $pay_utr_code = "0";
}

// initialize payment object
$paymentObj = new ManagePayments($conn,$pay_user_id,$recharge_amount,
$pay_order_id,$payee_name,$pay_utr_code);

$pgVarsResponse = $paymentObj -> getPGVars();
$pgVarsJsonArr = json_decode($pgVarsResponse,true);

$pg_merchant = $pgVarsJsonArr['pg_merchant'];
$bp_token = $pgVarsJsonArr['pg_token'];
$bp_cookie = $pgVarsJsonArr['pg_cookie'];

// check if utr code passed or not
if($pay_utr_code!="0"){
  $referenceResponse = $paymentObj -> checkReferenceNum();
}else{
 $referenceResponse = $paymentObj -> checkRechargeByName();   
}

$referenceJsonArr = json_decode($referenceResponse,true);

if($referenceJsonArr['response_code']=="success"){
  $resArr['payment_details'] = $referenceJsonArr['data'];  

  $paymentResponse = $paymentObj -> makeRechargeSuccess();
  $resArr['status_code'] = $paymentResponse;
}else if($referenceJsonArr['response_code']=="conflict"){
  $resArr['status_code'] = "conflict";
}else{
  
  $validateResponse = $paymentObj -> validateNewPayment($pg_merchant,$bp_token,$bp_cookie,$pg_from_date,$pg_to_date);
  $validateJsonArr = json_decode($validateResponse,true);

  if($validateJsonArr['response_code']=="success"){
    if($pay_utr_code!="0"){
      $referenceResponse = $paymentObj -> checkReferenceNum();
    }else{
      $referenceResponse = $paymentObj -> checkRechargeByName();   
    }  
    
    $referenceJsonArr = json_decode($referenceResponse,true);
  
    if($referenceJsonArr['response_code']=="success"){
      $resArr['payment_details'] = $referenceJsonArr['data'];  
    
      $paymentResponse = $paymentObj -> makeRechargeSuccess();
      $resArr['status_code'] = $paymentResponse;
    }else if($referenceJsonArr['response_code']=="conflict"){
      $resArr['status_code'] = "conflict";
    }
  }

}

echo json_encode($resArr);
}