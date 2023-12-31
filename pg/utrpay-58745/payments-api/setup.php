<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");

$SERVER_URL = $_SERVER['SERVER_NAME'];

if($SERVER_URL==""){
  echo "Server URL error";
  return;
}
  
include '../../../api.'.$SERVER_URL.'/security/config.php';

$resArr = array();
$resArr['pg_status'] = "";

 $pre_sql = "SELECT service_value FROM allservices WHERE service_name='UTR_PAY' AND service_value!='' ";
 $pre_result = mysqli_query($conn, $pre_sql) or die('error');
 $res_data = mysqli_fetch_assoc($pre_result);

 function generateRandom($min = 1, $max = 20) {
    if (function_exists('random_int')):
        return random_int($min, $max); // more secure
    elseif (function_exists('mt_rand')):
        return mt_rand($min, $max); // faster
    endif;
    return rand($min, $max); // old
 }

 if (mysqli_num_rows($pre_result) > 0){
  $str_arr = explode (",", $res_data['service_value']);
  $rand_index = generateRandom(0,count($str_arr)-1);
  
  $resArr['rand_index'] = $rand_index;
  $resArr['pg_status'] = "ON";
  $resArr['pg_payee_id'] = $str_arr[$rand_index];
 }else{
  $resArr['pg_status'] = '404';
 }
 
 mysqli_close($conn);
 echo json_encode($resArr);
?>