<?php
header("Access-Control-Allow-Origin: *");

$SERVER_URL = $_SERVER['SERVER_NAME'];

if($SERVER_URL==""){
  echo "Server URL error";
  return;
}

define("ACCESS_SECURITY","true");
include '../../../api.'.$SERVER_URL.'/security/config.php';

$resArr = array();
$resArr['pg_status'] = "";

 $pre_sql = "SELECT service_value FROM allservices WHERE service_name='QR_PAY' AND service_value!='' ";
 $pre_result = mysqli_query($conn, $pre_sql) or die('error');
 $res_data = mysqli_fetch_assoc($pre_result);

 if (mysqli_num_rows($pre_result) > 0){
  $str_arr = explode (",", $res_data['service_value']);
  $pg_status = $str_arr[0];
  $pg_payee_id = $str_arr[1];
  
  $resArr['pg_status'] = $pg_status;
  $resArr['pg_payee_id'] = $pg_payee_id;
 }else{
  $resArr['pg_status'] = '404';
 }
 
 mysqli_close($conn);
 echo json_encode($resArr);
?>