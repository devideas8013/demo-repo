import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/LoadingDialog';
import { API_ACCESS_URL,getURLParam } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function AllBankCards(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Bank Cards",
        bankCardMethod: "",
        isLoadingShow: false,
        recordList: [],
    });

    const topBarClickAction = (data) =>{
        if(data=="multiBtn2"){
          navigate('/addbankcard?M='+pageConst.bankCardMethod, { replace: false });
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

    const setCardPrimary = (id) =>{
      const fecthApiData = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          updateLoadingStatus(false);

          if(data.status_code=="success"){
            getGameRecords();
          }
        } catch (error) {
          updateLoadingStatus(false);
        }
      };

      if(id!=""){
        updateLoadingStatus(true);

        fecthApiData(API_ACCESS_URL+"update-bankcard-primary.php?USER_ID="+getCookie("uid")+"&CARD_ID="+id);
      
      }
    }

    const updateRecordList = (data) => {
      let tempData = [];

        for (let i = 0; i < data.length; i++) {          
            tempData.push(
              <div key={i} className='pr-v w-100 br-5 mg-t-15 bg-white' onClick={() => setCardPrimary(data[i]['c_bank_id'])}>
                <div className={`pd-5-15 br-5 ft-sz-16 ft-wgt-b cl-white ${data[i]['c_is_primary']=='true' ? 'bg-blue' : 'bg-grey-2'}`}>{data[i]['c_is_primary']=='true' ? 'Primary' : 'Set Primary'}</div>
                <div className={`ps-ab ps-tp ps-rgt pd-5-15 br-5 ft-sz-16 cl-white bg-yellow`}>Verified</div>

                <div className='col-view w-100 mg-t-5 pd-5-15'>
                  <span className='ft-sz-15 mg-t-5'>Name: {data[i]['c_beneficiary']}</span>
                  <span className='ft-sz-15 mg-t-5'>ACC: {data[i]['c_bank_account']}</span>
                  <span className='ft-sz-15 mg-t-5'>{data[i]['c_bank_ifsc_code']=="none" ? 'Method: UPI' : 'IFSC: '+data[i]['c_bank_ifsc_code']}</span>
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
        fecthApiData(API_ACCESS_URL+"load-bank-cards.php?USER_ID="+getCookie("uid")+"&PAGE_NUM=1");
    }
  
    useEffect(() => {
      if(getURLParam('M')!=null && getURLParam('M')!=""){
        setConstants(previousState => {
          return { ...previousState, bankCardMethod: getURLParam('M') }
        });
      }

      getGameRecords();
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-tar-black">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="Add New" updateState={topBarClickAction}/>
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