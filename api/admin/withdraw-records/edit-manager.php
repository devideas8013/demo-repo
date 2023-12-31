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
 
if (in_array("access_withdraw", $account_access_arr)){
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

$select_sql = "SELECT * FROM userswithdraw WHERE uniq_id='$order_id'";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_id = $select_res_data['user_id'];
  $withdraw_amount = $select_res_data['withdraw_amount'];
  $actual_name = $select_res_data['actual_name'];
  $bank_name = $select_res_data['bank_name'];
  $bank_account = $select_res_data['bank_account'];
  $bank_ifsc_code = $select_res_data['bank_ifsc_code'];
  $user_state = $select_res_data['user_state'];
  $request_status = $select_res_data['request_status'];
  $request_date_time = $select_res_data['request_date_time'];
}else{
  echo 'Invalid order-id or order-id already confirmed!';
  return;
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
    <p>Order id: <?php echo $order_id; ?></p>
    <p>Withdraw Amount: ₹<?php echo $withdraw_amount; ?></p>
    <p>Recharge DateTime: <?php echo $request_date_time; ?></p>

    <?php if($request_status=="approve"){ ?>
      <p>Status: <label id="status_approved_tv"><?php echo $request_status; ?></label></p>
    <?php }else if($request_status=="success"){ ?>
      <p>Status: <label id="status_approved_tv"><?php echo $request_status; ?></label></p>
    <?php }else if($request_status=="rejected"){ ?>
      <p>Status: <label id="status_pending_tv"><?php echo $request_status; ?></label></p>
    <?php } ?>

    <br>
    <p>Withdraw Details:</p>

    <div class="light_back">
      <?php if($bank_ifsc_code!="null"){ ?>
        <p><?php echo 'Actual Name: '.$actual_name.'<br>Bank Name: '.$bank_name.'<br>Bank Account: '.$bank_account
      .'<br>IFSC Code: '.$bank_ifsc_code.'<br>User State: '.$user_state; ?></p>
      <?php }else{ ?>
        <p><?php echo 'Actual Name: '.$actual_name.'<br>UPI Id: '.$bank_account
      .'<br>User State: '.$user_state; ?></p>
      <?php } ?>
    </div>

    <br>
    <?php if($request_status=="approve"){ ?>
      <button class="action_btn" onclick="SucessRequest('success')">Success Request</button>
      <button class="action_btn reject_btn" onclick="RejectRequest()">Reject Request</button>
    <?php }else if($request_status=="pending"){ ?>
      <button class="action_btn" onclick="SucessRequest('approve')">Approve Request</button>
      <button class="action_btn reject_btn" onclick="RejectRequest()">Reject Request</button>
    <?php } ?>

  </div>

</div>

<script>
  function RejectRequest(){
    window.open("../update-order-request.php?order-type=rejected&order-id=<?php echo $order_id; ?>");
  }

  function SucessRequest(status){
    window.open("../update-order-request.php?order-type="+status+"&order-id=<?php echo $order_id; ?>");
  }
</script>
    
</body>
</html>