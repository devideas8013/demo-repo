import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/LoadingDialog';
import { API_ACCESS_URL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function AllBankCards(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Withdraw Records",
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

    const updateRecordList = (data) => {
      let tempData = [];

        for (let i = 0; i < data.length; i++) {          
            tempData.push(
              <div key={i} className='pr-v w-100 br-5 mg-t-15 bg-white'>
                <div className={`ps-ab ps-tp ps-lf pd-2-8 br-5 ft-sz-15 cl-white ${data[i]['w_status']=="success" ? 'bg-green' : (data[i]['w_status']=="rejected" ? 'bg-red' : 'bg-l-black')}`}>{data[i]['w_status']=="success" ? 'completed' : (data[i]['w_status']=="rejected" ? 'cancelled' : 'processing')}</div>
                <div className='ps-ab ps-tp ps-rgt pd-2-8 ft-sz-15'>{data[i]['w_date']+" "+data[i]['w_time']}</div>
                  
                <div className='col-view pd-5-15'>

                <div className='pr-v col-view w-100 ft-sz-14 mg-t-20'>
                  <span className='ft-sz-15 mg-t-5'>OrderId: {data[i]['w_uniq_id']}</span>
                  <span className='ft-sz-15 mg-t-5 mg-b-10'>UPI: <span className='cl-blue'></span></span>

                  <div className='ps-ab ps-btm ps-rgt pd-2-8 mg-t-25 ft-sz-25'>₹{data[i]['w_request']}</div>
                </div>

                <div className='row-view sb-view ft-sz-14 mg-t-15'>
                  <span>Wallet:</span>
                  <span>Bank: <span className='cl-blue'>{data[i]['w_bank_account']}</span></span>
                </div>

                <div className='row-view sb-view ft-sz-14 mg-t-5'>
                  <span>Primary: <span className='cl-blue'>IMPS</span></span>
                  <span>IFSC: <span className='cl-blue'>{data[i]['w_bank_ifsc']}</span></span>
                </div>

                <div className='line-hv-grey mg-t-5'></div>
                <span className='ft-sz-14 mg-t-5'>{data[i]['w_beneficiary']} | Bank: {data[i]['w_amount']} | Fees: {Number(data[i]['w_request'])-Number(data[i]['w_amount'])}</span>

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
        fecthApiData(API_ACCESS_URL+"load-withdraw-records.php?USER_ID="+getCookie("uid")+"&PAGE_NUM=1");
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

export default AllBankCards;