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
 
if (in_array("access_recharge", $account_access_arr)){
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

$select_sql = "SELECT user_id,recharge_amount FROM usersrecharge WHERE uniq_id='$order_id' AND request_status='pending' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);
  $user_id = $select_res_data['user_id'];
  $recharge_amount = $select_res_data['recharge_amount'];
  
  $update_sql = "";
  if($order_type=="success"){
    $update_sql = "UPDATE usersrecharge SET request_status='success' WHERE uniq_id='$order_id' ";
  }else{
    $update_sql = "UPDATE usersrecharge SET request_status='rejected' WHERE uniq_id='$order_id' ";
  }
  
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  
  if ($update_result){

  if($order_type=="success"){
    $new_coins = number_format($recharge_amount*0.1,2,".","");
    $update_sql = "UPDATE usersdata SET user_balance = user_balance + $recharge_amount,user_total_coins = user_total_coins + $new_coins,account_level='2' WHERE uniq_id='$user_id' AND user_status = 'true' ";
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
    
    <?php } }else{
     $update_result = true;
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
    
    <?php } } }else{ ?>

      <script>
        alert('Failed to update!');
        window.close();
      </script>

<?php } } ?>