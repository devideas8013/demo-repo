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
    $bankcard_id = "";

    if (
        isset($_POST["USER_ID"]) &&
        isset($_POST["CARD_ID"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_POST["USER_ID"]);
        $bankcard_id = mysqli_real_escape_string($conn, $_POST["CARD_ID"]);
    }

    if (
        isset($_GET["USER_ID"]) &&
        isset($_GET["CARD_ID"])
    ) {
        $user_id = mysqli_real_escape_string($conn, $_GET["USER_ID"]);
        $bankcard_id = mysqli_real_escape_string($conn, $_GET["CARD_ID"]);
    }


    if (
        $user_id != "" &&
        $bankcard_id != ""
    ) {
        $select_sql = "SELECT bank_account FROM allbankcards WHERE user_id='{$user_id}' AND uniq_id='{$bankcard_id}' ";
        $select_query = mysqli_query($conn, $select_sql);

        if (mysqli_num_rows($select_query) > 0) {
            
            $update_sql = "UPDATE allbankcards SET bank_card_primary = 'false' WHERE user_id = '{$user_id}'";
            $update_query = mysqli_query($conn, $update_sql);
            
            $update_primary_sql = "UPDATE allbankcards SET bank_card_primary = 'true' WHERE user_id = '{$user_id}' AND uniq_id='{$bankcard_id}' ";
            $update_primary_query = mysqli_query($conn, $update_primary_sql);
            
            if($update_primary_query){
              $resArr["status_code"] = "success";
            }
        } else {
            $resArr["status_code"] = "invalid_card_id";
        }
    } else {
        $resArr["status_code"] = "invalid_params";
    }

    mysqli_close($conn);
    echo json_encode($resArr);
}
?>
