<?php
define("ACCESS_SECURITY","true");
include '../security/config.php';
include '../security/constants.php';
 
 $host_os = "";
 if(isset($_GET['host'])){
    $host_os = mysqli_real_escape_string($conn,$_GET['host']);
 }
 
 $signin_id = "";
 if(isset($_GET['id'])){
    $signin_id = mysqli_real_escape_string($conn,$_GET['id']);
 }
 
 $signin_password = "";
 if(isset($_GET['password'])){
    $signin_password = mysqli_real_escape_string($conn,$_GET['password']);
 }
 

 session_start();
 if (isset($_SESSION["pb_admin_user_id"])) {
  header('location:dashboard.php');
 }
 
/*submit button*/
if (isset($_POST['submit'])){
      $auth_user_id = mysqli_real_escape_string($conn,$_POST['user_id']);
      $auth_user_password = mysqli_real_escape_string($conn,$_POST['password']);

      $pre_sql = "SELECT * FROM adminauth WHERE user_id='$auth_user_id' ";
      $pre_result = mysqli_query($conn, $pre_sql) or die('error');
      $pre_res_data = mysqli_fetch_assoc($pre_result);

      if (mysqli_num_rows($pre_result) > 0){
        $decoded_password = password_verify($auth_user_password,$pre_res_data['user_password']);
        if($decoded_password == 1){ if($host_os=="android"){ ?>
         <script>
            Handle.saveLogin(<?php echo $auth_user_id; ?>,<?php echo $auth_user_password; ?>);
         </script>
        <?php }
          $_SESSION["pb_admin_user_id"] = $auth_user_id;
          $_SESSION["pb_admin_category"] = $pre_res_data['user_category'];
          $_SESSION["pb_admin_access"] = $pre_res_data['user_access_list'];
          header('location:dashboard.php');
        }else{ ?>
          <script>
            alert('id & password not matched');
          </script>
        <?php } }else{ ?>
        <script>
          alert('No account exit with this ID!');
        </script>
<?php } } ?>

<!DOCTYPE html>
<html>
<head>
  <?php include "header_file.php" ?>
  <title><?php echo $APP_NAME; ?>: Admin Panel</title>
    
<style>
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
  width: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 15px 0;
  border-radius: 5px;
  background: #ffffff;
  border: 1px solid rgba(0,0,0,0.09);
}
form div{
  width: 80%;
  margin: 7px 0;
}
form p{
  color: #27AE60;
  font-size: 1.1em;
}
input[type="text"]{
   width: 85%;
   height: 50px;
   margin: 10px 0;
   font-size: 20px;
   padding: 0 10px;
   border: 1px solid rgba(0,0,0,0.09);
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
 	<p><i class='bx bxs-check-circle' ></i>&nbsp;<?php echo $APP_NAME; ?> Dashboard</p><br>
 	<input type="text" name="user_id" placeholder="Enter ID" required>
 	<input type="text" name="password" placeholder="Enter password" autocomplete="off" required>
 	<input type="submit" name="submit" value="Continue" class="control_btn">
   </form>
 </div>

</body>
</html>