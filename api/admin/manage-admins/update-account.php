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

if(!isset($_GET['access-code'])){
  echo "request block";
  return;
}else{
  $admin_acccess = mysqli_real_escape_string($conn,$_GET['access-code']);
}
 
if($admin_acccess!=$AdminIDAccessKey){
 echo "request block";
 return;
}

if(!isset($_GET['uniq-id'])){
  echo "invalid request";
  return;
}else{
  $user_uniq_id = mysqli_real_escape_string($conn,$_GET['uniq-id']);
}

// update settings btn
if (isset($_POST['submit'])){

  $auth_user_password = mysqli_real_escape_string($conn,password_hash($_POST["new_password"],PASSWORD_BCRYPT));
  
  $update_sql = "UPDATE adminauth SET user_password='{$auth_user_password}' WHERE uniq_id='{$user_uniq_id}'";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>

  <script>
    alert('Password updated!');
    window.history.back();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to update account!');
  </script>

<?php } }

$select_sql = "SELECT * FROM adminauth WHERE uniq_id='$user_uniq_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_mobile_num = $select_res_data['user_id'];
}else{
  echo 'Invalid User-Id!';
  return;
}

?>
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
 	
 	<div>
 	  <p>Mobile Number</p>
   	  <input type="text" name="new_mobile_number" placeholder="Enter Mobile Number" value="<?php echo $user_mobile_num; ?>" required disabled>
 	</div>
 	
    <div>
 	  <p>New Password</p>
 	  <input type="text" name="new_password" placeholder="New Password" required>
  	</div>

 	<input type="submit" name="submit" value="Update Data" class="control_btn">
  </form>
</div>
    
</body>
</html>