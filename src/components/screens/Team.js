import React,{useState, useEffect} from 'react'
import { Link,useNavigate } from 'react-router-dom';
import ToastDialog from '../dialogs/ToastDialog';
import { setCookie,getCookie,deleteCookie } from '../modals/Cookie';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,WEBSITE_URL,redirectTo,copyText } from '../modals/Constants';
import BottomNavbar from '../other-components/BottomNavbar';

function Team(){
  const navigate = useNavigate();
  const [isLoadingShow, setLoadingShow] = useState(false);
  const [pageConst, setConstants] = useState({
    accountBalance: 0,
    totalPeople: 0,
    totalIncome: 0,
    totalLevel1: 0,
    totalLevel2: 0,
    totalLevel3: 0,
    totalLevel4: 0,
    totalLevel5: 0,

    totalLevel1Bonus: 0,
    totalLevel2Bonus: 0,
    totalLevel3Bonus: 0,
    totalLevel4Bonus: 0,
    totalLevel5Bonus: 0,
    toastDialogShow: false,
    toastMessage: "",
    recordList: [],
  });

  function getTeamRecords(){
    setLoadingShow(true);

    const fecthApiData = async (url) => {
      
      try {
        const res = await fetch(url);
        const data = await res.json();
        setLoadingShow(false);

        setConstants({...pageConst, totalPeople: data.total_invite, totalIncome: data.total_income, totalLevel1: data.total_level_1,
          totalLevel2: data.total_level_2, totalLevel3: data.total_level_3, totalLevel4: data.total_level_4, totalLevel5: data.total_level_5,
          totalLevel1Bonus: data.total_level_1_bonus, totalLevel2Bonus: data.total_level_2_bonus, totalLevel3Bonus: data.total_level_3_bonus,
          totalLevel4Bonus: data.total_level_4_bonus, totalLevel5Bonus: data.total_level_5_bonus});
        
      } catch (error) {
        setLoadingShow(false);
      }
    };

    if(!isLoadingShow){
      fecthApiData(API_ACCESS_URL+"load-team-records.php?USER_ID="+getCookie("uid"));
    }

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

  const copyTxtNow = (text,msg) =>{
    copyText(text);
    updateToastDialogState(true,msg);
  }

  useEffect(() => {
    if(getCookie("uid")){
      getTeamRecords();
    }else{
      redirectTo(LOGIN_REDIRECT_URL);
    }
  }, []);

  return (
    <div className="v-center">
      <div className="h-100vh res-wth ovf-scrl-y bg-cus-color hide-sb">
        <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

        <div className='w-100 col-view v-center pd-15 cl-white'>
          <div className='ft-sz-18'>My Team</div>
          <Link className='cl-white mg-t-10' to={"/viewallmembers"}>View all Members</Link>
        </div>
        
        <div className='g-v-2 w-100 pd-10-20'>
            <div className='col-view'>
              <div>
                <p className='ft-sz-13 cl-white'>Total People</p>
                <p className='ft-sz-20 cl-white mg-t-5'>{pageConst.totalPeople}</p>
              </div>

              <div className='mg-t-15'>
                <p className='ft-sz-13 cl-white'>Total Income</p>
                <p className='ft-sz-20 cl-white mg-t-5'>₹{pageConst.totalIncome}</p>
              </div>
            </div>

            <div className='col-view a-right'>
              <img className={`h-120-p`} src={require('../icons/teamwork_bg.png')} />
            </div>
        </div>

        <div className='col-view pd-10-20 mg-b-150'>

          <div className='pr-v w-100 bg-white br-10'>
            <img className='ps-ab ps-tp ps-lf-20 w-30-p' src={require("../icons/lv_1_bedge.png")} />

            <div className='col-view pd-10 mg-l-80'>
                <p>Level 1 team</p>

                <div className='row-view w-100 mg-t-20 sb-view'>
                  <div className='col-view a-center pd-10'>
                    <div>{pageConst.totalLevel1}</div>
                    <div className='ft-sz-13'>People</div>
                  </div>

                  <div className='line-v bg-l-grey'></div>

                  <div className='col-view a-center pd-10'>
                    <div>₹{pageConst.totalLevel1Bonus}</div>
                    <div className='ft-sz-13'>Rebate income</div>
                  </div>
                </div>
            </div>

          </div>

          <div className='pr-v w-100 bg-white br-10 mg-t-20'>
            <img className='ps-ab ps-tp ps-lf-20 w-30-p' src={require("../icons/lv_2_bedge.png")} />

            <div className='col-view pd-10 mg-l-80'>
                <p>Level 2 team</p>

                <div className='row-view w-100 mg-t-20 sb-view'>
                  <div className='col-view a-center pd-10'>
                    <div>{pageConst.totalLevel2}</div>
                    <div className='ft-sz-13'>People</div>
                  </div>

                  <div className='line-v bg-l-grey'></div>

                  <div className='col-view a-center pd-10'>
                    <div>₹{pageConst.totalLevel2Bonus}</div>
                    <div className='ft-sz-13'>Rebate income</div>
                  </div>
                </div>
            </div>

          </div>

          <div className='pr-v w-100 bg-white br-10 mg-t-20'>
            <img className='ps-ab ps-tp ps-lf-20 w-30-p' src={require("../icons/lv_3_bedge.png")} />

            <div className='col-view pd-10 mg-l-80'>
                <p>Level 3 team</p>

                <div className='row-view w-100 mg-t-20 sb-view'>
                  <div className='col-view a-center pd-10'>
                    <div>{pageConst.totalLevel3}</div>
                    <div className='ft-sz-13'>People</div>
                  </div>

                  <div className='line-v bg-l-grey'></div>

                  <div className='col-view a-center pd-10'>
                    <div>₹{pageConst.totalLevel3Bonus}</div>
                    <div className='ft-sz-13'>Rebate income</div>
                  </div>
                </div>
            </div>

          </div>

          <div className='pr-v w-100 bg-white br-10 mg-t-20'>
            <img className='ps-ab ps-tp ps-lf-20 w-30-p' src={require("../icons/lv_1_bedge.png")} />

            <div className='col-view pd-10 mg-l-80'>
                <p>Level 4 team</p>

                <div className='row-view w-100 mg-t-20 sb-view'>
                  <div className='col-view a-center pd-10'>
                    <div>{pageConst.totalLevel4}</div>
                    <div className='ft-sz-13'>People</div>
                  </div>

                  <div className='line-v bg-l-grey'></div>

                  <div className='col-view a-center pd-10'>
                    <div>₹{pageConst.totalLevel4Bonus}</div>
                    <div className='ft-sz-13'>Rebate income</div>
                  </div>
                </div>
            </div>

          </div>

          <div className='pr-v w-100 bg-white br-10 mg-t-20'>
            <img className='ps-ab ps-tp ps-lf-20 w-30-p' src={require("../icons/lv_2_bedge.png")} />

            <div className='col-view pd-10 mg-l-80'>
                <p>Level 5 team</p>

                <div className='row-view w-100 mg-t-20 sb-view'>
                  <div className='col-view a-center pd-10'>
                    <div>{pageConst.totalLevel5}</div>
                    <div className='ft-sz-13'>People</div>
                  </div>

                  <div className='line-v bg-l-grey'></div>

                  <div className='col-view a-center pd-10'>
                    <div>₹{pageConst.totalLevel5Bonus}</div>
                    <div className='ft-sz-13'>Rebate income</div>
                  </div>
                </div>
            </div>

          </div>

          <div className='col-view w-100 mg-t-20 pd-10 bg-white br-10'>

            <div className='row-view w-100 sb-view'>
              <div className='col-view a-center pd-10'>{getCookie("uid")} | My Code</div>

              <div className='col-view a-center pd-10' onClick={() => copyTxtNow(getCookie("uid"),"My Code Copied!")}>
                <img className='w-20-p' src={require("../icons/copy_icon.png")} />
              </div>
            </div>

            <div className='row-view w-100 mg-t-10 sb-view'>
              <div className='col-view a-center pd-10'>{generateReferalURL()}</div>

              <div className='col-view a-center pd-10' onClick={() => copyTxtNow(generateReferalURL(),"Invite URL Copied!")}>
                <img className='w-20-p' src={require("../icons/copy_icon.png")} />
              </div>
            </div>

          </div>

        </div>

        <BottomNavbar activeBar="team"/>
      </div>
    </div>
  );
}

export default Team;