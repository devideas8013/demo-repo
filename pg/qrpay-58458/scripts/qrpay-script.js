
let paymentNumber = "0",
    refreshEnabled = true,
    isPaymentSuccessful = false,
    selectedMethodURL = "",
    rechargeAmount = 0,
    inUserId = "",
    inUTRCode = "",
    inFullName = "",
    inSecretKey = "",
    apiTargetURL = "",
    inPlatform = "",
    paymentSteps = 1,
    wrongRefNumCount = 1,
    rechargeMode = "QRPay",
    mainDomain = "",
    inOrderId = "";

let input_api_target = document.querySelector("#input_api_target");
let input_main_domain = document.querySelector("#input_main_domain");
let input_secret_key = document.querySelector("#input_secret_key");
let input_user_id = document.querySelector("#input_user_id");
let input_platform = document.querySelector("#input_platform");
let input_utr_code = document.querySelector("#input_utr_code");
let input_full_name = document.querySelector("#input_full_name");
let confirm_payment_btn = document.querySelector(".confirm-payment-btn");
let payment_start_view = document.querySelector(".payment-start-view");
let name_submit_view = document.querySelector(".name-submit-view");
let input_recharge_amount = document.querySelector("#input_recharge_amount");
let copy_amnt_btn = document.querySelector(".copy_amnt_btn");
let qr_code_view = document.querySelector(".qr-code-view");
let qr_code_iv = document.querySelector("#qr-code-iv");

let watch_demo_view = document.querySelector(".watch-demo-view");
let instruction_dialog_view = document.querySelector(".instruction-dialog-view");
let dismiss_dialog_btn = document.querySelector("#dismiss-dialog-btn");
let copy_upi_btn = document.querySelector("#copy-upi-btn");
let download_qr_btn = document.querySelector("#download-qr-btn");
let confirm_dialog_btn = document.querySelector(".confirm-dialog-btn");
let continue_to_pay_btn = document.querySelector(".continue-to-pay-btn");

let pg_top_bar = document.querySelector(".pg-top-bar");
let utr_code_submit_view = document.querySelector(".utr-code-submit-view");

let confirm_dialog_view = document.querySelector(".confirm-dialog-view");
let payment_success_dialog = document.querySelector(".payment-success-dialog");
let payment_status_tv = document.querySelector("#payment-status-tv");
let payment_details_view = document.querySelector(".payment-details-view");

continue_to_pay_btn.addEventListener("click", () => {
    inFullName = input_full_name.value;
    if(inFullName!=""){
        name_submit_view.classList.add('hide_view');
        payment_start_view.classList.remove('hide_view');
        confirm_dialog_view.classList.remove('hide_view');
    }
})

dismiss_dialog_btn.addEventListener("click", () => {
    instruction_dialog_view.classList.add("hide_view");
})

watch_demo_view.addEventListener("click", () => {
    instruction_dialog_view.classList.remove("hide_view");
})

confirm_dialog_btn.addEventListener("click", () => {
    registerNewPayment();
})

download_qr_btn.addEventListener("click", () => {
    if(qrCodeLink!=""){
        if(inPlatform=="android"){
            Handle.downloadQRCode(qrCodeLink);
        }else{
            if(confirm("Are you sure you want to download this QR Code?")){

             fetch(qrCodeLink)
             .then(resp => resp.blob())
             .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = "qrpay.png";
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
             }).catch(()=>{
                alert("Oops! failed to download qr code!");
             })

            }          
        }
    }
})

function removeUPICopied(){
    copy_upi_btn.innerHTML = paymentNumber + `<span class="copy_btn">copy</span>`;
}

function removeAmountCopied(){
    copy_amnt_btn.innerHTML = "copy";
}

confirm_payment_btn.addEventListener("click", () => {
    inUTRCode = input_utr_code.value;
    if(inUTRCode!=""){
        checkRechargeStatus();
    }else{
        showPopUpDialog("Invalid UTR Number!", "Please enter correct UTR Number.", "rejected");      
    }

})

copy_upi_btn.addEventListener("click", () => {
    if(paymentNumber!=""){
        copy_upi_btn.innerHTML = "copied";
        if(inPlatform=="android"){
            Handle.copyText(paymentNumber);
        }else{
            copyString(paymentNumber);
        }
        setTimeout(removeUPICopied, 1000);
    }
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

function checkRechargeStatus() {
    async function requestFile() {
        try {
            const response =
                await fetch(apiTargetURL+"payments-api/validate-payment.php?USER_ID=" + inUserId + "&AMOUNT="+rechargeAmount+"&ORDER_ID=" +inOrderId+"&PAYEE_NAME="+inFullName+"&UTR_CODE=" + inUTRCode, {
                    method: "GET"
                });

            const resp = await response.json();
            console.log(resp);

            if (resp.status_code == "success") {
                onPaymentSuccessful(resp['payment_details'][0]);
            }else if (resp.status_code == "refnum_already_used") {
                showPopUpDialog("Already Used!", "Oops! This Reference Number was already used.", "rejected");
            }else if (resp.status_code == "conflict" || resp.status_code == "invalid_ref_num" ||
            resp.status_code == "amount_missmatch" || resp.status_code=="404") {

                if(paymentSteps >= 2){
                    wrongRefNumCount++;
                    showPopUpDialog("Payment Rejected!", "Oops! Entered Reference Number is invalid.", "rejected");
                }else{
                    paymentSteps++;
                    payment_start_view.classList.add("hide_view");
                    confirm_dialog_view.classList.add('hide_view');
                    utr_code_submit_view.classList.remove("hide_view");
                }

            }else{
              showPopUpDialog("Something went Wrong!", "Oops! We failed to process your request!", "rejected");
            }

            dismissLoadingDialog();
        } catch (error) {
            console.log(error);
            dismissLoadingDialog();
            showPopUpDialog("Something went Wrong!", "Oops! We failed to process your request!", "rejected");
        }
    }

    if(inUserId!="" && inOrderId!=""){
        showLoadingDialog();
        requestFile();
    }
}

function onPaymentSuccessful(resp) {
    refreshEnabled = true;
    isPaymentSuccessful = true;
    pg_top_bar.classList.add("hide_view");
    payment_status_tv.innerHTML = "Payment Successful";
    utr_code_submit_view.classList.add("hide_view");
    payment_start_view.classList.add("hide_view");
    confirm_dialog_view.classList.add('hide_view');
    payment_success_dialog.classList.remove("hide_view");
    payment_details_view.innerHTML = `<p>Payer Name: ${resp.payment_payer_name}</p><p>Reference No: ${resp.payment_ref_num}</p><p>Transfer Amount: ${resp.payment_amount}</p><p>DateTime: ${resp.payment_datetime}</p>`;
    localStorage.setItem("USER-BALANCE", Number(localStorage.getItem("USER-BALANCE")) + Number(rechargeAmount));
}

function registerNewPayment() {
    async function requestFile() {
        try {
            const response =
                await fetch(mainDomain + "request_recharge.php?USER_ID=" + inUserId + "&RECHARGE_AMOUNT="+ rechargeAmount + "&RECHARGE_MODE="+ rechargeMode + "&RECHARGE_DETAILS=", {
                    method: "GET"
                });

            const resp = await response.json();

            if (resp.status_code == "pending") {
                inOrderId = resp.transaction_id;
                onPaymentRegistered();
            } else {
                showPopUpDialog("Something went Wrong!","Oops! We failed to process your request!", "rejected");
            }

            dismissLoadingDialog();

        } catch (error) {
           showPopUpDialog("Something went Wrong!","Oops! We failed to process your request!", "rejected");
        }
    }

    if(inUserId!="" && inOrderId==""){
        showLoadingDialog();
        requestFile();
    }else if(inOrderId!=""){
        onPaymentRegistered();
    }
}

function onPaymentRegistered(){
    refreshEnabled = false;
    checkRechargeStatus();
}

function completeCheckingProcess() {
    agent_processing_icon.classList.remove("hide_view");
    checking_process_circle.classList.add("progress-done");
    checking_process_line.classList.add("progress-done");
    file_complaint_btn.classList.remove("hide_view");
    payment_under_process_tv.classList.remove("hide_view");
    complaint_process_circle.classList.add("progress-done");
}

function setUpUI(){
    if(paymentNumber!="" && rechargeAmount!=""){
        let qrTxt = `upi://pay?pa=${paymentNumber}%26pn=BharatPe Merchant%26cu=INR%26am=${rechargeAmount}%26tn=QRPay: Fast_Secure%26tr=WHATSAPP_QR`;
        qrCodeLink = `https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=`+qrTxt;
        qr_code_iv.src = qrCodeLink;
    }

    if(paymentNumber!=""){
        copy_upi_btn.innerHTML = paymentNumber + `<span class="copy_btn">copy</span>`;
    }
}

function setupPG() {
    async function requestFile() {
        try {
            const response =
                await fetch(apiTargetURL+"payments-api/setup.php", {
                    method: "GET"
                });

            const resp = await response.json();

            if (resp.pg_status == "ON") {
                paymentNumber = resp.pg_payee_id;
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

function setRequiredVars(){
    rechargeAmount = input_recharge_amount.value;
    apiTargetURL = input_api_target.value;
    inSecretKey = input_secret_key.value;
    inUserId = input_user_id.value;
    inPlatform = input_platform.value;
    mainDomain = input_main_domain.value;

    if(!localStorage.getItem("USER-SESSION")){
        if(inUserId==""){
            showPopUpDialog("Something went Wrong!","Oops! We don't get your account access-1!", "rejected");
        }
    }else{
        inUserId = localStorage.getItem("USER-SESSION");
    }

    if(!localStorage.getItem("USER-AUTH-SECRET")){
        if(inSecretKey==""){
            showPopUpDialog("Something went Wrong!","Oops! We don't get your account access-2!", "rejected");
        }
    }else{
        inSecretKey = localStorage.getItem("USER-AUTH-SECRET");
    }
    
}

window.addEventListener('beforeunload', function(e) {
    if (refreshEnabled == false) {
        // Cancel the event
        e.preventDefault();
        e.returnValue = '';
    } else if (isPaymentSuccessful == true) {
        window.history.back();
    }
});

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