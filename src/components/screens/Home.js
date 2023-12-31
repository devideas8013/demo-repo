import React,{useState, useEffect, useRef} from 'react'
import { Link,useNavigate } from 'react-router-dom';
import PurchaseDialog from '../dialogs/PurchaseDialog';
import ClaimDailyAdsDialog from '../dialogs/ClaimDailyAdsDialog';
import ToastDialog from '../dialogs/ToastDialog';
import RewardOptions from '../other-components/RewardOptions';
import { setCookie,getCookie,deleteCookie } from '../modals/Cookie';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,redirectTo } from '../modals/Constants';
import BottomNavbar from '../other-components/BottomNavbar';

function Home(){
  const navigate = useNavigate();
  const [slideShowIndex, setSlideShowIndex] = useState(0);
  const delay = 2500;

  const timeoutRef = useRef(null);

  function resetTimeout() {
    if (timeoutRef.current) {
      clearTimeout(timeoutRef.current);
    }
  }

  const [pageConst, setConstants] = useState({
    accountBalance: 0,
    purchaseDialogInvestId: "",
    purchaseDialogTitle: "",
    purchaseDialogDetails: "",
    purchaseDialogPrice: "",
    scrollingNotice: "",
    dailyAdsDialogShow: false,
    purchaseDialogShow: false,
    purchaseDialogTheme: "dlg-thm-green",
    toastMessage: "",
    toastDialogShow: false,
    sliderImages: [],
    recordList: [],
  });

  const updateToastDialogState = (data,msg) => {
    setConstants(previousState => {
      return { ...previousState, toastDialogShow: data }
    });

    setConstants(previousState => {
      return { ...previousState, toastMessage: msg }
    });
  }

  const updateDailyBonusDialogState = (e,data) => {

    if(getCookie("isDailyAdsAvailable")=="false"){
      return;
    }

    if(e!=null && e.target.className=="ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg activeDialog"){
      setConstants(previousState => {
        return { ...previousState, dailyAdsDialogShow: false }
      });
    }else if(data=="true"){

      if(getCookie("dailyBonusClaimed")!="true"){
        setConstants(previousState => {
          return { ...previousState, dailyAdsDialogShow: true }
        });
      }
      
    }else if(data=="dismiss"){
      setConstants(previousState => {
        return { ...previousState, dailyAdsDialogShow: false }
      });
    }
  }

  const updatePurchaseDialogState = (e,data,invest_id,title,details,total_price,is_available) => {

    if(e!=null && e.target.className=="ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg activeDialog"){
      setConstants(previousState => {
        return { ...previousState, purchaseDialogShow: false }
      });
    }else if(data=="true" && is_available=="false"){

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
        return { ...previousState, purchaseDialogPrice: total_price }
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
          <div key={i} className='col-view w-100 pd-10 br-10 mg-t-15 bg-white' onClick={() => updatePurchaseDialogState(null,"true",data[i]['invest_id'],data[i]['invest_name'],data[i]['invest_details'],data[i]['invest_price'],data[i]['invest_available'])}>
            <div className='row-view w-100 sb-view'>
              <div className='ft-sz-18'>{data[i]['invest_name']}</div>

              <div className={`row-view v-center ${data[i]['invest_available']!='true' ? 'hide-v' : ''}`}>
                <img src={require("../icons/check_icon.png")} className="h-13-p" alt="icon" />
                <label className='mg-l-5 ft-sz-13'>Active</label>
              </div>
            </div>

            <div className='row-view w-100 a-start sb-view mg-t-10'>
             <div className='row-view a-start'>
              <img className='w-100-p br-5' src={data[i]['invest_img_url']} />

              <div className='col-view mg-l-10'>
                {/* <div className='ft-sz-14'>Daily Income</div>
                <div className='cl-orange ft-sz-16'>₹{data[i]['invest_daily_income']}</div> */}
                <div className='ft-sz-16 mg-t-10'>Price: ₹{data[i]['invest_price']}</div>
              </div>
             </div>

            </div>
          </div>)
      };

      setConstants(previousState => {
          return { ...previousState, recordList: tempData }
      });
  }

  const updateSliderImagesArr = (data) =>{
    let tempData = [];
    const array = data.split(',');

    for (let i = 0; i < array.length; i++) {
      const image = array[i];
      tempData.push(image);
    }

    if(tempData.length > 0){
      setConstants(previousState => {
        return { ...previousState, sliderImages: tempData }
      });
    }
  }

  function getAccountInfo(){

    const fecthApiData = async (url) => {
      
      try {
        const res = await fetch(url);
        const data = await res.json();

        if(data.status_code=="success"){
          let accountMobileNum = data.data[0]['account_mobile_num'];
          let accountBalance = data.data[0]['account_balance'];
          let accountWinningBalance = data.data[0]['account_w_balance'];
          let accountCoinsBalance = data.data[0]['account_c_balance'];
          let accountReferalIncome = data.data[0]['account_referal_income'];
          let accountDailylevelIncome = data.data[0]['account_dailylevel_income'];
          let accountRewardRankIncome = data.data[0]['account_rewardrank_income'];
          let accountDailyBonusIncome = data.data[0]['account_dailybonus_income'];
          let accountSpecialRewardIncome = data.data[0]['account_special_reward_income'];
          let accountDailyBonusClaimed = data.data[0]['account_dailybonus_claimed'];
          let accountRoyaltyIncome = data.data[0]['account_royalty_income'];
          let accountAutoPoolIncome = data.data[0]['account_autopool_income'];
          let accountMyRank = data.data[0]['account_my_rank'];
          let service_app_status = data.data[0]['service_app_status'];
          let service_min_recharge = data.data[0]['service_min_recharge'];
          let service_min_withdraw = data.data[0]['service_min_withdraw'];
          let service_recharge_option = data.data[0]['service_recharge_option'];
          let service_telegram_url = data.data[0]['service_telegram_url'];
          let service_app_download_url = data.data[0]['service_app_download_url'];
          let service_daily_bonus = data.data[0]['service_daily_bonus'];
          let service_autopool_bonus = data.data[0]['service_autopool_bonus'];
          let service_yt_video_id = data.data[0]['service_yt_video_id'];
          let scrolling_notice = data.data[0]['scrolling_notice'];
          let isDailyAdsAvailable = data.data[0]['is_daily_ads_available'];
    
          setCookie("balance", accountBalance, 30);
          setCookie("cbalance", accountCoinsBalance, 30);
          setCookie("wbalance", accountWinningBalance, 30);
          setCookie("referincome", accountReferalIncome, 30);
          setCookie("dailylevelincome", accountDailylevelIncome, 30);
          setCookie("rewardRankincome", accountRewardRankIncome, 30);
          setCookie("dailyBonusincome", accountDailyBonusIncome, 30);
          setCookie("specialrewardincome", accountSpecialRewardIncome, 30);
          setCookie("royaltyncome", accountRoyaltyIncome, 30);
          setCookie("autoPoolincome", accountAutoPoolIncome, 30);
          setCookie("dailyBonusClaimed", accountDailyBonusClaimed, 30);
          setCookie("myRank", accountMyRank, 30);

          setCookie("minrecharge", service_min_recharge, 30);
          setCookie("minwithdraw", service_min_withdraw, 30);
          setCookie("rechargeoptions", service_recharge_option, 30);
          setCookie("telegramURL", service_telegram_url, 30);
          setCookie("appDownloadURL", service_app_download_url, 30);
          setCookie("serviceDailyBonus", service_daily_bonus, 30);
          setCookie("serviceAutopoolBonus", service_autopool_bonus, 30);
          setCookie("serviceYtVideoId", service_yt_video_id, 30);
          setCookie("isDailyAdsAvailable", isDailyAdsAvailable, 30);
          setConstants({...pageConst, accountBalance: accountBalance, scrollingNotice: scrolling_notice});

          updateSliderImagesArr(data.data[0]['slider_banner_imgs']);
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
      }
    };


    if(pageConst.recordList.length <= 0 && getCookie("uid")){
      fecthApiData(API_ACCESS_URL+"request-account-info.php?USER_ID="+getCookie("uid")+"&SECRET_KEY="+getCookie("secret")+"&APP_VERSION=3");
    }

  }

  useEffect(() => {
    getAccountInfo();
    resetTimeout();

    if(pageConst.sliderImages.length > 0){
      timeoutRef.current = setTimeout(() =>
        setSlideShowIndex((prevIndex) =>
          prevIndex === pageConst.sliderImages.length - 1 ? 0 : prevIndex + 1
        ),
        delay
      );
    }

    return () => {
      resetTimeout();
    }
  }, [pageConst.sliderImages,slideShowIndex]);


  return (
    <div className="v-center">
      <div className="h-100vh res-wth ovf-scrl-y bg-cus-color hide-sb">
        <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />
        <PurchaseDialog intentData={pageConst} updateState={updatePurchaseDialogState} toastUpdate={updateToastDialogState}/>
        <ClaimDailyAdsDialog intentData={pageConst} updateState={updateDailyBonusDialogState} toastUpdate={updateToastDialogState}/>
        
        <div className={`w-100 slideshow`}>
          <div className="slideshowSlider" style={{ transform: `translate3d(${-slideShowIndex * 100}%, 0, 0)` }}>
            {pageConst.sliderImages.map((img, index) => (
              <div className="slide" key={index}>
                <img className="w-100 obj-f-contain" src={img} />
              </div>
            ))}
          </div>
        </div>

        <RewardOptions />

        <marquee className={`mg-t-15 ${pageConst.scrollingNotice=='none' ? 'hide-v' : ''}`} style={{color: 'red'}}>{pageConst.scrollingNotice}</marquee>

        <div className='col-view pd-10-20 mg-b-150'>

          <div className='row-view sb-view mg-t-15'>
            <div>
              <div className='cl-white'>My Balance</div>
              <div className='cl-white ft-sz-18 mg-t-5'>₹{getCookie("balance")}</div>
            </div>

            <div>
              <img className='h-w-55' src={require("../icons/logo.png")} />
            </div>

            <div className='txt-a-end'>
              <div className='cl-white v-center'>
                <img className='h-w-20' src={require("../icons/nav_rank_icon.png")} />
                <span className='mg-l-5'>My Rank</span>
              </div>
              <div className='cl-white ft-sz-18 mg-t-5'>{getCookie("myRank")}</div>
            </div>
          </div>

          <div className="col-view w-100 mg-t-20">

            <Link className="txt-deco-n row-view a-center" onClick={() => updateDailyBonusDialogState(null,"true")}>
              <img className='h-w-36' src={require('../icons/income_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <div className='row-view v-center mg-l-15'>
                  <div className='col-view'>
                    <span className='ft-sz-16 cl-white'>Daily Ads Income</span>
                    <span className='ft-sz-13 cl-l-white'>Claim daily ads income.</span>
                  </div>
                  <div className={`pd-2-8 br-10 cl-white mg-l-15 bg-grad-lgt-drk-blue ${getCookie("isDailyAdsAvailable")=="false" ? 'hide-v' : ''}`}>{getCookie("dailyBonusClaimed") == "true" ? 'Claimed' : 'claim'}</div>
                </div>
                <span className='ft-sz-16 cl-white'>₹{getCookie("dailyBonusincome")}</span>
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10" to={"/autopoolincome"}>
              <img className='h-w-36' src={require('../icons/income_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <span className='ft-sz-16 cl-white mg-l-15'>Auto Pool Income</span>
                <span className='ft-sz-16 cl-white'>₹{getCookie("autoPoolincome")}</span>
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10" to={"/royaltyincome"}>
              <img className='h-w-36' src={require('../icons/award_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <span className='ft-sz-16 cl-white mg-l-15'>Royalty Income</span>
                <span className='ft-sz-16 cl-white'>₹{getCookie("royaltyncome")}</span>
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10">
              <img className='h-w-36' src={require('../icons/award_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <div className='col-view mg-l-15'>
                  <span className='ft-sz-16 cl-white'>Monthly salary</span>
                  <span className='ft-sz-12 cl-white'>Comming Soon</span>
                </div>
                {/* <span className='ft-sz-16 cl-white'>₹{getCookie("wbalance")}</span> */}
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10" to={"/referal-income"}>
              <img className='h-w-36' src={require('../icons/team_bonus_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <span className='ft-sz-16 cl-white mg-l-15'>Level Refer Income</span>
                <span className='ft-sz-16 cl-white'>₹{getCookie("referincome")}</span>
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10" to={"/level-daily-income"}>
              <img className='h-w-36' src={require('../icons/income_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <span className='ft-sz-16 cl-white mg-l-15'>Level Daily Income</span>
                <span className='ft-sz-16 cl-white'>₹{getCookie("dailylevelincome")}</span>
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10" to={"/rank"}>
              <img className='h-w-36' src={require('../icons/award_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <span className='ft-sz-16 cl-white mg-l-15'>Award Reward</span>
                <span className='ft-sz-16 cl-white'>₹{getCookie("rewardRankincome")}</span>
              </div>
            </Link>

            <Link className="txt-deco-n row-view a-center mg-t-10" to={"/specialbonus"}>
              <img className='h-w-36' src={require('../icons/award_icon.png')} />

              <div className='w-100 row-view sb-view'>
                <span className='ft-sz-16 cl-white mg-l-15'>Special Bonus</span>
                <span className='ft-sz-16 cl-white'>₹{getCookie("specialrewardincome")}</span>
              </div>
            </Link>
          </div>

          <div className='row-view mg-t-25'>
            <img className='h-w-20' src={require("../icons/investment_list_icon.png")} />
            <div className='ft-sz-16 cl-white mg-l-10'>Membership</div>
          </div>

          {pageConst.recordList}
        </div>

        <BottomNavbar activeBar="home"/>
      </div>
    </div>
  );
}

export default Home;