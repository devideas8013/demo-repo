<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');

// * EXPLAIN **
// -initializing array

$resArr = array();
$resArr['pagination'] = "false";

if($_SERVER['REQUEST_METHOD'] == 'POST' || 
$_SERVER['REQUEST_METHOD'] == 'GET') {

// * EXPLAIN *
// -setting up pre constants

$user_id = "";
$page_num = 1;
$content = 25;
$todays_income = 0;
$total_income = 0;
$todays_invite = 0;
$total_invite = 0;

$total_level_1 = 0;
$total_level_2 = 0;
$total_level_3 = 0;
$total_level_4 = 0;
$total_level_5 = 0;

$total_level_1_bonus = 0;
$total_level_2_bonus = 0;
$total_level_3_bonus = 0;
$total_level_4_bonus = 0;
$total_level_5_bonus = 0;


// * EXPLAIN **
// -checking for GET & POST request

if(isset($_POST['USER_ID'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
}

if(isset($_GET['USER_ID'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
}

if($user_id!="" && $page_num!=""){
    
// * EXPLAIN **
// -getting invite users list
$invite_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$user_id}' ";
$invite_query = mysqli_query($conn,$invite_sql);

while($row = mysqli_fetch_assoc($invite_query)){
    $total_invite++;
    $total_level_1++;
    
    $level_2_refered_id = $row['uniq_id'];
    
    $invite_2_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_2_refered_id}' ";
    $invite_2_query = mysqli_query($conn,$invite_2_sql);
    
    while($row2 = mysqli_fetch_assoc($invite_2_query)){
        $total_invite++;
        $total_level_2++;
        
        $level_3_refered_id = $row2['uniq_id'];
        
        $invite_3_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_3_refered_id}' ";
        $invite_3_query = mysqli_query($conn,$invite_3_sql);
    
        while($row3 = mysqli_fetch_assoc($invite_3_query)){
          $total_invite++; 
          $total_level_3++;
          
          $level_4_refered_id = $row3['uniq_id'];
          
          $invite_4_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_4_refered_id}' ";
          $invite_4_query = mysqli_query($conn,$invite_4_sql);
    
          while($row4 = mysqli_fetch_assoc($invite_4_query)){
            $total_invite++; 
            $total_level_4++;
          
            $level_5_refered_id = $row4['uniq_id'];
            
            $invite_5_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_5_refered_id}' ";
            $invite_5_query = mysqli_query($conn,$invite_5_sql);
    
            while($row4 = mysqli_fetch_assoc($invite_5_query)){
              $total_invite++; 
              $total_level_5++;
            }
          }
        }
    }
    
    if($curr_date==substr($row['user_joined'], 0, 10)){
        $todays_invite++;
    }
}

// * EXPLAIN **
// -getting transactions of this user

$transactions_sql = "SELECT * FROM  othertransactions WHERE user_id='{$user_id}' AND (type='commision' OR type='directbonus') ";
$transactions_query = mysqli_query($conn,$transactions_sql);
    
while($row = mysqli_fetch_assoc($transactions_query)){
  $total_income += $row['amount'];
  
  if($curr_date==substr($row['date_time'], 0, 10)){
    $todays_income += $row['amount'];
  }
  
  if($row['extra_msg']=="Level 1" || $row['extra_msg']=="Direct Bonus 1"){
    $total_level_1_bonus += $row['amount'];
  }else if($row['extra_msg']=="Level 2" || $row['extra_msg']=="Direct Bonus 2"){
    $total_level_2_bonus += $row['amount']; 
  }else if($row['extra_msg']=="Level 3" || $row['extra_msg']=="Direct Bonus 3"){
    $total_level_3_bonus += $row['amount']; 
  }else if($row['extra_msg']=="Level 4" || $row['extra_msg']=="Direct Bonus 4"){
    $total_level_4_bonus += $row['amount']; 
  }else if($row['extra_msg']=="Level 5" || $row['extra_msg']=="Direct Bonus 5"){
    $total_level_5_bonus += $row['amount']; 
  }
  
}

$numRows = mysqli_num_rows($transactions_query);

// * EXPLAIN **
// -checking for number of pages available


$resArr['todays_income'] = number_format($todays_income, 2, ".", "");
$resArr['total_income'] = number_format($total_income, 2, ".", "");
$resArr['todays_invite'] = $todays_invite;
$resArr['total_invite'] = $total_invite;
$resArr['total_level_1'] = $total_level_1;
$resArr['total_level_2'] = $total_level_2;
$resArr['total_level_3'] = $total_level_3;
$resArr['total_level_4'] = $total_level_4;
$resArr['total_level_5'] = $total_level_5;

$resArr['total_level_1_bonus'] = number_format($total_level_1_bonus,2,".","");
$resArr['total_level_2_bonus'] = number_format($total_level_2_bonus,2,".","");
$resArr['total_level_3_bonus'] = number_format($total_level_3_bonus,2,".","");
$resArr['total_level_4_bonus'] = number_format($total_level_4_bonus,2,".","");
$resArr['total_level_5_bonus'] = number_format($total_level_5_bonus,2,".","");


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


// * EXPLAIN **
// -closing mysqli db & responding json data

mysqli_close($conn);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resArr);
}
?>