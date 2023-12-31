<?php

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../index.php');
}else{
 $session_code = $_SESSION["pb_admin_user_id"];
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
 
if (in_array("access_settings", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}


if(!isset($_GET['service-id'])){
  echo "invalid request";
  return;
}else{
  $service_id = mysqli_real_escape_string($conn,$_GET['service-id']);
}

// update settings btn
if (isset($_POST['submit'])){
  $service_value = $_POST['service_value'];

  $update_sql = "UPDATE allservices SET service_value='{$service_value}' WHERE id='{$service_id}'";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>

  <script>
    alert('Settings updated!');
    window.history.back();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to update setting!');
  </script>

<?php } }

$select_sql = "SELECT * FROM allservices WHERE id='$service_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $service_name = $select_res_data['service_name'];
  $service_value = $select_res_data['service_value'];
}else{
  echo 'Invalid Service-id!';
  return;
}

if($service_name=="GIFT_CODE"){
  echo "Oops! You can't update this setting from here. Please goto > dashboard > Manage GiftCard";
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Game Settings</title>
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
  width: 80%;
  margin: 7px 0;
}

input[type="text"]{
   width: 85%;
   height: 50px;
   margin: 10px 0;
   font-size: 20px;
   padding: 0 10px;
   border: 1px solid rgba(0,0,0,0.09);
}

form textarea{
  width: 85%;
  height: 150px;
  padding: 10px;
  font-size: 20px;
  resize: none;
}
.main #info-tv{
    width: 100%;
    padding: 10px;
    background: rgba(0,0,0,0.03);
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
 		<h3>Game Setting</h3><br>
 		<input type="text" name="service_name" placeholder="Service Name" value="<?php echo $service_name; ?>" required disabled>
 		<textarea name="service_value" placeholder="Service Value"><?php echo $service_value; ?></textarea>
 		<?php if($account_category=="admin"){ ?>
 		<input type="submit" name="submit" value="Update Setting" class="control_btn">
 		<?php } ?>
  </form>
</div>
    
</body>
</html>