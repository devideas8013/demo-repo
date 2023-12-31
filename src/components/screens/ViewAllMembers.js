import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import '../../MainStyle.css';
import TopBar from '../other-components/TopBar';
import LoadingDialog from '../dialogs/LoadingDialog';
import { API_ACCESS_URL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function ViewAllMembers(){
    const navigate = useNavigate();
    const [pageConst, setConstants] = useState({
        pageTitle: "View All Members",
        totalActiveMembers: 0,
        totalInActiveMembers: 0,
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
                 <img className='h-w-32' src={require('../icons/avatar_icon.png')} />

                 <div className='col-view mg-l-20'>
                  <span className='ft-sz-17'>User ID: {data[i]['r_user_id']}</span>
                  <div className='row-view mg-t-5'>
                    <span className='ft-sz-12'>Mobile: {data[i]['r_mobile_num']}</span>
                    <span className='ft-sz-12 mg-l-10'>Level: {data[i]['r_level']}</span>
                  </div>
                 </div>
                </div>
                <span className='ft-sz-17 ft-wgt-b cl-green'>{data[i]['r_plan_active']=='true' ? 'Active' : ''}</span>
              </div>)
        };

        setConstants(previousState => {
            return { ...previousState, recordList: tempData }
        });
    }

    function getAllMembers(){
        const fecthApiData = async (url) => {
            try {
              const res = await fetch(url);
              const data = await res.json();
              let totalActive = data.total_active;
              let totalInActive = data.total_inactive;

              updateLoadingStatus(false);

              if(data.status_code="success"){
                setConstants({...pageConst, totalActiveMembers: totalActive, totalInActiveMembers: totalInActive});

                updateRecordList(data.data); 
              }
            } catch (error) {
              updateLoadingStatus(false);
            }
        };

        updateLoadingStatus(true);
        fecthApiData(API_ACCESS_URL+"load-team-members.php?USER_ID="+getCookie("uid")+"&PAGE_NUM=1");
    }
  
    useEffect(() => {
        getAllMembers();
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-cus-color">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <LoadingDialog intentData={pageConst}/>

          <div className="col-view mg-t-45">

            <div className="col-view br-right-t br-left-t mg-b-15">

               <div className='col-view pd-5-15'>
                
                  <div className='w-100 row-view sb-view mg-t-15 pd-10-20'>
                    <div>
                     <p className='ft-sz-13 cl-white'>Total Active</p>
                     <p className='ft-sz-20 cl-white mg-t-5'>{pageConst.totalActiveMembers}</p>
                    </div>

                    <div>
                     <p className='ft-sz-13 cl-white'>Total In-Active</p>
                     <p className='ft-sz-20 cl-white mg-t-5'>{pageConst.totalInActiveMembers}</p>
                    </div>
                  </div>

                  <div className="col-view">
                  {pageConst.recordList}
                  </div>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default ViewAllMembers;