import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/LoadingDialog';
import { API_ACCESS_URL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function Rank(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "Rank",
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

                   <div className="row-view mg-t-15">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Rank: Starter</span>
                         <span className='ft-sz-16 cl-orange'>₹500</span>
                       </div>
                       
                       <div className='mg-t-15'>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 10</span>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white mg-l-15'>Rank: 1</span>
                       </div>
                     </div>
                   </div>

                   <div className='line-hv-grey bg-l-white mg-t-20'></div>

                   <div className="row-view mg-t-20">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Rank: Bronze</span>
                         <span className='ft-sz-16 cl-orange'>₹1500</span>
                       </div>
                       
                       <div className='mg-t-15'>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 25</span>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white mg-l-15'>Rank: 2</span>
                       </div>
                     </div>
                   </div>

                   <div className='line-hv-grey bg-l-white mg-t-20'></div>

                   <div className="row-view mg-t-20">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Rank: Silver</span>
                         <span className='ft-sz-16 cl-orange'>₹5000</span>
                       </div>
                       
                       <div className='mg-t-15'>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 50</span>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white mg-l-15'>Rank: 3</span>
                       </div>
                     </div>
                   </div>

                   <div className='line-hv-grey bg-l-white mg-t-20'></div>

                   <div className="row-view mg-t-20">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Rank: Gold</span>
                         <span className='ft-sz-16 cl-orange'>₹30000 or Laptop</span>
                       </div>
                       
                       <div className='mg-t-15'>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 100</span>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white mg-l-15'>Rank: 4</span>
                       </div>
                     </div>
                   </div>

                   <div className='line-hv-grey bg-l-white mg-t-20'></div>

                   <div className="row-view mg-t-20">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Rank: Platinum</span>
                         <span className='ft-sz-16 cl-orange'>1Lakh or Bike</span>
                       </div>
                       
                       <div className='mg-t-15'>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 500</span>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white mg-l-15'>Rank: 5</span>
                       </div>
                     </div>
                   </div>

                   <div className='line-hv-grey bg-l-white mg-t-20'></div>

                   <div className="row-view mg-t-20">
                     <img className='h-w-45' src={require('../icons/nav_rank_icon.png')} />
                     <div className='col-view mg-l-10 w-100'>
                       <div className='row-view sb-view'>
                         <span className='ft-sz-16'>Rank: Diamond</span>
                         <span className='ft-sz-16 cl-orange'>Car</span>
                       </div>
                       
                       <div className='mg-t-15'>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white'>Team: 1000</span>
                        <span className='pd-5-10 ft-sz-13 br-10 bg-l-white mg-l-15'>Rank: 6</span>
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

export default Rank;