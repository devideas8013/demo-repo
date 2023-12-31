import React, { useEffect, useState, useRef } from 'react';
import { Link  } from 'react-router-dom';
import '../../MainStyle.css';
import ToastDialog from '../dialogs/ToastDialog';
import { API_ACCESS_URL,HOME_REDIRECT_URL,redirectTo,getURLParam } from '../modals/Constants';
import { setCookie,getCookie } from '../modals/Cookie';

function Register(){
  
    const Ref = useRef(null);
    const [resendOTPTimeLeft, setResendOTPTime] = useState(0);
    const [isInputValCorrect, setInValCorrect] = useState(false);

    const [pageConst, setConstants] = useState({
        pageTitle: "Register",
        inName: "",
        inMobileNum: "",
        inPassword: "",
        inConfirmPassword: "",
        inInviteCode: "",
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

    const checkForInputVal = (name,mobilenum,password,confirmPassword,inviteCode,verificationCode) =>{
      if(name.length > 2 && isMobileNumValidate(mobilenum) && isPasswordValidate(password,confirmPassword) && inviteCode.length > 5 && verificationCode.length > 4){
        setInValCorrect(true);
      }else{
        setInValCorrect(false);
      }
    }

    const onInputValChange = (source,data) =>{

      if(source=="name"){
        checkForInputVal(data, pageConst.inMobileNum, pageConst.inPassword, pageConst.inConfirmPassword, pageConst.inInviteCode, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inName: data }
        });
      }

      if(source=="mobile"){
        checkForInputVal(pageConst.inName, data, pageConst.inPassword, pageConst.inConfirmPassword, pageConst.inInviteCode, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inMobileNum: data }
        });
      }

      if(source=="password"){
        checkForInputVal(pageConst.inName, pageConst.inMobileNum, data, pageConst.inConfirmPassword, pageConst.inInviteCode, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inPassword: data }
        });
      }

      if(source=="confirmPassword"){
        checkForInputVal(pageConst.inName, pageConst.inMobileNum, pageConst.inPassword, data, pageConst.inInviteCode, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inConfirmPassword: data }
        });
      }

      if(source=="inviteCode"){
        checkForInputVal(pageConst.inName, pageConst.inMobileNum, pageConst.inPassword, pageConst.inConfirmPassword, data, pageConst.inVerificationCode);

        setConstants(previousState => {
          return { ...previousState, inInviteCode: data }
        });
      }

      if(source=="verificationCode"){
        checkForInputVal(pageConst.inName, pageConst.inMobileNum, pageConst.inPassword, pageConst.inConfirmPassword, pageConst.inInviteCode, data);

        setConstants(previousState => {
          return { ...previousState, inVerificationCode: data }
        });
      }
    }

    const validateSignup = () => {
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          updateLoadingStatus(false);

          if(data.status_code=="invalid_otp"){
            updateToastDialogState(true,"OTP is incorrect ! Please enter correct OTP!");
          }else if(data.status_code=="invalid_refer_code"){
            updateToastDialogState(true,"Invite Code is incorrect ! Please enter correct Invite Code!");
          }else if(data.status_code=="success"){
            setCookie("uid",data.data[0].account_id, 30);
            setCookie("mobile",data.data[0].account_mobile_num, 30);
            setCookie("balance",data.data[0].account_balance, 30);
            setCookie("secret",data.data[0].auth_secret_key, 30);
            redirectTo(HOME_REDIRECT_URL);
          }else if(data.status_code=="password_error"){
            updateToastDialogState(true,"Password not correct! try again");
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      if(isInputValCorrect){
        updateLoadingStatus(true);
        requestAPI(API_ACCESS_URL+"create-account.php?SIGNUP_NAME="+pageConst.inName+"&SIGNUP_PHONE="+pageConst.inMobileNum+"&SIGNUP_PASSWORD="+pageConst.inPassword+"&SIGNUP_OTP="+pageConst.inVerificationCode+"&SIGNUP_REFER_CODE="+pageConst.inInviteCode);
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

          updateOTPSendingStatus(false);
          if(data.status_code=="otp_error"){
            updateToastDialogState(true,"We failed to send OTP ! Please try again!");
          }else if(data.status_code=="already_registered"){
            updateToastDialogState(true,"Sorry, This account is already registered !");
          }else if(data.status_code=="mobile_num_error"){
            updateToastDialogState(true,"Mobile Number is invalid !");
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
          requestAPI(API_ACCESS_URL+"services/sms/send-sms.php?MOBILE="+pageConst.inMobileNum+"&PURPOSE=SIGNUP");
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
                  <input type="text" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Enter Name' autoComplete="new-password" onChange={e => onInputValChange('name',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-15'>
                    <img className="h-w-22" src={require('../icons/phone_icon.png')} />
                    <input type="number" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Enter Mobile Number' autoComplete="new-password" onChange={e => onInputValChange('mobile',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="password" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Password (≥6 characters)' autoComplete="new-password" onChange={e => onInputValChange('password',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="password" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Confirm Login Password' autoComplete="new-password" onChange={e => onInputValChange('confirmPassword',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/recommendation_icon.png')} />
                    <input type="number" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Invite Code (Use: 111111)' autoComplete="new-password" value={pageConst.inInviteCode} onChange={e => onInputValChange('inviteCode',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/secure_icon.png')} />
                    <input type="number" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='OTP' autoComplete="new-password" onChange={e => onInputValChange('verificationCode',e.target.value)}></input>

                    <div className='w-125-p h-40-p v-center ft-sz-18 br-10 cl-white bg-blu-blk' onClick={() => sendVerificationCode()}>{pageConst.isOTPSending ? 'Sending' : resendOTPTimeLeft > 0 ? resendOTPTimeLeft : 'OTP'}</div>
                </div>

                <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk bg-blu-blk`} onClick={() => validateSignup()}>
                  <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                  <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Sign up</span>
                </div>

                <div className='w-100 v-center ft-sz-16 mg-t-25'>
                    <span>Already have an account?</span>
                    <Link className='txt-deco-n mg-l-10 cl-blue' to={"/LG"}>Log in</Link>
                </div>
            </div>

          </div>
        </div>
      </div>
    );
}

export default Register;