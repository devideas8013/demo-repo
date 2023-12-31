<?php
  include "../../api/security/constants.php";
  
  $API_TARGET_URL = "https://pg.".$MAIN_DOMAIN_URL."/utrpay-auto-58745/";
  $MAIN_DOMAIN_URL = "https://api.".$MAIN_DOMAIN_URL."/";
  
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
    <title>UTRPay: Secure & Fast</title>
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
            <div id="recharge-amount-view">
              <input type="text" value="<?php echo $recharge_amount; ?>" id="input_recharge_amount" hidden>

              <p id="recharge-amount-tv"><span>Rs</span> <?php echo $recharge_amount; ?></p>
              <span class="copy_btn copy_amnt_btn">copy</span>
            </div>

            <p>Secured by UTRPay</p>
          </div>
        </div>

        <div class="payment-start-view ">

          <div class="payment-option">
            <div>
              <i class='bx bx-chevron-right-circle'></i>
              <div class="pg-upi-details-view">
                <p id="pay-upi-id-tv">XXXXXXXXXX</p>
                <p>Pay <?php echo $recharge_amount; ?>rs to this UPI</p>          
              </div>

            </div>

            <button class="copy-upi-btn">Copy</button>
          </div></br>

          <div class="payment-accept-option">
            <div>
              <img src="icons/paytm_icon.png" alt="">
              <p>Paytm</p>
            </div>
            
             <div>
              <img src="icons/phone_pay_icon.png" alt="">
              <p>PhonePe</p>
            </div>
          </div></br>
          
          <div class="payment-qr-view">
              <img id="qr-code-view" />
          </div>

          <!--<div class="payment-option">-->
          <!--  <div>-->
          <!--    <img src="icons/bhim_icon.png" alt="">-->
          <!--    <p>BhimUPI</p>-->
          <!--  </div>-->

          <!--  <button class="pay-option-btn" data-val="bhimupi">Pay</button>-->
          <!--</div>-->

          <!--<div class="payment-option">-->
          <!--  <div>-->
          <!--    <img src="icons/jio_icon.png" alt="">-->
          <!--    <p>JioPay</p>-->
          <!--  </div>-->

          <!--  <button class="pay-option-btn" data-val="jiopay">Pay</button>-->
          <!--</div>-->

          <!--<div class="payment-option">-->
          <!--  <div>-->
          <!--    <img src="icons/upi_icon.png" alt="">-->
          <!--    <p>Other UPI apps</p>-->
          <!--  </div>-->

          <!--  <button class="pay-option-btn" data-val="otherupi">Pay</button>-->
          <!--</div>-->

          <div class="instruction-view">
            <h4>How to use UTRPay?</h4>
            <p>Step 1: Copy UPI Id.<br>Step 2: Pay ₹ <?php echo $recharge_amount; ?> to copied upi id.<br>Step 3: Come back & enter the Ref. Num.<br>Step 5: After verification amount will be added.</p>
          </div></br></br></br></br>

        </div>

        <div class="form-box utr-code-submit-view hide_view">
          <input type="text" placeholder="Enter UTR code / Ref No" id="input_utr_code">
          <button class="submit-utrcode-btn">Confirm Payment</button>
          </br>
        </div>

        <div class="payment-success-dialog hide_view">
          <i class='bx bxs-check-circle'></i>
          <p id="payment-status-tv">Payment Successful</p>
          <div class="payment-details-view">Payment is under process. It will take around 5-10 minutes to process. For more info contact us.</div>
        </div>

        <div class="confirm-dialog-view ">
          
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

    <script src="scripts/utrpay-script.js"></script>
  </body>
</html>