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
$content = 150;

if(isset($_POST['USER_ID']) && isset($_POST['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_POST['PAGE_NUM']);
}

if(isset($_GET['USER_ID']) && isset($_GET['PAGE_NUM'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
  $page_num = mysqli_real_escape_string($conn,$_GET['PAGE_NUM']);
}

if($user_id!="" && $page_num!=""){

$offset = ($page_num-1)*$content;
$select_sql = "SELECT * FROM  othertransactions WHERE user_id='{$user_id}' ORDER BY id DESC LIMIT {$offset},{$content} ";
$select_query = mysqli_query($conn,$select_sql);
    
while($row = mysqli_fetch_assoc($select_query)){
    if($row['type']=="signupbonus"){
      $index['t_title'] = "Signup Bonus";
    }else if($row['type']=="lifafa"){
      $index['t_title'] = "Lifafa"; 
    }else if($row['type']=="investmentbonus"){
      $index['t_title'] = "Investment Bonus"; 
    }else if($row['type']=="commision"){
      $index['t_title'] = $row['extra_msg'].' Bonus';
    }else{
      $index['t_title'] = $row['extra_msg'];
    }
      
    $index['t_amount'] = $row['amount'];
    $index['t_receive_from'] = $row['receive_from'];
    $index['t_msg'] = $row['extra_msg'];
    $index['t_time_stamp'] = $row['date_time'];

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

}else{
 $resArr['status_code'] = "invalid_params";
}

$conn->close();
echo json_encode($resArr);
}
?>