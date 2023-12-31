<?php
define("ACCESS_SECURITY", "true");
include "../security/config.php";
include "../security/constants.php";
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
$start_date = new DateTime($curr_date_time);

$currIndex = 0;
$transactionVia = "app";
$transactionName = "";
$transactionType = "investmentbonus";

$select_sql = "SELECT * FROM myinvestments WHERE investment_status='pending' ";
($select_result = mysqli_query($conn, $select_sql)) or die(mysqli_error($conn));

if (mysqli_num_rows($select_result) > 0) {
    
    function getDecimalNum($num){
        return number_format($num,2,".","");
    }
    
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

    while ($row = mysqli_fetch_assoc($select_result)) {
        $user_uniq_id = $row["user_id"];
        $investment_record_id = $row["uniq_id"];
        $transactionName = $row["investment_name"];
        $investment_date = $row["investment_date"];
        $investment_time = $row["investment_time"];
        $investment_date_time = $investment_date.' '.$investment_time;
        
        $investment_total_days = $row["investment_total_days"];
        $investment_hourly_income = $row["investment_hourly_income"];
        $investment_last_update = $row["investment_last_update"];
        $user_invest_balance =
            (float) $row["investment_earnings"] +
            (float) $investment_hourly_income;

        $time_diff = $start_date->diff(new DateTime($investment_last_update));
        $total_minutes = $time_diff->days * 24 * 60;
        $total_minutes += $time_diff->h * 60;
        $total_minutes += $time_diff->i;

        if ($total_minutes > 50) {
            
            if(daysLeft($investment_date_time,$investment_total_days) <= 0){
                $investment_status = "completed";
                $update_invest_sql = $conn->prepare(
                    "UPDATE myinvestments SET investment_last_update = ?,investment_status = ? WHERE uniq_id = ?"
                );
                $update_invest_sql->bind_param(
                    "sss",
                    $curr_date_time,
                    $investment_status,
                    $investment_record_id
                );
                $update_invest_sql->execute();
                
                $currIndex++;
            }else{
            
              $user_record_sql = "SELECT * FROM usersdata WHERE uniq_id='$user_uniq_id' AND user_status='true' ";
              ($user_record_result = mysqli_query($conn, $user_record_sql)) or
                die(mysqli_error($conn));

              if (mysqli_num_rows($user_record_result) > 0) {
                $temp_res_data = mysqli_fetch_assoc($user_record_result);
                
                $user_updated_balance =
                    (float) $temp_res_data["user_balance"] +
                    (float) $investment_hourly_income;
                    
                $user_win_updated_balance =
                    (float) $temp_res_data["user_withdrawl_balance"] +
                    (float) $investment_hourly_income;

                $user_new_balance = getDecimalNum($user_updated_balance);
                $user_new_win_balance = getDecimalNum($user_win_updated_balance);

                $update_sql = $conn->prepare(
                    "UPDATE usersdata SET user_balance = ? ,user_withdrawl_balance = ? WHERE uniq_id = ?"
                );
                $update_sql->bind_param("sss", $user_new_balance, $user_new_win_balance, $user_uniq_id);
                $update_sql->execute();

                if ($update_sql->error == "") {
                    $new_invest_earning_balance = number_format(
                        $user_invest_balance,
                        2,
                        ".",
                        ""
                    );

                    $update_invest_sql = $conn->prepare(
                        "UPDATE myinvestments SET investment_earnings = ?, investment_last_update = ? WHERE uniq_id = ?"
                    );
                    $update_invest_sql->bind_param(
                        "sss",
                        $new_invest_earning_balance,
                        $curr_date_time,
                        $investment_record_id
                    );
                    $update_invest_sql->execute();

                    if ($update_invest_sql->error == "") {
                        // adding new match record---
                        $insert_sql = $conn->prepare(
                            "INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)"
                        );
                        $insert_sql->bind_param(
                            "ssssss",
                            $user_uniq_id,
                            $transactionVia,
                            $transactionType,
                            $investment_hourly_income,
                            $transactionName,
                            $curr_date_time
                        );

                        $insert_sql->execute();
                    }

                    $currIndex++;
                }
            }
            
            }
        }
    }

    //   executing all queries
    if ($currIndex > 0) {
        echo "Investment Amount sended to all eligible users (success)<br>Total (Transactions): " .
            $currIndex;
    } else {
        echo "No eligible user found!";
    }
} else {
    echo "No eligible user found!";
}

mysqli_close($conn);
?>