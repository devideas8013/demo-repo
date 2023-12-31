<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){
  
  if(isset($_POST["USER_ID"]) && isset($_POST["NEW_PASSWORD"])){
    $auth_user_id = mysqli_real_escape_string($conn,$_POST["USER_ID"]);
    $auth_new_password = mysqli_real_escape_string($conn,password_hash($_POST["NEW_PASSWORD"],PASSWORD_BCRYPT));
  }
  
  if(isset($_GET["USER_ID"]) && isset($_GET["NEW_PASSWORD"])){
    $auth_user_id = mysqli_real_escape_string($conn,$_GET["USER_ID"]);
    $auth_new_password = mysqli_real_escape_string($conn,password_hash($_GET["NEW_PASSWORD"],PASSWORD_BCRYPT));
  }
  
  if($auth_user_id!="" && $auth_new_password!=""){
  
    if(strlen($auth_new_password) >= 6){
  
     $select_sql = "SELECT uniq_id,user_mobile_num FROM usersdata WHERE uniq_id='{$auth_user_id}' AND user_status='true' ";
     $select_query = mysqli_query($conn, $select_sql);

     if (mysqli_num_rows($select_query) > 0) {
      
      $update_sql = $conn->prepare("UPDATE usersdata SET user_password = ? WHERE uniq_id = ? ");
      $update_sql->bind_param("ss", $auth_new_password, $auth_user_id);
      $update_sql->execute();

      if ($update_sql->error == "") {
        $resArr['status_code'] = "success";
      }else{
        $resArr['status_code'] = "sql_error";
      }
  
     }else{
      $resArr['status_code'] = "account_error"; 
     }
  
    }else{
      $resArr['status_code'] = "password_error";
    }
  
  }else{
    $resArr['status_code'] = "invalid_params"; 
  }

}else{
    $resArr['status_code'] = "invalid_request_method"; 
}

mysqli_close($conn);
echo json_encode($resArr); 

?>