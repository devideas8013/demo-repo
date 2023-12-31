import React, {useEffect} from 'react'
import { Link,useNavigate } from 'react-router-dom';
import { openNewPage,redirectTo } from '../modals/Constants';
import { getCookie,deleteCookie } from '../modals/Cookie';

const BottomNavbar = ({activeBar}) => {
  const navigate = useNavigate();
  const signOutAccount = () =>{
    if(deleteCookie(1)){
      navigate('/LG', { replace: true });
    }
  }

    return (
      <div className="row-view">

        <nav className='btm-navbar ps-fx ps-btm z-100 bg-cus-color res-wth'>
            
          <Link className={`nav-col`} to={"/home"}>
            <img className={`${activeBar!="home" ? 'hide-v' : ''}`} src={require('../icons/home_icon_active.png')} alt="home" />
            <img className={`${activeBar=="home" ? 'hide-v' : ''}`} src={require('../icons/home_icon.png')} alt="home" />
            <p className='cl-white'>Home</p>
          </Link>

          {/* <Link className={`nav-col`} to={"/invest"}>
            <img className={`${activeBar!="invest" ? 'hide-v' : ''}`} src={require('../icons/invest_icon_active.png')} alt="home" />
            <img className={`${activeBar=="invest" ? 'hide-v' : ''}`} src={require('../icons/invest_icon.png')} alt="home" />
            <p className='cl-white'>Invest</p>
          </Link> */}

          <Link className={`nav-col`} to={"/shopping"}>
            <img src={require('../icons/shopping_icon.png')} alt="home" />
            <p className='cl-white'>Shopping</p>
          </Link>
          
          <div className={`nav-col`} onClick={() => signOutAccount()}>
            <img src={require('../icons/signout_icon.png')} alt="home" />
            <p className='cl-white'>Signout</p>
          </div>

          <Link className={`nav-col ${activeBar=="team" ? 'opac-f' : ''}`} to={"/team"}>
            <img className={`${activeBar!="team" ? 'hide-v' : ''}`} src={require('../icons/team_icon_active.png')} alt="home" />
            <img className={`${activeBar=="team" ? 'hide-v' : ''}`} src={require('../icons/team_icon.png')} alt="home" />
            <p className='cl-white'>Team</p>
          </Link>

          <Link className={`nav-col ${activeBar=="mine" ? 'opac-f' : ''}`} to={"/mine"}>
            <img className={`${activeBar!="my" ? 'hide-v' : ''}`} src={require('../icons/mine_icon_active.png')} alt="home" />
            <img className={`${activeBar=="my" ? 'hide-v' : ''}`} src={require('../icons/mine_icon.png')} alt="home" />
            <p className='cl-white'>Mine</p>
          </Link>

        </nav>

      </div>
    );
};

export default BottomNavbar;