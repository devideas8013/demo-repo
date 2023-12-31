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
 
if (in_array("access_users_data", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}


if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_uniq_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

function generateOrderID($length = 15) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return 'RR0'.$randomString;
}

$uniqId = generateOrderID();

$user_balance = 0;
$account_level = 1;
$select_sql = "SELECT * FROM usersdata WHERE uniq_id='$user_uniq_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_mobile_num = $select_res_data['user_mobile_num'];
  $user_email_id = $select_res_data['user_email_id'];
  $user_balance = $select_res_data['user_balance'];
  $account_level = $select_res_data['account_level'];
}else{
  echo 'Invalid User-Id!';
  return;
}

// update settings btn
if (isset($_POST['submit'])){
    
  $new_user_balance = $_POST['new_user_balance'];
  
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-m-Y h:i a');

  if($new_user_balance < $user_balance){
    $update_balance = $user_balance - $new_user_balance;
  }else{
    $update_balance = $new_user_balance - $user_balance;
  }

  if($account_level < 2){
    $update_sql = "UPDATE usersdata SET user_balance='{$new_user_balance}',account_level='2' WHERE uniq_id='{$user_uniq_id}'";     
  }else{
    $update_sql = "UPDATE usersdata SET user_balance='{$new_user_balance}' WHERE uniq_id='{$user_uniq_id}'";    
  }
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  
  if ($update_result){
    if($new_user_balance < $user_balance){
      $request_status = "deducted";
      $update_balance = "-".$update_balance;
    }else{
      $request_status = "success";
    }
    
    $recharge_mode = "Manual";
    $recharge_details = "Manual-Method";
    
    $insert_sql = $conn->prepare("INSERT INTO usersrecharge(uniq_id,user_id,recharge_amount,recharge_mode,recharge_details,request_status,request_date_time) VALUES(?,?,?,?,?,?,?)");
    $insert_sql->bind_param("sssssss", $uniqId,$user_uniq_id,$update_balance,$recharge_mode, $recharge_details,$request_status,$curr_date_time);
    $insert_sql->execute();
  
    if ($insert_sql->error == "") { ?>
      <script>
        alert('Account updated!');
        window.history.back();
      </script>
  <?php }else{ ?>

    <script>
      alert('Failed to update!');
      window.history.back();
    </script>

  <?php } }else{ ?>
  
  <script>
    alert('Failed to update account!');
  </script>

<?php } } ?>

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

input[type="text"],input[type="number"]{
   width: 85%;
   height: 50px;
   margin: 10px 0;
   font-size: 20px;
   padding: 0 10px;
   border: 1px solid rgba(0,0,0,0.09);
}

form div > input{
  width: 100% !important;
}

form textarea{
  width: 85%;
  height: 150px;
  padding: 10px;
  font-size: 20px;
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
 	<h3><i class='bx bx-user-circle' ></i>&nbsp;Update Account</h3><br>
 	<input type="text" name="user_uniq_id" placeholder="Enter Unique Id" value="<?php echo $user_uniq_id; ?>" required disabled>
 	
 	<div>
 	  <p>Mobile Number</p>
   	  <input type="text" name="new_mobile_number" placeholder="Enter Mobile Number" value="<?php echo $user_mobile_num; ?>" required disabled>
 	</div>
 	
 	<div>
 	  <p>Account Balance</p>
 	  <input type="text" name="new_user_balance" placeholder="Enter Balance" value="<?php echo $user_balance; ?>" required>
 	</div>
    
 	<input type="submit" name="submit" value="Update Data" class="control_btn">
  </form>
  
</div>

</body>
</html>