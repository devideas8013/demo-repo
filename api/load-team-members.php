<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY","true");
include 'security/config.php';

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');

// * EXPLAIN **
// -initializing array

$resArr = array();
$resArr['data'] = array();
$resArr['pagination'] = "false";
$resArr['total_active'] = 0;
$resArr['total_inactive'] = 0;

if($_SERVER['REQUEST_METHOD'] == 'POST' || 
$_SERVER['REQUEST_METHOD'] == 'GET') {

// * EXPLAIN *
// -setting up pre constants

$user_id = "";
$page_num = 1;
$content = 25;
$total_invite = 0;

// * EXPLAIN **
// -checking for GET & POST request

if(isset($_POST['USER_ID'])){
  $user_id = mysqli_real_escape_string($conn,$_POST['USER_ID']);
}

if(isset($_GET['USER_ID'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
}

if($user_id!="" && $page_num!=""){
    
// * EXPLAIN **
// -getting invite users list
$invite_sql = "SELECT uniq_id,user_mobile_num,user_joined FROM usersdata WHERE user_refered_by='{$user_id}' ";
$invite_query = mysqli_query($conn,$invite_sql);


while($row = mysqli_fetch_assoc($invite_query)){
    $total_invite++;
    
    $level_2_refered_id = $row['uniq_id'];
    $index['r_user_id'] = $level_2_refered_id;
    $index['r_mobile_num'] = $row['user_mobile_num'];
    $index['r_level'] = "1";
    
    $invest_1_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_2_refered_id}' ";
    $invest_1_query = mysqli_query($conn,$invest_1_sql);
    if(mysqli_num_rows($invest_1_query) > 0){
        $resArr['total_active'] += 1;
        $index['r_plan_active'] = "true";
    }else{
        $resArr['total_inactive'] += 1;
        $index['r_plan_active'] = "false";
    }

    array_push($resArr['data'], $index);
    
    $invite_2_sql = "SELECT uniq_id,user_mobile_num,user_joined FROM usersdata WHERE user_refered_by='{$level_2_refered_id}' ";
    $invite_2_query = mysqli_query($conn,$invite_2_sql);
    
    while($row2 = mysqli_fetch_assoc($invite_2_query)){
        $total_invite++;
        
        $level_3_refered_id = $row2['uniq_id'];
        $index2['r_user_id'] = $level_3_refered_id;
        $index2['r_mobile_num'] = $row2['user_mobile_num'];
        $index2['r_level'] = "2";
        
        $invest_2_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_3_refered_id}' ";
        $invest_2_query = mysqli_query($conn,$invest_2_sql);
        if(mysqli_num_rows($invest_2_query) > 0){
          $resArr['total_active'] += 1;
          $index2['r_plan_active'] = "true";
        }else{
          $resArr['total_inactive'] += 1;
          $index2['r_plan_active'] = "false";
        }

        array_push($resArr['data'], $index2);
        
        $invite_3_sql = "SELECT uniq_id,user_mobile_num,user_joined FROM usersdata WHERE user_refered_by='{$level_3_refered_id}' ";
        $invite_3_query = mysqli_query($conn,$invite_3_sql);
    
        while($row3 = mysqli_fetch_assoc($invite_3_query)){
          $total_invite++; 
          
          $level_4_refered_id = $row3['uniq_id'];
          $index3['r_user_id'] = $level_4_refered_id;
          $index3['r_mobile_num'] = $row3['user_mobile_num'];
          $index3['r_level'] = "3";
          
          $invest_3_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_4_refered_id}' ";
          $invest_3_query = mysqli_query($conn,$invest_3_sql);
          if(mysqli_num_rows($invest_3_query) > 0){
            $resArr['total_active'] += 1;
            $index3['r_plan_active'] = "true";
          }else{
            $resArr['total_inactive'] += 1;
            $index3['r_plan_active'] = "false";
          }

          array_push($resArr['data'], $index3);
          
          $invite_4_sql = "SELECT uniq_id,user_mobile_num,user_joined FROM usersdata WHERE user_refered_by='{$level_4_refered_id}' ";
          $invite_4_query = mysqli_query($conn,$invite_4_sql);
    
          while($row4 = mysqli_fetch_assoc($invite_4_query)){
            $total_invite++; 
          
            $level_5_refered_id = $row4['uniq_id'];
            $index4['r_user_id'] = $level_5_refered_id;
            $index4['r_mobile_num'] = $row4['user_mobile_num'];
            $index4['r_level'] = "4";
            
            $invest_4_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_5_refered_id}' ";
            $invest_4_query = mysqli_query($conn,$invest_4_sql);
            if(mysqli_num_rows($invest_4_query) > 0){
              $resArr['total_active'] += 1;
              $index4['r_plan_active'] = "true";
            }else{
              $resArr['total_inactive'] += 1;
              $index4['r_plan_active'] = "false";
            }

            array_push($resArr['data'], $index4);
            
            $invite_5_sql = "SELECT uniq_id,user_mobile_num,user_joined FROM usersdata WHERE user_refered_by='{$level_5_refered_id}' ";
            $invite_5_query = mysqli_query($conn,$invite_5_sql);
    
            while($row5 = mysqli_fetch_assoc($invite_5_query)){
              $total_invite++; 
              
              $level_6_refered_id = $row5['uniq_id'];
              $index5['r_user_id'] = $level_6_refered_id;
              $index5['r_mobile_num'] = $row5['user_mobile_num'];
              $index5['r_level'] = "5";
              
              $invest_5_sql = "SELECT user_id FROM myinvestments WHERE user_id='{$level_6_refered_id}' ";
              $invest_5_query = mysqli_query($conn,$invest_5_sql);
              if(mysqli_num_rows($invest_5_query) > 0){
                $resArr['total_active'] += 1;
                $index5['r_plan_active'] = "true";
              }else{
                $resArr['total_inactive'] += 1;
                $index5['r_plan_active'] = "false";
              }
              
              array_push($resArr['data'], $index5);
            }
          }
        }
    }
}


$numRows = mysqli_num_rows($invite_query);

if($numRows > 0){ 
  $resArr['status_code'] = "success";
}else{
  $resArr['status_code'] = "404";
}

}else{
 $resArr['status_code'] = "invalid_params";
}


// * EXPLAIN **
// -closing mysqli db & responding json data

mysqli_close($conn);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resArr);
}
?>