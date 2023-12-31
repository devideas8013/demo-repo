<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../index.php');
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
 
if (in_array("access_recharge", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

if($account_category!="admin"){
    echo "You're not allowed to view this page";
    return;
}

if(!isset($_GET['order-id'])){
  echo "invalid request";
  return;
}else{
  $order_id = mysqli_real_escape_string($conn,$_GET['order-id']);
}

$select_sql = "SELECT * FROM usersrecharge WHERE uniq_id='$order_id'";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_id = $select_res_data['user_id'];
  
  $select_sql1 = "SELECT * FROM usersdata WHERE uniq_id='$user_id'";
  $select_result1 = mysqli_query($conn, $select_sql1) or die('error');
  $select_res_data1 = mysqli_fetch_assoc($select_result1);
  
  $user_full_name = $select_res_data1['user_full_name'];
  $user_mobile_num = $select_res_data1['user_mobile_num'];
  
  $recharge_amount = $select_res_data['recharge_amount'];
  $recharge_mode = $select_res_data['recharge_mode'];
  $recharge_details = $select_res_data['recharge_details'];
  $request_status = $select_res_data['request_status'];
  $request_date_time = $select_res_data['request_date_time'];
}else{
  echo 'Invalid order-id!';
  return;
}

if($recharge_details!=""){
  $word = "screenshots";
  if(strpos($recharge_details, $word) !== false){
    $recharge_details = "https://vipclub.click/files/".$recharge_details;
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage Recharge Request</title>
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
  background: #6C3483;
  font-size: 18px;
  margin-top: 15px;
  display: inline-block;
}
.content .reject_btn{
  background: #EC7063;
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

.form_box label{
  padding: 3px 5px;
  color: #ffffff;
  margin-left: 5px;
  border-radius: 5px;
}

.form_box #status_approved_tv{
  background: #27AE60;
}

.form_box #status_pending_tv{
  background: rgba(0,0,0,0.5);
}

.form_box #status_pending_tv{
  background: #E74C3C;
}
.content .light_grey_back{
    background: #AAB7B8 !important;
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
    <h3>Requested By: <?php echo $user_id; ?></h3>

    <br>
    <p>User id: <?php echo $user_id; ?></p>
    <p>User Name: <?php echo $user_full_name; ?></p>
    <p>Mobile : <?php echo $user_mobile_num; ?></p>
    <p>Recharge Amount: ₹<?php echo $recharge_amount; ?></p>
    <p>Recharge Mode: <?php echo $recharge_mode; ?></p>
    <p>Recharge DateTime: <?php echo $request_date_time; ?></p>

    <?php if($request_status=="success"){ ?>
      <p>Status: <label id="status_approved_tv"><?php echo $request_status; ?></label></p>
    <?php }else if($request_status=="rejected"){ ?>
      <p>Status: <label id="status_pending_tv"><?php echo $request_status; ?></label></p>
    <?php } ?>

    <br>
    <p>Recharge Details</p>

    <div class="recharge_details_view light_back">
      <p><?php echo $recharge_details; ?></p>
    </div>

    <br>
    <?php if($request_status=="success"){ ?>
      <button class="action_btn reject_btn" onclick="RejectRequest()">Reject Request</button>
    <?php }else if($request_status=="pending"){ ?>
      <button class="action_btn" onclick="SucessRequest('approve')">Approve Request</button>
      <button class="action_btn reject_btn" onclick="RejectRequest()">Reject Request</button>
    <?php }else{ ?>
      <button class="action_btn" onclick="SucessRequest('approve')">Approve Request</button>
    <?php } ?></br>

</div>

</div>

<script>
  let recharge_details_view = document.querySelector(".recharge_details_view p");
  
  function RejectRequest(){
    window.open("../update-order-request.php?order-type=rejected&order-id=<?php echo $order_id; ?>");
  }

  function SucessRequest(){
    window.open("../update-order-request.php?order-type=success&order-id=<?php echo $order_id; ?>");
  }
  
  recharge_details_view.addEventListener("click", ()=>{
    let screenshotURL = recharge_details_view.innerHTML;
    if(screenshotURL.includes("screenshots")){
      window.open(screenshotURL, "_blank");
    }
  })

  
</script>
    
</body>
</html>