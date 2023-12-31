<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY", "true");
include 'security/config.php';

$resArr = array();
$resArr['account_balance'] = "0";

date_default_timezone_set('Asia/Kolkata');
$curr_date_time = date('d-m-Y h:i a');

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){

  if(isset($_POST['USER_ID']) && isset($_POST['REWARD_ID'])){
    $user_id = mysqli_real_escape_string($conn, $_POST['USER_ID']);
    $reward_id = mysqli_real_escape_string($conn, $_POST['REWARD_ID']);   
  }

  if(isset($_GET['USER_ID']) && isset($_GET['REWARD_ID'])){
    $user_id = mysqli_real_escape_string($conn, $_GET['USER_ID']);
    $reward_id = mysqli_real_escape_string($conn, $_GET['REWARD_ID']);   
  }
  
  $select_reward_sql = "SELECT * FROM availablerewards WHERE reward_id='{$reward_id}' AND reward_status='true' ";
  $select_reward_query = mysqli_query($conn,$select_reward_sql);
  
  if(mysqli_num_rows($select_reward_query) > 0){
    $select_reward_data = mysqli_fetch_assoc($select_reward_query);
    $reward_bonus = $select_reward_data['reward_bonus'];
    
    $select_sql = "SELECT user_balance,user_status FROM usersdata WHERE uniq_id='$user_id' AND user_status='true' ";
    $select_query = mysqli_query($conn,$select_sql);

    if(mysqli_num_rows($select_query) > 0){
      $res_data = mysqli_fetch_assoc($select_query);
      $user_available_balance = $res_data['user_balance'];


      $select_transc_sql = "SELECT user_id FROM othertransactions WHERE user_id='$user_id' AND type='$reward_id' ";
      $select_transc_query = mysqli_query($conn,$select_transc_sql);
      
        if(mysqli_num_rows($select_transc_query) <= 0){
                
            $envelop_status = "success";
            $receive_from = "app";
            $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,date_time) VALUES(?,?,?,?,?)");
              $insert_sql->bind_param("sssss", $user_id, $receive_from, $reward_id,$reward_bonus, $curr_date_time);
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
        }else{
          $resArr['status_code'] = "already_claimed";
        }

      }
  }else{
      $resArr['status_code'] = "code_not_exist";
  }

   mysqli_close($conn);
   echo json_encode($resArr);
}
?>