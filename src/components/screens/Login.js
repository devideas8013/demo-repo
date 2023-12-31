import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom';
import '../../MainStyle.css';
import ToastDialog from '../dialogs/ToastDialog';
import { API_ACCESS_URL,HOME_REDIRECT_URL,redirectTo } from '../modals/Constants';
import { setCookie,getCookie } from '../modals/Cookie';

function Login(){
    const [isInputValCorrect, setInValCorrect] = useState(false);
    const [pageConst, setConstants] = useState({
        pageTitle: "Login",
        inMobileNum: "",
        inPassword: "",
        isLoadingShow: false,
        toastDialogShow: false,
        toastTimeAvail: 7,
        toastMessage: "",
        isSessionExist: true,
    });

    const updateLoadingStatus = (data) => {
        setConstants(previousState => {
            return { ...previousState, isLoadingShow: data }
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

    const checkForInputVal = (mobilenum,password) =>{
      if(mobilenum != "" && mobilenum != undefined && password!="" && password != undefined){
        if(mobilenum.length == 10 && password.length >= 6){
          setInValCorrect(true);
        }else{
          setInValCorrect(false);
        }
      }else{
        setInValCorrect(false);
      }
    }

    const onInputValChange = (source,data) =>{

      if(source=="mobile"){
        checkForInputVal(data, pageConst.inPassword);

        setConstants(previousState => {
          return { ...previousState, inMobileNum: data }
        });
      }

      if(source=="password"){
        checkForInputVal(pageConst.inMobileNum, data);

        setConstants(previousState => {
          return { ...previousState, inPassword: data }
        });
      }
    }

    const validateLogin = () => {
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          updateLoadingStatus(false);

          if(data.status_code=="user_not_exist"){
            updateToastDialogState(true,"Account not exist!");
          }else if(data.status_code=="password_error"){
            updateToastDialogState(true,"Password not correct! try again");
          }else if(data.status_code=="success"){
            setCookie("uid",data.data[0].account_id, 30);
            setCookie("mobile",data.data[0].account_mobile_num, 30);
            setCookie("balance",data.data[0].account_balance, 30);
            setCookie("secret",data.data[0].auth_secret_key, 30);
            redirectTo(HOME_REDIRECT_URL);
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      if(isInputValCorrect){
        updateLoadingStatus(true);
        requestAPI(API_ACCESS_URL+"request-login.php?LOGIN_ID="+pageConst.inMobileNum+"&LOGIN_PASSWORD="+pageConst.inPassword);
      }
    }

    useEffect(() => {
      if(getCookie("uid")){
        redirectTo(HOME_REDIRECT_URL);
      }else{
        setConstants(previousState => {
          return { ...previousState, isSessionExist: false }
        });
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
                    <input type="number" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Enter Mobile Number' autoComplete="new-password" onChange={e => onInputValChange('mobile',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="password" className='mg-l-20 ft-sz-18 cutm-inp' placeholder='Password (≥6 characters)' autoComplete="new-password" onChange={e => onInputValChange('password',e.target.value)}></input>
                </div>

                <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk bg-blu-blk`} onClick={() => validateLogin()}>
                  <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                  <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Login</span>
                </div>

                <div className='w-100 row-view mg-t-20 sb-view'>
                  <Link className='txt-deco-n w-100 h-50-p v-center br-5 cl-drk-blue ft-sz-14 br-a-drk-blue hover-bg-drk-blue' to={"/RG"}>
                    Create an account
                  </Link>

                  <Link className='txt-deco-n w-100 h-50-p v-center br-5 cl-drk-blue ft-sz-14 mg-l-15 br-a-drk-blue hover-bg-drk-blue' to={"/forgot-password"}>
                    Forgot Password?
                  </Link>
                </div>
            </div>

          </div>
        </div>
      </div>
    );
}

export default Login;