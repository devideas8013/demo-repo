import React, {useEffect,useState} from 'react'
import { Link } from 'react-router-dom';
import { setCookie,getCookie } from '../modals/Cookie';
import { API_ACCESS_URL } from '../modals/Constants';

const PurchaseDialog = ({intentData,updateState,toastUpdate}) => {

    const [dialogData, setDialogData] = useState({
        loadingStatus: false,
    });

    const resetDialogData = (e) => {
        if(e!=null && e.target.className=="ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg activeDialog"){
            setDialogData(previousState => {
                return { ...previousState, loadingStatus: false }
            });
        }
    }

    const confirmPurchase = (intentData) => {

        setDialogData(previousState => {
            return { ...previousState, loadingStatus: true }
        });

        const fecthApiData = async (url) => {
        
            try {
              const res = await fetch(url);
              const data = await res.json();

              updateState(null,'dismiss');
              
              if(data.status_code=="success"){
                setCookie("balance", data.account_balance, 30);
                toastUpdate(true, "Purchase Successful");
              }else if(data.status_code=="balance_error"){
                toastUpdate(true, "Your balance is insufficient ! Please recharge now !");
              }else if(data.status_code=="account_error"){
                toastUpdate(true, "Account Problem ! Please try again!");
              }else if(data.status_code=="already_purchased"){
                toastUpdate(true, "You've already bought this item!");
              }else if(data.status_code=="auth_error"){
                toastUpdate(true, "Authentication Error! Please login again!");
              }else{
                toastUpdate(true, "There was a technical issue! Please try again!");
              }

              setDialogData(previousState => {
                return { ...previousState, loadingStatus: false }
              });
              
            } catch (error) {
              updateState(null,'dismiss');
            }
        };

        fecthApiData(API_ACCESS_URL+"request-purchase.php?USER_ID="+getCookie("uid")+"&INVEST_ID="+intentData.purchaseDialogInvestId);
    }

    return (
        <div className={`ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg ${intentData.purchaseDialogShow ? 'activeDialog' : ''}`} onClick={(e)=>{updateState(e,'false','','');resetDialogData(e)}}>
              <div className={`dlg-c ps-fx ps-btm z-i-1000 res-wth bg-white pd-15 ${intentData.purchaseDialogTheme}`}>
                <p className='ft-sz-23 w-100 mg-b-10 dlg-thm-txt txt-a-center'>{intentData.purchaseDialogTitle}</p>

                {/* <div className='row-view sb-view avail-bl-v pd-5 w-100'>
                    <p className='m-lft-5 ft-sz-25'>₹{getCookie("balance")}</p>
                    <Link className="rc-btn txt-deco-n bg-grad-drk-blue-180" to={"/recharge"}>Recharge</Link>
                </div> */}

                <div className='ft-sz-14 mg-t-20'>{intentData.purchaseDialogDetails}</div>

                <div className='row-view sb-view w-100 mg-t-10'>
                    <div>Daily income:</div>

                    <div>₹{getCookie("serviceDailyBonus")}</div>
                </div>


                <div className='row-view sb-view w-100 mg-t-15'>
                    <div className='ft-sz-20'>Total Price:</div>

                    <div className='ft-sz-20'>₹{intentData.purchaseDialogPrice}</div>
                </div>

                <div className='h-45-p ft-sz-18 pd-10 br-10 cl-white mg-t-30 v-center bg-grad-drk-blue-180 w-100' onClick={()=>confirmPurchase(intentData)}>
                    <img className={`smpl-btn-l spin-anim ${dialogData.loadingStatus==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                    <span className={`${dialogData.loadingStatus==true ? 'hide-v' : ''}`}>Buy Now</span>
                </div>
              </div>
        </div>
    );
};

export default PurchaseDialog;