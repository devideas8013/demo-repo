<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

session_start();
if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../index.php');
}

if(!isset($_GET['access-code'])){
  echo "request block";
  return;
}else{
  $admin_acccess = mysqli_real_escape_string($conn,$_GET['access-code']);
}
 
if($admin_acccess!=$AdminIDAccessKey){
 echo "request block";
 return;
}

if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

if(!isset($_GET['request-type'])){
  echo "invalid request";
  return;
}else{
  $request_type = mysqli_real_escape_string($conn,$_GET['request-type']);
}

$select_sql = "SELECT user_status FROM usersdata WHERE uniq_id='$user_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $update_sql = "UPDATE usersdata SET user_status='{$request_type}' WHERE uniq_id='{$user_id}'";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>

  <script>
    alert('Request updated!');
    window.close();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to update!');
    window.close();
  </script>

<?php } } ?> ?>