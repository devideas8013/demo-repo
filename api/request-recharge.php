<?php
header("Access-Control-Allow-Origin: *");

define("ACCESS_SECURITY","true");
include 'security/config.php';
include 'security/constants.php';
require_once("services/send-notification-to-admin.php");
  
$resArr = array();
$resArr['account_balance'] = "0";

date_default_timezone_set('Asia/Kolkata');
$curr_date_time = date('d-m-Y h:i a');

if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

function generateOrderID($length = 15) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return 'RR0'.$randomString;
}

$uniqId = generateOrderID();
$user_id = "";
$recharge_amount = "";
$recharge_mode = "";
$recharge_details = "";

if(isset($_POST['USER_ID']) && isset($_POST['RECHARGE_AMOUNT']) && isset($_POST['RECHARGE_MODE'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
  $recharge_amount = mysqli_real_escape_string($conn,$_POST['RECHARGE_AMOUNT']);
  $recharge_mode = mysqli_real_escape_string($conn,$_POST['RECHARGE_MODE']);
  $recharge_details = mysqli_real_escape_string($conn,$_POST['RECHARGE_DETAILS']);
}

if(isset($_GET['USER_ID']) && isset($_GET['RECHARGE_AMOUNT']) && isset($_GET['RECHARGE_MODE'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
  $recharge_amount = mysqli_real_escape_string($conn,$_GET['RECHARGE_AMOUNT']);
  $recharge_mode = mysqli_real_escape_string($conn,$_GET['RECHARGE_MODE']);
  $recharge_details = mysqli_real_escape_string($conn,$_GET['RECHARGE_DETAILS']);
}

if($user_id!="" && $recharge_amount!="" && $recharge_mode!="" && $recharge_details!=""){

// validate entered utr code
function checkUTRCodeExist($conn,$verifyDetail){
    $returnVal = "false";
    
    // verify details in array
    $data_arr = explode(",", $verifyDetail);
    
    $select_sql = "SELECT * FROM usersrecharge WHERE recharge_details like '%$data_arr[0]%' ";
    $select_query = mysqli_query($conn,$select_sql);
          
    if(mysqli_num_rows($select_query) > 0){
        while ($recharge_data = mysqli_fetch_assoc($select_query)){
            $str_arr = explode(",", $recharge_data['recharge_details']);
            
            if(count($str_arr) > 1){
                    
                if($str_arr[0]==$data_arr[0]){
                   $returnVal = "true";
                }else if($str_arr[0]==$data_arr[0]){
                   $returnVal = "true";
                }
                
            }else if($str_arr[0]==$data_arr[0]){
                $returnVal = "true";
            }
        }
    }
    
    return $returnVal;
}

$available_balance = "";
$select_sql = "SELECT user_balance,user_status FROM usersdata WHERE uniq_id='$user_id' ";
$select_query = mysqli_query($conn,$select_sql);
  
if(mysqli_num_rows($select_query) > 0){
    $res_data = mysqli_fetch_assoc($select_query);
  
    if($res_data['user_status']=="true"){
        if(checkUTRCodeExist($conn,$recharge_details)=="true"){
            $resArr['status_code'] = "utr_exit";
        }else{
            
          if($recharge_mode=="UTRPay"){
            $request_status = "pending";
          
            $insert_sql = $conn->prepare("INSERT INTO usersrecharge(uniq_id,user_id,recharge_amount,recharge_mode,recharge_details,request_status,request_date_time) VALUES(?,?,?,?,?,?,?)");
            $insert_sql->bind_param("sssssss", $uniqId,$user_id,$recharge_amount,$recharge_mode, $recharge_details,$request_status,$curr_date_time);
            $insert_sql->execute();
  
            if ($insert_sql->error == ""){
            $resArr['status_code'] = "pending";
            sendNotification('Recharge Pending!','Someone request for a new recharge of Rs.'.$recharge_amount.' using UTRpay',$MESSAGE_TOKEN);
            }
          }else if($recharge_mode=="QRPay"){
            $request_status = "pending";
          
            $insert_sql = $conn->prepare("INSERT INTO usersrecharge(uniq_id,user_id,recharge_amount,recharge_mode,recharge_details,request_status,request_date_time) VALUES(?,?,?,?,?,?,?)");
            $insert_sql->bind_param("sssssss", $uniqId,$user_id,$recharge_amount,$recharge_mode, $recharge_details,$request_status,$curr_date_time);
            $insert_sql->execute();
  
            if ($insert_sql->error == "") {
              $resArr['status_code'] = "pending";
              $resArr['transaction_id'] = $uniqId;
              sendNotification('New Recharge!','Someone registered a new recharge of Rs.'.$recharge_amount.' using '.$recharge_mode,$MESSAGE_TOKEN);
            }
          }
            
        }
    }else{
        $resArr['status_code'] = "failed2"; 
    }
}else{
    $resArr['status_code'] = "failed1";
}

}else{
  $resArr['status_code'] = "invalid_params";  
}

mysqli_close($conn);
echo json_encode($resArr);

}
?>