<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../index.php');
}

if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_withdraw", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

if(!isset($_GET['order-id'])){
  echo "invalid request";
  return;
}else{
  $order_id = mysqli_real_escape_string($conn,$_GET['order-id']);
}

if(!isset($_GET['order-type'])){
  echo "invalid request";
  return;
}else{
  $order_type = mysqli_real_escape_string($conn,$_GET['order-type']);
}

$select_sql = "SELECT user_id,withdraw_request FROM userswithdraw WHERE uniq_id='$order_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);
  $user_id = $select_res_data['user_id'];
  $withdraw_request = $select_res_data['withdraw_request'];
}

$update_sql = "UPDATE userswithdraw SET request_status='{$order_type}' WHERE uniq_id='$order_id' ";
$update_result = mysqli_query($conn, $update_sql) or die('error');
if ($update_result){

  if($order_type=="rejected"){
    $update_sql = "UPDATE usersdata SET user_balance = user_balance + $withdraw_request WHERE uniq_id='$user_id' AND user_status = 'true' ";
    $update_result = mysqli_query($conn, $update_sql) or die('error');

    if ($update_result){ ?>

        <script>
          alert('Request updated!');
          window.close();
        </script>
    
    <?php }else{ ?>
    
        <script>
          alert('User Account Error!');
          window.close();
        </script>
    
    <?php } }else{ ?>

        <script>
          alert('Request Updated!');
          window.close();
        </script>

    <?php } }else{ ?>

         <script>
           alert('Failed to update!');
           window.close();
         </script>

<?php } ?>