import React, {useEffect,useState} from 'react'
import { Link } from 'react-router-dom';
import '../../MainStyle.css';

const LoadingDialog = ({intentData}) => {

    return (
        <div className={`ps-fx ps-tp ps-btm h-100vh res-wth z-i--100 bg-l-black v-center ${intentData.isLoadingShow ? 'activeDialog' : ''}`}>
            <div className='h-w-45 br-50 bg-blue v-center'>
              <img className='smpl-btn-l spin-anim' src={require('../icons/loader-icon.png')} />
            </div>
        </div>
    );
};

export default LoadingDialog;