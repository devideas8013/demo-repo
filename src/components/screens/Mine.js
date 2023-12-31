import React,{useEffect} from "react";
import { Link,useNavigate } from 'react-router-dom';
import BottomNavbar from '../other-components/BottomNavbar';
import { setCookie,getCookie,deleteCookie } from '../modals/Cookie';
import { openNewPage,redirectTo } from '../modals/Constants';

function My() {
  const navigate = useNavigate();
  const signOutAccount = () =>{
    if(deleteCookie(1)){
      navigate('/LG', { replace: true });
    }
  }

  useEffect(() => {
    if(!getCookie("uid")){
      navigate('/LG', { replace: true });
    }
  }, []);

  return (
    <div className="v-center">
      <div className="h-100vh res-wth ovf-scrl-y bg-cus-color hide-sb">

        <BottomNavbar activeBar="my"/>

        <div className="flt-view w-100">
          <div className="pr-v w-100 h-60-p bg-cus-color">
            <div className="flt-view-details pd-10">
              <div className="row-view">
                <img
                  src={require("../icons/avatar_icon.png")}
                  className="h-w-45"
                  alt="icon"
                />

                <div className="col-view mg-l-15">
                  <span className="cl-white ft-sz-18">{getCookie("uname") != "" ? getCookie("uname") : getCookie("mobile")}</span>
                  <span className="cl-white ft-sz-12 mg-t-5">
                    Mob: {getCookie("mobile")}, ID: {getCookie("uid")}
                  </span>
                </div>
              </div>

              <div className='row-view w-100 mg-t-15 sb-view'>
                <div className='col-view a-center w-100'>
                  <div className='cl-white ft-sz-16'>₹{getCookie("balance")}</div>
                  <div className='ft-sz-13 cl-white mg-t-10'>Balance</div>
                </div>

                <div className='col-view a-center w-100'>
                  <div className='cl-white ft-sz-16'>₹{getCookie("wbalance")}</div>
                  <div className='ft-sz-13 cl-white mg-t-10'>Earnings</div>
                </div>

                <div className='col-view a-center w-100'>
                  <div className='cl-white ft-sz-16'>{getCookie("myRank")}</div>
                  <div className='ft-sz-13 cl-white v-center mg-t-10'>
                    <img className='h-w-20' src={require("../icons/nav_rank_icon.png")} />
                    <span className='mg-l-5'>Rank</span>
                  </div>
                </div>
             </div>
            </div>
          </div>
        </div>

        <div className="mg-t-5em">

        <div className="col-view bg-cus-color pd-10">
            
            <Link className="row-view cl-black pd-15 sb-view txt-deco-n" to={"/recharge"}>
            <div className="v-center">
              <img src={require("../icons/finantial_icon.png")} className="h-w-22" alt="icon" />
              <span className="cl-white mg-l-10">Deposit</span>
            </div>
            
            <img
              src={require("../icons/arrowRight_icon.png")}
              className="h-15-p"
              alt="icon" />
            </Link>

            <span className='line-hv-grey bg-l-white'></span>

            <Link className="row-view cl-black pd-15 sb-view txt-deco-n" to={"/withdraw?M=B"}>
            <div className="v-center">
              <img src={require("../icons/finantial_icon.png")} className="h-w-22" alt="icon" />
              <span className="cl-white mg-l-10">Withdrawl</span>
            </div>
            
            <img
              src={require("../icons/arrowRight_icon.png")}
              className="h-15-p"
              alt="icon"/>
            </Link>

          </div>

          <div className="col-view bg-cus-color pd-10">
            
            <Link className="row-view cl-black pd-15 sb-view txt-deco-n" to={"/transactions"}>
            <div className="v-center">
              <img src={require("../icons/history_icon.png")} className="h-w-22" alt="icon" />
              <span className="cl-white mg-l-10">Transactions</span>
            </div>
            
            <img
              src={require("../icons/arrowRight_icon.png")}
              className="h-15-p"
              alt="icon" />
            </Link>

            <span className='line-hv-grey bg-l-white'></span>

            <Link className="row-view cl-black pd-15 sb-view txt-deco-n" to={"/change-password"}>
            <div className="v-center">
              <img src={require("../icons/unlock_icon.png")} className="h-w-22" alt="icon" />
              <span className="cl-white mg-l-10">Change Password</span>
            </div>
            
            <img
              src={require("../icons/arrowRight_icon.png")}
              className="h-15-p"
              alt="icon"/>
            </Link>

          </div>

          <div className="col-view bg-cus-color pd-10">
            
            <div className="row-view pd-15 sb-view" onClick={() => openNewPage(getCookie("appDownloadURL"))}>
            <div className="v-center">
              <img src={require("../icons/download_icon.png")} className="h-w-28" alt="icon" />
              <span className="cl-white mg-l-10">Download</span>
            </div>
            
            <img
              src={require("../icons/arrowRight_icon.png")}
              className="h-15-p"
              alt="icon"
            />
            </div>

            <span className='line-hv-grey bg-l-white'></span>

            <div className="row-view pd-15 sb-view" onClick={() => openNewPage(getCookie("telegramURL"))}>
            <div className="v-center">
              <img src={require("../icons/telegram_icon.png")} className="h-w-28" alt="icon" />
              <span className="cl-white mg-l-10">Follow us</span>
            </div>
            
            <img
              src={require("../icons/arrowRight_icon.png")}
              className="h-15-p"
              alt="icon"
            />
            </div>

            <span className='line-hv-grey bg-l-white'></span>

            <Link className="row-view cl-black pd-15 sb-view txt-deco-n" to={"/help-support"}>
              <div className="v-center">
                <img src={require("../icons/support_icon.png")} className="h-w-22" alt="icon" />
                <span className="cl-white mg-l-10">Help & Support</span>
              </div>
            
              <img src={require("../icons/arrowRight_icon.png")} className="h-15-p"
              alt="icon"/>
            </Link>

            <span className='line-hv-grey bg-l-white'></span>

            <Link className="row-view cl-black pd-15 sb-view txt-deco-n mg-b-70">
              <div className="v-center">
                <img src={require("../icons/support_icon.png")} className="h-w-22" alt="icon" />
                <div className="col-view cl-white mg-l-10">
                  <span>Mail us at: </span>
                  <span className="ft-sz-13 mg-t-5">adsexchanges61@gmail.com</span>
                </div>
              </div>
            
              {/* <img src={require("../icons/arrowRight_icon.png")} className="h-15-p"
              alt="icon"/> */}
            </Link>

          </div>

          <div className="col-view bg-cus-color v-center pd-30 mg-t-15 mg-b-70 hide-v">
            <div className="cl-white mg-t-15 txt-deco-u pd-10-15" onClick={() => signOutAccount()}>Sign Out</div>
          </div>

        </div>

      </div>
    </div>
  );
}

export default My;
