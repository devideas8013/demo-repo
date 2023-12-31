<?php
header("Access-Control-Allow-Origin: *");
$resArr = [];
$resArr["data"] = [];
$pre_req_status = "true";
$resArr["status_code"] = "false";

date_default_timezone_set("Asia/Kolkata");
$curr_date = date("d-m-Y");
$curr_time = date("h:i:s a");

define("ACCESS_SECURITY", "true");
include "../../security/constants.php";

// header("Access-Control-Allow-Origin: https://" . $MAIN_DOMAIN_URL);
// header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");
// header("Access-Control-Allow-Credentials: true");

include "../../security/config.php";

class globalVar{
    static $MOBILE_NUM = "";
    static $CURRENT_DATE = "";
    static $CURRENT_TIME = "";
    static $SMS_API_TOKEN = "";
    static $USER_ID_LENGTH = 6;
    static $OTP_LENGTH = 5;
}


if (
    $_SERVER["REQUEST_METHOD"] == "POST" ||
    $_SERVER["REQUEST_METHOD"] == "GET"
) {
    
    if (isset($_GET["MOBILE"])) {
        $auth_user_mobile = mysqli_real_escape_string($conn, $_GET["MOBILE"]);
    } else {
        echo "param_missing";
        return;
    }

    if (isset($_GET["PURPOSE"])) {
        $auth_purpose = mysqli_real_escape_string($conn, $_GET["PURPOSE"]);
    } else {
        echo "param_missing";
        return;
    }
    
    // setting up global vars
    globalVar::$SMS_API_TOKEN = $SMS_API_TOKEN;
    globalVar::$MOBILE_NUM = $auth_user_mobile;
    globalVar::$CURRENT_DATE = $curr_date;
    globalVar::$CURRENT_TIME = $curr_time;
    
    function getRealIP(){
        $content = file_get_contents('https://api.ipify.org');
        if($content === FALSE) {
            $returnVal = "IP_ERROR";
        }else{
            $returnVal = $content;
        }
        
        return $returnVal;
    }

    function sendNewOTP($otp)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                "https://www.fast2sms.com/dev/bulkV2?authorization=" .
                globalVar::$SMS_API_TOKEN .
                "&variables_values=" .
                $otp .
                "&route=otp&numbers=" .
                globalVar::$MOBILE_NUM,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $jsonArr = json_decode($response, true);
        
        return $jsonArr["message"][0];
    }
    
    function updateNewOTP($conn,$new_otp,$mobile){
        $returnVal = "false";
        
        $update_sql = $conn->prepare(
            "UPDATE usersdata SET user_last_otp = ? WHERE user_mobile_num = ? "
        );
        $update_sql->bind_param("ss", $new_otp, $mobile);
        $update_sql->execute();

        if ($update_sql->error == "") {
            $returnVal = "true";
        }
        
        return $returnVal;
    }
    
    function decodeSMSResponse($smsResponse){
        $returnVal = "false";
        
        if($smsResponse=="SMS sent successfully."){
            $returnVal = "true";
        }
        
        return $returnVal;
    }

    function generateRandomNumber($length)
    {
        $characters = "0123456789";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    $new_otp = generateRandomNumber(globalVar::$OTP_LENGTH);
    
    if ($new_otp=="" || $auth_user_mobile == "" || $auth_purpose=="") {
        echo "param_missing";
        return;      
    }
    
    function createNewAccount($conn,$unique_id,$new_otp){
        $returnVal = "failed";
        
        $unknown_data = "unknown";
        $empty_data = "";
        $zero_data = "0";
        $none_data = "none";
        $account_level = "1";
        $false_value = "false";
        $curr_date_time = globalVar::$CURRENT_DATE . " " . globalVar::$CURRENT_TIME;
        
        $pre_req_status = "true";
        $user_real_ip = getRealIP();
        
        // check banned mobile number
        $bannedMobNumList = "1234567890,1234567899,0987654321";
        $bannedMobNumber = explode(",", $bannedMobNumList);
        if (in_array(globalVar::$MOBILE_NUM, $bannedMobNumber)) {
            $pre_req_status = "false";
        }
        
        if(strlen(globalVar::$MOBILE_NUM) != 10){
            $pre_req_status = "false";
        }
        
        if ($pre_req_status == "true") {
            
          $insert_sql = $conn->prepare(
          "INSERT INTO usersdata(uniq_id,user_auth_secret,user_mobile_num,user_email_id,user_full_name,user_password,user_balance,user_withdrawl_balance,user_total_coins,user_refered_by,user_last_active_date,user_last_active_time,user_last_otp,account_level,user_status,user_login_ip,user_joined_ip,user_joined) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
          );
          $insert_sql->bind_param(
          "ssssssssssssssssss",
          $unique_id,
          $empty_data,
          globalVar::$MOBILE_NUM,
          $unknown_data,
          $unknown_data,
          $empty_data,
          $zero_data,
          $zero_data,
          $zero_data,
          $empty_data,
          globalVar::$CURRENT_DATE,
          globalVar::$CURRENT_TIME,
          $empty_data,
          $account_level,
          $false_value,
          $user_real_ip,
          $user_real_ip,
          $curr_date_time
         );
         $insert_sql->execute();

          if ($insert_sql->error == "") {
            $updateResponse = updateNewOTP($conn,$new_otp,globalVar::$MOBILE_NUM);
            if ($updateResponse == "true") {
                $sms_response = decodeSMSResponse(sendNewOTP($new_otp));
                $resArr["extra_val"] = $sms_response;
                
                if($sms_response=="true"){
                    $returnVal = "success";
                }else{
                    $returnVal = "otp_error";
                }
            }
        }

        }else{
            $returnVal = "mobile_num_error";
        }
        
        return $returnVal;
    }
    
    function checkForDuplicateId($conn,$user_mobile,$unique_id){
        $returnVal = "false";
        
        $search_uniqid_sql = "SELECT user_mobile_num FROM usersdata WHERE user_mobile_num='{$user_mobile}' OR uniq_id='{$unique_id}' ";
        $search_uniqid_query = mysqli_query($conn, $search_uniqid_sql);
        
        if (mysqli_num_rows($search_uniqid_query) > 0) {
            $returnVal = "true";
        }
        
        return $returnVal;
    }
    

    $select_sql = "SELECT user_mobile_num,user_status FROM usersdata WHERE user_mobile_num='$auth_user_mobile' ";
    $select_query = mysqli_query($conn, $select_sql);

    if (mysqli_num_rows($select_query) > 0) {
       $response_data = mysqli_fetch_assoc($select_query);
       $returnVal = "otp_error";
       
       if($auth_purpose == "SIGNUP" && $response_data['user_status']=="false"){
         $updateResponse = updateNewOTP($conn,$new_otp,$auth_user_mobile);
         
         if ($updateResponse == "true") {
            $sms_response = decodeSMSResponse(sendNewOTP($new_otp));
            
            if($sms_response=="true"){
                $returnVal = "success";
            }
            
            $resArr["status_code"] = $returnVal;
         } 
       }else if($auth_purpose == "SIGNUP" && $response_data['user_status']=="true"){
           $resArr["status_code"] = "already_registered";
       }else if($auth_purpose == "RESETPASSWORD" && $response_data['user_status']!="ban"){
         $updateResponse = updateNewOTP($conn,$new_otp,$auth_user_mobile);
         
         if ($updateResponse == "true") {
            $sms_response = decodeSMSResponse(sendNewOTP($new_otp));
             
            if($sms_response=="true"){
                $returnVal = "success";
            }
            
            $resArr["status_code"] = $returnVal;
         } 
       }else{
           $resArr["status_code"] = "account_error";
       }

    } elseif ($auth_purpose == "SIGNUP") {
        $unique_id = generateRandomNumber(globalVar::$USER_ID_LENGTH);
      
        if(checkForDuplicateId($conn,$auth_user_mobile,$unique_id)=="true"){
            $unique_id = generateRandomNumber(globalVar::$USER_ID_LENGTH);
            
            if(checkForDuplicateId($conn,$auth_user_mobile,$unique_id)=="true"){
                $unique_id = generateRandomNumber(globalVar::$USER_ID_LENGTH);
                
                if(checkForDuplicateId($conn,$auth_user_mobile,$unique_id)=="true"){
                    $unique_id = generateRandomNumber(globalVar::$USER_ID_LENGTH);
                    
                    if(checkForDuplicateId($conn,$auth_user_mobile,$unique_id)=="false"){
                      $resArr["status_code"] = createNewAccount($conn,$unique_id,$new_otp);
                    }else{
                      $resArr["status_code"] = "multi_process_error";
                    }
                }else{
                    $resArr["status_code"] = createNewAccount($conn,$unique_id,$new_otp);
                }
            }else{
                $resArr["status_code"] = createNewAccount($conn,$unique_id,$new_otp);
            }
        }else{
            $resArr["status_code"] = createNewAccount($conn,$unique_id,$new_otp);
        }
    }
}

echo json_encode($resArr);