<?php
define("ACCESS_SECURITY","true");
include '../../../security/config.php';
include '../../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../../index.php');
}else{
    $session_id = $_SESSION["pb_admin_user_id"];
}

if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_users_data", $account_access_arr)){
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

if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_uniq_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}
 

// in-app message
if (isset($_POST['submit'])){
    
  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-M-Y h:i:s a');
    
  $message_title = $_POST['message_title'];
  $message_description = $_POST['message_description'];
  $final_message = $message_title.','.$message_description;

  $update_sql = "UPDATE usersdata SET in_app_message='{$final_message}' WHERE uniq_id='{$user_uniq_id}'";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){
      
    $insert_sql = "INSERT INTO allnotices(user_id,notice_title,notice_details,notice_timestamp) VALUES('{$user_uniq_id}','{$message_title}','{$message_description}','{$curr_date_time}')";
    $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');
    
    if($insert_result){
        echo "<script>alert('Notice Created!');window.history.back();</script>";
        
    } }else{ ?>
  
  <script>
    alert('Failed to sent message!');
  </script>

<?php } } ?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../../components/header.php"; ?>
<title>Manage: Notice</title>
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
   resize: none;
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
   background-color: <?php echo $ADMIN_COLOR; ?>;
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
 	<h3><i class='bx bx-user-circle' ></i>&nbsp;Notice</h3><br>
 	
 	<div>
 	  <p>Message Title</p>
   	  <input type="text" name="message_title" placeholder="Message Title" required>
 	</div>
 	
    <div>
 	  <p>Message Description</p>
 	  <textarea name="message_description" placeholder="Message Description"></textarea>
  	</div>

 	<input type="submit" name="submit" value="Send Message" class="control_btn">
  </form>
</div>
    
</body>
</html>