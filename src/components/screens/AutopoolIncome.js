import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/LoadingDialog';
import { API_ACCESS_URL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function AutoPoolIncome(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Auto Pool Income",
        isLoadingShow: false,
        recordList: [],
    });

    const topBarClickAction = (data) =>{
        if(data=="multiBtn1"){
          navigate('/withdraw', { replace: false });
        }
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
              <div key={i} className="row-view sb-view pd-10-15 bg-white br-5 mg-t-15">
                <div className='row-view'>
                 <img className='h-w-32' src={require('../icons/rupee_icon.png')} />

                 <div className='col-view mg-l-20'>
                  <span className='ft-sz-17'>{data[i]['t_title']}</span>
                  <span className='ft-sz-12 mg-t-5'>{data[i]['t_time_stamp']}</span>
                 </div>
                </div>
                <span className='ft-sz-17 ft-wgt-b cl-green'>₹{data[i]['t_amount']}</span>
              </div>)
        };

        setConstants(previousState => {
            return { ...previousState, recordList: tempData }
        });
    }

    function getGameRecords(){
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
        fecthApiData(API_ACCESS_URL+"load-transactions.php?USER_ID="+getCookie("uid")+"&PAGE_NUM=1");
    }
  
    useEffect(() => {
        getGameRecords();
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-cus-color">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <LoadingDialog intentData={pageConst}/>

          <div className="col-view mg-t-45">

            <div className="col-view br-right-t br-left-t mg-b-15">

               <div className='col-view pd-5-15'>
                 <div className={`col-view min-h cl-white`}>

                   <div className="row-view a-start mg-t-15">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Auto Pool Reward</span>
                         <span className='ft-sz-16 cl-orange'>₹{getCookie("serviceAutopoolBonus")!="0" ? getCookie("serviceAutopoolBonus") : '500'}</span>
                       </div>
                       
                       <div className='col-view mg-t-10'>
                        <span className='w-80-p pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 19</span>

                        <div className='ft-sz-13 mg-t-10'>
                            When you complete 19 active refers then you will get Auto Pool Reward.
                        </div>
                       </div>
                     </div>
                   </div>

                  </div>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default AutoPoolIncome;