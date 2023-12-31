import React, {useEffect,useState} from 'react'
import { Link } from 'react-router-dom';
import YouTube from 'react-youtube';
import { setCookie,getCookie } from '../modals/Cookie';
import { API_ACCESS_URL } from '../modals/Constants';

const ClaimDailyAdsDialog = ({intentData,updateState,toastUpdate}) => {

    const [dialogData, setDialogData] = useState({
        loadingStatus: false,
        embedVideoState: false,
    });

    const opts = {
      height: '220',
      width: window.innerWidth,
      playerVars: {
        autoplay: 0,
        showinfo: 0,
        controls: 0,
      },
    };

    const statusVideoEnd = () =>{
      setDialogData(previousState => {
        return { ...previousState, embedVideoState: true }
      });
    }

    const resetDialogData = (e) => {
        if(e!=null && e.target.className=="ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg activeDialog"){
            setDialogData(previousState => {
                return { ...previousState, loadingStatus: false }
            });
        }
    }

    const confirmPurchase = () => {

      if(dialogData.embedVideoState){
        const requestAPI = async (url) => {
          try {
            const res = await fetch(url);
            const data = await res.json();
  
            updateState(null,'dismiss');
            setDialogData(previousState => {
              return { ...previousState, loadingStatus: false }
            });

            if(data.status_code=="already_claimed"){
              toastUpdate(true,"Sorry, You've already claimed this bonus !");
            }else if(data.status_code=="success"){
              toastUpdate(true,"Bonus Claimed!");
            }else if(data.status_code=="not_eligible"){
              toastUpdate(true,"Sorry, You're not eligible!");
            }else if(data.status_code=="not_available"){
              toastUpdate(true,"Sorry, This bonus is not available today!");
            }else{
              toastUpdate(true,"Something went wrong! Please try again!");
            }
  
          } catch (error) {
            setDialogData(previousState => {
              return { ...previousState, loadingStatus: false }
            });
            toastUpdate(true,"There was a technical issue! Please try again!");
          }
        };
  
        setDialogData(previousState => {
          return { ...previousState, loadingStatus: true }
        });
        requestAPI(API_ACCESS_URL+"claim-daily-bonus.php?USER_ID="+getCookie("uid"));
      }
      
    }

    return (
        <div className={`ps-fx h-100vh res-wth z-i--100 bg-l-black bt-dlg ${intentData.dailyAdsDialogShow ? 'activeDialog' : ''}`} onClick={(e)=>{updateState(e,'false','','');resetDialogData(e)}}>
              <div className={`dlg-c ps-fx ps-btm z-i-1000 res-wth bg-white ${intentData.purchaseDialogTheme}`}>
                <p className='ft-sz-23 w-100 mg-t-10 dlg-thm-txt txt-a-center'>Watch & Claim Bonus</p>

                <div className='ft-sz-14 pd-10 mg-t-20'>Watch & Claim your daily ads bonus. Remember: Everyday you can claim daily ads income after watching videos.</div>

                <YouTube videoId={getCookie("serviceYtVideoId")} opts={opts} onEnd={statusVideoEnd} className='res-wth v-center mg-t-15'/>

                <div className={`h-45-p ft-sz-18 pd-10 br-10 cl-white mg-t-30 v-center w-100 mg-b-10 ${dialogData.embedVideoState ? 'bg-grad-drk-blue-180' : 'bg-l-black'}`} onClick={()=>confirmPurchase()}>
                    <img className={`smpl-btn-l spin-anim ${dialogData.loadingStatus==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                    <span className={`${dialogData.loadingStatus==true ? 'hide-v' : ''}`}>Claim Now</span>
                </div>
              </div>
        </div>
    );
};

export default ClaimDailyAdsDialog;