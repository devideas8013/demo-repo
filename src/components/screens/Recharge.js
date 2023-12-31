import React, { useEffect, useState } from 'react'
import { Link,useNavigate } from 'react-router-dom';
import TopBar from '../other-components/TopBar';
import { PG_ACCESS_URL,openNewPage } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function Recharge(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Deposit",
        isLoadingShow: false,
        inRechargeAmnt: "",
        recordList: [],
    });

    const topBarClickAction = (data) =>{
      if(data=="multiBtn2"){
        navigate('/RechargeRecords', { replace: false });
      }
    }

    const onInputValChange = (source,data) =>{

      if(source=="rechargeamount"){
        setConstants(previousState => {
          return { ...previousState, inRechargeAmnt: data }
        });
      }

    }

    const rechargeNow = () =>{

      if(Number(pageConst.inRechargeAmnt) >= getCookie("minrecharge")){
        openNewPage(PG_ACCESS_URL+"utrpay-58745/?user_id="+getCookie("uid")+"&amount="+pageConst.inRechargeAmnt+"&secret="+getCookie("secret")+"&platform=web");
      }
    }

    const setAllRechargeOptions = () =>{
      let tempData = [];
      const recentBetArr = getCookie("rechargeoptions").split(',');

      for (let i = 0; i < recentBetArr.length; i++) {          
          tempData.push(
            <div key={i} className='pd-10-15 v-center br-5 ft-sz-16 bg-l-blue hover-bg-drk-blue-180' onClick={() => onInputValChange('rechargeamount',recentBetArr[i])}>
              ₹{recentBetArr[i]}
            </div>)
      };

      setConstants(previousState => {
          return { ...previousState, recordList: tempData }
      });
    }
  
    useEffect(() => {
      if(!getCookie("uid")){
        navigate('/LG', { replace: true });
      }else{
        setAllRechargeOptions();
      }
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-cus-color">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="Records" updateState={topBarClickAction}/>

          <div className="col-view pd-10-20 mg-t-45 mg-b-70">

            <div className='col-view w-100 v-center mg-t-10'>
             <span className='cl-white'>Balance</span>
             <span className='ft-sz-25 ft-wgt-b cl-white mg-t-10'>₹{getCookie("balance")}</span>
            </div>

            <div className='col-view mg-t-25'>
              <span className='ft-sz-18 ft-wgt-b cl-l-white'>Amount</span>
              <div className='row-view br-b-grey'>
                <span className='ft-sz-32 cl-l-white'>₹</span>
                <input type="number" className='cutm-inp ft-sz-38 h-60-p inp-ph-white-color cl-white' value={pageConst.inRechargeAmnt} onChange={e => onInputValChange('rechargeamount',e.target.value)}></input>
              </div>
            </div>

            <div className='w-100 g-v-3 mg-t-25'>
              {pageConst.recordList}
            </div>
            
            <div className='w-100 mg-t-30 h-50-p ft-sz-20 v-center br-10 cl-white bg-grad-drk-blue-180' onClick={() => rechargeNow()}>
              <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
              <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Recharge</span>
            </div>

          </div>
        </div>
      </div>
    );
}

export default Recharge;