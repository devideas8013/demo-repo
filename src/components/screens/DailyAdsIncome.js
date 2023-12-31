import React, { useEffect, useState } from 'react';
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import '../../MainStyle.css';
import { API_ACCESS_URL,WEBSITE_NAME,LOGIN_REDIRECT_URL,WEBSITE_URL,redirectTo } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function DailyAdsIncome(){
    const [pageConst, setConstants] = useState({
        pageTitle: "Daily Ads Income",
        isLoadingShow: false,
        toastDialogShow: false,
        toastMessage: "",
        recordList: [],
    });

    const topBarClickAction = (data) =>{
    }

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

    const generateReferalURL = () =>{
        return WEBSITE_URL+"RG?C="+getCookie("uid");
    }

    const claimBonus = () =>{
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();

          updateLoadingStatus(false);
          if(data.status_code=="already_claimed"){
            updateToastDialogState(true,"Sorry, You've already claimed this bonus !");
          }else if(data.status_code=="success"){
            updateToastDialogState(true,"Bonus Claimed!");
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateLoadingStatus(false);
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      };

      updateLoadingStatus(true);
      requestAPI(API_ACCESS_URL+"claim-daily-bonus.php?USER_ID="+getCookie("uid"));
      
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
                  <span className='ft-sz-20 ft-wgt-b mg-t-30'>Ads Income is: ₹{getCookie("serviceDailyBonus")}</span>

                  <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk bg-grad-drk-blue-180`} onClick={() => claimBonus()}>
                    <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                    <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Claim Now</span>
                  </div>

                  <span className='ft-sz-13 mg-t-10'>Everyday you can claim your daily ads income. To claim bonus cick 'claim now' and bonus will be credited to your wallet.</span>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default DailyAdsIncome;