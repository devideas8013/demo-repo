<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';
include 'security/auth_secret.php';

$resArr = array();
$resArr['data'] = array();

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');
$curr_time = date('h:i:s a');

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

function generateRandomNumber($length) {
  $characters = '0123456789';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

function generateOrderID($length = 15) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return 'RR0'.$randomString;
}

$auth_user_mobile = "";
$auth_user_password = "";
$auth_verify_otp = "";

$uniq_id = generateOrderID();
$auth_new_otp =  generateRandomNumber(6);

if(isset($_POST['SIGNUP_PHONE']) && isset($_POST['SIGNUP_PASSWORD']) && isset($_POST['SIGNUP_OTP'])){
 $auth_user_mobile = mysqli_real_escape_string($conn,$_POST["SIGNUP_PHONE"]);
 $auth_user_password = mysqli_real_escape_string($conn,password_hash($_POST["SIGNUP_PASSWORD"],PASSWORD_BCRYPT));  
 $auth_verify_otp = mysqli_real_escape_string($conn,$_POST["SIGNUP_OTP"]);
}

if(isset($_GET['SIGNUP_PHONE']) && isset($_GET['SIGNUP_PASSWORD']) && isset($_GET['SIGNUP_OTP'])){
 $auth_user_mobile = mysqli_real_escape_string($conn,$_GET["SIGNUP_PHONE"]);
 $auth_user_password = mysqli_real_escape_string($conn,password_hash($_GET["SIGNUP_PASSWORD"],PASSWORD_BCRYPT));   
 $auth_verify_otp = mysqli_real_escape_string($conn,$_GET["SIGNUP_OTP"]);
}


$auth_refered_by = "";
if(isset($_GET['SIGNUP_REFER_CODE'])){
  $auth_refered_by = mysqli_real_escape_string($conn,$_GET["SIGNUP_REFER_CODE"]);
}

if(isset($_POST['SIGNUP_REFER_CODE'])){
  $auth_refered_by = mysqli_real_escape_string($conn,$_POST["SIGNUP_REFER_CODE"]);
}

$auth_user_fullname = "";
if(isset($_GET['SIGNUP_NAME'])){
  $auth_user_fullname = mysqli_real_escape_string($conn,$_GET["SIGNUP_NAME"]);
}

if(isset($_POST['SIGNUP_NAME'])){
  $auth_user_fullname = mysqli_real_escape_string($conn,$_POST["SIGNUP_NAME"]);
}

if($auth_user_fullname!="" && $auth_user_mobile!="" && $auth_user_password!="" && $auth_refered_by!="" && $auth_verify_otp!=""){

// update account data
function updateAccountData($conn,$user_auth_secret,$user_fullname,$user_password,$auth_new_otp,$user_refered_by,$user_status,$user_uniq_id){
    $update_sql = $conn->prepare(
        "UPDATE usersdata SET user_auth_secret = ?,user_full_name = ?,user_password = ?,user_last_otp = ?,user_refered_by = ?,user_status = ? WHERE uniq_id = ? "
     );
    $update_sql->bind_param("sssssss", $user_auth_secret,$user_fullname,$user_password,$auth_new_otp,$user_refered_by,$user_status, $user_uniq_id);
    $update_sql->execute();

    if ($update_sql->error == "") {
        return true;
    }else{
        return false;
    }
}
 
// checking for password
if(strlen($auth_user_password) >= 6){
 
$user_status = "false";
$select_user_sql = "SELECT uniq_id,user_last_otp,user_mobile_num FROM usersdata WHERE user_mobile_num='{$auth_user_mobile}' AND user_status = '{$user_status}' ";
$select_user_result = mysqli_query($conn, $select_user_sql) or die('error');

if (mysqli_num_rows($select_user_result) > 0) {
  $select_user_data = mysqli_fetch_assoc($select_user_result);
  $user_uniq_id = $select_user_data['uniq_id'];
  $user_last_otp = $select_user_data['user_last_otp'];
  
  if($user_last_otp==$auth_verify_otp){

  $zero_amt = "0";
  $user_status = "true";
  $curr_date_time = $curr_date.' '.$curr_time;
  
  // generating auth token
  $authObj = new AuthSecret($user_uniq_id,"");
  $user_auth_secret = $authObj -> getKey();
  
  // checking for refer code
  if($auth_refered_by!=""){
    $user_status = "true";
    
    $select_sql = "SELECT uniq_id FROM usersdata WHERE uniq_id='{$auth_refered_by}' AND user_status = '{$user_status}' ";
    $select_result = mysqli_query($conn, $select_sql) or die('error');
    
    if (mysqli_num_rows($select_result) > 0) {
        
     $responseVal = updateAccountData($conn,$user_auth_secret,$auth_user_fullname,$auth_user_password,$auth_new_otp,$auth_refered_by,$user_status,$user_uniq_id);

     if ($responseVal) {
        $index['account_id'] = $user_uniq_id;
        $index['account_mobile_num'] = $auth_user_mobile;
        $index['account_balance'] = $zero_amt;
        $index['account_w_balance'] = $zero_amt;
        $index['account_refered_by'] = $auth_refered_by;
        $index['auth_secret_key'] = $user_auth_secret;
        array_push($resArr['data'], $index);
        
        $resArr['status_code'] = "success";        
     }else {
        $resArr['status_code'] = "failed";
     }
 
    }else{
      $resArr['status_code'] = "invalid_refer_code";
    }
  }else{
     $responseVal = updateAccountData($conn,$user_auth_secret,$auth_user_fullname,$auth_user_password,$auth_new_otp,$auth_refered_by,$user_status,$user_uniq_id);

     if ($responseVal) {
        $index['account_id'] = $user_uniq_id;
        $index['account_mobile_num'] = $auth_user_mobile;
        $index['account_balance'] = $zero_amt;
        $index['account_w_balance'] = $zero_amt;
        $index['account_refered_by'] = "";
        $index['auth_secret_key'] = $user_auth_secret;
        array_push($resArr['data'], $index);
        
        $resArr['status_code'] = "success";        
     }else {
        $resArr['status_code'] = "failed";
     }
  }
  
  }else{
    $resArr['status_code'] = "invalid_otp";  
  }

}else{
    $resArr['status_code'] = "invalid_mobile_num";
}

}else{
   $resArr['status_code'] = "password_error";
}
    
}else{
    $resArr['status_code'] = "invalid_params";  
}

 mysqli_close($conn);
 echo json_encode($resArr);
}
?>