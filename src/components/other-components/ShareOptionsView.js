import React from 'react'
import { WEBSITE_NAME,copyText,generateReferalURL } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

const ShareOptionsView = ({updateState}) => {

    const openSocialMedia = (data) => {
        let share_url = "";
        let message = "Create account with "+WEBSITE_NAME+" ";

        if(data=="TELEGRAM"){
            share_url = "https://telegram.me/share/url?text="+message+"&url="+generateReferalURL(getCookie("uid"));
        }else if(data=="WHATSAPP"){
            share_url = "https://api.whatsapp.com/send?text="+message+generateReferalURL(getCookie("uid"));
        }else if(data=="FACEBOOK"){
          share_url = "https://www.facebook.com/sharer/sharer.php?u="+generateReferalURL(getCookie("uid"));
        }

        window.open(share_url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400");
    }
  
    const copyTxtNow = () =>{
        copyText(generateReferalURL(getCookie("uid")));
        updateState(true,"Invite URL Copied!");
    }      

    return (
      <div className="row-view mg-t-15">
            
        <div onClick={() => openSocialMedia('WHATSAPP')}>
          <img src={require("../icons/whatsapp_icon.png")} className="h-40-p" alt="icon" />
        </div>

        <div className='mg-l-10' onClick={() => openSocialMedia('FACEBOOK')}>
          <img src={require("../icons/facebook_icon.png")} className="h-40-p" alt="icon" />
        </div>

        <div className='mg-l-10' onClick={() => openSocialMedia('TELEGRAM')}>
          <img src={require("../icons/telegram_fill_icon.png")} className="h-40-p" alt="icon" />
        </div>

        <div className='mg-l-10' onClick={() => copyTxtNow()}>
          <img src={require("../icons/copy_fill_icon.png")} className="h-40-p" alt="icon" />
        </div>
        

      </div>
    );
};

export default ShareOptionsView;