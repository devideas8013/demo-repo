import React,{useState, useEffect} from 'react'
import PurchaseDialog from '../dialogs/PurchaseDialog';
import ToastDialog from '../dialogs/ToastDialog';
import RewardOptions from '../other-components/RewardOptions';
import { setCookie,getCookie,deleteCookie } from '../modals/Cookie';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,redirectTo } from '../modals/Constants';
import { Link,useNavigate } from 'react-router-dom';
import BottomNavbar from '../other-components/BottomNavbar';

function Prizes(){
  const navigate = useNavigate();
  const [isLoadingShow, setLoadingShow] = useState(false);
  const [pageConst, setConstants] = useState({
    accountBalance: 0,
    purchaseDialogInvestId: "",
    purchaseDialogTitle: "",
    purchaseDialogDetails: "",
    purchaseDialogHourIncome: "",
    purchaseDialogDailyIncome: "",
    purchaseDialogTotalIncome: "",
    purchaseDialogTotalDays: "",
    purchaseDialogShow: false,
    purchaseDialogTheme: "dlg-thm-green",
    toastMessage: "",
    toastDialogShow: false,
    recordList: [],
  });

  const updateToastDialogState = (data,msg) => {
    console.log(data+","+msg);
    setConstants(previousState => {
      return { ...previousState, toastDialogShow: data }
    });

    setConstants(previousState => {
      return { ...previousState, toastMessage: msg }
    });
  }

  const updatePurchaseDialogState = (e,data,invest_id,title,details,hourly_income,daily_income,total_income,total_days) => {

    console.log("Dialog Called!"+data);

    if(e!=null && e.target.className=="ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg activeDialog"){
      setConstants(previousState => {
        return { ...previousState, purchaseDialogShow: false }
      });
    }else if(data=="true"){

      setConstants(previousState => {
        return { ...previousState, purchaseDialogInvestId: invest_id }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogShow: true }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogTitle: title }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogDetails: details }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogHourIncome: hourly_income }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogDailyIncome: daily_income }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogTotalIncome: total_income }
      });

      setConstants(previousState => {
        return { ...previousState, purchaseDialogTotalDays: total_days }
      });
      
      
    }else if(data=="dismiss"){
      setConstants(previousState => {
        return { ...previousState, purchaseDialogShow: false }
      });
    }
  }

  const updateRecordList = (data) => {
    let tempData = [];

      for (let i = 0; i < data.length; i++) {          
          tempData.push(
          <div key={i} className='col-view w-100 pd-10 br-10 mg-t-15 bg-white' onClick={() => updatePurchaseDialogState(null,"true",data[i]['invest_id'],data[i]['invest_name'],data[i]['invest_details'],data[i]['invest_hourly_income'],data[i]['invest_daily_income'],data[i]['invest_total_revenue'],data[i]['invest_total_days'])}>
            <div className='row-view w-100 sb-view'>
              <div className='ft-sz-18'>{data[i]['invest_name']}</div>
              <div className='ft-sz-20 ft-wgt-700 cl-l-black'>VIP{data[i]['invest_vip_level']}</div>
            </div>

            <div className='row-view w-100 a-start sb-view mg-t-10'>
             <div className='row-view'>
              <img className='w-100-p br-5' src={data[i]['invest_img_url']} />

              <div className='col-view mg-l-10'>
                <div className='ft-sz-14'>Daily Income</div>
                <div className='cl-orange ft-sz-16'>₹{data[i]['invest_daily_income']}</div>
                <div className='ft-sz-14 mg-t-10'>Price: ₹{data[i]['invest_price']}</div>
              </div>
             </div>

             <div className='col-view a-right'>
              <div className='ft-sz-14'>Total revenue:</div>
              <div className='cl-orange ft-sz-16'>₹{data[i]['invest_total_revenue']}</div>

              <div className='pd-2-8 br-10 mg-t-10 cl-white bg-grad-lgt-drk-blue'>Buy</div>
             </div>

            </div>
          </div>)
      };

      setConstants(previousState => {
          return { ...previousState, recordList: tempData }
      });
  }

  function getAccountInfo(){
    setLoadingShow(true);

    const fecthApiData = async (url) => {
      
      try {
        const res = await fetch(url);
        const data = await res.json();
        
        setLoadingShow(false);

        if(data.status_code=="success"){
          let accountMobileNum = data.data[0]['account_mobile_num'];
          let accountBalance = data.data[0]['account_balance'];
          let accountWinningBalance = data.data[0]['account_w_balance'];
          let accountCommissionBalance = data.data[0]['account_c_balance'];
          let service_app_status = data.data[0]['service_app_status'];
          let service_min_recharge = data.data[0]['service_min_recharge'];
          let service_min_withdraw = data.data[0]['service_min_withdraw'];
          let service_recharge_option = data.data[0]['service_recharge_option'];
          let service_telegram_url = data.data[0]['service_telegram_url'];
          let service_app_download_url = data.data[0]['service_app_download_url'];
    
          setCookie("balance", accountBalance, 30);
          setCookie("cbalance", accountCommissionBalance, 30);
          setCookie("wbalance", accountWinningBalance, 30);
          setCookie("minrecharge", service_min_recharge, 30);
          setCookie("minwithdraw", service_min_withdraw, 30);
          setCookie("rechargeoptions", service_recharge_option, 30);
          setCookie("telegramURL", service_telegram_url, 30);
          setCookie("appDownloadURL", service_app_download_url, 30);
          setConstants({...pageConst, accountBalance: accountBalance});

          updateRecordList(data.investmentList);
  
          if(service_app_status=="OFF"){
            navigate('/um', { replace: true });
          }
        }else if(data.status_code=="account_error"){
          if(deleteCookie(1)){
            navigate('/LG', { replace: true });
          }
        }
        
      } catch (error) {
        setLoadingShow(false);
      }
    };

    if(!isLoadingShow){
      fecthApiData(API_ACCESS_URL+"request-account-info.php?USER_ID="+getCookie("uid")+"&SECRET_KEY="+getCookie("secret")+"&APP_VERSION=3");
    }

  }

  useEffect(() => {
    if(getCookie("uid")){
      getAccountInfo();
    }else{
      redirectTo(LOGIN_REDIRECT_URL);
    }
  }, []);

  return (
    <div className="v-center">
      <div className="h-100vh res-wth ovf-scrl-y bg-tar-black hide-sb">

        <div className='w-100 v-center cl-white mg-t-30'>Comming Soon</div>

        <BottomNavbar activeBar="prize"/>
      </div>
    </div>
  );
}

export default Prizes;