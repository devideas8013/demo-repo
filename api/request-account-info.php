<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY", "true");
include "security/config.php";
include "security/constants.php";

$resArr = [];
$resArr["data"] = [];
$resArr["investmentList"] = [];
$resArr["slideShowList"] = [];

date_default_timezone_set("Asia/Kolkata");
$curr_date = date("d-m-Y");
$curr_time = date("h:i:s a");
$curr_day = date("D");

function checkSetData($number)
{
    $returnVal = $number;
    if (fmod($number, 1) !== 0.0) {
        $decimalCount = (int) strpos(strrev($number), ".");

        if ($decimalCount > 2) {
            $modifiedVal = number_format($number, 2, ".", "");
            $returnVal = $modifiedVal;
        }
    }

    return $returnVal;
}

if (
    $_SERVER["REQUEST_METHOD"] == "POST" ||
    $_SERVER["REQUEST_METHOD"] == "GET"
) {
    if (isset($_POST["USER_ID"])) {
        $user_id = mysqli_real_escape_string($conn, $_POST["USER_ID"]);
    }

    if (isset($_POST["SECRET_KEY"])) {
        $secret_key = mysqli_real_escape_string($conn, $_POST["SECRET_KEY"]);
    }

    if (
        isset($_GET["USER_ID"]) &&
        isset($_GET["SECRET_KEY"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_GET["USER_ID"]);
        $secret_key = mysqli_real_escape_string($conn, $_GET["SECRET_KEY"]);
    } else {
        return;
    }
    
    function isTimeInRange($timeToCheck){
      $returnVal = "false";
      $todayDateTime = date("d-m-Y");
      // $hour = date("H");
      $hour = 12;
    
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
    
    $select_sql = "SELECT * FROM usersdata WHERE uniq_id='{$user_id}' ";
    $select_query = mysqli_query($conn, $select_sql);

    if (mysqli_num_rows($select_query) > 0) {
        $res_data = mysqli_fetch_assoc($select_query);
        $user_last_active = $res_data["user_last_active_date"];
        $account_balance = $res_data["user_balance"];
        
        $service_app_status = "";
        $service_min_recharge = "";
        $service_min_withdraw = "";
        $service_recharge_option = "";
        $service_telegram_url = "";
        $service_daily_bonus = "";
        $service_ads_video_id = "";
        
        $services_sql = "SELECT * FROM allservices ";
        $services_query = mysqli_query($conn, $services_sql);
        while($row = mysqli_fetch_array($services_query)){
            if($row['service_name']=="APP_STATUS"){
               $service_app_status = $row['service_value'];
            }else if($row['service_name']=="RECHARGE_OPTIONS"){
               $service_recharge_option = $row['service_value'];
            }else if($row['service_name']=="MIN_RECHARGE"){
               $service_min_recharge = $row['service_value'];
            }else if($row['service_name']=="MIN_WITHDRAW"){
               $service_min_withdraw = $row['service_value'];
            }else if($row['service_name']=="TELEGRAM_URL"){
               $service_telegram_url = $row['service_value'];
            }else if($row['service_name']=="SLIDESHOW_BANNER"){
               $service_slideshow_banner = $row['service_value'];
            }else if($row['service_name']=="DAILY_BONUS"){
               $service_daily_bonus = $row['service_value'];
            }else if($row['service_name']=="AUTO_POOL_INCOME"){
               $service_autopool_bonus = $row['service_value'];
            }else if($row['service_name']=="ADS_VIDEO_ID"){
               $service_ads_video_id = $row['service_value'];
            }else if($row['service_name']=="SCROLLING_NOTICE"){
               $scrolling_notice = $row['service_value'];
            }
        }
        
        $sliders_sql = "SELECT * FROM allsliders WHERE status='true' ";
        $sliders_query = mysqli_query($conn, $sliders_sql);
        while($row = mysqli_fetch_array($sliders_query)){
          $slideIndex['slider_img'] = $row["slider_img"];
          $slideIndex['slider_action'] = $row["slider_action"];

          array_push($resArr['slideShowList'], $slideIndex);
        }
        
        
        // investment list
        $investment_sql = "SELECT * FROM investmentlist ";
        $investment_query = mysqli_query($conn, $investment_sql);
        while($row = mysqli_fetch_array($investment_query)){
        
          $invest_id = $row["uniq_id"];
          $myinvestment_sql = "SELECT * FROM myinvestments WHERE user_id='{$user_id}' AND investment_id='{$invest_id}' ";
          $myinvestment_query = mysqli_query($conn, $myinvestment_sql);
          
          if(mysqli_num_rows($myinvestment_query) > 0){
            $index['invest_available'] = "true";
          }else{
            $index['invest_available'] = "false";
          }
        
          $index['invest_id'] = $invest_id;
          $index['invest_name'] = $row["investment_name"];
          $index['invest_details'] = $row["investment_details"];
          $index['invest_hourly_income'] = $row["investment_hourly_income"];
 
          $index['invest_price'] = $row["investment_price"];
          $index['invest_total_days'] = $row["investment_total_days"];
          $index['invest_img_url'] = $row["investment_image_url"];
          
          $index['invest_daily_income'] = number_format(24*$row["investment_hourly_income"],2,".","");
          $index['invest_total_revenue'] = number_format($row["investment_hourly_income"]*$row["investment_total_days"]*24,2,".","");

          array_push($resArr['investmentList'], $index);
          
        }
        
        $transactions_sql = "SELECT * FROM  othertransactions WHERE user_id='{$user_id}' ";
        $transactions_query = mysqli_query($conn,$transactions_sql);

        $total_referal_income = 0;
        $total_dailylevel_income = 0;
        $total_rewardRank_income = 0;
        $total_dailybonus_income = 0;
        $total_royalty_income = 0;
        $total_autopool_income = 0;
        $is_dailybonus_claimed = "false";

        while($row = mysqli_fetch_assoc($transactions_query)){
         
          if($row['type']=="directbonus"){
            $total_referal_income += $row['amount'];
          }else if($row['type']=="commision"){
            $total_dailylevel_income += $row['amount']; 
          }else if($row['type']=="rankreward"){
            $total_rewardRank_income += $row['amount'];
          }else if($row['type']=="royalty"){
            $total_royalty_income += $row['amount'];
          }else if($row['type']=="autopool"){
            $total_autopool_income += $row['amount'];
          }else if($row['type']=="dailybonus"){
            $total_dailybonus_income += $row['amount'];
          }
        }
        
        $transactions_sql = "SELECT date_time FROM  othertransactions WHERE user_id='{$user_id}' AND type='dailybonus' ORDER BY id DESC";
        $transactions_query = mysqli_query($conn,$transactions_sql);
        
        if(mysqli_num_rows($transactions_query) > 0){
            $transac_res_data = mysqli_fetch_assoc($transactions_query);
            $bonus_date_time = $transac_res_data['date_time'];
            $resArr["bonus_date_time"] = $bonus_date_time;
            $is_dailybonus_claimed = isTimeInRange($bonus_date_time);
        }
        
        // $user_record_sql = "SELECT * FROM usersdata WHERE user_refered_by ='$user_id' AND user_status='true' ";
        // $user_record_result = mysqli_query($conn, $user_record_sql) or
        //         die(mysqli_error($conn));
        
        $total_refer_record = 0;
        
        // * EXPLAIN **
        // -getting invite users list
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
        
        $rank_name = "Normal";
        if($total_refer_record >= 20000){
            $rank_name = "Diamond";
        }else if ($total_refer_record >= 5000) {
            $rank_name = "Platinum";
        }else if ($total_refer_record >= 1000) {
            $rank_name = "Gold";
        }else if ($total_refer_record > 300) {
            $rank_name = "Silver";
        }else if ($total_refer_record >= 50) {
            $rank_name = "Bronze";
        }else if ($total_refer_record >= 10) {
            $rank_name = "Starter";
        }

        $index["total_refer_no"] = $total_refer_record;
        $index["account_id"] = $user_id;
        $index["account_mobile_num"] = $res_data["user_mobile_num"];
        $index["account_balance"] = checkSetData($res_data["user_balance"]);
        $index["account_w_balance"] = checkSetData($res_data["user_withdrawl_balance"]);
        $index["account_c_balance"] = checkSetData($res_data["user_total_coins"]);
        
        $index["account_my_rank"] = $rank_name;
        $index["account_referal_income"] = strval(checkSetData($total_referal_income));
        $index["account_dailylevel_income"] = strval(checkSetData($total_dailylevel_income));
        $index["account_rewardrank_income"] = strval(checkSetData($total_rewardRank_income));
        $index["account_dailybonus_income"] = strval(checkSetData($total_dailybonus_income));
        $index["account_royalty_income"] = $total_royalty_income;
        $index["account_autopool_income"] = $total_autopool_income;
        $index["account_dailybonus_claimed"] = $is_dailybonus_claimed;
        
        $index["account_refered_by"] = $res_data["user_refered_by"];
        
        if($curr_day=="Sat" || $curr_day=="Sun"){
            $index["is_daily_ads_available"] = "false";
        }else{
            $index["is_daily_ads_available"] = "true";
        }
        
        $index["service_app_status"] = $service_app_status;
        $index["service_min_recharge"] = $service_min_recharge;
        $index["service_min_withdraw"] = $service_min_withdraw;
        $index["service_recharge_option"] = $service_recharge_option;
        $index["service_telegram_url"] = $service_telegram_url;
        $index["service_app_download_url"] = $APP_DOWNLOAD_URL;
        $index["slider_banner_imgs"] = $service_slideshow_banner;
        $index["service_daily_bonus"] = $service_daily_bonus;
        $index["service_autopool_bonus"] = $service_autopool_bonus;
        $index["service_yt_video_id"] = $service_ads_video_id;
        $index["scrolling_notice"] = $scrolling_notice;
        
        array_push($resArr["data"], $index);

        if ($user_last_active != $curr_date) {
            $update_sql = "UPDATE usersdata SET user_last_active_date = '{$curr_date}' WHERE uniq_id = '{$user_id}' ";
            $update_query = mysqli_query($conn, $update_sql);
        }

        $none = "none";
        $update_sql = "UPDATE usersdata SET user_last_active_time = '{$curr_time}' WHERE uniq_id = '{$user_id}'";
        $update_query = mysqli_query($conn, $update_sql);
        
        $resArr["status_code"] = "success";
    } else {
        $resArr["status_code"] = "account_error";
    }

    mysqli_close($conn);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($resArr);
}

?>
