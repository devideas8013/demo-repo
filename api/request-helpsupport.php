<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY", "true");
include 'security/config.php';

$resArr = array();

date_default_timezone_set('Asia/Kolkata');
$curr_date_time = date('d-m-Y h:i a');

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){

  if(isset($_POST['USER_ID']) && isset($_POST['DETAILS'])){
    $user_id = mysqli_real_escape_string($conn, $_POST['USER_ID']);
    $reward_id = mysqli_real_escape_string($conn, $_POST['DETAILS']);   
  }

  if(isset($_GET['USER_ID']) && isset($_GET['DETAILS'])){
    $user_id = mysqli_real_escape_string($conn, $_GET['USER_ID']);
    $details = mysqli_real_escape_string($conn, $_GET['DETAILS']);   
  }
  
  if($user_id=="" || $details==""){
    echo "invalid_params";
    return;
  }
  
  function generateOrderID($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPRSTUVWYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return 'RR0'.$randomString;
  }
  
  $select_user_sql = "SELECT * FROM usersdata WHERE uniq_id='{$user_id}' AND user_status='true' ";
  $select_user_query = mysqli_query($conn,$select_user_sql);
  
  if(mysqli_num_rows($select_user_query) > 0){
    $complaint_status = "pending";
    $complaint_id = generateOrderID(12);
    $insert_sql = $conn->prepare("INSERT INTO userscomplaints(uniq_id,user_id,complain_details,complain_status,complain_date_time) VALUES(?,?,?,?,?)");
    $insert_sql->bind_param("sssss", $complaint_id, $user_id, $details, $complaint_status, $curr_date_time);
    $insert_sql->execute();
    
    if ($insert_sql->error == ""){
      $resArr['status_code'] = "success";
    }else{
      $resArr['status_code'] = "sql_failed";
    }
  }else{
    $resArr['status_code'] = "account_error";
  }

   mysqli_close($conn);
   echo json_encode($resArr);
}
?>