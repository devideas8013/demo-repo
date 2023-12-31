<?php

$SERVER_URL = $_SERVER['SERVER_NAME'];

if($SERVER_URL==""){
    echo "Server URL error";
    return;
}

define("ACCESS_SECURITY","true");
include '../api.'.$SERVER_URL.'/security/constants.php';
include '../api.'.$SERVER_URL.'/security/config.php';
include '../api.'.$SERVER_URL.'/security/auth_secret.php';

$DB_CREATE_URL = 'https://api.'.$SERVER_URL."/security/setup-database.php";

//check subdomain status
$api_url = 'https://api.'.$SERVER_URL."/api_subdomain_test.php";

// Use get_headers() function
$headers = @get_headers($api_url);
   
// Use condition to check the existence of URL
if($headers && strpos( $headers[0], '200')) {
    $IS_SUBDOMAIN_ACTIVE = "true";
}else {
    $IS_SUBDOMAIN_ACTIVE = "false";
}

if (extension_loaded('mysqli')==1) {
    $IS_ND_MSQLI_ON = "true";
}
else {
    $IS_ND_MSQLI_ON = "false";
}

$PHP_VERSION = phpversion();

$NUM_OF_DB_TBL = 0;
if ($is_db_connected=="true") {
  $IS_DB_CONNECTED = "true";
  
  $search_sql = "SHOW TABLES";
  $search_res = mysqli_query($conn,$search_sql);
  $NUM_OF_DB_TBL = mysqli_num_rows($search_res);
}else{
  $IS_DB_CONNECTED = "false";
}

if ($SMS_API_TOKEN!="") {
    $IS_SMS_TOKEN_SET = "true";
}
else {
    $IS_SMS_TOKEN_SET = "false";
}

if ($MESSAGE_TOKEN!="") {
    $IS_MSG_TOKEN_SET = "true";
}
else {
    $IS_MSG_TOKEN_SET = "false";
}

if ($STARTER_TOKEN!="") {
  $authObj = new AuthSecret("STARTER",$STARTER_TOKEN);
  $auth_secret = $authObj -> validateSimpleKey();

  if($auth_secret!="true"){
    $IS_STARTER_TOKEN_VALID = "false";
  }else{
    $IS_STARTER_TOKEN_VALID = "true";
  }
}
else {
  $IS_STARTER_TOKEN_VALID = "false";
}

$ADMIN_PANEL_URL = 'https://api.'.$SERVER_URL."/admin";

if($IS_SUBDOMAIN_ACTIVE=="true"){
  $CRON_JOB_URL = 'https://api.'.$SERVER_URL."/services/send-investment-return.php?accessToken=".$CRON_ACCESS_TOKEN;  
}else{
  $CRON_JOB_URL = "Please setup subdomain first";
}


?>
<!DOCTYPE html>
<html lang="en" translate="no">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<title>Install & Setup</title>
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'poppins', sans-serif;
    }
      
      .resp-w{
          width: 550px;
      }
      
      .w-100{
          width: 100%;
      }
      
      .col-view{
        display: flex;
        justify-content: center;
        flex-direction: column;
      }
      
      .row-view{
        display: flex;
        align-items: center;
        justify-content: center;
      }
      
      .a-center{
          align-items: center;
      }
      
      .sb-view{
         justify-content: space-between !important;
      }
      
      .content-view{
          min-height: 100vh;
          width: 100%;
          overflow-y: scroll;
          background: #1c1c1c;
      }
      
      .action-btn{
          color: #fff;
          font-size: 25px;
          font-weight: 700;
          background: #2ca90a;
          text-align: center;
          border-radius: 3px;
          cursor: pointer;
          padding: 10px 0;
          box-shadow: 0 4px rgba(255,255,255,0.3);
          text-shadow: 0px 1px 1px #30662b;
      }
      
      .cl-white{
          color: #FFFFFF !important;
      }
      
      .cl-red{
          color: #E74C3C !important;
      }
      
      .bg-red{
          background: #E74C3C !important;
      }
      
      .bg-green{
          background: #2ca90a !important;
      }
      
      .mg-t-10{
          margin-top: 10px !important;
      }
      
      .mg-t-20{
          margin-top: 20px !important;
      }
      
      .mg-t-30{
          margin-top: 30px !important;
      }
      
      .ft-sz-13{
          font-size: 13px;
      }
      
      .ft-sz-18{
          font-size: 18px;
      }
      
      .ft-sz-25{
          font-size: 25px;
      }
      
      .ft-wgt-b{
          font-weight: bold;
      }
      
      .pd-5-10{
          padding: 5px 10px;
      }
      
      .view-disable{
          filter: grayscale(100%) !important;
      }
      
      @media (max-width: 550px) {
        .resp-w{
          width: 90% !important;
        }
      }
    </style>
</head>
<body>

<!--<input type="text" id="input_api_target" value="<?php echo $API_TARGET_URL; ?>" hidden>-->

<div class="content-view col-view a-center">
    </br></br>
    <div class="col-view a-center resp-w">
      <div class="row-view sb-view mg-t-10 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">Website URL:</p>
        <p class="cl-white ft-sz-18 ft-wgt-b"><?php echo $SERVER_URL; ?></p>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">App Name:</p>
        <p class="cl-white ft-sz-18 ft-wgt-b"><?php echo $APP_NAME; ?></p>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
          <div class="col-view">
              <p class="cl-white ft-sz-18 ft-wgt-b">Subdomain:</p>
              <p class="cl-white ft-sz-13"><?php echo 'https://api.'.$SERVER_URL; ?></p>
          </div>
        
        <?php if($IS_SUBDOMAIN_ACTIVE=="true"){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10">Active</p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10">Not Active</p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">ND_MYSQLI:</p>
        
        <?php if($IS_ND_MSQLI_ON=="true"){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b mg-t-10 pd-5-10">Active</p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b mg-t-10 pd-5-10">Not Active</p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">PHP Version:</p>
        <?php if($PHP_VERSION > 8){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10"><?php echo $PHP_VERSION; ?></p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10"><?php echo $PHP_VERSION; ?></p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">Databse Connected:</p>
        <?php if($IS_DB_CONNECTED=="true"){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10">Connected</p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10">Not Connected</p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">No. DB Table:</p>
        <?php if($NUM_OF_DB_TBL > 3){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10"><?php echo $NUM_OF_DB_TBL; ?></p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10"><?php echo $NUM_OF_DB_TBL; ?></p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">SMS Token</p>
        <?php if($IS_SMS_TOKEN_SET=="true"){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10">Available</p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10">Not Available</p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">MSG Token</p>
        <?php if($IS_MSG_TOKEN_SET=="true"){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10">Available</p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10">Not Available</p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <p class="cl-white ft-sz-18 ft-wgt-b">Starter Token</p>
        <?php if($IS_STARTER_TOKEN_VALID=="true"){ ?>
        <p class="cl-white bg-green ft-sz-18 ft-wgt-b pd-5-10">Valid</p>
        <?php }else{ ?>
        <p class="cl-white bg-red ft-sz-18 ft-wgt-b pd-5-10">Not Valid</p>
        <?php } ?>
      </div>
      
      <div class="row-view sb-view mg-t-20 w-100">
        <div class="col-view">
         <p class="cl-white ft-sz-18 ft-wgt-b">Admin Panel</p>
         <p class="cl-white ft-sz-13"><?php echo $ADMIN_PANEL_URL; ?></p>
        </div>
        <p class="cl-white ft-sz-18 ft-wgt-b"></p>
     </div>
     
     <div class="row-view sb-view mg-t-20 w-100">
        <div class="col-view">
         <p class="cl-white ft-sz-18 ft-wgt-b">Cron Job</p>
         <p class="cl-white ft-sz-13"><?php echo $CRON_JOB_URL; ?></p>
        </div>
        <p class="cl-white ft-sz-18 ft-wgt-b"></p>
     </div>
      
    </div>
    
    
    <br>
    <?php if($IS_SUBDOMAIN_ACTIVE=="true"){ ?>
      <div class="action-btn mg-t-30 bg-red resp-w" onclick="setupFiles('takedown.php')">Take Down</div>
    <?php }else{ ?>
      <div class="action-btn mg-t-30 view-disable resp-w">Setup Files</div>
    <?php } ?>
    
    <?php if($IS_DB_CONNECTED=="true" && $NUM_OF_DB_TBL == 0 && $IS_STARTER_TOKEN_VALID=="true" && $IS_SUBDOMAIN_ACTIVE=="true"){ ?>
      <div class="action-btn mg-t-30 resp-w" onclick="createDatabases('<?php echo $DB_CREATE_URL; ?>')">Create Tables</div>
    <?php }else{ ?>
      <div class="action-btn mg-t-30 view-disable resp-w">Create Tables</div>
    <?php } ?>
    </br></br>
    
</div>



<script>
    function createDatabases(url){
        if(confirm("Are you sure you want to create tables?")){
            window.open(url);
        }
    }
    
    function setupFiles(url){
        window.location.href = url;
    }
</script>

</body>
</html>