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
$content = 10;

if(isset($_POST['USER_ID']) && isset($_POST['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_POST['PAGE_NUM']);
}

if(isset($_GET['USER_ID']) && isset($_GET['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_GET['PAGE_NUM']);
}

$offset = ($page_num-1)*$content;
$select_sql = "SELECT * FROM usersrecharge WHERE user_id='{$user_id}' ORDER BY id DESC LIMIT {$offset},{$content} ";
$select_query = mysqli_query($conn,$select_sql);
    
while($row = mysqli_fetch_assoc($select_query)){
  $index['r_uniq_id'] = $row['uniq_id'];
  $index['r_amount'] = $row['recharge_amount'];
  $index['r_mode'] = $row['recharge_mode'];
  $index['r_details'] = $row['recharge_details'];
  $index['r_date'] = substr($row['request_date_time'], 0, 5);
  $index['r_time'] = substr($row['request_date_time'], 11);
  $index['r_status'] = $row['request_status'];

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