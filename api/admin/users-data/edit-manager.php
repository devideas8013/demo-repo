<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../index.php');
}else{
  $session_id = $_SESSION["pb_admin_user_id"];
}

if (!isset($_SESSION["pb_admin_category"])) {
  header('location:../index.php');
}else{
  $account_category = $_SESSION["pb_admin_category"];
}

if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_users_data", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

if($account_category!="admin"){
  echo "You're not allowed to view this page";
  return;
}

if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

$select_sql = "SELECT * FROM usersdata WHERE uniq_id='$user_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_mobile_num = $select_res_data['user_mobile_num'];
  $user_full_name = $select_res_data['user_full_name'];
  $user_email_id = $select_res_data['user_email_id'];
  $user_balance = $select_res_data['user_balance'];  
  $user_earnings_balance = $select_res_data['user_withdrawl_balance'];  
  $user_total_coins = $select_res_data['user_total_coins'];  
  $user_refered_by = $select_res_data['user_refered_by'];
  $user_last_active_date = $select_res_data['user_last_active_date'];
  $user_last_active_time = $select_res_data['user_last_active_time'];
  $user_winning_balance = $select_res_data['user_withdrawl_balance'];
  $account_level = $select_res_data['account_level'];
  $user_status = $select_res_data['user_status'];
  $user_joined = $select_res_data['user_joined'];
  
}else{
  echo 'Invalid user-id!';
  return;
}

$user_reward_balance = 0;
$select_sql = "SELECT * FROM othertransactions WHERE user_id='{$user_id}' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  while ($row = mysqli_fetch_assoc($select_result)){
    if($row['type']!="investmentbonus"){
      $user_reward_balance += $row['amount'];
    }
    
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: User</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400&display=swap');
*{
    margin:0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'poppins', sans-serif;
}
body{
    min-height: 100vh;
    width: 100%;
    display: grid;
    place-items: center;
}
.content{
  width: 480px;
  padding: 18px;
  background: #fff;
  box-shadow: 0.1px 2px 8px 4px rgba(0, 0, 0, 0.05);
}

.content .action_btn{
  border: none;
  outline: none;
  color: #fff;
  cursor: pointer;
  padding: 12px 18px;
  border-radius: 5px;
  background: #28B463;
  font-size: 18px;
  margin-top: 15px;
  text-decoration: none;
}
.content .reject_btn{
  background: #EC7063;
}

.content .light_grey_back{
    background: #AAB7B8 !important;
}

.select_op_box{
  margin-top: 1.2em;
}

.select_op_box select{
  display: inline-block;
  padding: 10px;
  font-size: 16px;
  width: 100%;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.select_op_box p{
  margin-bottom: 10px;
}

.form_box input{
  width: 100%;
  height: 50px;
  padding: 10px;
  font-size: 18px;
  outline: none;
  border: 1px solid rgba(0, 0, 0, 0.1);
}
.form_box textarea{
  width: 100%;
  height: 100px;
  padding: 10px;
  font-size: 20px;
  margin-top: 6px;
  resize: none;
}
.content .form_box .hide_view{
  display: none;
}

.form_box p{
  margin-top: 6px;
  font-size: 17px;
  line-break: anywhere;
}
.light_back{
  padding: 10px;
  margin-top: 10px;
  background: rgba(0,0,0,0.08);
}

#status_active label{
  color: #ffffff;
  background: #28B463;
  padding: 3px 10px;
}

#status_ban label{
  color: #ffffff;
  background: #CB4335;
  padding: 3px 10px;
}


.blue_back{
    background: #3498DB !important;
}

@media (max-width: 500px) {
    .content{
        width: 100%;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: none;
    }
}
    </style>
</head>
<body>

<div class="content">

  <div class="form_box">
    <h3><i class='bx bx-user-circle' ></i>&nbsp;User ID: <?php echo $user_id; ?></h3>

    <br>
    <p>User Name: <?php echo $user_full_name; ?></p>
    <p>User Mobile: <?php echo $user_mobile_num; ?></p>
    <p>User Balance: ₹<?php echo $user_balance; ?></p>
    <p>User Earnings: ₹<?php echo $user_earnings_balance; ?></p>
    <p>User Coins: ₹<?php echo $user_total_coins; ?></p>
    <p>User Rewards: ₹<?php echo $user_reward_balance; ?></p>
    <p>Account Level: <?php echo $account_level; ?></p>
    <p>Refered By: <?php echo $user_refered_by; ?></p>
    <p>User Last Active: <?php echo $user_last_active_date.' '.$user_last_active_time; ?></p>
    <p>User Joined: <?php echo $user_joined; ?></p>
    </br>
    <?php if($user_status=="true"){ ?>
      <p id="status_active">User Status: <label>Active</label></p>
    <?php }else if($user_status=="false"){ ?>
      <p id="status_ban">User Status: <label>In progress</label></p>
     <?php }else { ?>
      <p id="status_ban">User Status: <label>Ban</label></p>
     <?php }  ?>
    
    <?php if($user_status=="true"){ ?>
      <br>
      <button class="action_btn reject_btn" onclick="BanAccount()">Ban Account</button>
    <?php }else{ ?>
      <br>
      <button class="action_btn" onclick="ActiveAccount()">Active Account</button>
    <?php } ?>

    <!--<a href="../update-account.php?user-id=<?php echo $user_id; ?>" class="action_btn">Update&nbsp;<i class='bx bx-chevron-right' ></i></a>-->
    
    <a href="../view-all-refers.php?user-id=<?php echo $user_id; ?>" class="action_btn blue_back">All Refers&nbsp;<i class='bx bx-chevron-right' ></i></a>
</div>

</div>

<script>
  function BanAccount(){
    window.open("../update-order-request.php?request-type=ban&user-id=<?php echo $user_id; ?>");
  }

  function ActiveAccount(){
    window.open("../update-order-request.php?request-type=true&user-id=<?php echo $user_id; ?>");
  }
</script>
    
</body>
</html>