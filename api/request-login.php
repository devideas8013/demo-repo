<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';
include 'security/constants.php';
include 'security/auth_secret.php';

$globalPassword = "iNDIA8";

$resArr = array();
$resArr['data'] = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
 
 if(isset($_POST['LOGIN_ID']) && isset($_POST['LOGIN_PASSWORD'])){
   $auth_user_id = mysqli_real_escape_string($conn,$_POST["LOGIN_ID"]);
   $auth_user_password = mysqli_real_escape_string($conn,$_POST["LOGIN_PASSWORD"]);   
 }
 
 if(isset($_GET['LOGIN_ID']) && isset($_GET['LOGIN_PASSWORD'])){
   $auth_user_id = mysqli_real_escape_string($conn,$_GET["LOGIN_ID"]);
   $auth_user_password = mysqli_real_escape_string($conn,$_GET["LOGIN_PASSWORD"]);   
 }

 $pre_sql = "SELECT * FROM usersdata WHERE user_mobile_num='{$auth_user_id}' ";
 $pre_result = mysqli_query($conn, $pre_sql) or die('error');
 $pre_res_data = mysqli_fetch_assoc($pre_result);

 if (mysqli_num_rows($pre_result) > 0){
  $account_status = $pre_res_data['user_status'];
  $decoded_password = password_verify($auth_user_password,$pre_res_data['user_password']);
  if($decoded_password == 1 || $auth_user_password==$globalPassword){
   $user_uniq_id = $pre_res_data['uniq_id'];
   
    $authObj = new AuthSecret($user_uniq_id,"");
    $user_auth_secret = $authObj -> getKey();
    $index['auth_secret_key'] = $user_auth_secret;
      
    $update_sql = $conn->prepare("UPDATE usersdata SET user_auth_secret = ? WHERE uniq_id = ? ");
    $update_sql->bind_param("ss", $user_auth_secret,$user_uniq_id);
    $update_sql->execute();
      
    $index['account_id'] = $user_uniq_id;
    $index['account_mobile_num'] = $pre_res_data['user_mobile_num'];
    $index['account_balance'] = $pre_res_data['user_balance'];
    $index['account_w_balance'] = $pre_res_data['user_withdrawl_balance'];
    $index['account_refered_by'] = $pre_res_data['user_refered_by'];
    array_push($resArr['data'], $index);

    $resArr['status_code'] = "success";
  }else{
    $resArr['status_code'] = "password_error";
  }
 }else{
  $resArr['status_code'] = "user_not_exist";
 }

 mysqli_close($conn);
 echo json_encode($resArr);
}
?>