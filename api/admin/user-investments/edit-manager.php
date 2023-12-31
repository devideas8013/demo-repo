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

if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_investments", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

if(!isset($_GET['id'])){
  echo "invalid request";
  return;
}else{
  $uniq_id = mysqli_real_escape_string($conn,$_GET['id']);
}

$select_sql = "SELECT * FROM myinvestments WHERE uniq_id='{$uniq_id}' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $investment_name = $select_res_data['investment_name'];
  $investment_hourly_income = $select_res_data['investment_hourly_income'];
  $investment_price = $select_res_data['investment_price'];
  $investment_total_days = $select_res_data['investment_total_days'];
  $investment_earnings = $select_res_data['investment_earnings'];
  $investment_status = $select_res_data['investment_status'];
  
}else{
  echo 'Invalid Uniqid!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
<title>Manage: Investments</title>
<style>
*{
    margin:0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
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
    <h3><i class='bx bx-receipt'></i>&nbsp;Investment Details: <?php echo $uniq_id; ?></h3>

    <br>
    <p>Investment Name: <?php echo $investment_name; ?></p>
    <p>Investment Hourly Income: ₹<?php echo $investment_hourly_income; ?></p>
    <p>Investment price: ₹<?php echo $investment_price; ?></p>
    <p>Investment Daily Income: ₹<?php echo $investment_hourly_income*24; ?></p>
    <p>Investment Total Earnings: ₹<?php echo $investment_earnings; ?></p>
    <p>Investment Total Days: <?php echo $investment_total_days; ?></p>
    </br></br>

    <a onclick="deletePost('<?php echo $uniq_id; ?>')" class="action_btn reject_btn">Delete&nbsp;<i class='bx bx-chevron-right' ></i></a>
    
    <!--<a href="update-post.php?uniq-id=<?php echo $uniq_id; ?>" class="action_btn">Update&nbsp;<i class='bx bx-chevron-right' ></i></a></br>-->
    </br>
  </div>

</div>

<script>
  function deletePost(uniq_id){
     if(confirm("Are you sure you want to delete this investment?")){
        window.open("../delete-post.php?uniq-id="+uniq_id);
    }
  }
</script>
    
</body>
</html>