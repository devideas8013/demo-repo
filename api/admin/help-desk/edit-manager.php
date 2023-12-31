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
 
if (in_array("access_help", $account_access_arr)){
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

$select_sql = "SELECT * FROM userscomplaints WHERE uniq_id='$order_id' AND complain_status='pending' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_id = $select_res_data['user_id'];
  $complain_description = $select_res_data['complain_details'];
  $complain_date_time = $select_res_data['complain_date_time'];
}else{
  echo 'Invalid order-id or order-id already solved!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage Help Desk</title>
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
    <p>Request DateTime: <?php echo $complain_date_time; ?></p>
    <br>
    <p>Form Details</p>

    <div class="light_back">
      <p><?php echo $complain_description; ?></p>
    </div>

    <br>
    <button class="action_btn" onclick="SucessRequest()">Problem Solved</button>
    <button class="action_btn reject_btn" onclick="RejectRequest()">Reject Request</button>

</div>

</div>

<script>
  function RejectRequest(){
    window.open("../update-order-request.php?order-type=rejected&order-id=<?php echo $order_id; ?>");
  }

  function SucessRequest(){
    window.open("../update-order-request.php?order-type=success&order-id=<?php echo $order_id; ?>");
  }
</script>
    
</body>
</html>