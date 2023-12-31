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
 
if (in_array("access_help", $account_access_arr)){
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


$update_sql = "";
if($order_type=="success"){
  $update_sql = "UPDATE userscomplaints SET complain_status='success' WHERE uniq_id='$order_id' AND complain_status = 'pending' ";
}else{
  $update_sql = "UPDATE userscomplaints SET complain_status='rejected' WHERE uniq_id='$order_id' AND complain_status = 'pending' ";
}

$update_result = mysqli_query($conn, $update_sql) or die('error');
if ($update_result){ ?>

    <script>
      alert('Request updated!');
      window.close();
    </script>
    
<?php }else{ ?>

    <script>
        alert('Failed to Updated!');
        window.close();
    </script>

<?php } ?>