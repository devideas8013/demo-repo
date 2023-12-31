<?php
header("Access-Control-Allow-Origin: *");

define("ACCESS_SECURITY", "true");
include 'security/config.php';
include 'security/constants.php';
require_once("services/send-notification-to-admin.php");

$resArr = array();
$TAX_AMOUNT = 5;
$WITHDRAW_PERCENT_ALLOWED = 100;
$MAX_WITHDRAW_ALLOWED = 20;

$resArr['account_balance'] = "0";

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');
$curr_date_time = date('d-m-Y h:i a');

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){
    
    $user_id = "";
    $withdraw_amount = "";
    $actual_name = "";
    $bank_name = "";
    $bank_account = "";
    $bank_ifsc_code = "";
    $withdraw_method = "";

    function generateOrderID($length = 15){
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0;$i < $length;$i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1) ];
        }
        return 'RW0' . $randomString;
    }

    $uniqId = generateOrderID();
    
    if(isset($_POST['USER_ID']) && isset($_POST['WITHDRAW_AMOUNT']) && isset($_POST['WITHDRAW_METHOD'])){
      $user_id = mysqli_real_escape_string($conn, $_POST['USER_ID']);
      $withdraw_amount = mysqli_real_escape_string($conn, $_POST['WITHDRAW_AMOUNT']);
      $withdraw_method = mysqli_real_escape_string($conn, $_POST['WITHDRAW_METHOD']);
    }

    if(isset($_GET['USER_ID']) && isset($_GET['WITHDRAW_AMOUNT']) && isset($_GET['WITHDRAW_METHOD'])){
      $user_id = mysqli_real_escape_string($conn, $_GET['USER_ID']);
      $withdraw_amount = mysqli_real_escape_string($conn, $_GET['WITHDRAW_AMOUNT']);
      $withdraw_method = mysqli_real_escape_string($conn, $_GET['WITHDRAW_METHOD']);
    }

    $select_sql = "SELECT * FROM allservices WHERE service_value!=''";
    $select_query = mysqli_query($conn,$select_sql);
    
    $service_min_withdraw = null;
    
    while($row = mysqli_fetch_assoc($select_query)){
      if($row['service_name']=="MIN_WITHDRAW"){
        $service_min_withdraw = $row['service_value'];  
      }else if($row['service_name']=="WITHDRAW_TAX"){
        $TAX_AMOUNT = (float) $row['service_value']/100;  
      }
    }
    
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
    
    // getting primary bank account details
    $select_bank_sql = "SELECT * FROM allbankcards WHERE user_id='{$user_id}' AND bank_card_primary='true'";
    $select_bank_query = mysqli_query($conn,$select_bank_sql);
    
    if($total_refer_record < 2){
        $resArr['status_code'] = "min_refer_error";
    }else if(mysqli_num_rows($select_bank_query) <= 0){
        $resArr['status_code'] = "primary_bankcard_error";
    }else{
        $res_data = mysqli_fetch_assoc($select_bank_query);
        $actual_name = $res_data['beneficiary_name'];
        $bank_name = $res_data['bank_name'];
        $bank_account = $res_data['bank_account'];
        $bank_ifsc_code = $res_data['bank_ifsc_code'];
        
        
      $select_sql = "SELECT user_password,user_withdrawl_balance,user_balance,account_level,user_status FROM usersdata WHERE uniq_id='$user_id' ";
      $select_query = mysqli_query($conn, $select_sql);
  
      if (mysqli_num_rows($select_query) > 0){
         $res_data = mysqli_fetch_assoc($select_query);
         $account_level = $res_data['account_level'];
         $account_main_balance = $res_data['user_balance'];
         $available_balance = $res_data['user_balance'];
         $user_withdrawl_balance = $res_data['user_withdrawl_balance'];
         
        //  if($withdraw_method=="W"){
        //     $available_balance = $user_withdrawl_balance;
        //  }else if($withdraw_method=="C"){
        //     $available_balance = $user_commission_balance;
        //  }  
  
         $allowed_balance = ($WITHDRAW_PERCENT_ALLOWED / 100) * $available_balance;
         
          $withdraw_count_sql = "SELECT withdraw_request,request_date_time FROM userswithdraw WHERE user_id='{$user_id}' AND request_status!='rejected' ";
          $withdraw_count_query = mysqli_query($conn, $withdraw_count_sql);
          
        //   total num of withdraw today
          $total_num_withdraw = 0;
          
          if(mysqli_num_rows($withdraw_count_query) > 0){
            while($withdrawRow = mysqli_fetch_array($withdraw_count_query)){
              $withdraw_date = substr($withdrawRow['request_date_time'], 0, strpos( $withdrawRow['request_date_time'], ' '));
              
              if($curr_date==$withdraw_date){
                $total_num_withdraw++;
              }
            }   
          }
  
         if ($total_num_withdraw >= $MAX_WITHDRAW_ALLOWED){
          $resArr['status_code'] = "maximum_withdraw_limit";  
         }else if($withdraw_amount > $available_balance){
          $resArr['status_code'] = "insufficient_balance"; 
         }else if($withdraw_amount > $allowed_balance){
          $resArr['status_code'] = "maximum_withdraw_error";
          $resArr['maximum_withdraw'] = $allowed_balance;
         }else if($withdraw_amount < $service_min_withdraw){
          $resArr['status_code'] = "minimum_withdraw_error";            
          $resArr['minimum_withdraw'] = $service_min_withdraw;            
         }else if($account_level < 2){
          $resArr['status_code'] = "no_premium";
         }else{
          $decoded_password = 1;
          if ($decoded_password == 1){
              if ($res_data['user_status'] == "true"){
                $extra_msg = "";
                $request_status = "pending";
                $withdraw_request_amount = $withdraw_amount - ($withdraw_amount * $TAX_AMOUNT);
                
                $withdraw_request_amount = number_format($withdraw_request_amount, 2, '.', '');
                $insert_sql = $conn->prepare("INSERT INTO userswithdraw(uniq_id,user_id,withdraw_request,withdraw_amount,actual_name,bank_name,bank_account,bank_ifsc_code,request_status,extra_message,request_date_time) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                $insert_sql->bind_param("sssssssssss", $uniqId, $user_id, $withdraw_amount, $withdraw_request_amount, $actual_name, $bank_name, $bank_account, $bank_ifsc_code, $request_status,$extra_msg, $curr_date_time);
                $insert_sql->execute();
  
                if ($insert_sql->error == ""){
  
                    $updated_balance = $available_balance - $withdraw_amount;
                    $updated_main_balance = $account_main_balance - $withdraw_amount;
                    
                    if($updated_main_balance <= 0){
                        $updated_main_balance = 0;
                    }
                    
                    if($withdraw_method=="W"){
                        $update_sql = $conn->prepare("UPDATE usersdata SET user_withdrawl_balance = ?,user_balance = ? WHERE uniq_id = ?");
                        $update_sql->bind_param("sss", $updated_balance,$updated_main_balance, $user_id);
                    }else{
                        $update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?");
                        $update_sql->bind_param("ss", $updated_balance, $user_id);
                    }
                    
                    $update_sql->execute();
  
                    if ($update_sql->error == ""){
                        $resArr['account_balance'] = $updated_balance;
                        $resArr['status_code'] = "success";
                        sendNotification('Request Withdraw!','Someone request for a new withdraw of Rs.'.$withdraw_request_amount,$MESSAGE_TOKEN);
                    }else{
                        $resArr['status_code'] = "failed";
                    }
  
                }else{
                  $resArr['status_code'] = "sql_failed";
                }
              }else{
                $resArr['status_code'] = "failed";
              }
          }else{
            $resArr['status_code'] = "password_error";
          } 
         }
  
      }else{
        $resArr['status_code'] = "failed";
      }
        
    }

    mysqli_close($conn);
    echo json_encode($resArr);
}
?>