<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include '../../../api/security/config.php';

$resArr = array();
$resArr['pg_status'] = "";

 $pre_sql = "SELECT service_value FROM allservices WHERE service_name='ZEE_PAY' AND service_value!='' ";
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