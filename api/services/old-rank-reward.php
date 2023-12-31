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
$reward_type = "rankreward";

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
    
         $invite_2_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_2_refered_id}' ";
         $invite_2_query = mysqli_query($conn,$invite_2_sql);
    
         while($row2 = mysqli_fetch_assoc($invite_2_query)){
           
           $level_3_refered_id = $row2['uniq_id'];
           
           $investment_3_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_3_refered_id}' ";
           $investment_3_query = mysqli_query($conn,$investment_3_sql);
         
           if(mysqli_num_rows($investment_3_query) > 0){
              $total_refer_record++;  
           }
        
           $invite_3_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_3_refered_id}' ";
           $invite_3_query = mysqli_query($conn,$invite_3_sql);
    
           while($row3 = mysqli_fetch_assoc($invite_3_query)){
          
             $level_4_refered_id = $row3['uniq_id'];
             
             $investment_4_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_4_refered_id}' ";
             $investment_4_query = mysqli_query($conn,$investment_4_sql);
         
             if(mysqli_num_rows($investment_4_query) > 0){
               $total_refer_record++;  
             }
          
             $invite_4_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_4_refered_id}' ";
             $invite_4_query = mysqli_query($conn,$invite_4_sql);
    
             while($row4 = mysqli_fetch_assoc($invite_4_query)){
          
               $level_5_refered_id = $row4['uniq_id'];
               
               $investment_5_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_5_refered_id}' ";
               $investment_5_query = mysqli_query($conn,$investment_5_sql);
         
               if(mysqli_num_rows($investment_5_query) > 0){
                 $total_refer_record++;  
               }
             
               $invite_5_sql = "SELECT uniq_id,user_joined FROM usersdata WHERE user_refered_by='{$level_5_refered_id}' ";
               $invite_5_query = mysqli_query($conn,$invite_5_sql);
    
               while($row5 = mysqli_fetch_assoc($invite_5_query)){
                 
                 $level_6_refered_id = $row5['uniq_id'];
                 
                 $investment_6_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_6_refered_id}' ";
                 $investment_6_query = mysqli_query($conn,$investment_6_sql);
         
                 if(mysqli_num_rows($investment_6_query) > 0){
                   $total_refer_record++;  
                 }
               }
             }
           }
         }

        }
         
        $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
         
        $update_sql = $conn->prepare("UPDATE usersdata SET user_balance = user_balance + ? WHERE uniq_id = ?");
         
        $reward_bonus = "0";
        $extra_msg = "";
            
        if ($total_refer_record >= 20000) {
            
            $reward_bonus = "100000";
            $extra_msg = "Diamond";
            $currIndex++;
              
        }else if ($total_refer_record >= 5000) {
                
            $reward_bonus = "50000";
            $extra_msg = "Platinum";
            $currIndex++;
              
        }else if ($total_refer_record >= 1000) {
                
            $currIndex++;
            $reward_bonus = "30000";
            $extra_msg = "Gold";
              
        }else if ($total_refer_record > 300) {
                
            $currIndex++;
            $reward_bonus = "10000";
            $extra_msg = "Silver";
              
        }else if ($total_refer_record >= 50) {
                
            $currIndex++;
            $reward_bonus = "1500";
            $extra_msg = "Bronze";
              
        }else if ($total_refer_record >= 10) {
                
            $currIndex++;
            $reward_bonus = "500";
            $extra_msg = "Starter";
              
         }
         
        if($reward_bonus!="0"){
          $check_transaction_sql = "SELECT * FROM othertransactions WHERE type='{$reward_type}' AND extra_msg='{$extra_msg}' AND user_id='{$user_id}' ";
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
        echo "All Rank Reward sent! (success)<br>Total (Transactions): " .
            $currIndex;
    } else {
        echo "No eligible user found!";
    }
} else {
    echo "No eligible user found (2)!";
}

mysqli_close($conn);
?>