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
 
if (in_array("access_admins", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

if (!isset($_SESSION["pb_admin_category"])) {
  header('location:../index.php');
}else{
  $account_category = $_SESSION["pb_admin_category"];
}

// update settings btn
if (isset($_POST['submit'])){
  
  $account_category = mysqli_real_escape_string($conn,$_POST["account_category"]);
  $controller_id = mysqli_real_escape_string($conn,$_POST["controller_id"]);
  $auth_user_id = mysqli_real_escape_string($conn,$_POST["signup_mobile"]);
  $auth_user_password = mysqli_real_escape_string($conn,password_hash($_POST["signup_password"],PASSWORD_BCRYPT));
  
  
  $user_access_list = "";
  
  $access_investments = mysqli_real_escape_string($conn,$_POST["access_investments"]);
  if($access_investments=="on"){
      $user_access_list .= "access_investments,";
  }
  
  $access_users_data = mysqli_real_escape_string($conn,$_POST["access_users_data"]);
  if($access_users_data=="on"){
      $user_access_list .= "access_users_data,";
  }
  
  $access_recharge = mysqli_real_escape_string($conn,$_POST["access_recharge"]);
  if($access_recharge=="on"){
      $user_access_list .= "access_recharge,";
  }
  
  $access_withdraw = mysqli_real_escape_string($conn,$_POST["access_withdraw"]);
  if($access_withdraw=="on"){
      $user_access_list .= "access_withdraw,";
  }
  
  $access_help = mysqli_real_escape_string($conn,$_POST["access_help"]);
  if($access_match=="on"){
      $access_help .= "access_help,";
  }
  
  $access_message = mysqli_real_escape_string($conn,$_POST["access_message"]);
  if($access_message=="on"){
      $user_access_list .= "access_message,";
  }
  
  $access_settings = mysqli_real_escape_string($conn,$_POST["access_settings"]);
  if($access_settings=="on"){
      $user_access_list .= "access_settings,";
  }
  
  $access_admins = mysqli_real_escape_string($conn,$_POST["access_admins"]);
  if($access_admins=="on"){
      $user_access_list .= "access_admins,";
  }
  
  
  $user_access_list = substr($user_access_list, 0, -1);

  function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  $unique_id = generateRandomString();

  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-M-Y h:i:s a');

  if($auth_user_id != "" && $account_category!=""){

    $pre_sql = "SELECT * FROM adminauth WHERE uniq_id='$unique_id' or user_id='$auth_user_id' ";
    $pre_result = mysqli_query($conn, $pre_sql) or die('error');
  
    if (mysqli_num_rows($pre_result) <= 0){
     $insert_sql = "INSERT INTO adminauth(uniq_id,user_id,user_password,user_category,user_access_list,date_time) VALUES('{$unique_id}','{$auth_user_id}','{$auth_user_password}','{$account_category}','{$user_access_list}','{$curr_date_time}')";
     $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');
     if($insert_result){
        echo "<script>alert('New account Created!');window.history.back();</script>";
     }

    }else{
      echo "Entered mobile or uniqid is already registered!";
      return;
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>New: Add Admin</title>
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

form > div{
  width: 85%;
  margin: 7px 0;
}

input[type="text"],input[type="number"],select{
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

form div > select{
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
   background-color: <?php echo $ADMIN_COLOR; ?>;
}

.access-checkbox{
    display: flex;
    align-items: center;
    margin-top: 10px;
    
    -webkit-user-select: none; /* Safari */
    -ms-user-select: none; /* IE 10 and IE 11 */
    user-select: none; /* Standard syntax */
}
.access-checkbox input{
    width: auto !important;
    margin-right: 10px;
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
 	<h3><i class='bx bx-user-circle' ></i>&nbsp;New Account</h3><br>
   	<input type="text" name="controller_id" placeholder="Controller Id" hidden>
   	  
    <div style="display: none;">
 	  <p>Account Category</p>
      <select name="account_category" required>
       <option value="admin" selected="true">Admin</option>
      </select>
 	</div>
 	
 	<div>
 	  <p>Mobile Number</p>
   	  <input type="text" name="signup_mobile" placeholder="Enter Mobile Number" required>
 	</div>
 	
 	<div>
 	  <p>Password</p>
 	  <input type="text" name="signup_password" placeholder="Enter Password" required>
 	</div>
 	
 	<div>
 	 <p>Access List</p>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_all" id="access_all">
        <label for="access_all">All Access</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_investments" id="access_investments">
        <label for="access_investments">Access Investments</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_users_data" id="access_users_data">
        <label for="access_users_data">Access Users Data</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_recharge" id="access_recharge">
        <label for="access_recharge">Access Recharge</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_withdraw" id="access_withdraw">
        <label for="access_withdraw">Access Withdraw</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_help" id="access_help">
        <label for="access_help">Access Help Desk</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_message" id="access_message">
        <label for="access_message">Access Message</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_settings" id="access_settings">
        <label for="access_settings">Access Settings</label>
 	 </div>
 	 
 	 <div class="access-checkbox">
 	    <input type="checkbox" name="access_admins" id="access_admins">
        <label for="access_admins">Access Admins</label>
 	 </div>
     
 	</div>
 	
 	<?php if($account_category=="admin"){ ?>
 	  <input type="submit" name="submit" value="Create Account" class="control_btn">
 	<?php } ?>
  </form>
</div>

<script>
    let access_all = document.querySelector("#access_all");
    let access_checkbox = document.querySelectorAll(".access-checkbox");
    let access_checkbox_input = document.querySelectorAll(".access-checkbox input");
    
    access_all.addEventListener("click", function(){
        if (access_all.checked == true){
          enableDisableAll("enable");
        } else {
          enableDisableAll("disable");
        }
    })
    
    function enableDisableAll(type){
      for (let i = 0; i < access_checkbox_input.length; i++) {
          if(type=="enable"){
            if(i!=0){
              access_checkbox_input[i].checked = true;
            }
          }else{
            if(i!=0){
              access_checkbox_input[i].checked = false;
            }
          }
      }
    }

    for (let i = 0; i < access_checkbox_input.length; i++) {
        access_checkbox_input[i].addEventListener("click", ()=>{
            if(i!=0){
                if (access_checkbox_input[i].checked != true){
                    access_checkbox_input[0].checked = false;
                }
            }
        })
    }
</script>
    
</body>
</html>