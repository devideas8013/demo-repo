import React, { useEffect, useState, useRef } from 'react';
import { Link  } from 'react-router-dom';
import '../../MainStyle.css';
import ToastDialog from '../dialogs/ToastDialog';
import { API_ACCESS_URL,HOME_REDIRECT_URL,redirectTo,getURLParam } from '../modals/Constants';
import { setCookie,getCookie } from '../modals/Cookie';

function ForgetPassword(){
  
    const Ref = useRef(null);
    const [resendOTPTimeLeft, setResendOTPTime] = useState(0);
    const [isInputValCorrect, setInValCorrect] = useState(false);

    const [pageConst, setConstants] = useState({
        pageTitle: "Forget Password",
        inMobileNum: "",
        inPassword: "",
        inConfirmPassword: "",
        inVerificationCode: "",
        isLoadingShow: false,
        toastDialogShow: false,
        toastTimeAvail: 7,
        toastMessage: "",
        isSessionExist: true,
        isOTPSending: false,
        resendOTPTimeLimit: 60,
    });

    const updateLoadingStatus = (data) => {
        setConstants(previousState => {
            return { ...previousState, isLoadingShow: data }
        });
    }

    const updateOTPSendingStatus = (data) =>{
      setConstants(previousState => {
        return { ...previousState, isOTPSending: data }
      });
    }

    const updateToastDialogState = (data,msg) => {
      setConstants(previousState => {
        return { ...previousState, toastDialogShow: data }
      });

      setConstants(previousState => {
        return { ...previousState, toastMessage: msg }
      });
    }

    const isMobileNumValidate = (mobilenum) =>{
      if(mobilenum != "" && mobilenum != undefined && mobilenum.length == 10){
        return true;
      }else{
        return false;
      }
    }

    const isPasswordValidate = (password,confirmPassword) =>{
      if(password!="" && password != undefined && password.length >= 6 && password==confirmPassword){
        return true;
      }else{
        return false;
      }
    }

    const checkForInputVal = (mobilenum,password,confirmPassword,verificationCode) =>{
      if(isMobileNumValidate(mobilenum) && isPasswordValidate(password,confirmPassword) && verificationCode.length > 4){
        setInValCorrect(true);
      }else{
        setInValCorrect(false);
      }
    }

    const onInputValChange = (source,data) =>{
      if(source=="mobile"){
        checkForInputVal(data, pageConst.inPassword, pageConst.inConfirmPassword, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inMobileNum: data }
        });
      }

      if(source=="password"){
        checkForInputVal(pageConst.inMobileNum, data, pageConst.inConfirmPassword, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inPassword: data }
        });
      }

      if(source=="confirmPassword"){
        checkForInputVal(pageConst.inMobileNum, pageConst.inPassword, data, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inConfirmPassword: data }
        });
      }

      if(source=="verificationCode"){
        checkForInputVal(pageConst.inMobileNum, pageConst.inPassword, pageConst.inConfirmPassword, data);

        setConstants(previousState => {
          return { ...previousState, inVerificationCode: data }
        });
      }
    }

    const validateResetPassword = () => {
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          console.log(data);
          updateLoadingStatus(false);

          if(data.status_code=="invalid_otp"){
            updateToastDialogState(true,"OTP is incorrect ! Please enter correct OTP!");
          }else if(data.status_code=="success"){
            updateToastDialogState(true,"Password Changed !");
          }else if(data.status_code=="account_error"){
            updateToastDialogState(true,"Sorry, There is an error related to account !");
          }else if(data.status_code=="invalid_mobile_num"){
            updateToastDialogState(true,"Invalid mobile number !");
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateLoadingStatus(false);
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      if(isInputValCorrect){
        updateLoadingStatus(true);
        requestAPI(API_ACCESS_URL+"reset-password.php?USER_MOBILE="+pageConst.inMobileNum+"&NEW_PASSWORD="+pageConst.inPassword+"&USER_OTP="+pageConst.inVerificationCode);
      }
    }

    const getTimeRemaining = (e) => {
      const total = Date.parse(e) - Date.parse(new Date());
      const seconds = Math.floor((total / 1000) % 60);
      return {
          total, seconds
      };
    }

    const startCountDownTimer = (e) =>{
      let { total, seconds } = getTimeRemaining(e);
      if (total >= 0) {
        setResendOTPTime((seconds > 9 ? seconds : '0' + seconds))
      }
    }

    const getDeadTime = (e) => {
      let deadline = new Date();

      deadline.setSeconds(deadline.getSeconds() + e);
      return deadline;
    }

    
    const clearTimer = (e) => {
 
      setResendOTPTime(30);

      if (Ref.current) clearInterval(Ref.current);
      const id = setInterval(() => {
        startCountDownTimer(e);
      }, 1000)
      Ref.current = id;
    }

    const sendVerificationCode = () =>{
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          console.log(data);

          updateOTPSendingStatus(false);
          if(data.status_code=="otp_error"){
            updateToastDialogState(true,"We failed to send OTP ! Please try again!");
          }else if(data.status_code=="account_error"){
            updateToastDialogState(true,"Sorry, There is an error related to account !");
          }else if(data.status_code=="success"){
            setCookie("otptimeout",getDeadTime(pageConst.resendOTPTimeLimit), 30);
            clearTimer(getDeadTime(pageConst.resendOTPTimeLimit));
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateOTPSendingStatus(false);
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      if(isMobileNumValidate(pageConst.inMobileNum)){
        if(resendOTPTimeLeft > 0){
          updateToastDialogState(true,"Please after sometime!");
        }else if(pageConst.isOTPSending==false){
          updateOTPSendingStatus(true);
          requestAPI(API_ACCESS_URL+"services/sms/send-sms.php?MOBILE="+pageConst.inMobileNum+"&PURPOSE=RESETPASSWORD");
        }
      }else{
        updateToastDialogState(true,"Mobile Number is incorrect!");
      }
    }

    useEffect(() => {
      if(getCookie("uid")){
        redirectTo(HOME_REDIRECT_URL);
      }else{

        setConstants({...pageConst, isSessionExist: false});

        if(getCookie("otptimeout")){
          let { total, seconds } = getTimeRemaining(getCookie("otptimeout"));
          if(seconds > 0){
            clearTimer(getDeadTime(seconds));
          }
        }

        if(getURLParam('C')!=null && getURLParam('C')!=""){
          let referCode = getURLParam('C');
          setConstants(previousState => {
            return { ...previousState, inInviteCode: referCode }
          });
        }
        
      }
    }, []);

    return (
      <div className={`v-center ${pageConst.isSessionExist == true ? 'hide-v' : ''}`}>
        <div className="imgbg-v h-100vh pr-v res-wth ovf-scrl-y hide-sb">
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="game-manage-view pd-15 col-view">

           <div className='pd-15 mg-t-55'>
             <div className='ft-sz-16 cl-white'>Welcome back</div>
             <div className='ft-sz-23 cl-white mg-t-10'>Many rewards are waiting for you to collect</div>
            </div>

            <div className='col-view bg-white w-100 v-center br-15 mg-t-10 pd-18'>

                <div className='cutm-inp-bx pd-5-15 mg-t-15'>
                    <img className="h-w-22" src={require('../icons/phone_icon.png')} />
                    <input type="number" className='mg-l-20 ft-sz-18 cutm-inp' autoComplete="new-password" placeholder='Enter Mobile Number' onChange={e => onInputValChange('mobile',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="password" className='mg-l-20 ft-sz-18 cutm-inp' autoComplete="new-password" placeholder='Password (≥6 characters)' onChange={e => onInputValChange('password',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="password" className='mg-l-20 ft-sz-18 cutm-inp' autoComplete="new-password" placeholder='Confirm Login Password' onChange={e => onInputValChange('confirmPassword',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/secure_icon.png')} />
                    <input type="number" className='mg-l-20 ft-sz-18 cutm-inp' autoComplete="new-password" placeholder='OTP' onChange={e => onInputValChange('verificationCode',e.target.value)}></input>

                    <div className='w-125-p h-40-p v-center ft-sz-18 br-10 cl-white bg-blu-blk' onClick={() => sendVerificationCode()}>{pageConst.isOTPSending ? 'Sending' : resendOTPTimeLeft > 0 ? resendOTPTimeLeft : 'OTP'}</div>
                </div>

                <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk bg-blu-blk`} onClick={() => validateResetPassword()}>
                  <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                  <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Reset Password</span>
                </div>
            </div>

          </div>
        </div>
      </div>
    );
}

export default ForgetPassword;