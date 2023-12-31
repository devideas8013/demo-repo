<?php
function get_operating_system() {
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $u_agent = strtolower($u_agent);
  $operating_system = 'Unknown OS';

  //Get the operating_system name
  if (preg_match('/linux/i', $u_agent)) {
      $operating_system = 'Linux';
  }
  
  return $operating_system;
}

$OperatingSystem =  get_operating_system();

if($OperatingSystem=="Linux"){

define("ACCESS_SECURITY","true");
include 'security/config.php';
  
$resArr = array();
$resArr['signup_bonus'] = "0";
$resArr['signup_new_account'] = "true";
$resArr['signup_bonus_message'] = "Get Free SignUp Bonus";

if($_SERVER['REQUEST_METHOD'] == 'POST') {

  $select_sql = "SELECT service_value FROM allservices WHERE service_name='SIGNUP_BONUS' ";
  $select_query = mysqli_query($conn,$select_sql);
  
  if(mysqli_num_rows($select_query) > 0){
    $res_data = mysqli_fetch_assoc($select_query);
    $resArr['signup_bonus'] = $res_data['service_value']; 
  }
  
  mysqli_close($conn);
  echo json_encode($resArr);
}

}else{
  echo 'permission denied!';
}
?>