<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();
$resArr['data'] = array();

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

$user_id = "";
$content = 5;

if(isset($_POST['USER_ID'])){
 $user_id = mysqli_real_escape_string($conn,$_POST["USER_ID"]);   
}

if(isset($_GET['USER_ID'])){
 $user_id = mysqli_real_escape_string($conn,$_GET["USER_ID"]);   
}

$select_sql = "SELECT * FROM allbankcards WHERE user_id='$user_id' ORDER BY id DESC LIMIT $content ";
$select_query = mysqli_query($conn,$select_sql);
    
while($row = mysqli_fetch_array($select_query)){
  $index['c_bank_id'] = $row['1'];
  $index['c_beneficiary'] = $row['3'];
  $index['c_bank_name'] = $row['4'];
  $index['c_bank_account'] = $row['5'];
  $index['c_bank_ifsc_code'] = $row['6'];
  $index['c_is_primary'] = $row['7'];

  array_push($resArr['data'], $index);
}

if(mysqli_num_rows($select_query) > 0){ 
  $resArr['status_code'] = "success";
}else{
  $resArr['status_code'] = "404";
}

$conn->close();
echo json_encode($resArr);
}
?>