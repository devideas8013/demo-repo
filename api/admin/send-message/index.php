<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
  header('location:../index.php');
}else{
  $session_id = $_SESSION["pb_admin_user_id"];
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
 
if (in_array("access_message", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../components/header.php"; ?>
  <title>Admin: Send Message</title>
<style>
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
    width: 450px;
    padding: 18px;
    background: #fff;
    box-shadow: 0.1px 2px 8px 4px rgba(0, 0, 0, 0.1);
}

.content .bank_details{
    margin-top: 15px;
}

.content #sub_heading{
    font-weight: bold;
    font-size: 16px;
    margin-top: 12px;
}

.content #small_txt{
    font-size: 15px;
    margin-top: 10px;
}

.content .bank_details p{
    margin-top: 5px;
}

.content .action_btn{
    border: none;
    outline: none;
    color: #fff;
    cursor: pointer;
    padding: 12px 18px;
    border-radius: 5px;
    background: <?php echo $ADMIN_COLOR; ?>;
    font-size: 16px;
    margin-top: 20px;
    text-decoration: none;
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
    <h3>Send Message</h3>

    <div class="select_op_box">
      <p>Title</p>
      <input type="text" name="message_title" placeholder ="Message Title" id="message_title" required>
    </div>

    <div class="select_op_box">
      <p>Message</p>
      <input type="text" name="message_description" placeholder ="Message Description" id="message_description" required>
    </div>

   <?php if($account_category=="admin"){ ?>
     <button class="action_btn" onclick="SendMessage()">Send new message</button>  
     <button class="action_btn" style="background: #28B463;" onclick="SendNotice()">Send Notice&nbsp<i class='bx bx-send'></i></button> 
   <?php } ?>

</div>

</div>

<script>
  let in_msg_title,in_msg_desc;
  let msg_title = document.querySelector("#message_title");
  let msg_description = document.querySelector("#message_description");

  function SendMessage(){
    in_msg_title = msg_title.value;
    in_msg_desc = msg_description.value;

    if(in_msg_title!="" && in_msg_desc!=""){
      window.open("manage-message.php?title="+in_msg_title+"&message="+in_msg_desc);
    }else{
      alert("Invalid data!");
    }
  }
  
  function SendNotice(){
    in_msg_title = msg_title.value;
    in_msg_desc = msg_description.value;

    if(in_msg_title!="" && in_msg_desc!=""){
      window.open("manage-notice.php?title="+in_msg_title+"&message="+in_msg_desc);
    }else{
      alert("Invalid data!");
    }
  }
</script>
    
</body>
</html>