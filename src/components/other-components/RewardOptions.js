import React from 'react'
import { Link } from 'react-router-dom';

function RewardOptions(){
    return (
        <div className="g-v-5 w-100 mg-t-10">
            <Link className="txt-deco-n col-view a-center" to={"/recharge"}>
              <img className='h-w-45' src={require('../icons/nav_recharge_icon.png')} />
              <span className='ft-sz-12 mg-t-5 cl-white'>Deposit</span>
            </Link>

            <Link className="txt-deco-n col-view a-center" to={"/withdraw?M=B"}>
              <img className='h-w-45' src={require('../icons/nav_withdraw_icon.png')} />
              <span className='ft-sz-12 mg-t-5 cl-white'>Withdraw</span>
            </Link>

            <Link className="txt-deco-n col-view a-center" to={"/help-support"}>
              <img className='h-w-45' src={require('../icons/nav_help_icon.png')} />
              <span className='ft-sz-12 mg-t-5 cl-white'>Help</span>
            </Link>

            <Link className="txt-deco-n col-view a-center" to={"/transactions"}>
              <img className='h-w-45' src={require('../icons/nav_withdraw_icon.png')} />
              <span className='ft-sz-12 mg-t-5 cl-white'>Transactions</span>
            </Link>

            <Link className="txt-deco-n col-view a-center" to={"/share"}>
              <img className='h-w-45' src={require('../icons/nav_share_icon.png')} />
              <span className='ft-sz-12 mg-t-5 cl-white'>Share</span>
            </Link>
        </div>
    );
}

export default RewardOptions;