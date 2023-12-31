import React, { useEffect, useState } from 'react';
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import '../../MainStyle.css';
import { WEBSITE_NAME,LOGIN_REDIRECT_URL,WEBSITE_URL,redirectTo } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function LevelDailyIncome(){
    const [pageConst, setConstants] = useState({
        pageTitle: "Level Daily Income",
        isLoadingShow: false,
        toastDialogShow: false,
        toastMessage: "",
        recordList: [],
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

    const generateReferalURL = () =>{
        return WEBSITE_URL+"RG?C="+getCookie("uid");
    }

    const claimBonus = () =>{

    }

    useEffect(() => {
        if(!getCookie("uid")){
            redirectTo(LOGIN_REDIRECT_URL);
        }
      }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-cus-color">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="col-view mg-t-45">

            <div className="col-view mg-b-15">

               <div className='col-view pd-5-15'>
                <span className='ft-sz-18 cl-white mg-t-30'>Everyday you will receive from your team members</span>

                <div className='col-view min-h pd-18 mg-t-20 cl-white'>
                   <div className="row-view sb-view">
                     <span>Level</span>
                     <span>Reward</span>
                   </div>

                   <div className="row-view mg-t-15 sb-view">
                     <span className='ft-sz-14'>1</span>
                     <span className="ft-sz-14">₹2</span>
                   </div>

                   <div className="row-view mg-t-15 sb-view">
                     <span className='ft-sz-14'>2</span>
                     <span className="ft-sz-14">₹2</span>
                   </div>

                   <div className="row-view mg-t-15 sb-view">
                     <span className='ft-sz-14'>3</span>
                     <span className="ft-sz-14">₹2</span>
                   </div>

                   <div className="row-view mg-t-15 sb-view">
                     <span className='ft-sz-14'>4</span>
                     <span className="ft-sz-14">₹2</span>
                   </div>

                   <div className="row-view mg-t-15 sb-view">
                     <span className='ft-sz-14'>5</span>
                     <span className="ft-sz-14">₹3</span>
                   </div>

                </div>

               </div>

            </div>

          </div>

        </div>
      </div>
    );
}

export default LevelDailyIncome;