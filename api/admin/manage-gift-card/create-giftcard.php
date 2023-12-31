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
 
if (in_array("access_gift", $account_access_arr)){
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

$service_id = "GIFT_CODE";

// update settings btn
if (isset($_POST['submit'])){
  $gift_card_id = $_POST['gift_card_id'];
  $gift_card_reward = $_POST['gift_card_reward'];
  $gift_card_limit = $_POST['gift_card_limit'];
  $input_balance_required = $_POST['input_balance_required'];
  $input_single_user_id = $_POST['input_single_user_id'];
  
  if($input_balance_required==""){
      $input_balance_required = "none";
  }
  
  if($input_single_user_id==""){
      $input_single_user_id = "none";
  }
  
  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-M-Y h:i:s a');
  
  $pre_sql = "SELECT * FROM allgiftcards WHERE gift_card='$gift_card_id' ";
  $pre_result = mysqli_query($conn, $pre_sql) or die('error');

  if (mysqli_num_rows($pre_result) > 0){
     echo "<script>alert('Sorry, Giftcard Already Exist!');window.history.back();</script>";
  }else{
    $insert_sql = "INSERT INTO allgiftcards(gift_card,gift_amount,gift_limit,gift_target_user,gift_balance_limit,gift_card_status,gift_date_time) VALUES('{$gift_card_id}','{$gift_card_reward}','{$gift_card_limit}','{$input_single_user_id}','{$input_balance_required}','true','{$curr_date_time}')";
    $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');
    
    if($insert_result){
      echo "<script>alert('Giftcard Created!');window.history.back();</script>";
    }   
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Gift Card</title>
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
  margin: 10px 0;
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

form div input{
   width: 100% !important;
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

.checkbox-view{
    display: flex !important;
}

.checkbox-view input{
    width: auto !important;
    margin-right: 10px;
}

.hide_view{
    display: none !important;
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
 		<h3>Manage GiftCard</h3><br>
 		<input type="text" name="service_name" placeholder="Service Name" hidden>
 		
 		<div>
 		 <p>GiftCard Id</p>
 		 <input type="text" name="gift_card_id" placeholder="GiftCard Id" required>
 		</div>
 		
 		<div>
 	 	 <p>GiftCard Reward</p>
 		 <input type="text" name="gift_card_reward" placeholder="GiftCard Reward" required>   
 		</div>
 		
 		<div>
 	 	 <p>GiftCard Limit</p>
 		 <input type="text" name="gift_card_limit" placeholder="Giftcards Limit" required>   
 		</div>
 		
 		<div class="checkbox-view">
 	      <input type="checkbox" name="balance_limit" id="balance_limit">
          <label for="balance_limit">Balance Limit</label>
        </div>
        
        <div class="balance_required_input_view hide_view">
 		 <input type="text" name="input_balance_required" placeholder="Enter Balance Required">   
 		</div>
        
        <div class="checkbox-view">
 	      <input type="checkbox" name="single_user" id="single_user">
          <label for="single_user">Single User Only</label>
        </div>
        
 		<div class="single_user_input_view hide_view">
 		 <input type="text" name="input_single_user_id" placeholder="Enter User Id">   
 		</div>
 		
 		<?php if($account_category=="admin"){ ?>
 		<input type="submit" name="submit" value="Create Card" class="control_btn">
 		<?php } ?>
 	</form>
</div>
    
<script>
    let single_user = document.querySelector("#single_user");
    let single_user_input_view = document.querySelector(".single_user_input_view");
    let balance_limit = document.querySelector("#balance_limit");
    let balance_required_input_view = document.querySelector(".balance_required_input_view");
    
    single_user.addEventListener("click", function(){
        if (single_user.checked == true){
          single_user_input_view.classList.remove("hide_view");
        } else {
          single_user_input_view.classList.add("hide_view");
        }
    })
    
    balance_limit.addEventListener("click", function(){
        if (balance_limit.checked == true){
          balance_required_input_view.classList.remove("hide_view");
        } else {
          balance_required_input_view.classList.add("hide_view");
        }
    })
</script>
    
</body>
</html>