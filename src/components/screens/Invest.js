import React,{useState, useEffect} from 'react'
import { setCookie,getCookie,deleteCookie } from '../modals/Cookie';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,redirectTo } from '../modals/Constants';
import { Link,useNavigate } from 'react-router-dom';
import BottomNavbar from '../other-components/BottomNavbar';

function Invest(){
  const navigate = useNavigate();
  const [isLoadingShow, setLoadingShow] = useState(false);
  const [pageConst, setConstants] = useState({
    accountBalance: 0,
    investmentsNum: 0,
    totalIncome: 0,
    tabActiveReord: "pending",
    recordList: [],
  });

  const updateActiveTab = (data) => {
    setConstants(previousState => {
        return { ...previousState, tabActiveReord: data }
    });

    getInvestmentRecords(data);
  }

  const updateRecordList = (data) => {
    let tempData = [];

      for (let i = 0; i < data.length; i++) {     
          tempData.push(
          <div key={i} className='col-view w-100 pd-10 br-10 mg-t-15 bg-white'>
            
            <div className='row-view'>
              <img className='w-100-p br-5' src={data[i]['i_img_url']} />

              <div className='col-view mg-l-10'>
                <div className='ft-sz-14'>{data[i]['i_title']}</div>
                <div className='ft-sz-13 mg-t-5'>Total Revenue</div>
                <div className='ft-sz-16 mg-t-5 cl-orange'>₹{data[i]['i_earnings']}</div>
              </div>

            </div>

            <div className='row-view w-100 sb-view mg-t-15'>

              <div className='col-view a-center w-100'>
                <div className='ft-sz-16'>₹{data[i]['i_hourly_income']}</div>
                <div className='ft-sz-13 mg-t-10'>Hourly income</div>
              </div>

              <div className='line-v bg-l-grey'></div>

              <div className='col-view a-center w-100'>
                <div className='ft-sz-16'>{data[i]['i_days_left']}/{data[i]['i_total_days'] > 11500 ? 'LifeTime' : data[i]['i_total_days']+" days"}</div>
                <div className='ft-sz-13 mg-t-10'>Equipment cycle</div>
              </div>

            </div>

          </div>)
      };

      setConstants(previousState => {
          return { ...previousState, recordList: tempData }
      });
  }


  function getInvestmentRecords(investStatus){
    setLoadingShow(true);

    const fecthApiData = async (url) => {
      
      try {
        const res = await fetch(url);
        const data = await res.json();

        setLoadingShow(false);

        if(data.status_code=="success"){
          
          setConstants(previousState => {
            return { ...previousState, investmentsNum: data.investments_number }
          });

          setConstants(previousState => {
            return { ...previousState, totalIncome: data.total_income }
          });
          
          updateRecordList(data.data);
        }else{
          let tempData = [];

          setConstants(previousState => {
            return { ...previousState, recordList: tempData }
          });
        }
        
      } catch (error) {
        setLoadingShow(false);
      }
    };

    if(!isLoadingShow){
      fecthApiData(API_ACCESS_URL+"load-investment-records.php?USER_ID="+getCookie("uid")+"&SECRET_KEY="+getCookie("secret")+"&INVEST_STATUS="+investStatus);
    }

  }

  useEffect(() => {
    if(getCookie("uid")){
      getInvestmentRecords(pageConst.tabActiveReord);
    }else{
      redirectTo(LOGIN_REDIRECT_URL);
    }
  }, []);

  return (
    <div className="v-center">
      <div className="h-100vh res-wth ovf-scrl-y bg-cus-color hide-sb">
        <div className='w-100 cl-white ft-sz-18 v-center pd-15'>My Invest</div>

        <div className='col-view pd-10-20 mg-b-150'>

          <div className='row-view w-100 mg-t-20 pd-10-20 sb-view bg-grad-lgt-drk-blue br-10'>
            <div className='col-view a-center pd-15 w-100'>
              <div className='cl-white'>No of Investments</div>
              <div className='ft-sz-18 cl-white mg-t-10'>{pageConst.investmentsNum}</div>
            </div>

            <div className='line-v bg-l-grey'></div>

            <div className='col-view a-center pd-15 w-100'>
              <div className='cl-white'>Total Income</div>
              <div className='ft-sz-18 cl-white mg-t-10'>₹{pageConst.totalIncome}</div>
            </div>
          </div>

          <div className='col-view pd-15 ft-sz-13 w-100 cl-white bg-l-white br-10 mg-t-10'>
          After the purchase is successful, your equipment income will be automatically credited to the account every hour.
          </div>

          <div className="tab-slct-v w-100 mg-t-15">
            <div className={`v-center tab-in-v pd-10 ft-sz-18 w-100 ${pageConst.tabActiveReord=="pending" ? 'active' : ''}`} onClick={()=>updateActiveTab('pending')}>In progress</div>
            <div className={`v-center tab-in-v pd-10 ft-sz-18 w-100 ${pageConst.tabActiveReord=="completed" ? 'active' : ''}`} onClick={()=>updateActiveTab('completed')}>Completed</div>
          </div>

          <div className={`col-view`}>
            {pageConst.recordList}
          </div>

        </div>

        <BottomNavbar activeBar="invest"/>
      </div>
    </div>
  );
}

export default Invest;