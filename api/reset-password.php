<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){
  function generateRandomNumber($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  $new_otp =  generateRandomNumber(5);
  
  if(isset($_POST["USER_MOBILE"]) && isset($_POST["USER_OTP"]) && isset($_POST["NEW_PASSWORD"])){
    $auth_user_mobile = mysqli_real_escape_string($conn,$_POST["USER_MOBILE"]);
    $auth_user_otp = mysqli_real_escape_string($conn,$_POST["USER_OTP"]);
    $auth_new_password = mysqli_real_escape_string($conn,password_hash($_POST["NEW_PASSWORD"],PASSWORD_BCRYPT));
  }
  
  if(isset($_GET["USER_MOBILE"]) && isset($_GET["USER_OTP"]) && isset($_GET["NEW_PASSWORD"])){
    $auth_user_mobile = mysqli_real_escape_string($conn,$_GET["USER_MOBILE"]);
    $auth_user_otp = mysqli_real_escape_string($conn,$_GET["USER_OTP"]);
    $auth_new_password = mysqli_real_escape_string($conn,password_hash($_GET["NEW_PASSWORD"],PASSWORD_BCRYPT));
  }

  
  $select_user_sql = "SELECT * FROM usersdata WHERE user_mobile_num='{$auth_user_mobile}' AND user_status='true' ";
  $select_user_query = mysqli_query($conn,$select_user_sql);
  
  if(mysqli_num_rows($select_user_query) > 0){
    $select_user_data = mysqli_fetch_assoc($select_user_query);
    $user_last_otp = $select_user_data['user_last_otp'];
    $user_status = $select_user_data['user_status'];
    
    if($user_status=="true"){
        
      if($user_last_otp==$auth_user_otp){
        $update_sql = $conn->prepare("UPDATE usersdata SET user_password = ?, user_last_otp = ? WHERE user_mobile_num = ? ");
        $update_sql->bind_param("sss", $auth_new_password,$new_otp, $auth_user_mobile);
        $update_sql->execute();

        if ($update_sql->error == "") {
          $resArr['status_code'] = "success";
        }else{
          $resArr['status_code'] = "sql_error";
        }
      }else{
        $resArr['status_code'] = "invalid_otp";
      }
      
    }else{
      $resArr['status_code'] = "account_error"; 
    }

  }else{
    $resArr['status_code'] = "invalid_mobile_num"; 
  }

  mysqli_close($conn);
  echo json_encode($resArr);  
}

?>