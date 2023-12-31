<?php
session_start();
 
if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_investments", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

if(isset($_GET['imageid'])){
  $imgID = $_GET['imageid'];
}

if($imgID != ""){
    define("ACCESS_SECURITY","true");
    include '../../security/config.php';
    
    $sql = "DELETE FROM allimages WHERE imageid='{$imgID}' ";
    $result = mysqli_query($conn, $sql) or die('query failed');

    if($result){ 
        unlink("../../../storage/images/".$imgID); ?>
        <script>
           alert("Image deleted successfully!");
           window.close();
        </script>
    <?php }else{ ?>
    <script>
        alert("Failed to delete image!");
    </script>
    <?php }

} ?>