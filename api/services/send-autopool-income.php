<?php
define("ACCESS_SECURITY", "true");
include "../security/config.php";
include "../security/constants.php";
set_time_limit(6000);

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
$receive_from = "app";
$reward_type = "autopool";

$service_sql = "SELECT * FROM allservices WHERE service_name='AUTO_POOL_INCOME' ";
$service_result = mysqli_query($conn, $service_sql) or die(mysqli_error($conn));

if(mysqli_num_rows($service_result) > 0){
    $service_res_data = mysqli_fetch_assoc($service_result);
    $service_auto_pool_val = $service_res_data['service_value'];
    
    if($service_auto_pool_val > 0){
      $select_sql = "SELECT * FROM usersdata WHERE account_level >= 2 AND user_status='true' ";
      $select_result = mysqli_query($conn, $select_sql) or die(mysqli_error($conn));

      if (mysqli_num_rows($select_result) > 0) {

       while ($row = mysqli_fetch_assoc($select_result)) {
        $user_id = $row["uniq_id"];
            
        $total_refer_record = 0;
         
        $invite_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$user_id}' ";
        $invite_query = mysqli_query($conn,$invite_sql);

        while($row = mysqli_fetch_assoc($invite_query)){
         
         $level_2_refered_id = $row['uniq_id'];
         
         $investment_2_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_2_refered_id}' ";
         $investment_2_query = mysqli_query($conn,$investment_2_sql);
         
         if(mysqli_num_rows($investment_2_query) > 0){
            $total_refer_record++; 
         }

        }
         
        $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
         
        $update_sql = $conn->prepare("UPDATE usersdata SET user_balance = user_balance + ? WHERE uniq_id = ?");
         
        $reward_bonus = "0";
        $extra_msg = "";
            
        if ($total_refer_record >= 19) {
            
            $reward_bonus = $service_auto_pool_val;
            $extra_msg = "Auto Pool Income";
            $currIndex++;
              
        }
         
        if($reward_bonus!="0"){
          $check_transaction_sql = "SELECT * FROM othertransactions WHERE type='{$reward_type}' AND user_id='{$user_id}' ";
          $check_transaction_result = mysqli_query($conn, $check_transaction_sql) or die(mysqli_error($conn));
            
          if(mysqli_num_rows($check_transaction_result) <= 0){
            $insert_sql->bind_param("ssssss", $user_id, $receive_from, $reward_type,$reward_bonus,$extra_msg, $curr_date_time);
            $insert_sql->execute();
            
            $update_sql->bind_param("ss", $reward_bonus, $user_id);
            $update_sql->execute();
          }
        }
    }

       //   executing all queries
       if ($currIndex > 0) {
        echo "All Auto Pool income sent! (success)<br>Total (Transactions): " .
            $currIndex;
       } else {
        echo "No eligible user found!";
       }
      } else {
    echo "No eligible user found (2)!";
}
    }
}

mysqli_close($conn);
?>