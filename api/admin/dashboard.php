<?php
define("ACCESS_SECURITY","true");
include '../security/config.php';
include '../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
    header('location:index.php');
}

if (!isset($_SESSION["pb_admin_category"])) {
    header('location:index.php');
}else{
    $account_category = $_SESSION["pb_admin_category"];
}

if (!isset($_SESSION["pb_admin_access"])) {
    header('location:index.php');
}else{
    $account_access = $_SESSION["pb_admin_access"];
    $account_access_arr = explode (",", $account_access);
}

$anlyt_number_recharge = 0;
$anlyt_number_withdraw = 0;
$anlyt_total_recharge = 0;
$anlyt_total_withdraw = 0;

$search_recharge_sql = "SELECT recharge_amount,request_date_time FROM usersrecharge WHERE request_status='success' AND (recharge_mode='UTRPay' OR recharge_mode='QRPay') ";   
$search_recharge_result = mysqli_query($conn, $search_recharge_sql) or die($conn -> error);
    
while ($search_recharge_row = mysqli_fetch_assoc($search_recharge_result)){
  $anlyt_number_recharge++;
  $anlyt_total_recharge += $search_recharge_row['recharge_amount'];
}
    
$search_withdraw_sql = "SELECT withdraw_amount,request_date_time FROM userswithdraw WHERE request_status='success' ";   
$search_withdraw_result = mysqli_query($conn, $search_withdraw_sql) or die('search failed2');
    
while ($search_withdraw_row = mysqli_fetch_assoc($search_withdraw_result)){
  $anlyt_number_withdraw++;
  $anlyt_total_withdraw += $search_withdraw_row['withdraw_amount'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "header_file.php" ?>
    <title><?php echo $APP_NAME; ?>: Dashboard</title>
</head>

<style>
*{
  padding: 0;
  margin: 0;
  font-family: 'poppins',sans-serif;
}

.main{
  padding: 15px;
  margin-bottom: 15px;
}

.GridView{
  display: grid;
  grid-template-columns: repeat(5,1fr);
  grid-gap: 10px;
  margin-top: 2em;
}

.GridView a{
    position: relative;
    height: auto;
    color: #ffffff;
    display: inline-block;
    cursor: pointer;
    text-decoration:none;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    border-radius: 5px;
    padding: 25px 15px;
    overflow: hidden;
    background-color: <?php echo $ADMIN_COLOR; ?>;
}

.GridView a i{
  font-size: 3.9em;
}

.GridView p:last-child{
  margin-top: 15px;
}

.GridView .design-circle{
    position: absolute;
    right: -30px;
    top: -30px;
    height: 100px;
    width: 100px;
    border-radius: 50%;
    background: rgba(0,0,0,0.05);
}

.admin_view{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px;
}

.admin_view #admin_info_tv{
  font-size: 18px;
  margin-top: 10px;
}

.view{
  height: 1px;
  width: 100%;
  background-color: rgba(0,0,0,0.08);
}

.control_btn{
  border: none;
  outline: none;
  color: #ffffff;
  cursor: pointer;
  padding: 12px 18px;
  border-radius: 5px;
  font-size: 18px;
  background-color: <?php echo $ADMIN_COLOR; ?>;
}

@media (max-width: 900px) {
    .GridView{
      grid-template-columns: repeat(3,1fr);
    }
}

@media (max-width: 550px) {
  .GridView{
    grid-template-columns: repeat(2,1fr);
  }
  .admin_view{
    flex-direction: column;
  }
  .control_btn,.view{
    margin-top: 10px;
  }
}

@media (max-width: 360px) {
  .GridView{
    grid-template-columns: 1fr;
  }
}

</style>

<body>
    
<div class="main">

  <div class="admin_view">
    <div>
     <h2>Dashboard</h2>
     <p id="admin_info_tv"><i class='bx bx-user' ></i>&nbsp;<?php echo $_SESSION["pb_admin_user_id"] ?></p>
    </div>

    <button class="control_btn" onclick="LogoutAccount()"><i class='bx bx-log-out-circle' ></i>&nbsp;Logout</button>
  </div>

  <div class="view"></div>

  <div class="GridView">
    
    <?php if (in_array("access_recharge", $account_access_arr)){ ?>
     <a style="background: #28B463;">
      <p style="font-size: 24px !important">₹ <?php echo $anlyt_total_recharge; ?></p>
      <p style="font-size: 13px !important;margin-top: 5px;">Total Recharge</p>
      
      <div class="design-circle"></div>
     </a>
    <?php } ?>
    
    <?php if (in_array("access_recharge", $account_access_arr)){ ?>
     <a style="background: #EC7063;">
      <p style="font-size: 24px !important">₹ <?php echo $anlyt_total_withdraw; ?></p>
      <p style="font-size: 13px !important;margin-top: 5px;">Total Withdraw</p>
      
      <div class="design-circle"></div>
     </a>
    <?php } ?>
    
    <?php if (in_array("access_investments", $account_access_arr)){ ?>
     <a href="upload-image/">
      <p><i class='bx bx-cloud-upload'></i></p>
      <p>Upload Image</p>
     </a>
    <?php } ?>
    
    <?php if (in_array("access_investments", $account_access_arr)){ ?>
     <a href="all-investments/">
      <p><i class='bx bx-notepad'></i></p>
      <p>All Investments</p>
     </a>
    <?php } ?>
    
    <?php if (in_array("access_investments", $account_access_arr)){ ?>
     <a href="user-investments/">
      <p><i class='bx bx-notepad'></i></p>
      <p>User Investments</p>
     </a>
    <?php } ?>

    <?php if (in_array("access_users_data", $account_access_arr)){ ?>
     <a href="users-data/">
      <p><i class='bx bx-group' ></i></p>
      <p>Users Data</p>
     </a>
    <?php } ?>

    <?php if (in_array("access_recharge", $account_access_arr)){ ?>
     <a href="recharge-records/">
      <p><i class='bx bx-detail' ></i></p>
      <p>Recharge Records</p>
     </a>
    <?php } ?>

    <?php if (in_array("access_withdraw", $account_access_arr)){ ?>
     <a href="withdraw-records/">
      <p><i class='bx bx-receipt' ></i></p>
      <p>Withdraw Records</p>
     </a>
    <?php } ?>
    
    <?php if (in_array("access_help", $account_access_arr)){ ?>
     <a href="help-desk/">
      <p><i class='bx bx-help-circle' ></i></p>
      <p>Help Desk</p>
     </a>
    <?php } ?>
    
    <?php if (in_array("access_message", $account_access_arr)){ ?>
     <a href="send-message/">
      <p><i class='bx bx-comment-dots' ></i></p>
      <p>Send Message</p>
     </a>
    <?php } ?>

    <?php if (in_array("access_settings", $account_access_arr)){ ?>
     <a href="game-settings/">
      <p><i class='bx bx-cog' ></i></p>
      <p>Game Settings</p>
     </a>
    <?php } ?>

    <?php if (in_array("access_admins", $account_access_arr)){ ?>
     <a href="manage-admins/">
      <p><i class='bx bx-user-plus' ></i></p>
      <p>Manage Admins</p>
     </a>
    <?php } ?>

</div>

</div>

<script>
function LogoutAccount(){
  if (confirm("Are you sure want to Logout?")) {
    window.open("logout-account/?access-code=<?php echo $AdminIDAccessKey; ?>");
  }
}
</script>

</body>
</html>