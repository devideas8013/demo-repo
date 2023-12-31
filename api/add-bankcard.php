<?php
header("Access-Control-Allow-Origin: *");
define("ACCESS_SECURITY", "true");
include "security/config.php";

$resArr = [];

date_default_timezone_set("Asia/Kolkata");
$curr_date_time = date("d-m-Y h:i:s a");

if (
    $_SERVER["REQUEST_METHOD"] == "POST" ||
    $_SERVER["REQUEST_METHOD"] == "GET"
) {
    $user_id = "";
    $user_actual_name = "";
    $user_bank_name = "";
    $user_bank_account = "";
    $user_bank_ifsc_code = "";
    $is_primary = "";
    $card_method = "";
    $max_cards_limit = 10;

    function generateBankCardID($length = 15)
    {
        $characters = "0123456789HEAMR";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return "BC0" . $randomString;
    }

    $new_uniq_id = generateBankCardID();

    if (
        isset($_POST["USER_ID"]) &&
        isset($_POST["BENEFICIARY_NAME"]) &&
        isset($_POST["USER_BANK_NAME"]) &&
        isset($_POST["USER_BANK_ACCOUNT"]) &&
        isset($_POST["CARD_METHOD"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_POST["USER_ID"]);
        $user_actual_name = mysqli_real_escape_string(
            $conn,
            $_POST["BENEFICIARY_NAME"]
        );
        $user_bank_name = mysqli_real_escape_string(
            $conn,
            $_POST["USER_BANK_NAME"]
        );
        $user_bank_account = mysqli_real_escape_string(
            $conn,
            $_POST["USER_BANK_ACCOUNT"]
        );
        $user_bank_ifsc_code = mysqli_real_escape_string(
            $conn,
            $_POST["USER_BANK_IFSC_CODE"]
        );
        $is_primary = mysqli_real_escape_string($conn, $_POST["IS_PRIMARY"]);
        $card_method = mysqli_real_escape_string($conn, $_POST["CARD_METHOD"]);
    }

    if (
        isset($_GET["USER_ID"]) &&
        isset($_GET["BENEFICIARY_NAME"]) &&
        isset($_GET["USER_BANK_NAME"]) &&
        isset($_GET["USER_BANK_ACCOUNT"]) &&
        isset($_GET["CARD_METHOD"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_GET["USER_ID"]);
        $user_actual_name = mysqli_real_escape_string(
            $conn,
            $_GET["BENEFICIARY_NAME"]
        );
        $user_bank_name = mysqli_real_escape_string(
            $conn,
            $_GET["USER_BANK_NAME"]
        );
        $user_bank_account = mysqli_real_escape_string(
            $conn,
            $_GET["USER_BANK_ACCOUNT"]
        );
        $user_bank_ifsc_code = mysqli_real_escape_string(
            $conn,
            $_GET["USER_BANK_IFSC_CODE"]
        );
        $is_primary = mysqli_real_escape_string($conn, $_GET["IS_PRIMARY"]);
        $card_method = mysqli_real_escape_string($conn, $_GET["CARD_METHOD"]);
    }
    
    if($card_method=="upi"){
        $user_bank_name = "none";
        $user_bank_ifsc_code = "none";
    }

    if (
        $user_id != "" &&
        $user_actual_name != "" &&
        $user_bank_name != "" &&
        $user_bank_account != "" &&
        $user_bank_ifsc_code != "" &&
        $is_primary != "" &&
        $card_method != ""
    ) {
        
        $select_sql = "SELECT bank_account FROM allbankcards WHERE bank_account='{$user_bank_account}' ";
        $select_query = mysqli_query($conn, $select_sql);

        if (mysqli_num_rows($select_query) > 0) {
            $resArr["status_code"] = "already_exist";
        } else {
            $select_sql = "SELECT * FROM allbankcards WHERE user_id='{$user_id}' ";
            $select_query = mysqli_query($conn, $select_sql);
            $data_count = mysqli_num_rows($select_query);
 
            if ($data_count >= $max_cards_limit) {
                $resArr["status_code"] = "limit_reached";
            } else {
                    if ($is_primary == "true") {
                        $update_sql = "UPDATE allbankcards SET bank_card_primary = 'false' WHERE user_id = '{$user_id}'";
                        $update_query = mysqli_query($conn, $update_sql);
                    }

                    $insert_sql = $conn->prepare(
                        "INSERT INTO allbankcards(uniq_id,user_id,beneficiary_name,bank_name,bank_account,bank_ifsc_code,bank_card_primary,date_time) VALUES(?,?,?,?,?,?,?,?)"
                    );
                    $insert_sql->bind_param(
                        "ssssssss",
                        $new_uniq_id,
                        $user_id,
                        $user_actual_name,
                        $user_bank_name,
                        $user_bank_account,
                        $user_bank_ifsc_code,
                        $is_primary,
                        $curr_date_time
                    );
                    $insert_sql->execute();

                    if ($insert_sql->error == "") {
                        $resArr["status_code"] = "success";
                    } else {
                        $resArr["status_code"] = "failed";
                    }
                }
        }
    } else {
        $resArr["status_code"] = "invalid_params";
    }

    mysqli_close($conn);
    echo json_encode($resArr);
}
?>
