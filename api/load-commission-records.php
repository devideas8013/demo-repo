<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');

// * EXPLAIN **
// -initializing array

$resArr = array();
$resArr['data'] = array();
$resArr['pagination'] = "false";

if($_SERVER['REQUEST_METHOD'] == 'POST' || 
$_SERVER['REQUEST_METHOD'] == 'GET') {

// * EXPLAIN *
// -setting up pre constants

$user_id = "";
$page_num = "";
$content = 25;
$todays_income = 0;
$total_income = 0;
$todays_invite = 0;
$total_invite = 0;


// * EXPLAIN **
// -checking for GET & POST request

if(isset($_POST['USER_ID']) && isset($_POST['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_POST['PAGE_NUM']);
}

if(isset($_GET['USER_ID']) && isset($_GET['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_GET['PAGE_NUM']);
}

if($user_id!="" && $page_num!=""){
    
// * EXPLAIN **
// -getting invite users list
$invite_sql = "SELECT user_joined FROM  usersdata WHERE user_refered_by='{$user_id}' ";
$invite_query = mysqli_query($conn,$invite_sql);

while($row = mysqli_fetch_assoc($invite_query)){
    $total_invite++;
    if($curr_date==substr($row['user_joined'], 0, 10)){
        $todays_invite++;
    }
}

// * EXPLAIN **
// -getting total transactions of this user

$total_income_sql = "SELECT type,amount FROM  othertransactions WHERE user_id='{$user_id}' AND type='commision' ";
$total_income_query = mysqli_query($conn,$total_income_sql);
    
while($row = mysqli_fetch_assoc($total_income_query)){
  $total_income += $row['amount'];
}


// * EXPLAIN **
// -getting list of transactions

$offset = ($page_num-1)*$content;
$select_sql = "SELECT * FROM  othertransactions WHERE user_id='{$user_id}' AND type='commision' ORDER BY id DESC LIMIT {$offset},{$content} ";
$select_query = mysqli_query($conn,$select_sql);
    
while($row = mysqli_fetch_assoc($select_query)){
  $index['t_amount'] = $row['amount'];
  $index['t_receive_from'] = $row['receive_from'];
  $index['t_type'] = $row['type'];
  $index['t_msg'] = $row['extra_msg'];
  $index['t_date'] = substr($row['date_time'], 0, 5);
  $index['t_time'] = substr($row['date_time'], 11);
  
  if($curr_date==substr($row['date_time'], 0, 10)){
    $todays_income += $row['amount'];
  }

  array_push($resArr['data'], $index);
}

$numRows = mysqli_num_rows($select_query);

// * EXPLAIN **
// -checking for number of pages available

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

$resArr['todays_income'] = number_format($todays_income, 2, ".", "");
$resArr['total_income'] = number_format($total_income, 2, ".", "");
$resArr['todays_invite'] = $todays_invite;
$resArr['total_invite'] = $total_invite;

}else{
 $resArr['status_code'] = "invalid_params";
}


// * EXPLAIN **
// -closing mysqli db & responding json data

mysqli_close($conn);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resArr);
}
?>