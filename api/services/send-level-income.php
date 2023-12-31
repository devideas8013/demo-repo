<?php
define("ACCESS_SECURITY", "true");
include "../security/config.php";
include "../security/constants.php";
include "send-commission-bonus.php";
set_time_limit(5000);

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
$curr_day = date("D");

if($curr_day=="Sat" || $curr_day=="Sun"){
    echo "Bonus disabled today!";
    return;
}

$currIndex = 0;
$transactionName = "";

$select_sql = "SELECT * FROM myinvestments WHERE investment_status='pending' ";
$select_result = mysqli_query($conn, $select_sql) or die(mysqli_error($conn));

if (mysqli_num_rows($select_result) > 0) {

    while ($row = mysqli_fetch_assoc($select_result)) {
        $user_uniq_id = $row["user_id"];
        $investment_date = $row["investment_date"];
        $investment_time = $row["investment_time"];
        $investment_date_time = $investment_date.' '.$investment_time;
        
        $today_date = date("d-m-Y");
        $select_transaction_sql = "SELECT * FROM othertransactions WHERE type='commision' AND receive_from='{$user_uniq_id}' AND date_time like '%$today_date%' ";
        $select_transaction_result = mysqli_query($conn, $select_transaction_sql) or die(mysqli_error($conn));
        
        if (mysqli_num_rows($select_transaction_result) <= 0) {
            
         $user_record_sql = "SELECT * FROM usersdata WHERE uniq_id ='$user_uniq_id' AND user_status='true' ";
         $user_record_result = mysqli_query($conn, $user_record_sql) or
                die(mysqli_error($conn));
            
         if (mysqli_num_rows($user_record_result) > 0) {
            $user_record_data = mysqli_fetch_assoc($user_record_result);
            $user_refered_by = $user_record_data['user_refered_by'];
                
            $currIndex++;
                
            $initiateCommission = new InitiateCommission($user_uniq_id,$user_refered_by,"commision","null",true);
            $initiateCommission->SendBonus($conn);
              
        }
        
        }
    }

    //   executing all queries
    if ($currIndex > 0) {
        echo "All Level Income sent! (success)<br>Total (Transactions): " .
            $currIndex;
    } else {
        echo "No eligible user found!";
    }
} else {
    echo "No eligible user found (2)!";
}

mysqli_close($conn);
?>