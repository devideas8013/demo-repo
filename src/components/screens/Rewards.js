import React, { useEffect, useState } from 'react';
import { Link,useNavigate } from 'react-router-dom';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/loadingDialog';
import ToastDialog from '../dialogs/toastDialog';
import '../../MainStyle.css';
import { API_ACCESS_URL } from '../modals/constants';
import { getCookie } from '../modals/cookie';

function MyGameRecords(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Rewards",
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

    const updateLoadingStatus = (data) => {
      setConstants(previousState => {
          return { ...previousState, isLoadingShow: data }
      });
    }

    const updateRecordList = (data) => {
      let tempData = [];

        for (let i = 0; i < data.length; i++) {          
            tempData.push(
              <div key={i} className='pr-v w-100 col-view a-center mg-t-15 pd-15 br-5 bg-extm-l-white br-a-l-blue'>
                <div className='w-100 row-view sb-view ft-sz-15'>
                  <span className='ft-wgt-b ft-sz-16'>{data[i]['r_title']}</span>
                  <span className='ft-wgt-b ft-sz-20'>₹{data[i]['r_bonus']}</span>
                </div>

                <div className='row-view w-100 bg-l-grey br-10 pd-5 mg-t-20'>
                  <span className='w-100 pd-8 bg-grad-lgt-drk-blue br-10'></span>
                </div>

                <span className='mg-t-10 ft-sz-14'>We will reward you with ₹{data[i]['r_bonus']}</span>
                <div className={`pd-10-15 br-10 mg-t-15 cl-white ${data[i]['r_applied']=="true" ? 'bg-grey-2' : 'bg-grad-lgt-drk-blue'}`} onClick={() => claimBonus(data[i]['r_id'],data[i]['r_applied'])}>Claim Reward</div> 
              </div>)
        };

        setConstants(previousState => {
            return { ...previousState, recordList: tempData }
        });
    }

    function getAvailableRewards(){
      const fecthApiData = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          console.log(data);
          updateLoadingStatus(false);

          if(data.status_code="success"){
            updateRecordList(data.data); 
          }
        } catch (error) {
          updateLoadingStatus(false);
        }
      };

      updateLoadingStatus(true);
      fecthApiData(API_ACCESS_URL+"load-avilable-rewards.php?USER_ID="+getCookie("uid"));
    }

    const claimBonus = (reward_id,is_applied) =>{
      const requestAPI = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          console.log(data.data);
          updateLoadingStatus(false);

          if(data.status_code=="user_not_exist"){
            updateToastDialogState(true,"Account not exist!");
          }else if(data.status_code=="code_not_exist"){
            updateToastDialogState(true,"Code not exist!");
          }else if(data.status_code=="already_claimed"){
            updateToastDialogState(true,"You've already claimed this reward!");
          }else if(data.status_code=="success"){
            updateToastDialogState(true,"Reward Claimed!");
          }else{
            updateToastDialogState(true,"Something went wrong! Please try again!");
          }

        } catch (error) {
          updateLoadingStatus(false);
          updateToastDialogState(true,"There was a technical issue! Please try again!");
        }
      }

      if(reward_id!="" && is_applied=="false"){
        updateLoadingStatus(true);
        requestAPI(API_ACCESS_URL+"claim-bonus.php?USER_ID="+getCookie("uid")+"&REWARD_ID="+reward_id);
      }
    }

    useEffect(() => {
      getAvailableRewards();
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-white">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <LoadingDialog intentData={pageConst}/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="game-manage-view col-view mg-t-45">

            <div className="col-view br-right-t br-left-t mg-b-15 bg-white">

              <div className='col-view min-h'>
                <div className="col-view pd-5-15">
                  {pageConst.recordList}
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default MyGameRecords;