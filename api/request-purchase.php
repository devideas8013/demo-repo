<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY", "true");
include "security/constants.php";

// header("Access-Control-Allow-Origin: https://" . $MAIN_DOMAIN_URL);
// header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");
// header("Access-Control-Allow-Credentials: true");

include "security/config.php";
$authorization = "";

$resArr = [];
$resArr["match_order_id"] = "";
$resArr["account_balance"] = "0";
$resArr["status_code"] = "failed";

date_default_timezone_set("Asia/Kolkata");
$curr_date = date("d-m-Y");
$curr_time = date("h:i a");
$curr_date_time = $curr_date.' '.$curr_time;

foreach (getallheaders() as $name => $value) {
    if ($name == "Authorization") {
        $authorization = $value;
        break;
    }
}

if (
    $_SERVER["REQUEST_METHOD"] == "POST" ||
    $_SERVER["REQUEST_METHOD"] == "GET"
) {

    $user_id = "";
    $invest_id = "";

    function generateOrderID($length = 16)
    {
        $characters = "0123456789AGPR";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return "IN0" . $randomString;
    }

    if (isset($_GET["USER_ID"]) && isset($_GET["INVEST_ID"])) {
        $user_id = mysqli_real_escape_string($conn, $_GET["USER_ID"]);
        $invest_id = mysqli_real_escape_string($conn, $_GET["INVEST_ID"]);
    }
    
    
    function sendCommissions($IS_COMISSION_ALLOWED,$conn,$user_id,$total_amount,$curr_date_time){
        if ($IS_COMISSION_ALLOWED == "true") {
            
           $select_services_sql = "SELECT * FROM allservices WHERE service_name='COMISSION_BONUS' ";
           $select_services_query = mysqli_query($conn, $select_services_sql);

           if (mysqli_num_rows($select_services_query) > 0) {
             $services_data = mysqli_fetch_assoc($select_services_query);
             $resArray = explode(",",$services_data["service_value"]);
            
             if(count($resArray) >= 3){
                $LEVEL_1_BONUS_RETURN = $resArray[0]/100;
                $LEVEL_2_BONUS_RETURN = $resArray[1]/100; 
                $LEVEL_3_BONUS_RETURN = $resArray[2]/100;
             }else{
               echo 'bonus data error';
               return;  
             }
           }else{
             echo 'invalid service data';
             return;  
           }
            
            $transac_type = "commision";

            // fetching user refered by
            $search_sql = "SELECT user_refered_by FROM usersdata WHERE uniq_id='{$user_id}' AND account_level >= 2 ";
            $search_query = mysqli_query($conn,$search_sql);

            if (mysqli_num_rows($search_query) > 0) {
                $search_res_data = mysqli_fetch_assoc($search_query);
                $user_refered_by = $search_res_data["user_refered_by"];

                if ($user_refered_by != "") {
                    $level_1_bonus = $total_amount * $LEVEL_1_BONUS_RETURN;
                    $level_1_bonus = number_format($level_1_bonus,2,".","");
                    
                    $level1_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = user_balance + ? WHERE uniq_id = ?");
                    $level1_update_sql->bind_param("ss",$level_1_bonus,
                                            $user_refered_by);
                    $level1_update_sql->execute();

                    // save comission data
                    $extra_msg = "Level 1";

                    $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                    $insert_sql->bind_param("ssssss",$user_refered_by,$user_id,
                        $transac_type,$level_1_bonus,$extra_msg,$curr_date_time);
                    $insert_sql->execute();


                    // level 2 commision
                    $level2_sql = "SELECT user_refered_by FROM usersdata WHERE uniq_id='{$user_refered_by}' AND account_level >= 2 ";
                    $level2_query = mysqli_query($conn,$level2_sql);

                    if (mysqli_num_rows($level2_query) > 0) {
                        $level2_res_data = mysqli_fetch_assoc($level2_query);
                        $level2_refered_by =$level2_res_data["user_refered_by"];

                        if ($level2_refered_by != "") {
                            $level_2_bonus = $total_amount *$LEVEL_2_BONUS_RETURN;

                            $level_2_bonus = number_format($level_2_bonus,2,".",
                                                    "");

                            $level2_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = user_balance + ? WHERE uniq_id = ?");
                            $level2_update_sql->bind_param("ss",$level_2_bonus,
                                            $level2_refered_by);
                            $level2_update_sql->execute();

                            // save comission data
                            $extra_msg = "Level 2";

                            $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                            $insert_sql->bind_param("ssssss",$level2_refered_by,
                                $user_id,$transac_type,$level_2_bonus,$extra_msg,$curr_date_time);
                            $insert_sql->execute();

                            // level 3 commision
                            $level3_sql = "SELECT user_refered_by FROM usersdata WHERE uniq_id='{$level2_refered_by}' AND account_level >= 2 ";
                            $level3_query = mysqli_query($conn,$level3_sql);

                            if (mysqli_num_rows($level3_query) > 0) {
                                $level3_res_data = mysqli_fetch_assoc(
                                                        $level3_query);
                                $level3_refered_by =$level3_res_data[
                                                            "user_refered_by"];
                                if ($level3_refered_by != "") {
                                    $level_3_bonus =$total_amount *$LEVEL_3_BONUS_RETURN;

                                    $level_3_bonus = number_format($level_3_bonus,2, ".","");
                                    
                                    $level3_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = user_balance + ? WHERE uniq_id = ?");
                                    $level3_update_sql->bind_param("ss",$level_3_bonus,$level3_refered_by);
                                    $level3_update_sql->execute();

                                    // save comission data
                                    $extra_msg = "Level 3";

                                    $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                                    $insert_sql->bind_param("ssssss",
                                        $level3_refered_by,$user_id,$transac_type,$level_3_bonus,$extra_msg,$curr_date_time);
                                    $insert_sql->execute();
                                }
                            }
                        }
                    }
                }
            }

        }
    }

    if ($user_id != "" && $invest_id != "") {

        $select_invest_sql = "SELECT * FROM investmentlist WHERE uniq_id='{$invest_id}' ";
        $select_invest_query = mysqli_query($conn, $select_invest_sql);

        if (mysqli_num_rows($select_invest_query) > 0) {
            $invest_data = mysqli_fetch_assoc($select_invest_query);
            $invest_name = $invest_data["investment_name"];
            $invest_hourly_income = $invest_data["investment_hourly_income"];
            $invest_price = $invest_data["investment_price"];
            $invest_img = $invest_data["investment_image_url"];
            
            $invest_total_revenue = number_format($invest_data["investment_hourly_income"]*$invest_data["investment_total_days"]*24,2,".","");
            
            $invest_total_days = $invest_data["investment_total_days"];

            $select_sql = "SELECT user_balance,user_status FROM usersdata WHERE uniq_id='$user_id' ";
            $select_query = mysqli_query($conn, $select_sql);

            if (mysqli_num_rows($select_query) > 0) {
                $res_data = mysqli_fetch_assoc($select_query);
                
                $investment_order_id = generateOrderID();
                
                $my_invest_sql = "SELECT uniq_id,user_id FROM myinvestments WHERE uniq_id='{$investment_order_id}' OR (user_id='{$user_id}' AND investment_id='{$invest_id}') ";
                $my_invest_query = mysqli_query($conn, $my_invest_sql);

                if (mysqli_num_rows($my_invest_query) > 0) {
                   $resArr["status_code"] = "already_purchased";
                }else{
                    
                  if ($res_data["user_status"] == "true") {
                    if ($res_data["user_balance"] < $invest_price) {
                        $resArr["status_code"] = "balance_error";
                    } else {
                        $updated_balance =
                            $res_data["user_balance"] - $invest_price;

                        $update_sql = $conn->prepare(
                            "UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?"
                        );
                        $update_sql->bind_param(
                            "ss",
                            $updated_balance,
                            $user_id
                        );
                        $update_sql->execute();

                        if ($update_sql->error == "") {
                            
                            $zero_val = "0";
                            $investment_status = "pending";
                            
                            $insert_sql = $conn->prepare(
                                "INSERT INTO myinvestments(uniq_id,user_id,investment_id,investment_name,investment_price,investment_earnings,investment_img,investment_hourly_income,investment_total_days,investment_date,investment_time,investment_last_update,investment_status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            );
                            $insert_sql->bind_param(
                                "sssssssssssss",
                                $investment_order_id,
                                $user_id,
                                $invest_id,
                                $invest_name,
                                $invest_price,
                                $zero_val,
                                $invest_img,
                                $invest_hourly_income,
                                $invest_total_days,
                                $curr_date,
                                $curr_time,
                                $curr_date_time,
                                $investment_status
                            );
                            $insert_sql->execute();

                            if ($insert_sql->error == "") {
                                
                                // sendCommissions($IS_COMISSION_ALLOWED,$conn,$user_id,$invest_price,$curr_date_time);
                                
                                $resArr["account_balance"] = $updated_balance;
                                $resArr["status_code"] = "success";
                            } else {
                                $resArr["status_code"] = "sql_failed";
                            }
                        } else {
                            $resArr["status_code"] = "sql_failed";
                        }
                    }
                  } else {
                    $resArr["status_code"] = "account_error";
                  }
                  
                }
            } else {
                $resArr["status_code"] = "auth_error";
            }
        } else {
            $resArr["status_code"] = "invalid_params";
        }
    } else {
        $resArr["status_code"] = "invalid_params";
    }

    mysqli_close($conn);
    echo json_encode($resArr);
}
?>
