import React, { useEffect, useState } from 'react'
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import '../../MainStyle.css';
import { API_ACCESS_URL,getURLParam } from '../modals/Constants';
import { Link,useNavigate } from 'react-router-dom';
import { getCookie, setCookie } from '../modals/Cookie';

function Withdraw(){
    const navigate = useNavigate();
    const [isInputValCorrect, setInValCorrect] = useState(false);
    const [pageConst, setConstants] = useState({
        pageTitle: "Withdraw",
        isLoadingShow: false,
        availBalance: 0,
        inWithdrawAmount: "",
        withdrawlMode: "W",
        toastDialogShow: false,
        toastMessage: "",
        recordList: [],
    });

    const topBarClickAction = (data) =>{
      if(data=="multiBtn1"){
        navigate('/withdraw', { replace: false });
      }else if(data=="multiBtn2"){
        navigate('/withdrawrecords', { replace: false });
      }
    }

    const updateLoadingStatus = (data) => {
        setConstants(previousState => {
            return { ...previousState, isLoadingShow: data }
        });
    }

    const updateToastDialogState = (data,msg) => {
      setConstants(previousState => {
        return { ...previousState, toastDialogShow: data }
      });

      setConstants(previousState => {
        return { ...previousState, toastMessage: msg }
      });
    }

    const onInputValChange = (source,data) =>{

      if(source=="withdrawamount"){

        if(data.indexOf(".")==-1){

          if(Number(data) >= getCookie("minwithdraw")){
            setInValCorrect(true);
          }else{
            setInValCorrect(false);
          }
  
          setConstants(previousState => {
            return { ...previousState, inWithdrawAmount: data }
          });
        }
        
      }
    }

    const updatePrimaryCard = (data, available) => {
      let tempData = [];

      if(available){
        tempData.push(
          <Link key={0} className="txt-deco-n cl-black col-view mg-t-10" to={"/bankcards?M=bank"}>
            <span className='ft-sz-17'>ACC: {data[0]['c_bank_account']}</span>
            <span className='ft-sz-17 mg-t-5'>{data[0]['c_bank_ifsc_code']=="none" ? 'Method: UPI' : 'IFSC: '+data[0]['c_bank_ifsc_code']}</span>
          </Link>)
      }else{
        tempData.push(
          <Link key={0} className="txt-deco-n txt-a-center cl-black col-view mg-t-10" to={"/bankcards?M=bank"}>
              <span className='ft-sz-12'>Add UPI, Bank Account to get withdrawals.</span>
              <span className='ft-sz-14 txt-deco-u mg-t-10'>Click here to add.</span>
          </Link>)
      }

      updateLoadingStatus(false);

      setConstants(previousState => {
          return { ...previousState, recordList: tempData }
      });
    }

    function getPrimarBankCard(){
        const fecthApiData = async (url) => {
            try {
              const res = await fetch(url);
              const data = await res.json();

              if(data.status_code=="success"){
                updatePrimaryCard(data.data, true);
              }else{
                updatePrimaryCard(null, false);
              }
            } catch (error) {
              console.log(error);
            }
        };

        fecthApiData(API_ACCESS_URL+"load-primary-bankcard.php?USER_ID="+getCookie("uid"));
    }

    const withdrawBalance = () =>{
      const fecthApiData = async (url) => {
        try {
          const res = await fetch(url);
          const data = await res.json();
          updateLoadingStatus(false);

          if(data.status_code=="insufficient_balance"){
            updateToastDialogState(true,"Insufficient Balance! Please try again!");
          }else if(data.status_code=="no_premium"){
            updateToastDialogState(true,"You need to recharge first ! Please try again!");
          }else if(data.status_code=="primary_bankcard_error"){
            updateToastDialogState(true,"Makesure to create a bankcard!");
          }else if(data.status_code=="min_refer_error"){
            updateToastDialogState(true,"Please complete atleast 2 active refers!");
          }else if(data.status_code=="success"){
            setCookie("wbalance", data.account_balance, 30);
            updateToastDialogState(true,"Withdraw Successful!");
          }

          
        } catch (error) {
          updateLoadingStatus(false);
          console.log(error);
        }
      };

      if(isInputValCorrect && pageConst.isLoadingShow==false){
        updateLoadingStatus(true);
        fecthApiData(API_ACCESS_URL+"request-withdrawl.php?USER_ID="+getCookie("uid")+"&WITHDRAW_AMOUNT="+pageConst.inWithdrawAmount+"&WITHDRAW_METHOD="+pageConst.withdrawlMode);
      }
      
    }

    useEffect(() => {
      if(getURLParam('M')!=null && getURLParam('M')!=""){
        let withdrawMode = getURLParam('M');
        let availBalance = 0;

        if(withdrawMode=="W"){
          availBalance = getCookie("wbalance");
        }else if(withdrawMode=="C"){
          availBalance = getCookie("cbalance");
        }else if(withdrawMode=="B"){
          availBalance = getCookie("balance");
        }

        getPrimarBankCard();

        setConstants({...pageConst, withdrawlMode: withdrawMode});

        setConstants(previousState => {
          return { ...previousState, availBalance: availBalance }
        });
      }
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-tar-black">
          <TopBar intentData={pageConst} multiBtn={true} multiBtn1="" multiBtn2="Records" updateState={topBarClickAction}/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="col-view pd-10-20 mg-t-45">

            <div className='col-view w-100 v-center mg-t-10'>
             <span className='cl-white'>Balance</span>
             <span className='ft-sz-25 ft-wgt-b cl-white mg-t-10'>₹{pageConst.availBalance}</span>
            </div>

            <div className='pr-v w-100 br-5 mg-t-15 bg-l-blue br-a-l-blue'>
              <div className='w-100-p pd-2-8 br-5 ft-sz-16 ft-wgt-b cl-white bg-blue'>SELECTED</div>

              <div className='col-view pd-15'>
                {pageConst.recordList}
              </div>
            </div>

            <div className='col-view mg-t-20'>
              <span className='ft-sz-18 ft-wgt-b cl-l-white'>Amount</span>
              <div className='row-view br-b-grey'>
                <span className='ft-sz-32 cl-l-white'>₹</span>
                <input type="number" className='cutm-inp ft-sz-38 h-60-p inp-ph-l-color cl-white' value={pageConst.inWithdrawAmount} placeholder={`${getCookie("minwithdraw")} ~ 50000`} onChange={e => onInputValChange('withdrawamount',e.target.value)}></input>
              </div>
            </div>
            
            <div className={`w-100 mg-t-30 h-50-p ft-sz-20 v-center br-10 cl-white ${isInputValCorrect ? 'bg-grad-drk-blue-180' : 'bg-grey-2'}`} onClick={() => withdrawBalance()}>
              <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
              <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Withdrawal</span>
            </div>

          </div>
        </div>
      </div>
    );
}

export default Withdraw;