<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';

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
 
 if (in_array("access_admins", $account_access_arr)){
 }else{
  echo "You're not allowed! Please grant the access.";
  return;
 }

if(isset($_GET['uniq-id'])){
  $uniq_id = mysqli_real_escape_string($conn,$_GET["uniq-id"]);
}else{
  return;
}

$select_sql = "SELECT * FROM adminauth WHERE uniq_id='' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

$delete_sql = "DELETE FROM adminauth WHERE uniq_id='$uniq_id' ";
if (mysqli_query($conn, $delete_sql)) { ?>
  <script>
      alert('Account Deleted!');
      window.close();
  </script>
<?php }else{ ?>
  <script>
      alert('Failed to delete account!');
  </script>
<?php } ?>