<?php
if(isset($_GET['title']) && isset($_GET['message'])){
  define("ACCESS_SECURITY","true");
  require_once("../../security/config.php");
  $message_title = $_GET['title'];
  $message_description = $_GET['message'];
  $final_message = $message_title.','.$message_description;

  $update_sql = "UPDATE usersdata SET in_app_message='{$final_message}' ";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>
    <script>
      alert("Notice sended!");
      window.close();
    </script>
<?php } mysqli_close($conn); } ?>