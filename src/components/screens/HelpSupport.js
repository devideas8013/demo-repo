import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,redirectTo } from '../modals/Constants';
import { setCookie,getCookie } from '../modals/Cookie';

function HelpSupport(){
    const [isInputValCorrect, setInValCorrect] = useState(false);
    const [pageConst, setConstants] = useState({
        pageTitle: "Help & Support",
        inHelpsupport: "",
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

    const checkForInputVal = (helpsupportdetails) =>{
      if(helpsupportdetails != "" && helpsupportdetails != undefined && helpsupportdetails.length >= 3){
        setInValCorrect(true);
      }else{
        setInValCorrect(false);
      }
    }

    const onInputValChange = (source,data) =>{

      if(source=="helpsupportdetails"){
        checkForInputVal(data);

        setConstants(previousState => {
          return { ...previousState, inHelpsupport: data }
        });
      }
    }

    const validateHelpSupport = () => {
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          console.log(data.data);
          updateLoadingStatus(false);

          if(data.status_code=="success"){
            updateToastDialogState(true,"Form Submitted!");
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      if(isInputValCorrect && pageConst.isLoadingShow==false){
        updateLoadingStatus(true);
        requestAPI(API_ACCESS_URL+"request-helpsupport.php?USER_ID="+getCookie("uid")+"&DETAILS="+pageConst.inHelpsupport);
      }
    }

    useEffect(() => {
      if(!getCookie("uid")){
        redirectTo(LOGIN_REDIRECT_URL);
      }
    }, []);

    return (
      <div className='v-center'>
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-cus-color">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2=""/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="game-manage-view col-view pd-15 mg-t-45">

            <div className='col-view w-100 v-center pd-18 bg-white br-10'>

                <div className='cutm-inp-bx pd-5-15 mg-t-15 h-250-p'>
                    <textarea className='ft-sz-18 cutm-inp h-250-p' autoComplete="new-password" placeholder='Describe here..' onChange={e => onInputValChange('helpsupportdetails',e.target.value)}></textarea>
                </div>

                <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white ${isInputValCorrect ? 'bg-grad-drk-blue-180' : 'bg-grey-2'}`} onClick={() => validateHelpSupport()}>
                  <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                  <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Submit Form</span>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default HelpSupport;