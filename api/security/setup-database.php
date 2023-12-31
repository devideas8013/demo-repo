<?php
define("ACCESS_SECURITY","true");
include 'config.php';
include 'constants.php';
include 'auth_secret.php';

$authObj = new AuthSecret("STARTER",$STARTER_TOKEN);
$auth_secret = $authObj -> validateSimpleKey();

if($auth_secret!="true"){
    echo 'starter token is wrong...';
    return;  
}

date_default_timezone_set("Asia/Kolkata");
$curr_date = date("d-m-Y");
$curr_time = date("h:i:s a");

// ======================================>>>
$num_tables = 0;
if ($is_db_connected=="true") {
    
$search_sql = "SHOW TABLES";
$search_res = mysqli_query($conn,$search_sql);
$num_tables = mysqli_num_rows($search_res);

if($num_tables > 0){
  echo "Database tables already setup!";
  return;
}

}else{
  echo "DB Connection not setup!";
  return;
}

// sql usersdata table
$sql = "CREATE TABLE IF NOT EXISTS usersdata (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
uniq_id VARCHAR(200) NOT NULL,
user_auth_secret VARCHAR(250) NOT NULL,
user_mobile_num VARCHAR(30) NOT NULL,
user_email_id VARCHAR(50) NOT NULL,
user_full_name VARCHAR(100) NOT NULL,
user_password VARCHAR(250) NOT NULL,
user_balance VARCHAR(50) NOT NULL,
user_withdrawl_balance VARCHAR(50) NOT NULL,
user_total_coins VARCHAR(50) NOT NULL,
user_refered_by VARCHAR(50) NOT NULL,
user_last_active_date VARCHAR(50) NOT NULL,
user_last_active_time VARCHAR(50) NOT NULL,
user_last_otp VARCHAR(35) NOT NULL,
account_level VARCHAR(20) NOT NULL,
user_status VARCHAR(30) NOT NULL,
user_login_ip VARCHAR(30) NOT NULL,
user_joined_ip VARCHAR(30) NOT NULL,
user_joined VARCHAR(50) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
} else {
  echo "Error creating table usersdata: " . $conn->error;
  return;
}


// insert new data
$unique_id = "111111";
$user_mobile_num = "1234567890";

$user_new_pass = "123456";
$user_password = mysqli_real_escape_string($conn,password_hash($user_new_pass,PASSWORD_BCRYPT));
 
$unknown_data = "unknown";
$empty_data = "";
$zero_data = "0";
$none_data = "none";
$account_level = "1";
$app_version = "3";
$false_value = "false";
$true_value = "true";
$default_user_balance = "500";
$curr_date_time = $curr_date . " " . $curr_time;
 
$insert_user_sql = $conn->prepare("INSERT INTO usersdata(uniq_id,user_auth_secret,user_mobile_num,user_email_id,user_full_name,user_password,user_balance,user_withdrawl_balance, user_total_coins,user_refered_by,user_last_active_date,user_last_active_time,user_last_otp,account_level,user_status,user_login_ip,user_joined_ip,user_joined) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$insert_user_sql->bind_param("ssssssssssssssssss", $unique_id,$empty_data,$user_mobile_num,$unknown_data, $unknown_data, $user_password, $default_user_balance, $zero_data, $zero_data, $empty_data, $curr_date, $curr_time, $empty_data, $account_level, $true_value, $none_data, $none_data, $curr_date_time);
$insert_user_sql->execute();

if ($insert_user_sql->error == "") {
} else {
  echo "Error: inserting data to usersdata:" . $conn->error;
  return;
}


// ======================================>>>

// sql allbankcards table
$sql = "CREATE TABLE IF NOT EXISTS allbankcards (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(200) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    beneficiary_name VARCHAR(50) NOT NULL,
    bank_name VARCHAR(50) NOT NULL,
    bank_account VARCHAR(50) NOT NULL,
    bank_ifsc_code VARCHAR(50) NOT NULL,
    bank_card_primary VARCHAR(30) NOT NULL,
    date_time VARCHAR(50) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table allbankcards: " . $conn->error;
    return;
}


// ======================================>>>

// sql allimages table
$sql = "CREATE TABLE IF NOT EXISTS allimages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    imageid VARCHAR(100) NOT NULL,
    upload_date_time VARCHAR(50) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table allimages: " . $conn->error;
    return;
}


// ======================================>>>

// sql availablerewards table
$sql = "CREATE TABLE IF NOT EXISTS availablerewards (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    reward_id VARCHAR(100) NOT NULL,
    reward_title VARCHAR(50) NOT NULL,
    reward_bonus VARCHAR(50) NOT NULL,
    reward_status VARCHAR(20) NOT NULL,
    reward_created VARCHAR(50) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table availablerewards: " . $conn->error;
    return;
}


// ======================================>>>

// sql investmentlist table
$sql = "CREATE TABLE IF NOT EXISTS investmentlist (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(100) NOT NULL,
    investment_name VARCHAR(100) NOT NULL,
    investment_details VARCHAR(500) NOT NULL,
    investment_hourly_income VARCHAR(50) NOT NULL,
    investment_price VARCHAR(50) NOT NULL,
    investment_total_days VARCHAR(30) NOT NULL,
    investment_image_url LONGTEXT NOT NULL,
    investment_status VARCHAR(20) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table investmentlist: " . $conn->error;
    return;
}


$uniqId = "0G3JNM7GDOY5G00E";
$investmentName = "Seacar Mars PDs2";
$investmentDetails = "This equipment is a novice benefit that can be obtained for free and earns hourly";
$investmentHourlyIncome = "0.50";
$investmentPrice = "100";
$investmentTotalDays = "10";
$investmentImgUrl = "https://blogger.googleusercontent.com/img/a/AVvXsEjKlO8t3FKEqK31HVssBMKPDXPO3h1OyJiUOu1LRBxnmbgcH4JF0QvrmUcVsrdyiYcheN8Iu5uxOVphxHVKjHSC5UWkQ3VjVlnho-YRHzkZXrHWu5Dz3x8omFtjYY3dNC9cGZ6Kr1HVtx9ofLHgAdXvvUGb0bISbVmeUII7x9EL21_rYqbDqSj2hBg4Eek";
$investmentStatuss = "true";
 
$insert_sql = $conn->prepare("INSERT INTO investmentlist(uniq_id,investment_name,investment_details,investment_hourly_income,investment_price,investment_total_days,investment_image_url,investment_status) VALUES(?,?,?,?,?,?,?,?)");
$insert_sql->bind_param("ssssssss", $uniqId,$investmentName,$investmentDetails,$investmentHourlyIncome, $investmentPrice, $investmentTotalDays, $investmentImgUrl, $investmentStatuss);
$insert_sql->execute();

if ($insert_sql->error == "") {
} else {
  echo "Error: inserting data to investmentlist" . $conn->error;
  return;
}


// ======================================>>>

// sql myinvestments table
$sql = "CREATE TABLE IF NOT EXISTS myinvestments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(100) NOT NULL,
    user_id VARCHAR(100) NOT NULL,
    investment_id VARCHAR(100) NOT NULL,
    investment_name VARCHAR(100) NOT NULL,
    investment_price VARCHAR(50) NOT NULL,
    investment_earnings VARCHAR(50) NOT NULL,
    investment_hourly_income VARCHAR(50) NOT NULL,
    investment_total_days VARCHAR(50) NOT NULL,
    investment_img LONGTEXT NOT NULL,
    investment_date VARCHAR(30) NOT NULL,
    investment_time VARCHAR(30) NOT NULL,
    investment_last_update VARCHAR(50) NOT NULL,
    investment_status VARCHAR(20) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table myinvestments: " . $conn->error;
    return;
}


// ======================================>>>

// sql usersrecharge table
$sql = "CREATE TABLE IF NOT EXISTS usersrecharge (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(200) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    recharge_amount VARCHAR(50) NOT NULL,
    recharge_mode VARCHAR(20) NOT NULL,
    recharge_details VARCHAR(200) NOT NULL,
    request_status VARCHAR(20) NOT NULL,
    request_date_time VARCHAR(30) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table usersrecharge: " . $conn->error;
    return;
}


// ======================================>>>

// sql userswithdraw table
$sql = "CREATE TABLE IF NOT EXISTS userswithdraw (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(200) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    withdraw_request VARCHAR(50) NOT NULL,
    withdraw_amount VARCHAR(50) NOT NULL,
    actual_name VARCHAR(50) NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    bank_account VARCHAR(50) NOT NULL,
    bank_ifsc_code VARCHAR(50) NOT NULL,
    user_state VARCHAR(30) NOT NULL,
    request_status VARCHAR(15) NOT NULL,
    extra_message VARCHAR(200) NOT NULL,
    request_date_time VARCHAR(35) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table userswithdraw: " . $conn->error;
    return;
}

// ======================================>>>

// sql userscomplaints table
$sql = "CREATE TABLE IF NOT EXISTS userscomplaints (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(100) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    complain_details VARCHAR(500) NOT NULL,
    complain_status VARCHAR(15) NOT NULL,
    complain_date_time VARCHAR(35) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table userscomplaints: " . $conn->error;
    return;
}


// ======================================>>>

// sql othertransactions table
$sql = "CREATE TABLE IF NOT EXISTS othertransactions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    receive_from VARCHAR(50) NOT NULL,
    type VARCHAR(30) NOT NULL,
    amount VARCHAR(30) NOT NULL,
    extra_msg VARCHAR(100) NOT NULL,
    date_time VARCHAR(50) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error: creating table othertransactions: " . $conn->error;
    return;
}


// ======================================>>>

// sql utrpayrecords table
$sql = "CREATE TABLE IF NOT EXISTS utrpayrecords (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    payment_payer_name VARCHAR(100) NOT NULL,
    payment_amount VARCHAR(50) NOT NULL,
    payment_host VARCHAR(100) NOT NULL,
    payment_ref_num VARCHAR(100) NOT NULL,
    payment_status VARCHAR(30) NOT NULL,
    payment_datetime VARCHAR(50) NOT NULL,
    payment_submitted VARCHAR(50) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error: creating table utrpayrecords: " . $conn->error;
    return;
}


// ======================================>>>

// sql allsliders table
$sql = "CREATE TABLE IF NOT EXISTS allsliders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    slider_img LONGTEXT NOT NULL,
    slider_action LONGTEXT NOT NULL,
    status VARCHAR(15) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table allsliders: " . $conn->error;
    return;
}


// ======================================>>>

// sql allservices table
$sql = "CREATE TABLE IF NOT EXISTS allservices (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    service_value LONGTEXT NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table allservices: " . $conn->error;
    return;
}

$allServicesSql = "INSERT INTO allservices (service_name, service_value)
VALUES 
    (
        'APP_STATUS',
        'ON'
    ),
    (
        'COMISSION_BONUS',
        '1,1,1'
    ),
    (
        'WITHDRAW_TAX',
        '5'
    ),
    (
        'MIN_WITHDRAW',
        '100'
    ),
    (
        'MIN_RECHARGE',
        '100'
    ),
    (
        'RECHARGE_OPTIONS',
        '100,200,500,1000,5000,10000'
    ),
    (
        'UTR_PAY',
        'NOT_SET'
    ),
    (
        'QR_PAY',
        'NOT_SET'
    ),
    (
        'TELEGRAM_URL',
        'https://telegram.com'
    ),
    (
        'SLIDESHOW_BANNER',
        'https://blogger.googleusercontent.com/img/a/AVvXsEgO_EZaZf6G33lhzS8ME6U5XFpSNabz-EC4k0BoPyjkJyYF8pn5sZf-SEU2PvYi0FQW_zRy2ldoqnstKzk7D9iCdtqDVpicHADBo0YVjO6fqfPHlL6ZbtAvintLlW4fg3UxIJzJnuHXvkxqJbQZkxneendnFAPLAEvo3g8x1Cd2A9WV67DREQiEM1s2YDc'
    );";

if ($conn->query($allServicesSql) === TRUE) {
} else {
  echo "Error: inserting data to allservices:" . $allServicesSql . "<br>" . $conn->error;
  return;
}


// ======================================>>>

// sql adminauth table
$sql = "CREATE TABLE IF NOT EXISTS adminauth (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uniq_id VARCHAR(100) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    user_access_list VARCHAR(200) NOT NULL,
    user_password VARCHAR(250) NOT NULL,
    user_category VARCHAR(20) NOT NULL,
    date_time VARCHAR(30) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
    echo "Table adminauth created".'<br>';
} else {
    echo "Error: creating table adminauth: " . $conn->error;
}

$uniqId = "G92TUN2AARC00G3JNM7GDOY5G00EEF";
$userId = "1234567890";
$accountCategory = "admin";
$newPassword = "123456";
$admin_password = mysqli_real_escape_string($conn,password_hash($newPassword,PASSWORD_BCRYPT));
$accountCreated = $curr_date.' '.$curr_time;
$accountAccesList = "access_investments,access_users_data,access_recharge,access_withdraw,access_message,access_help,access_settings,access_admins";
 
$insert_sql = $conn->prepare("INSERT INTO adminauth(uniq_id,user_id,user_password,user_access_list,user_category,date_time) VALUES(?,?,?,?,?,?)");
$insert_sql->bind_param("ssssss", $uniqId,$userId,$admin_password,$accountAccesList,$accountCategory, $accountCreated);
$insert_sql->execute();

if ($insert_sql->error == "") {
} else {
  echo "Error: inserting data to adminauth" . $conn->error;
  return;
}


// sql allnotices table
$sql = "CREATE TABLE IF NOT EXISTS allnotices (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    notice_title VARCHAR(50) NOT NULL,
    notice_details VARCHAR(500) NOT NULL,
    notice_image LONGTEXT NOT NULL,
    notice_timestamp VARCHAR(50) NOT NULL
    )";
    
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating table allnotices: " . $conn->error;
    return;
}


$conn->close();

?>
<script>
    alert('Database Tables Created!');
    window.close();
</script>