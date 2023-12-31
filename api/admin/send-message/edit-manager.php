<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

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

session_start();
if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../index.php');
}

if (!isset($_SESSION["pb_admin_category"])) {
  header('location:../index.php');
}else{
  $account_category = $_SESSION["pb_admin_category"];
}

if(!isset($_GET['id'])){
  echo "invalid request";
  return;
}else{
  $message_id = mysqli_real_escape_string($conn,$_GET['id']);
}

$select_sql = "SELECT * FROM automessages WHERE id='$message_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);
 
  $message_title = $select_res_data['message_title'];
  $message_desc = $select_res_data['message_desc'];
  $message_set_on = $select_res_data['message_set_on'];
  $message_status = $select_res_data['status'];
  
}else{
  echo 'Invalid message-id!';
  return;
}

if (isset($_POST['submit'])){
 $message_title = $_POST['message_title'];
 $message_desc = $_POST['message_description'];
 $message_set_time = $_POST['message_set_time'];
 
 $update_sql = "UPDATE automessages SET message_title='{$message_title}', message_desc='{$message_desc}', message_set_on='{$message_set_time}' WHERE id='{$message_id}'";    
 $update_result = mysqli_query($conn, $update_sql) or die('error');
  
 if ($update_result) { ?>
      <script>
        alert('Message Updated!');
        window.history.back();
      </script>
 <?php }else{ ?>

    <script>
      alert('Failed to update auto message!');
      window.history.back();
    </script>

 <?php } }
 
 if (isset($_POST['disable'])){

  if($message_status=="true"){
    $updated_status = "false";
  }else{
    $updated_status = "true";
  }

 $update_sql = "UPDATE automessages SET status='{$updated_status}' WHERE id='{$message_id}'";    
 $update_result = mysqli_query($conn, $update_sql) or die('error');
  
 if ($update_result) {
  if($updated_status=="true"){ ?>
    <script>
      alert('Message Enabled!');
      window.history.back();
    </script>
 <?php }else{ ?>
    <script>
      alert('Message Disabled!');
      window.history.back();
    </script>
 <?php } }else{ ?>

    <script>
      alert('Failed to update auto message!');
      window.history.back();
    </script>

 <?php } }
 
  if (isset($_POST['delete'])){

  if($message_status=="true"){
    $updated_status = "false";
  }else{
    $updated_status = "true";
  }

 $delete_sql = "DELETE FROM automessages WHERE id='{$message_id}' ";  
 $delete_result = mysqli_query($conn, $delete_sql) or die('error');
  
 if ($delete_result) { ?>
    <script>
      alert('Message Deleted!');
      window.history.back();
    </script>
 <?php }else{ ?>
    <script>
      alert('Failed to update auto message!');
      window.history.back();
    </script>
 <?php } } ?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Auto Message</title>
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

input{
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

.control_btn,.sub_control_btn{
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
.sub_control_btn{
  height: 45px;
  margin-top: 10px !important;  
  background-color: #E74C3C;
}
.disable_btn{
    background-color: #F5B041 !important;
}

.enable_btn{
    background-color: #2ECC71 !important;
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
 	<h3>Manage Message</h3><br>
 	
 	<div>
 	  <p>Message Title</p>
   	  <input type="text" name="message_title" placeholder="Message Title" value="<?php echo $message_title; ?>" required>
 	</div>
 	
    <div>
 	  <p>Meesage Description</p>
 	  <input type="text" name="message_description" placeholder="Message Description" value="<?php echo $message_desc; ?>" required>
  	</div>
 	
 	<div>
 	  <p>Set Time</p>
 	  <input type="time" name="message_set_time" placeholder="Set time" value="<?php echo $message_set_on; ?>" required>
 	</div>

 	<input type="submit" name="submit" value="Update Message" class="control_btn">
 	
 	<?php if($message_status=="true"){ ?>
 	  <input type="submit" name="disable" value="Disable Message" class="control_btn sub_control_btn disable_btn">
 	<?php }else{ ?>
 	  <input type="submit" name="disable" value="Enable Message" class="control_btn sub_control_btn enable_btn">
 	<?php } ?>
 	
 	<input type="submit" name="delete" value="Delete Message" class="control_btn sub_control_btn">
  </form>
</div>
    
</body>
</html>