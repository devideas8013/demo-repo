<?php
header("Access-Control-Allow-Origin: *");

define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();
$resArr['data'] = array();
$resArr['pagination'] = "false";

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

$user_id = "";
$page_num = "";
$content = 30;

if(isset($_POST['USER_ID']) && isset($_POST['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_POST['PAGE_NUM']);
}

if(isset($_GET['USER_ID']) && isset($_GET['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_GET['PAGE_NUM']);
}

$offset = ($page_num-1)*$content;
$select_sql = "SELECT * FROM userswithdraw WHERE user_id='{$user_id}' ORDER BY id DESC LIMIT {$offset},{$content} ";
$select_query = mysqli_query($conn,$select_sql);
    
while($row = mysqli_fetch_assoc($select_query)){
  $index['w_uniq_id'] = $row['uniq_id'];
  $index['w_amount'] = $row['withdraw_amount'];
  $index['w_request'] = $row['withdraw_request'];
  $index['w_beneficiary'] = $row['actual_name'];
  $index['w_bank_name'] = $row['bank_name'];
  $index['w_bank_account'] = $row['bank_account'];
  $index['w_bank_ifsc'] = $row['bank_ifsc_code'];
  $index['w_date'] = substr($row['request_date_time'], 0, 5);
  $index['w_time'] = substr($row['request_date_time'], 11);
  $index['w_status'] = $row['request_status'];

  array_push($resArr['data'], $index);
}

$numRows = mysqli_num_rows($select_query);

if($page_num>1){
  if($numRows > 0){ 
    $resArr['status_code'] = "success";
  }else{
    $resArr['status_code'] = "no_more";
  }

  $resArr['pagination'] = "true";
}else{
  if($numRows > 0){ 
    $resArr['status_code'] = "success";
  }else{
    $resArr['status_code'] = "404";
  }

  if($numRows >= $content){
    $resArr['pagination'] = "true";
  }
}

mysqli_close($conn);
echo json_encode($resArr);
}
?>