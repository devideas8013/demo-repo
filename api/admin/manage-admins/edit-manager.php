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

if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

$select_sql = "SELECT * FROM adminauth WHERE uniq_id='$user_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);
  
  $user_id = "XXXXXXXXXX";
  if($account_category=="admin"){
    $user_id = $select_res_data['user_id'];    
  }
  $uniq_id = $select_res_data['uniq_id'];
  $user_category = $select_res_data['user_category'];
  $user_joined = $select_res_data['date_time'];
  
}else{
  echo 'Invalid user-id!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Admin</title>
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
  background: #28B463;
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
    <h3><i class='bx bx-user-circle' ></i>&nbsp;User ID: <?php echo $user_id; ?></h3>

    <br>
    <p>Account Id: <?php echo $user_id; ?></p>
    <p>Account Category: <?php echo $user_category; ?></p>
    <p>Account Created: <?php echo $user_joined; ?></p>
    
    </br>
    <?php if($account_category=="admin"){ ?>
    <button class="action_btn reject_btn" onclick="removeAccount('<?php echo $uniq_id; ?>')">Remove Account</button>
    <a href="../update-account.php?uniq-id=<?php echo $uniq_id; ?>&access-code=<?php echo $admin_acccess; ?>" class="action_btn">Update Password&nbsp;<i class='bx bx-chevron-right' ></i></a>
    <?php } ?>
</div>

</div>

<script>
  function removeAccount(admin_uniq_id){
    if(confirm("Are you sure you want to remove this account?")){
        window.open("../remove-account.php?uniq-id="+admin_uniq_id);
    }
  }

//   function ActiveAccount(){
//     window.open("../update-order-request.php?access-code=<?php echo $admin_acccess; ?>&request-type=true&user-id=<?php echo $user_id; ?>");
//   }
</script>
    
</body>
</html>