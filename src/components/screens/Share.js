import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import '../../MainStyle.css';
import { WEBSITE_NAME,LOGIN_REDIRECT_URL,WEBSITE_URL,redirectTo,copyText,openNewPage,generateReferalURL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';
import ShareOptionsView from '../other-components/ShareOptionsView';

function Share(){
    const navigate = useNavigate();

    const [pageConst, setConstants] = useState({
        pageTitle: "Share",
        isLoadingShow: false,
        toastDialogShow: false,
        toastMessage: "",
    });

    const topBarClickAction = (data) =>{
    }
    
    const updateToastDialogState = (data,msg) => {
        setConstants(previousState => {
          return { ...previousState, toastDialogShow: data }
        });
  
        setConstants(previousState => {
          return { ...previousState, toastMessage: msg }
        });
    }

    useEffect(() => {
        if(!getCookie("uid")){
            redirectTo(LOGIN_REDIRECT_URL);
        }
      }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-white">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="col-view mg-t-45">

            <div className="col-view mg-b-15 bg-white">

               <div className='col-view v-center pd-5-15'>
                  <span className='ft-sz-18 mg-t-15'>My Invite Link</span>

                  <div className='w-100 v-center pd-10-15 ft-sz-20 br-a-grey br-5 mg-t-10'>
                    <span>{generateReferalURL(getCookie("uid"))}</span>
                  </div>

                  <ShareOptionsView updateState={updateToastDialogState} />
                  
                  <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk bg-grad-lgt-drk-blue`} onClick={() => openNewPage(getCookie("appDownloadURL"))}>
                    <span>Download App</span>
                  </div>

                  <span className='ft-sz-13 mg-t-10'>{WEBSITE_NAME} rules and regulations prohibit multiple accounts. You may be blocked if you use multiple accounts or conduct suspicious activities.</span>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default Share;