import React, { useEffect, useState } from 'react'
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import '../../MainStyle.css';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,redirectTo } from '../modals/Constants';
import { setCookie,getCookie } from '../modals/Cookie';

function ChangePassword(){
    const [isInputValCorrect, setInValCorrect] = useState(false);
    const [pageConst, setConstants] = useState({
        pageTitle: "Change Password",
        inNewPassword: "",
        inPassword: "",
        isLoadingShow: false,
        toastDialogShow: false,
        toastMessage: "",
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

    const checkForInputVal = (newpassword,password) =>{
      if(newpassword != "" && newpassword != undefined && password!="" && password != undefined){
        if(newpassword==password && newpassword.length >= 6){
          setInValCorrect(true);
        }else{
          setInValCorrect(false);
        }
      }else{
        setInValCorrect(false);
      }
    }

    const onInputValChange = (source,data) =>{

      if(source=="newpassword"){
        checkForInputVal(data, pageConst.inPassword);

        setConstants(previousState => {
          return { ...previousState, inNewPassword: data }
        });
      }

      if(source=="password"){
        checkForInputVal(pageConst.inNewPassword, data);

        setConstants(previousState => {
          return { ...previousState, inPassword: data }
        });
      }
    }

    const validateChangePassword = () => {
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          console.log(data.data);
          updateLoadingStatus(false);

          if(data.status_code=="account_error"){
            updateToastDialogState(true,"Account not exist!");
          }else if(data.status_code=="password_error"){
            updateToastDialogState(true,"Password not correct! try again");
          }else if(data.status_code=="success"){
            updateToastDialogState(true,"Password Changed!");
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      if(isInputValCorrect && pageConst.isLoadingShow==false){
        updateLoadingStatus(true);
        requestAPI(API_ACCESS_URL+"request-change-password.php?USER_ID="+getCookie("uid")+"&NEW_PASSWORD="+pageConst.inPassword);
      }
    }

    useEffect(() => {
      if(!getCookie("uid")){
        redirectTo(LOGIN_REDIRECT_URL);
      }
    }, []);

    return (
      <div className='v-center'>
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-tar-black">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2=""/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="game-manage-view col-view pd-15 mg-t-45">

            <div className='col-view w-100 v-center pd-18 bg-white br-10'>

                <div className='cutm-inp-bx pd-5-15 mg-t-15'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="text" className='mg-l-20 ft-sz-18 cutm-inp' autoComplete="new-password" placeholder='New Password' onChange={e => onInputValChange('newpassword',e.target.value)}></input>
                </div>

                <div className='cutm-inp-bx pd-5-15 mg-t-5'>
                    <img className="h-w-22" src={require('../icons/lock_icon.png')} />
                    <input type="password" className='mg-l-20 ft-sz-18 cutm-inp' autoComplete="new-password" placeholder='Repeat Password (≥6 characters)' onChange={e => onInputValChange('password',e.target.value)}></input>
                </div>

                <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white ${isInputValCorrect ? 'bg-grad-drk-blue-180' : 'bg-grey-2'}`} onClick={() => validateChangePassword()}>
                  <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                  <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Change Password</span>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default ChangePassword;