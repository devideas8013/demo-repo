<?php
class ManagePayments {
  public $refNum,$rechargeAmount;
  
  function __construct($conn,$user_id,$recharge_amount,$utr_code){
    $this->conn =$conn;
    $this->userId =$user_id;
    $this->rechargeAmount =$recharge_amount;
    $this->refNum = $utr_code;
  }
  
  function generateOrderID($length = 15) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return 'RR0'.$randomString;
  }
  
  function checkReferenceNum(){
    $resArr = array();
    $resArr['data'] = array();
    $resArr['response_code'] = "false";  

    $search_query = "SELECT * FROM utrpayrecords WHERE payment_ref_num='{$this->refNum}' AND payment_status='pending' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if (mysqli_num_rows($search_result) > 0){
      $serach_response = mysqli_fetch_assoc($search_result);
      $this->rechargeAmount = $serach_response['payment_amount'];
      $this->refNum = $serach_response['payment_ref_num'];
        
      $index['payment_datetime'] = $serach_response['payment_datetime'];
      $index['payment_ref_num'] = $serach_response['payment_ref_num'];
      $index['payment_amount'] = $serach_response['payment_amount'];
      $index['payment_payer_name'] = $serach_response['payment_payer_name'];
      $index['payment_host'] = $serach_response['payment_host'];
      $index['payment_status'] = $serach_response['payment_status'];
      array_push($resArr['data'], $index);

      $resArr['response_code'] = "success";  
    }
    
    return json_encode($resArr);
  }
  
  function getTimeStamp(){
    date_default_timezone_set('Asia/Kolkata');
    $currentTimeStamp = date('d-m-Y h:i:s a');
    return $currentTimeStamp;
  }
  
  function getRechargeTimeStamp(){
    date_default_timezone_set('Asia/Kolkata');
    $curr_date_time = date('d-m-Y h:i a');
    return $curr_date_time;
  }
  
  function getHoursDiff($epochTime){
    $timestamp_in_seconds = $epochTime/1000;
    date_default_timezone_set('Asia/Kolkata');
    $readble_datetime = date('d-m-Y H:i:s', $timestamp_in_seconds);
  
    $currentTimeStamp = date('d-m-Y h:i:s a');
    $timestamp1 = strtotime($currentTimeStamp);
    $timestamp2 = strtotime($readble_datetime);
    
    return abs($timestamp2 - $timestamp1)/(60*60);
  }
  
  function updateNewPayments($arrList,$jsonArrLen) {
    $returnVal = "false";
    $currentTimeStamp = $this->getTimeStamp();
    
    if($jsonArrLen > 0){
        
      $temp_payername = "null";
      for ($x = 0; $x < $jsonArrLen; $x++) {
        $ref_num = $arrList[$x]['rrn'];
        $pay_date = $arrList[$x]['date'];
        
        if(strtolower($arrList[$x]['status'])=="success" && $arrList[$x]['mode']=="credit" && $this->getHoursDiff($pay_date) < 5){
          $search_query = "SELECT payment_ref_num FROM utrpayrecords WHERE payment_ref_num='$ref_num' ";
          $search_result = mysqli_query($this->conn, $search_query) or die('error');

          if (mysqli_num_rows($search_result) <= 0){
            $temp_timestamp = $arrList[$x]['date'];
            $temp_refnum = $arrList[$x]['rrn'];
            $temp_amount = $arrList[$x]['amount'];
            $temp_payerhost = 'null';
            $temp_status = 'pending';

            $insert_sql = $this->conn->prepare("INSERT INTO utrpayrecords(payment_payer_name,payment_amount,payment_host,payment_ref_num,payment_status,payment_datetime,payment_submitted) VALUES(?,?,?,?,?,?,?)");       
            $insert_sql->bind_param("sssssss", $temp_payername,$temp_amount, $temp_payerhost,$temp_refnum,$temp_status, $temp_timestamp,$currentTimeStamp);
            $insert_sql->execute();
        
            if ($insert_sql->error == "" && $returnVal=="false") {
              $returnVal = "success";
            }
          }
        }

      }
      
    }
    
    return $returnVal;
  }

  function checkRechargeRefNum(){
    $returnVal = "not_exist";
    $search_query = "SELECT * FROM usersrecharge WHERE recharge_details='{$this->refNum}' AND recharge_details!='' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if (mysqli_num_rows($search_result) > 0){
       $returnVal = "already_exist";
    }
    
    return $returnVal;
  }

  function trimToSmallCase($val){
    $returnVal = str_replace(' ', '', $val);
    return strtolower($returnVal);
  }

  function makeRechargeSuccess(){
    $returnVal = "false";
    
    // check for reference number
    $rechargeRefNumStatus = $this->checkRechargeRefNum();
        
    if($rechargeRefNumStatus=="not_exist"){
        $returnVal = $this->updateAccountBalance();
    }else{
        $returnVal = "refnum_already_used";          
    }
    
    return $returnVal;
  }

  function updateAccountBalance(){
    $returnVal = "false";
    
    $search_query = "SELECT user_balance,account_level,user_status FROM usersdata WHERE uniq_id='$this->userId' AND user_status='true' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if (mysqli_num_rows($search_result) > 0){
        $search_response = mysqli_fetch_assoc($search_result);
        $user_balance = $search_response['user_balance'];
        
        $uniqId = $this->generateOrderID();
        $recharge_mode = "ZEEPay";
        $request_status = "success";
        $request_timestamp = $this->getRechargeTimeStamp();
        
        // make new recharge
        $insert_sql = $this->conn->prepare("INSERT INTO usersrecharge(uniq_id,user_id,recharge_amount,recharge_mode,recharge_details,request_status,request_date_time) VALUES(?,?,?,?,?,?,?)");
        $insert_sql->bind_param("sssssss", $uniqId,$this->userId, $this->rechargeAmount, $recharge_mode, $this->refNum,$request_status,$request_timestamp);
        $insert_sql->execute();
  
        if ($insert_sql->error == "") {
          $resArr['status_code'] = $request_status;
          $resArr['transaction_id'] = $uniqId;

          $account_level = "2";
          $updated_balance = $user_balance+$this->rechargeAmount;
          $updated_balance = number_format($updated_balance, 2, '.', '');
            
          $update_sql = $this->conn->prepare("UPDATE usersdata SET user_balance = ?, account_level = ? WHERE uniq_id = ?");
          $update_sql->bind_param("sss", $updated_balance,$account_level, $this->userId);
          $update_sql->execute();
            
          if($update_sql->error == "") {
            $new_status = "success";
            $update_sql = $this->conn->prepare("UPDATE utrpayrecords SET payment_status = ? WHERE payment_ref_num = ? ");
            $update_sql->bind_param("ss", $new_status, $this->refNum);
            $update_sql->execute();

            $returnVal = "success";
          }else{
            $returnVal = "failed";
          }

        }else{
          $returnVal = "sql_error";
        }
        
    }else{
      $returnVal = "account_error";
    }
    
    return $returnVal;
  }
  
  function validateNewPayment($token){
    $resArr = array();
    $resArr['data'] = array();
    $resArr['response_code'] = "false";  

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://webapi.mobikwik.com/p/wallet/history/v2',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
    'Authorization: '.$token,
    'X-Mclient: 0',
    'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36'
    ),
    ));
        
    $response = curl_exec($curl);
    curl_close($curl);
         
    $jsonArr = json_decode($response,true);
    $jsonArrLen = sizeof($jsonArr['data']['historyData']);
    $resArr['arr_length'] = $jsonArrLen;

    if($jsonArrLen > 0){
      $paymentResponse = $this->updateNewPayments($jsonArr['data']['historyData'],$jsonArrLen);
      $resArr['response_code'] = $paymentResponse; 
    }

   return json_encode($resArr);
  }
  
  function getPGVars(){
    $resArr = array();
    
    $search_query = "SELECT * FROM allservices WHERE service_name='ZEE_PAY' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if (mysqli_num_rows($search_result) > 0){
       $serach_response = mysqli_fetch_assoc($search_result);
       $str_arr = explode (",", $serach_response['service_value']);
       
       $resArr['pg_upi'] = $str_arr[1];
       $resArr['pg_token'] = $str_arr[2];
    }
    
    return json_encode($resArr);
  }
  
  function __destruct(){
  }
}