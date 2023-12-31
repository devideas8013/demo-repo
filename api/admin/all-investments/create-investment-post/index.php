<?php
define("ACCESS_SECURITY","true");
include '../../../security/config.php';
include '../../../security/constants.php';

session_start();

if (!isset($_SESSION["pb_admin_user_id"])) {
 header('location:../../../index.php');
}else{
 $session_id = $_SESSION["pb_admin_user_id"];
}

if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../../../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_users_data", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}
 

// in-app message
if (isset($_POST['submit'])){
    
 function generateRandomNumber($length){
    $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
        return $randomString;
  }
    
  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-M-Y h:i:s a');
    
  $invest_id = generateRandomNumber(16);
  $invest_name = $_POST['invest_name'];
  $invest_description = $_POST['invest_description'];
  $invest_hourly_income = "1";
  $invest_price = $_POST['invest_price'];
  $invest_total_days = "100000";
  $invest_image_url = $_POST['invest_image_url'];

  $insert_sql = "INSERT INTO investmentlist(uniq_id,investment_name,investment_details,investment_hourly_income,investment_price,investment_total_days,investment_image_url,investment_status) VALUES('{$invest_id}','{$invest_name}','{$invest_description}','{$invest_hourly_income}','{$invest_price}','{$invest_total_days}','{$invest_image_url}','true')";
  $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');
    
  if($insert_result){
    echo "<script>alert('Investment Post Created!');window.history.back();</script>";
        
  }else{ ?>
  
  <script>
    alert('Failed to create post!');
  </script>

<?php } } ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
<title>Manage: Create Investment Post</title>
<style>

*{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
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
 	<h3><i class='bx bx-receipt'></i>&nbsp;Investment Post</h3><br>
 	
 	<div>
 	  <p>Investment Name</p>
   	  <input type="text" name="invest_name" placeholder="Investment Name" required>
 	</div>
 	
    <div>
 	  <p>Investment Description</p>
 	  <textarea name="invest_description" placeholder="Investment Description"></textarea>
  	</div>
  	
  	<div>
 	  <p>Investment Price</p>
 	  <input type="text" name="invest_price" placeholder="Investment Price" required>
  	</div>
  	
  	<!--<div>-->
 	 <!-- <p>Hourly Income</p>-->
 	 <!-- <input type="text" name="invest_hourly_income" placeholder="Hourly Income" required>-->
  	<!--</div>-->
  	
  	<!--<div>-->
 	 <!-- <p>Total Days</p>-->
 	 <!-- <input type="text" name="invest_total_days" placeholder="Total Days" required>-->
  	<!--</div>-->
  	
  	<div>
 	  <p>Invest Image</p>
 	  <textarea name="invest_image_url" placeholder="Image URL"></textarea>
  	</div>

 	<input type="submit" name="submit" value="Create Post" class="control_btn">
  </form>
</div>
    
</body>
</html>