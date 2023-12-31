<?php
header("Access-Control-Allow-Origin: *");
$resArr = [];
define("ACCESS_SECURITY", "true");
include "security/constants.php";

// header("Access-Control-Allow-Origin: https://" . $MAIN_DOMAIN_URL);
// header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");
// header("Access-Control-Allow-Credentials: true");

include "security/config.php";
include "mainhandler/get-period-id.php";
$authorization = "";

$resArr["match_order_id"] = "";
$resArr["account_balance"] = "0";
$resArr["status_code"] = "failed";

date_default_timezone_set("Asia/Kolkata");
$curr_date_time = date("d-m-Y h:i a");

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
    
    // * EXPLAIN *
    // -setting up pre constants

    $user_id = "";
    $color_code = "";
    $num_lot = "";
    $project_name = "";
    $contract_amount = 0;
    
    function generateOrderID($length = 15)
    {
        $characters = "0123456789AGPR";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return "RB0" . $randomString;
    }

    function getInvestedAmount($amount, $GAME_TAX)
    {
        return $amount - $amount * $GAME_TAX;
    }

    function getMatchTax($amount, $GAME_TAX)
    {
        return $amount * $GAME_TAX;
    }

    if (
        isset($_POST["USER_ID"]) &&
        isset($_POST["BET_ON"]) &&
        isset($_POST["NUM_LOT"]) &&
        isset($_POST["CONTRACT_AMOUNT"]) &&
        isset($_POST["PROJECT"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_POST["USER_ID"]);
        $color_code = mysqli_real_escape_string($conn, $_POST["BET_ON"]);
        $num_lot = mysqli_real_escape_string($conn, $_POST["NUM_LOT"]);
        $project_name = mysqli_real_escape_string($conn, $_POST["PROJECT"]);
        $contract_amount = mysqli_real_escape_string(
            $conn,
            $_POST["CONTRACT_AMOUNT"]
        );
    }

    if (
        isset($_GET["USER_ID"]) &&
        isset($_GET["BET_ON"]) &&
        isset($_GET["NUM_LOT"]) &&
        isset($_GET["CONTRACT_AMOUNT"]) && 
        isset($_GET["PROJECT"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_GET["USER_ID"]);
        $color_code = mysqli_real_escape_string($conn, $_GET["BET_ON"]);
        $num_lot = mysqli_real_escape_string($conn, $_GET["NUM_LOT"]);
        $project_name = mysqli_real_escape_string($conn, $_GET["PROJECT"]);
        $contract_amount = mysqli_real_escape_string(
            $conn,
            $_GET["CONTRACT_AMOUNT"]
        );
    }
    
    // selected game array
    
    $games_sql = "SELECT * FROM gamecontrols WHERE service_name='{$project_name}' ";
    $games_query = mysqli_query($conn, $games_sql);
        
    if(mysqli_num_rows($games_query) <= 0){
        echo "invalid_project_name";
        return;   
    }else{
        $game_res_data = mysqli_fetch_assoc($games_query);
        $game_service_val = $game_res_data['service_value'];
        $game_service_times = $game_res_data['service_times'];
        $game_service_tax = $game_res_data['service_tax'];

        if($game_service_val!=""){
          $str_arr = explode (",", $game_service_val);
          $game_period_id = $str_arr[0];
        }
          
        if($game_service_times!=""){
          $str_arr = explode (",", $game_service_times);
          $game_play_time = $str_arr[0];
          $game_disable_time = $str_arr[1];
        }
          
    }

    if (
        $user_id != "" &&
        $num_lot >= 1 &&
        $color_code != "" &&
        $contract_amount >= 10
    ) {
        $generatePeriod = new GeneratePeriod($game_play_time);
        $generatePeriod->setupTimes();
        $period_id =
            $generatePeriod->getDateTime() . $generatePeriod->getPeriodId();
        $match_remaining_seconds = $generatePeriod->getRemainingSec();
        
        $resArr["match_remaining_sec"] = $match_remaining_seconds;
           
        if($match_remaining_seconds < $game_disable_time){
            $resArr["status_code"] = "betting_timeout_error";
        }else{
        
        $total_amount = (int) $contract_amount * (int) $num_lot;
        $invested_amount = getInvestedAmount($total_amount, $game_service_tax);
        $match_fee = getMatchTax($total_amount, $game_service_tax);

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

        $select_sql = "SELECT user_balance,user_withdrawl_balance,user_status FROM usersdata WHERE uniq_id='$user_id' ";
        $select_query = mysqli_query($conn, $select_sql);

        if (mysqli_num_rows($select_query) > 0) {
            $res_data = mysqli_fetch_assoc($select_query);

            if ($res_data["user_status"] == "true") {
                if ($res_data["user_balance"] < $total_amount) {
                    $resArr["status_code"] = "balance_error";
                } else {
                    $updated_balance =
                        $res_data["user_balance"] - $total_amount;
                        
                    if($res_data["user_withdrawl_balance"] < $total_amount){
                        $updated_win_balance = 0;
                    }else{
                        $updated_win_balance =
                        $res_data["user_withdrawl_balance"] - $total_amount;
                    }
                    
                    $update_sql = $conn->prepare(
                        "UPDATE usersdata SET user_balance = ?,user_withdrawl_balance = ? WHERE uniq_id = ?"
                    );
                    $update_sql->bind_param(
                        "sss",
                        $updated_balance,
                        $updated_win_balance,
                        $user_id
                    );
                    $update_sql->execute();

                    if ($update_sql->error == "") {
                        $match_order_id = generateOrderID();
                        $match_result = "";
                        $open_price = "";
                        $match_status = "wait";
                        $match_profit = "0";
                        $insert_sql = $conn->prepare(
                            "INSERT INTO matchplayed(user_id,order_id,match_id,match_open_price,match_invested_on,match_cost,match_color_lot,match_invested,match_fee,match_profit,match_result,last_account_balance,match_status,project_name,time_stamp) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        );
                        $insert_sql->bind_param(
                            "sssssssssssssss",
                            $user_id,
                            $match_order_id,
                            $period_id,
                            $open_price,
                            $color_code,
                            $total_amount,
                            $num_lot,
                            $invested_amount,
                            $match_fee,
                            $match_profit,
                            $match_result,
                            $updated_balance,
                            $match_status,
                            $project_name,
                            $curr_date_time
                        );
                        $insert_sql->execute();

                        if ($insert_sql->error == "") {
                            
                            if ($IS_COMISSION_ALLOWED == "true") {
                                $transac_type = "commision";

                                // fetching user refered by
                                $search_sql = "SELECT user_refered_by FROM usersdata WHERE uniq_id='{$user_id}' AND account_level >= 2 ";
                                $search_query = mysqli_query(
                                    $conn,
                                    $search_sql
                                );

                                if (mysqli_num_rows($search_query) > 0) {
                                    $search_res_data = mysqli_fetch_assoc(
                                        $search_query
                                    );
                                    $user_refered_by =
                                        $search_res_data["user_refered_by"];

                                    if ($user_refered_by != "") {
                                        $level_1_bonus =
                                            $total_amount *
                                            $LEVEL_1_BONUS_RETURN;

                                        $level_1_bonus = number_format(
                                            $level_1_bonus,
                                            2,
                                            ".",
                                            ""
                                        );

                                        $level1_update_sql = $conn->prepare(
                                            "UPDATE usersdata SET user_commission_balance = user_commission_balance + ? WHERE uniq_id = ?"
                                        );
                                        $level1_update_sql->bind_param(
                                            "ss",
                                            $level_1_bonus,
                                            $user_refered_by
                                        );
                                        $level1_update_sql->execute();

                                        // save comission data
                                        $extra_msg = "Level 1";

                                        $insert_sql = $conn->prepare(
                                            "INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)"
                                        );
                                        $insert_sql->bind_param(
                                            "ssssss",
                                            $user_refered_by,
                                            $user_id,
                                            $transac_type,
                                            $level_1_bonus,
                                            $extra_msg,
                                            $curr_date_time
                                        );
                                        $insert_sql->execute();


                                        // level 2 commision
                                        $level2_sql = "SELECT user_refered_by FROM usersdata WHERE uniq_id='{$user_refered_by}' AND account_level >= 2 ";
                                        $level2_query = mysqli_query(
                                            $conn,
                                            $level2_sql
                                        );

                                        if (
                                            mysqli_num_rows($level2_query) > 0
                                        ) {
                                            $level2_res_data = mysqli_fetch_assoc(
                                                $level2_query
                                            );
                                            $level2_refered_by =
                                                $level2_res_data[
                                                    "user_refered_by"
                                                ];

                                            if ($level2_refered_by != "") {
                                                $level_2_bonus =
                                                    $total_amount *
                                                    $LEVEL_2_BONUS_RETURN;

                                                $level_2_bonus = number_format(
                                                    $level_2_bonus,
                                                    2,
                                                    ".",
                                                    ""
                                                );
                                                $level2_update_sql = $conn->prepare(
                                                    "UPDATE usersdata SET user_commission_balance = user_commission_balance + ? WHERE uniq_id = ?"
                                                );
                                                $level2_update_sql->bind_param(
                                                    "ss",
                                                    $level_2_bonus,
                                                    $level2_refered_by
                                                );
                                                $level2_update_sql->execute();

                                                // save comission data
                                                $extra_msg = "Level 2";

                                                $insert_sql = $conn->prepare(
                                                    "INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)"
                                                );
                                                $insert_sql->bind_param(
                                                    "ssssss",
                                                    $level2_refered_by,
                                                    $user_id,
                                                    $transac_type,
                                                    $level_2_bonus,
                                                    $extra_msg,
                                                    $curr_date_time
                                                );
                                                $insert_sql->execute();

                                                // level 3 commision
                                                $level3_sql = "SELECT user_refered_by FROM usersdata WHERE uniq_id='{$level2_refered_by}' AND account_level >= 2 ";
                                                $level3_query = mysqli_query(
                                                    $conn,
                                                    $level3_sql
                                                );

                                                if (
                                                    mysqli_num_rows(
                                                        $level3_query
                                                    ) > 0
                                                ) {
                                                    $level3_res_data = mysqli_fetch_assoc(
                                                        $level3_query
                                                    );
                                                    $level3_refered_by =
                                                        $level3_res_data[
                                                            "user_refered_by"
                                                        ];
                                                    if (
                                                        $level3_refered_by != ""
                                                    ) {
                                                        $level_3_bonus =
                                                            $total_amount *$LEVEL_3_BONUS_RETURN;

                                                        $level_3_bonus = number_format(
                                                            $level_3_bonus,
                                                            2,
                                                            ".",
                                                            ""
                                                        );
                                                        $level3_update_sql = $conn->prepare(
                                                            "UPDATE usersdata SET user_commission_balance = user_commission_balance + ? WHERE uniq_id = ?"
                                                        );
                                                        $level3_update_sql->bind_param("ss",
                                                            $level_3_bonus,
                                                            $level3_refered_by
                                                        );
                                                        $level3_update_sql->execute();

                                                        // save comission data
                                                        $extra_msg = "Level 3";

                                                        $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)"
                                                        );
                                                        $insert_sql->bind_param(
                                                            "ssssss",
                                                            $level3_refered_by,
                                                            $user_id,
                                                            $transac_type,
                                                            $level_3_bonus,
                                                            $extra_msg,
                                                            $curr_date_time
                                                        );
                                                        $insert_sql->execute();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }

                            $resArr["match_order_id"] = $match_order_id;
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
        } else {
            $resArr["status_code"] = "auth_error";
        }
        
        }
    }else {
        $resArr["status_code"] = "invalid_params";
    }

    mysqli_close($conn);
    echo json_encode($resArr);
}
?>