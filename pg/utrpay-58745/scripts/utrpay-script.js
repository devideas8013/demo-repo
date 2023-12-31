let paymentUPI = "",
    selectedMethodURL = "",
    rechargeAmount = 0,
    inUserId = "",
    inSecretKey = "",
    inFullName = "",
    inUTRCode = "",
    apiTargetURL = "",
    inPlatform = "",
    paymentSteps = 1;
    rechargeMode = "UTRPay",
    mainDomain = "";

let input_api_target = document.querySelector("#input_api_target");
let input_main_domain = document.querySelector("#input_main_domain");
let input_secret_key = document.querySelector("#input_secret_key");
let input_user_id = document.querySelector("#input_user_id");
let input_platform = document.querySelector("#input_platform");
let input_utr_code = document.querySelector("#input_utr_code");
let payment_start_view = document.querySelector(".payment-start-view");
let input_recharge_amount = document.querySelector("#input_recharge_amount");
let copy_amnt_btn = document.querySelector(".copy_amnt_btn");
let pay_upi_id_tv = document.querySelector("#pay-upi-id-tv");
let qr_code_view = document.querySelector("#qr-code-view");

let name_submit_view = document.querySelector(".name-submit-view");
let submit_utrcode_btn = document.querySelector(".submit-utrcode-btn");

let pg_top_bar = document.querySelector(".pg-top-bar");
let utr_code_submit_view = document.querySelector(".utr-code-submit-view");

let payment_success_dialog = document.querySelector(".payment-success-dialog");
let payment_status_tv = document.querySelector("#payment-status-tv");
let payment_details_view = document.querySelector(".payment-details-view");

let copy_upi_btn = document.querySelector(".copy-upi-btn");
let confirm_dialog_view = document.querySelector(".confirm-dialog-view");
let confirm_dialog_btn = document.querySelector(".confirm-dialog-btn");

let pay_option_btn = document.querySelectorAll(".pay-option-btn");

for (let i = 0; i < pay_option_btn.length; i++) {
    pay_option_btn[i].addEventListener("click", ()=>{
        openUPIApps(pay_option_btn[i].getAttribute('data-val'));
    })
}

function openUPIApps(app){
    if(rechargeAmount!="" && rechargeAmount!=0 && paymentUPI!=""){
        runCopyUPI();
        
        if(app=="paytm"){
            
          if(inPlatform=="android"){
            Handle.callUPIApps("paytm");             
          }else{
            location.href = "paytmmp://upi";  
          }
          
        }else if(app=="phonepe"){
            
          if(inPlatform=="android"){
            Handle.callUPIApps("phonepe");             
          }else{
            location.href = "phonepe://upi";  
          }

        }else if(app=="bhimupi"){
            
          if(inPlatform=="android"){
            Handle.callUPIApps("bhimupi");             
          }else{
            location.href = "gpay://";  
          }
          
        }else{
            
          if(inPlatform=="android"){
            Handle.callUPIApps("upiapps");             
          }else{
            location.href = "upi://";  
          }
          
        }
    }
}

confirm_dialog_btn.addEventListener("click", () => {
    confirm_dialog_view.classList.add("hide_view");
    payment_start_view.classList.add("hide_view");
    utr_code_submit_view.classList.remove("hide_view");
})

submit_utrcode_btn.addEventListener("click", () => {
    registerNewPayment();
})

function stringToFloat(string){
    return parseInt(string).toFixed(2);
}

function removeUPICopied(){
    copy_upi_btn.innerHTML = "Copy";
}

function removeAmountCopied(){
    copy_amnt_btn.innerHTML = "copy";
}

copy_upi_btn.addEventListener("click", () => {
  runCopyUPI();
})

copy_amnt_btn.addEventListener("click", () => {
    if(rechargeAmount!=""){
        copy_amnt_btn.innerHTML = "copied";
        if(inPlatform=="android"){
            Handle.copyText(rechargeAmount);
        }else{
            copyString(rechargeAmount);
        }
        setTimeout(removeAmountCopied, 1000);
    }
})

function runCopyUPI(){
    if(paymentUPI!=""){
        copy_upi_btn.innerHTML = "Copied";
        if(inPlatform=="android"){
            Handle.copyText(paymentUPI);
        }else{
            copyString(paymentUPI);
        }
        setTimeout(removeUPICopied, 1000);
    }    
}

function onPaymentSuccessful() {
    pg_top_bar.classList.add("hide_view");
    utr_code_submit_view.classList.add("hide_view");
    payment_start_view.classList.add("hide_view");
    payment_success_dialog.classList.remove("hide_view");
}

function registerNewPayment() {
    async function requestFile() {
        try {
            const rechargeDetails = inUTRCode+","+paymentUPI;
            const response =
                await fetch(mainDomain + "request-recharge.php?USER_ID=" + inUserId + "&RECHARGE_AMOUNT="+ rechargeAmount + "&RECHARGE_MODE="+ rechargeMode + "&RECHARGE_DETAILS="+rechargeDetails, {
                    method: "GET"
                });

            const resp = await response.json();

            if (resp.status_code == "pending") {
                onPaymentSuccessful();
            } else if(resp.status_code == "utr_exit"){
                showPopUpDialog("Invalid UTR Code!","Oops! Entered UTR Code is already exist!", "rejected");
            }else{
                showPopUpDialog("Something went Wrong!","Oops! We failed to process your request!", "rejected");
            }

            dismissLoadingDialog();

        } catch (error) {
            console.log(error);
           showPopUpDialog("Something went Wrong!","Oops! We failed to process your request!", "rejected");
        }
    }

    inUTRCode = input_utr_code.value;

    if(inUserId!="" && inUTRCode!="" && inUTRCode.length >= 11){
        showLoadingDialog();
        requestFile();
    }else{
        showPopUpDialog("Invalid UTR Code!","Oops! Enter UTR code is Invalid!", "rejected");       
    }
}

function setupPG() {
    async function requestFile() {
        try {
            const response =
                await fetch(apiTargetURL + "payments-api/setup.php", {
                    method: "GET"
                });

            const resp = await response.json();

            if (resp.pg_status == "ON") {
                paymentUPI = resp.pg_payee_id;
                pay_upi_id_tv.innerHTML = paymentUPI;
                setUpUI();
            } else {
                showPopUpDialog("UTRPay Not Available!", "Oops! UTRPay is under maintenance. Please try after sometimes.", "rejected-back");
            }

            dismissLoadingDialog();

        } catch (error) {
            dismissLoadingDialog();
            showPopUpDialog("Something went Wrong!","Oops! We failed to process your request!", "rejected");
        }
    }

    setRequiredVars();

    if (rechargeAmount > 0) {
        showLoadingDialog();
        requestFile();
    } else {
        alert("Oops! Something went wrong! Please try again!")
    }
}

function setUpUI(){
    if(paymentUPI!="" && rechargeAmount!=""){
        let qrTxt = `upi://pay?pa=${paymentUPI}%26pn=Merchant%26cu=INR%26am=${rechargeAmount}%26tn=QRPay: Fast_Secure%26tr=WHATSAPP_QR`;
        qrCodeLink = `https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=`+qrTxt;
        qr_code_view.src = qrCodeLink;
    }else{
        console.log("Payment Error! UPI id not fetched!");
    }
}

function setRequiredVars(){
    rechargeAmount = input_recharge_amount.value;
    apiTargetURL = input_api_target.value;
    inSecretKey = input_secret_key.value;
    inUserId = input_user_id.value;
    inPlatform = input_platform.value;
    mainDomain = input_main_domain.value;

    if(inUserId==""){
        showPopUpDialog("Something went Wrong!","Oops! We don't get your account access-1!", "rejected");
    }
}

async function copyString(string) {
  const el = document.createElement('textarea');
  el.value = string;
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  const selected =
    document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
  if (selected) {
    document.getSelection().removeAllRanges();
    document.getSelection().addRange(selected);
  }
}

setupPG();