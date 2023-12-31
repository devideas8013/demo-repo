<?php
  $SERVER_URL = $_SERVER['SERVER_NAME'];

  if($SERVER_URL==""){
    echo "Server URL error";
    return;
  }

  $API_TARGET_URL = "https://".$SERVER_URL."/pg/qrpay-58458/";
  $MAIN_DOMAIN_URL = "https://api.".$SERVER_URL."/";
  
  $recharge_amount = "0";
  if(isset($_GET['amount'])){
    $recharge_amount = $_GET['amount'];
  }else{
    echo "Someting went wrong! Try again!";
    return;
  }
  
  $user_id = "";
  if(isset($_GET['user_id'])){
    $user_id = $_GET['user_id'];
  }

  $secret_key = "";
  if(isset($_GET['secret'])){
    $secret_key = $_GET['secret'];
  }
  
  $access_platform = "";
  if(isset($_GET['platform'])){
    $access_platform = $_GET['platform'];
  }
  
  if($recharge_amount <= 0){
    echo "Someting went wrong! Try again!";
    return;
  }
  
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='css/style.css' rel='stylesheet'>
    <title>QRPay: Secure & Fast</title>
  </head>
  <body>
    <input type="text" id="input_api_target" value="<?php echo $API_TARGET_URL; ?>" hidden>
    <input type="text" id="input_main_domain" value="<?php echo $MAIN_DOMAIN_URL; ?>" hidden>
    <input type="text" id="input_secret_key" value="<?php echo $secret_key; ?>" hidden>
    <input type="text" id="input_user_id" value="<?php echo $user_id; ?>" hidden>
    <input type="text" id="input_platform" value="<?php echo $access_platform; ?>" hidden>

    <div class="main-view">
      <div class="pg-view">

        <div class="pg-top-bar">
          <div class="topbar-nav">
            <i class='bx bx-arrow-back' onclick="history.back()"></i>
            <p></p>
          </div>
          <div class="pg-details-view">
            <p>Recharge Amount:</p>
            <div id="recharge-amount-view">
              <input type="text" value="<?php echo $recharge_amount; ?>" id="input_recharge_amount" hidden>

              <p id="recharge-amount-tv"><span>Rs</span> <?php echo $recharge_amount; ?></p>
              <span class="copy_btn copy_amnt_btn">copy</span>
            </div>

            <div class="watch-demo-view">
              <i class='bx bx-play-circle' ></i>
              <p>Example</p>
            </div>
          </div>
        </div>

        <div class="payment-start-view hide_view">
          <p>Scan & Pay</p>

          <div class="scan-view">
            <img id="qr-code-iv">

            <div class="control-options">
              <button id="copy-upi-btn">UPI ID</button>
              <button id="download-qr-btn">Download</button>
            </div>
          </div>

          <div class="instruction-view">
            <h4>How to use QRPay?</h4>
            <p>Step 1: Scan the QR Code.<br>Step 2: Pay ₹ <?php echo $recharge_amount; ?> to scanned QR Code.<br>Step 3: Copy Reference Num Or UTR Code.<br>Step 4: Come back & enter the code.<br>Step 5: After auto verification amount will be added.</p>
            <br><br><br><br><br>
          </div>

        </div>

        <div class="form-box name-submit-view ">
          <input type="text" placeholder="Enter Full Name" id="input_full_name">
          <div class="info-box">
            <p><i class='bx bx-info-circle' ></i>&nbsp;&nbsp;Make sure to enter the name as per bank account.</p>
          </div>
          <button class="continue-to-pay-btn">Continue to pay</button>
          </br>
        </div>

        <div class="form-box utr-code-submit-view hide_view">
          <input type="text" placeholder="Enter UTR code / Ref No" id="input_utr_code">
          <button class="confirm-payment-btn">Confirm Payment</button>
          </br>
        </div>

        <div class="payment-success-dialog hide_view">
          <i class='bx bxs-check-circle'></i>
          <p id="payment-status-tv">Payment Successful</p>
          <div class="payment-details-view"></div>
        </div>

        <div class="instruction-dialog-view hide_view">
          <button id="dismiss-dialog-btn">x</button>
          <img src="icons/pay-gif.gif" alt="">
        </div>

        <div class="confirm-dialog-view hide_view">
          
          <p>If you have paid then Click 'I have paid'</p>
          <div class="action-btn confirm-dialog-btn">
            <p>I have Paid</p>
            <i class='bx bx-check-double' ></i>
          </div>

        </div>

        <?php include 'dialogs/loading-dialog.php'; ?>
        <?php include 'dialogs/popup-message-dialog.php'; ?>
      </div>
    </div>

    <script src="scripts/qrpay-script.js"></script>
  </body>
</html>