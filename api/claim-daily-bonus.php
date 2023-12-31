<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY", "true");
include 'security/config.php';

$resArr = array();
$resArr['account_balance'] = "0";

date_default_timezone_set('Asia/Kolkata');
$curr_date_time = date('d-m-Y h:i a');
$curr_date = date("d-m-Y");
$curr_day = date("D");


if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){
    
  $reward_type = "dailybonus";
  
  if(isset($_POST['USER_ID'])){
    $user_id = mysqli_real_escape_string($conn, $_POST['USER_ID']); 
  }

  if(isset($_GET['USER_ID'])){
    $user_id = mysqli_real_escape_string($conn, $_GET['USER_ID']);  
  }
  
  
  if($curr_day=="Sat" || $curr_day=="Sun"){
    $resArr['status_code'] = "not_available";
    echo json_encode($resArr);
    return;
  }
  
  function isTimeInRange($timeToCheck){
      $returnVal = "false";
      $todayDateTime = date("d-m-Y");
       $hour = date("H");
    
      if($hour >= 6 && $hour < 12){
        $fromTime = $todayDateTime." 06:00 am";
        $toTime = $todayDateTime." 12:00 pm";
      }else if($hour >= 12 && $hour < 18){
        $fromTime = $todayDateTime." 12:00 pm";
        $toTime = $todayDateTime." 06:00 pm"; 
      }else if($hour >= 18 && $hour < 24){
        $fromTime = $todayDateTime." 06:00 pm";
        $toTime = date('d-m-Y', strtotime("+1 day"))." 12:00 am"; 
      }else{
        $fromTime = $todayDateTime." 12:00 am";
        $toTime = $todayDateTime." 06:00 am"; 
      }
    
      $date1 = DateTime::createFromFormat('d-m-Y h:i a', $timeToCheck);
      $date2 = DateTime::createFromFormat('d-m-Y h:i a', $fromTime);
      $date3 = DateTime::createFromFormat('d-m-Y h:i a', $toTime);
    
      if ($date1 >= $date2 && $date1 < $date3){
        $returnVal = "true";
      } 
    
      return $returnVal;
  }
  
  $select_reward_sql = "SELECT * FROM allservices WHERE service_name='DAILY_BONUS' ";
  $select_reward_query = mysqli_query($conn,$select_reward_sql);
  
  if(mysqli_num_rows($select_reward_query) > 0){
    $select_reward_data = mysqli_fetch_assoc($select_reward_query);
    $reward_bonus = $select_reward_data['service_value'];
    
   $select_sql = "SELECT user_balance,user_status FROM usersdata WHERE uniq_id='$user_id' AND user_status='true' ";
   $select_query = mysqli_query($conn,$select_sql);

   if(mysqli_num_rows($select_query) > 0){
      $res_data = mysqli_fetch_assoc($select_query);
      $user_available_balance = $res_data['user_balance'];
      
      $select_myinvestments = "SELECT * FROM myinvestments WHERE user_id='$user_id' AND investment_status='pending' ";
      $select_myinvestments_query = mysqli_query($conn,$select_myinvestments);
       
      if(mysqli_num_rows($select_myinvestments_query) > 0){
        $select_transc_sql = "SELECT date_time FROM  othertransactions WHERE user_id='{$user_id}' AND type='{$reward_type}' ORDER BY id DESC";
        $select_transc_query = mysqli_query($conn,$select_transc_sql);
      
        if(mysqli_num_rows($select_transc_query) > 0){
         $transac_res_data = mysqli_fetch_assoc($select_transc_query);
         $bonus_date_time = $transac_res_data['date_time'];
         
         if(isTimeInRange($bonus_date_time)=="true"){
           $resArr['status_code'] = "already_claimed"; 
         }else{
                
           $envelop_status = "success";
           $receive_from = "app";
           $extra_msg = "Daily Bonus";
        
           $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
           $insert_sql->bind_param("ssssss", $user_id, $receive_from, $reward_type,$reward_bonus, $extra_msg, $curr_date_time);
           $insert_sql->execute();
    
           if ($insert_sql->error == ""){
    
            $updated_balance = $user_available_balance+$reward_bonus;
            $update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?");
            $update_sql->bind_param("ss", $updated_balance, $user_id);
            $update_sql->execute();
    
            if ($update_sql->error == "") {
                $resArr['account_balance'] = $updated_balance;
                $resArr['status_code'] = "success";
            }else{
                $resArr['status_code'] = "sql_failed";
            }
                
           }else{
            $resArr['status_code'] = "sql_failed";
           }
         
         }
        
        }else{
          $resArr['status_code'] = "already_claimed";
        }
        
      }else{
        $resArr['status_code'] = "not_eligible";
      }

    }else{
        $resArr['status_code'] = "authentication_error";
    }
      
  }else{
      $resArr['status_code'] = "code_not_exist";
  }

   mysqli_close($conn);
   echo json_encode($resArr);
}
?>