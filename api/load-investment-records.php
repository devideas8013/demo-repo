<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

$resArr = array();
$resArr['data'] = array();
$resArr['pagination'] = "false";

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

$page_num = 1;
$content = 200;

if(isset($_POST['USER_ID'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
}

if(isset($_GET['USER_ID']) && isset($_GET['INVEST_STATUS'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
  $invest_status = mysqli_real_escape_string($conn,$_GET['INVEST_STATUS']);
}


if($user_id!="" && $invest_status!=""){
    
function daysLeft($boughtDate,$totalDays){
  date_default_timezone_set("Asia/Kolkata");
  $curr_date = date("d-m-Y h:i a");
  $newDate = date('d-m-Y h:i a', strtotime($boughtDate. ' + '.$totalDays.' days'));

  $date1 = new DateTime($curr_date);
  $date2 = new DateTime($newDate);

  $daysLeft = $date2->diff($date1)->format("%a");
  $daysCompleted = $totalDays-$daysLeft;
 
  return $daysCompleted;
}

$total_income = 0;
$offset = ($page_num-1)*$content;
$select_sql = "SELECT * FROM  myinvestments WHERE user_id='{$user_id}' AND investment_status='{$invest_status}' ORDER BY id DESC LIMIT {$offset},{$content} ";
$select_query = mysqli_query($conn,$select_sql);

while($row = mysqli_fetch_assoc($select_query)){
  $index['i_id'] = $row['investment_id'];
  $index['i_title'] = $row['investment_name'];
  $index['i_img_url'] = $row['investment_img'];
  $index['i_earnings'] = $row['investment_earnings'];
  $index['i_hourly_income'] = $row['investment_hourly_income'];
  $index['i_total_days'] = $row['investment_total_days'];
  
  $investment_date_time = $row['investment_date'].' '.$row['investment_time'];
  
  $index['i_days_left'] = daysLeft($investment_date_time,$row['investment_total_days']);
  
  $total_income += $row['investment_earnings'];

  array_push($resArr['data'], $index);
}

$numRows = mysqli_num_rows($select_query);
$resArr['investments_number'] = $numRows;
$resArr['total_income'] = number_format($total_income,2,".","");

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