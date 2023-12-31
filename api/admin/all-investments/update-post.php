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


if(!isset($_GET['uniq-id'])){
  echo "invalid request";
  return;
}else{
  $uniq_id = mysqli_real_escape_string($conn,$_GET['uniq-id']);
}


$user_balance = 0;
$account_level = 1;
$select_sql = "SELECT * FROM investmentlist WHERE uniq_id='{$uniq_id}' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $investment_name = $select_res_data['investment_name'];
  $investment_details = $select_res_data['investment_details'];
  $investment_hourly_income = $select_res_data['investment_hourly_income'];
  $investment_price = $select_res_data['investment_price'];
  $investment_total_days = $select_res_data['investment_total_days'];
  $investment_image_url = $select_res_data['investment_image_url'];
  $investment_status = $select_res_data['investment_status'];
}else{
  echo 'Invalid uniq-Id!';
  return;
}

// update settings btn
if (isset($_POST['submit'])){
    
  $invest_name = $_POST['invest_name'];
  $invest_description = $_POST['invest_description'];
  $invest_hourly_income = "1";
  $invest_price = $_POST['invest_price'];
  $invest_total_days = "100000";
  $invest_image_url = $_POST['invest_image_url'];
  
  if($invest_image_url!="" && $invest_description!=""){
  
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-m-Y h:i a');
  
  $update_sql = "UPDATE investmentlist SET investment_name='{$invest_name}',investment_details='{$invest_description}',investment_hourly_income='{$invest_hourly_income}',investment_price='{$invest_price}',investment_total_days='{$invest_total_days}',investment_image_url='{$invest_image_url}' WHERE uniq_id='{$uniq_id}'";    
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  
  if ($update_result){ ?>
  
  <script>
    alert('Post Updated!!');
  </script>
    
  <?php }else{ ?>
  
  <script>
    alert('Failed to update post!');
  </script>

<?php } } } ?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Update Account</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400&display=swap');
*{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'popins', sans-serif;
}
.main{
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  position: relative;
  margin: 0 0 20px 0;
  background: rgba(0,0,0,0.02);
}
form{
  width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 15px 0;
  border-radius: 5px;
  background: #ffffff;
  box-shadow: 0.1px 2px 8px 4px rgba(0, 0, 0, 0.05);
}

form div{
  width: 85%;
  margin: 7px 0;
}

input[type="text"],input[type="number"],textarea{
   width: 85%;
   height: 50px;
   margin: 10px 0;
   font-size: 20px;
   padding: 0 10px;
   border: 1px solid rgba(0,0,0,0.09);
}

form div > input, form div > textarea{
  width: 100% !important;
}

form textarea{
  height: 150px;
  padding: 10px;
  resize: none;
}

.control_btn{
  width: 85%;
   height: 50px;
   margin-top: 30px;
   cursor: pointer;
   font-size: 22px;
   color: #ffffff;
   outline: none;
   border: none;
   background-color: #3949AB;
}
.bg_red{
    background: red !important;
}


.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 30px;
  margin-top: 10px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 8px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(22px);
  -ms-transform: translateX(22px);
  transform: translateX(22px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 30px;
}

.slider.round:before {
  border-radius: 50%;
}

@media (max-width: 500px) {
  form{
    width: 95%;
    height: 100%;
  }
}
</style>
</head>
<body>

<div class="main">
  <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
 	<h3><i class='bx bx-receipt'></i>&nbsp;Update Post</h3><br>
 	
 	<div>
 	  <p>Investment Name</p>
   	  <input type="text" name="invest_name" placeholder="Enter Investment Name" value="<?php echo $investment_name; ?>" required>
 	</div>
 	
    <div>
 	  <p>Investment Description</p>
 	  <textarea name="invest_description" placeholder="Enter Investment Description"><?php echo $investment_details; ?></textarea>
  	</div>
 	
 	<div>
 	  <p>Investment Price</p>
 	  <input type="text" name="invest_price" placeholder="Enter Investment Price" value="<?php echo $investment_price; ?>" required>
 	</div>
 	
 	<!--<div>-->
 	<!--  <p>Hourly Income</p>-->
 	<!--  <input type="text" name="invest_hourly_income" placeholder="Enter Hourly Income" value="<?php echo $investment_hourly_income; ?>" required>-->
 	<!--</div>-->
 	
 	<!--<div>-->
 	<!--  <p>Total Days</p>-->
 	<!--  <input type="text" name="invest_total_days" placeholder="Enter Total days" value="<?php echo $investment_total_days; ?>" required>-->
 	<!--</div>-->
 	
 	<div>
 	  <p>Investment Image URL</p>
 	  <textarea name="invest_image_url" placeholder="Enter Image URL"><?php echo $investment_image_url; ?></textarea>
  	</div>
    
    
 	<input type="submit" name="submit" value="Update Post" class="control_btn">
  </form>
  
</div>

</body>
</html>