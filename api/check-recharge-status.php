<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();
$resArr['payment_amount'] = "0";

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
 
 if(isset($_POST['USER_ID']) && isset($_POST['ORDER_ID'])){
   $user_id = mysqli_real_escape_string($conn,$_POST["USER_ID"]);
   $order_id = mysqli_real_escape_string($conn,$_POST["ORDER_ID"]);   
 }
 
  if(isset($_GET['USER_ID']) && isset($_GET['ORDER_ID'])){
   $user_id = mysqli_real_escape_string($conn,$_GET["USER_ID"]);
   $order_id = mysqli_real_escape_string($conn,$_GET["ORDER_ID"]);   
 }

 $pre_sql = "SELECT recharge_amount,request_status FROM usersrecharge WHERE uniq_id='{$order_id}' AND user_id='{$user_id}' ";
 $pre_result = mysqli_query($conn, $pre_sql) or die('error');
 $pre_res_data = mysqli_fetch_assoc($pre_result);

 if (mysqli_num_rows($pre_result) > 0){
  $resArr['payment_amount'] = $pre_res_data['recharge_amount']; 
  $resArr['status_code'] = $pre_res_data['request_status']; 
 }else{
  $resArr['status_code'] = "404";   
 }

 mysqli_close($conn);
echo json_encode($resArr);
}
?>