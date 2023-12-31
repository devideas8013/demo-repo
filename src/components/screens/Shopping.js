import React,{useState, useEffect} from 'react'
import { Link,useNavigate } from 'react-router-dom';
import ToastDialog from '../dialogs/ToastDialog';
import { setCookie,getCookie,deleteCookie } from '../modals/Cookie';
import { API_ACCESS_URL,LOGIN_REDIRECT_URL,WEBSITE_URL,redirectTo,copyText } from '../modals/Constants';
import BottomNavbar from '../other-components/BottomNavbar';

function Shopping(){
  const navigate = useNavigate();

  const [pageConst, setConstants] = useState({
    accountBalance: 0,
    toastDialogShow: false,
    toastMessage: "",
    recordList: [],
  });


  useEffect(() => {
    if(getCookie("uid")){
    }else{
      redirectTo(LOGIN_REDIRECT_URL);
    }
  }, []);

  return (
    <div className="v-center">
      <div className="h-100vh res-wth ovf-scrl-y bg-cus-color hide-sb">

        <div className='w-100 col-view v-center pd-15 cl-white'>
          <div className='ft-sz-25'>Shopping</div>
          <div className='ft-sz-16 mg-t-15'>Comming Soon</div>
        </div>

        <BottomNavbar activeBar="shopping"/>
      </div>
    </div>
  );
}

export default Shopping;