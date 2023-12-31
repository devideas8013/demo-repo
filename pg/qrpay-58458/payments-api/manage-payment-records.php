<?php
class ManagePayments {
  public $refNum,$rechargeAmount;
  
  function __construct($conn,$user_id,$recharge_amount,$order_id,$payer_name,$utr_code){
    $this->conn =$conn;
    $this->userId =$user_id;
    $this->rechargeAmount =$recharge_amount;
    $this->orderId =$order_id;
    $this->payerName =$payer_name;
    $this->refNum = $utr_code;
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
  
  function getHoursDiff($epochTime){
    $timestamp_in_seconds = $epochTime/1000;
    date_default_timezone_set('Asia/Kolkata');
    $readble_datetime = date('d-m-Y H:i:s', $timestamp_in_seconds);
  
    $currentTimeStamp = date('d-m-Y h:i:s a');
    $timestamp1 = strtotime($currentTimeStamp);
    $timestamp2 = strtotime($readble_datetime);
    
    return abs($timestamp2 - $timestamp1)/(60*60);
  }
  
  function updateNewPayments($arrList) {
    $returnVal = "false";
    $currentTimeStamp = $this->getTimeStamp();
    $jsonArrLen = sizeof($arrList);
    
    if($jsonArrLen > 0){

      for ($x = 0; $x < $jsonArrLen; $x++) {
        $ref_num = $arrList[$x]['bankReferenceNo'];
        $pay_date = $arrList[$x]['paymentTimestamp'];
        
        if(strtolower($arrList[$x]['status'])=="success" && $this->getHoursDiff($pay_date) < 5){
          $search_query = "SELECT payment_ref_num FROM utrpayrecords WHERE payment_ref_num='$ref_num' ";
          $search_result = mysqli_query($this->conn, $search_query) or die('error');

          if (mysqli_num_rows($search_result) <= 0){
            $temp_timestamp = $arrList[$x]['paymentTimestamp'];
            $temp_refnum = $arrList[$x]['bankReferenceNo'];
            $temp_amount = $arrList[$x]['amount'];
            $temp_payername = $arrList[$x]['payerName'];
            $temp_payerhost = $arrList[$x]['payerHandle'];
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

  function checkRechargeRecords(){
    $returnVal = "false";
    $search_query = "SELECT * FROM usersrecharge WHERE uniq_id='{$this->orderId}' AND uniq_id!='' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if (mysqli_num_rows($search_result) > 0){
      $search_response = mysqli_fetch_assoc($search_result);
      $pre_recharge_amount = $search_response['recharge_amount'];
      $pre_request_status = $search_response['request_status'];
      
      if($this->rechargeAmount != $pre_recharge_amount && $this->rechargeAmount!="0"){
        $returnVal = "amount_missmatch";
      }else if($pre_request_status=="pending"){
        $returnVal = "success"; 
      }else{
        $returnVal = "false"; 
      }

    }else{
      $returnVal = "404";
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

  function checkRechargeByName(){
    $resArr = array();
    $resArr['data'] = array();
    $resArr['response_code'] = "false";

    $search_query = "SELECT * FROM utrpayrecords WHERE payment_status='pending' AND payment_amount = '{$this->rechargeAmount}' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if(mysqli_num_rows($search_result) > 0){
      $samePayerrNameExist = 0;
 
      while($row = mysqli_fetch_assoc($search_result)){
        if($this->trimToSmallCase($row['payment_payer_name'])==$this->trimToSmallCase($this->payerName)){
            if($samePayerrNameExist==0){
               $this->refNum = $row['payment_ref_num'];
        
               $index['payment_datetime'] = $row['payment_datetime'];
               $index['payment_ref_num'] = $row['payment_ref_num'];
               $index['payment_amount'] = $row['payment_amount'];
               $index['payment_payer_name'] = $row['payment_payer_name'];
               $index['payment_host'] = $row['payment_host'];
               $index['payment_status'] = $row['payment_status'];
               array_push($resArr['data'], $index);         
            }
          $samePayerrNameExist++;
        }
      }
  
      if($samePayerrNameExist > 1){
        $resArr['response_code'] = "conflict";    
      }else if($samePayerrNameExist > 0){
        $resArr['response_code'] = "success";        
      }

    }
    
    return json_encode($resArr);
  }
  
  function makeRechargeSuccess(){
    $returnVal = "false";

    // check for order id
    $rechargeRecordStatus = $this->checkRechargeRecords();
    
    // check for reference number
    $rechargeRefNumStatus = $this->checkRechargeRefNum();
    
    if ($rechargeRecordStatus == "success"){
        
      if($rechargeRefNumStatus=="not_exist"){
        $returnVal = $this->updateAccountBalance();
      }else{
        $returnVal = "refnum_already_used";          
      }

    }else if($rechargeRecordStatus == "amount_missmatch"){
      $returnVal = "amount_missmatch";
    }else if($rechargeRecordStatus == "false"){
      $returnVal = "refnum_already_used";
    }else{
      $returnVal = "invalid_order_id";      
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
        
        $new_recharge_status = "success";
        $update_sql = $this->conn->prepare("UPDATE usersrecharge SET recharge_details = ?,request_status = ? WHERE user_id = ? AND uniq_id = ?");
        $update_sql->bind_param("ssss", $this->refNum, $new_recharge_status, $this->userId, $this->orderId);
        $update_sql->execute();

        if ($update_sql->error == "") {
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
  
  function validateNewPayment($merchantId,$token,$cookie,$fromDate,$toDate){
    $resArr = array();
    $resArr['data'] = array();
    $resArr['response_code'] = "false";  

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://payments-tesseract.bharatpe.in/api/v1/merchant/transactions?module=PAYMENT_QR&merchantId='.$merchantId.'&sDate='.$fromDate.'&eDate='.$toDate,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
    'token: '.$token,
    'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36',
        'Cookie: '.$cookie
    ),
    ));
        
    $response = curl_exec($curl);
    curl_close($curl);
         
    $jsonArr = json_decode($response,true);
    $jsonArrLen = sizeof($jsonArr['data']['transactions']);

    if($jsonArrLen > 0){
      $paymentResponse = $this->updateNewPayments($jsonArr['data']['transactions']);
      $resArr['response_code'] = $paymentResponse; 
    }

   return json_encode($resArr);
  }
  
  function getPGVars(){
    $resArr = array();
    
    $search_query = "SELECT * FROM allservices WHERE service_name='QR_PAY' ";
    $search_result = mysqli_query($this->conn, $search_query) or die('error');

    if (mysqli_num_rows($search_result) > 0){
       $serach_response = mysqli_fetch_assoc($search_result);
       $str_arr = explode (",", $serach_response['service_value']);
       
       $resArr['pg_upi'] = $str_arr[1];
       $resArr['pg_merchant'] = $str_arr[2];
       $resArr['pg_token'] = $str_arr[3];
       $resArr['pg_cookie'] = $str_arr[4];
    }
    
    return json_encode($resArr);
  }
  
  function __destruct(){
  }
}