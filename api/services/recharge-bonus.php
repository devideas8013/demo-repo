<?php
define("ACCESS_SECURITY","true");
include '../security/config.php';
include '../security/constants.php';
set_time_limit(1400);

if(isset($_GET['accessToken'])){
  $accessToken = mysqli_real_escape_string($conn,$_GET["accessToken"]);
  if($CRON_ACCESS_TOKEN!=$accessToken){
    echo "Access Token Error";
    return;
  }
}else{
    echo "Access Token Error";
    return;
}

date_default_timezone_set("Asia/Kolkata");
$curr_date_time = date("d-m-Y h:i a");

$currIndex = 0;
$rewardName = "Recharge Bonus";
$rewardType = "commision";
$recharge_refer_bonus = 0;

// minimum recharge accept bonus
$MIN_ALLOWED_RECHARGE = 100;

// all sql queries
$sqlQuery1 = "UPDATE usersdata SET user_commission_balance = user_commission_balance + (case";
$sqlQuery2 = " WHERE uniq_id in (";

$sqlQuery3 = "INSERT INTO othertransactions (userid, receive_from, type, amount, extra_msg, date_time) VALUES ";

$sqlQuery4 = "UPDATE usersdata SET recharge_bonus_sended = (case";
$sqlQuery5 = "WHERE uniq_id in (";

$select_sql = "SELECT * FROM usersdata WHERE recharge_bonus_sended='false' AND user_refered_by!='' AND account_level >= 2 ORDER BY id ASC";
$select_result = mysqli_query($conn, $select_sql) or die(mysqli_error($conn));

if (mysqli_num_rows($select_result) > 0) {
  
  while($row = mysqli_fetch_assoc($select_result)){
    $user_uniq_id = $row['uniq_id'];
    $user_refered_by = $row['user_refered_by'];
    
    $recharge_record_sql = "SELECT * FROM usersrecharge WHERE request_status='success' AND user_id='$user_uniq_id' AND recharge_amount >= $MIN_ALLOWED_RECHARGE ORDER BY id ASC";
    $recharge_record_result = mysqli_query($conn, $recharge_record_sql) or die(mysqli_error($conn));
    
    if (mysqli_num_rows($recharge_record_result) > 0) {
        $temp_res_data = mysqli_fetch_assoc($recharge_record_result);
        $user_recharge_amount = $temp_res_data['recharge_amount'];
        
        if($RECHARGE_BONUS_TYPE == "normal"){
          if($user_recharge_amount >= 100000){
            $recharge_refer_bonus = 5500; 
          }else if($user_recharge_amount >= 50000){
            $recharge_refer_bonus = 2300; 
          }else if($user_recharge_amount >= 10000){
            $recharge_refer_bonus = 1100; 
          }else if($user_recharge_amount >= 5000){
            $recharge_refer_bonus = 600; 
          }else if($user_recharge_amount >= 4000){
            $recharge_refer_bonus = 500; 
          }else if($user_recharge_amount >= 3000){
            $recharge_refer_bonus = 400; 
          }else if($user_recharge_amount >= 1000){
            $recharge_refer_bonus = 200; 
          }else if($user_recharge_amount >= $MIN_ALLOWED_RECHARGE){
            $recharge_refer_bonus = 10;
          }   
        }else{
          $recharge_refer_bonus = number_format($user_recharge_amount*$RECHARGE_BONUS_PERCENTAGE/100, 2, '.', '');   
        }
        
        if($recharge_refer_bonus > 0){
          $sqlQuery1 .= " when uniq_id = '".$user_refered_by."' then '".$recharge_refer_bonus."'";
          $sqlQuery4 .= " when uniq_id = '".$user_uniq_id."' then 'true'";
        
          if($currIndex > 0){
            $sqlQuery2 .= ",'".$user_refered_by."'";
            $sqlQuery5 .= ",'".$user_uniq_id."'";
            $sqlQuery3 .= ",('".$user_refered_by."', '".$user_uniq_id."', '".$rewardType."', '".$recharge_refer_bonus."', '".$rewardName."', '".$curr_date_time."')"; 
          }else{
            $sqlQuery2 .= "'".$user_refered_by."'";
            $sqlQuery5 .= "'".$user_uniq_id."'";
            $sqlQuery3 .= "('".$user_refered_by."', '".$user_uniq_id."', '".$rewardType."', '".$recharge_refer_bonus."', '".$rewardName."', '".$curr_date_time."')"; 
          }  
          
          $currIndex++;
        }
        
    }

  }

  $sqlQuery1 .= " end)";
  $sqlQuery2 .= ");";
  $sqlQuery4 .= " end)";
  $sqlQuery5 .= ");";

  $finalSql = $sqlQuery1.$sqlQuery2;
  $finalSql2 = $sqlQuery4.$sqlQuery5;
  
//   echo $finalSql.'<br><br>';
//   echo $finalSql2.'<br><br>';
//   echo $sqlQuery3;
  
//   executing all queries
  if($currIndex > 0){
    mysqli_query($conn, $finalSql);
    mysqli_query($conn, $finalSql2);
    mysqli_query($conn, $sqlQuery3);  
    
    echo 'Bonus sended to all eligible users (success)<br>Total: '.$currIndex;
  }else{
    echo 'No eligible user found!';
  }

}else{
    echo 'No eligible user found!';
}

mysqli_close($conn);
?>