import React, {useEffect,useState} from 'react'
import { Link } from 'react-router-dom';
import '../../MainStyle.css';

const toastDialog = ({intentData,updateState}) => {

    return (
        <div className={`ps-fx h-100vh res-wth z-i--100 v-center ${intentData.toastDialogShow ? 'activeDialog' : ''}`} onClick={()=>updateState(false,"")}>
            <div className='tst-content ft-sz-18'>
                {intentData.toastMessage}
                <div className='w-100 pd-5 v-center mg-t-15 cl-blue br-t-black'>OK</div>
            </div>
        </div>
    );
};

export default toastDialog;