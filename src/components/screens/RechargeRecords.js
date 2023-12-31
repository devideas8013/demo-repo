import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/LoadingDialog';
import { API_ACCESS_URL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function RechargeRecords(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Recharge Records",
        isLoadingShow: false,
        recordList: [],
    });

    const topBarClickAction = (data) =>{
        if(data=="multiBtn2"){
          navigate('/addbankcard', { replace: false });
        }
    }

    const updateLoadingStatus = (data) => {
        setConstants(previousState => {
            return { ...previousState, isLoadingShow: data }
        });
    }

    const getColourCodeVal = (data) => {
        let returnVal = "";

        if(data=="1" || data=="3" || data=="7" || data=="9"){
            returnVal = "g";
        }else if(data=="2" || data=="4" || data=="6" || data=="8"){
            returnVal = "r";
        }else if(data=="0"){
            returnVal = "rv";
        }else if(data=="5"){
            returnVal = "gv";
        }else if(data=="red"){
          returnVal = "r";
        }else if(data=="green"){
          returnVal = "g";
        }else if(data=="violet"){
          returnVal = "v";
        }

        return returnVal;
    }

    const updateRecordList = (data) => {
      let tempData = [];

        for (let i = 0; i < data.length; i++) {
            let recentBetArr = data[i]['r_details'].split(',');

            tempData.push(
            <div key={i} className='pr-v w-100 br-5 mg-t-15 bg-white'>
                <div className={`ps-ab ps-tp ps-lf pd-2-8 br-5 ft-sz-15 cl-white ${data[i]['r_status']=="success" ? 'bg-green' : (data[i]['r_status']=="rejected" ? 'bg-red' : 'bg-l-black')}`}>{data[i]['r_status']=="success" ? 'completed' : (data[i]['r_status']=="rejected" ? 'cancelled' : 'processing')}</div>
                <div className='ps-ab ps-tp ps-rgt pd-2-8 ft-sz-15'>{data[i]['r_date']+" "+data[i]['r_time']}</div>

                <div className='col-view pd-5-15'>

                  <div className='pr-v col-view w-100 ft-sz-14 mg-t-20'>
                    <span className='ft-sz-15 mg-t-5'>By: UTRPay</span>
                    <span className='ft-sz-15 mg-t-5'>To: <span className='cl-blue'>{recentBetArr[1]}</span></span>

                    <div className={`ps-ab ps-btm ps-rgt pd-2-8 ft-sz-25 ${data[i]['r_status']=="success" ? 'cl-green' : (data[i]['r_status']=="rejected" ? 'cl-red' : '')}`}>₹{data[i]['r_amount']}</div>
                  </div>

                </div>
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
              updateLoadingStatus(false);

              if(data.status_code="success"){
                updateRecordList(data.data); 
              }
            } catch (error) {
              updateLoadingStatus(false);
            }
        };

        updateLoadingStatus(true);
        fecthApiData(API_ACCESS_URL+"load-recharge-records.php?USER_ID="+getCookie("uid")+"&PAGE_NUM=1");
    }
  
    useEffect(() => {
        getGameRecords();
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-tar-black">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <LoadingDialog intentData={pageConst}/>

          <div className="col-view mg-t-45">

            <div className="col-view br-right-t br-left-t mg-b-15">

               <div className='col-view pd-5-15'>
                  {pageConst.recordList}
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default RechargeRecords;