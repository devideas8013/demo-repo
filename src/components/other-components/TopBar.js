import React from 'react'
import { Link,useNavigate  } from 'react-router-dom';

const GameTopBar = ({intentData,multiBtn,multiBtn1,multiBtn2,updateState}) => {
  const navigate = useNavigate();
  
    return (
        <nav className="ps-fx ps-tp h-45-p z-i-110 bg-grad-drk-blue-180 cl-white pd-10 row-view sb-view res-wth">
          
          <div className='row-view a-center'>
           <Link className={`${multiBtn1 =='' ? '' : 'hide-v'}`} onClick={() => navigate(-1)}>
             <img className='h-18-p' src={require('../icons/arrow_left_icon.png')} alt="icon" />
           </Link>

           <span className={`${multiBtn ? (multiBtn1 =='' ? 'hide-v' : '') : ''}`} onClick={()=>updateState("multiBtn1")}>{multiBtn1}</span>

           <span className={`mg-l-18 ${multiBtn ? 'hide-v' : ''}`}>{intentData.pageTitle}</span>
          </div>

          <span className={`ft-sz-18 ${multiBtn ? '' : 'hide-v'}`}>{intentData.pageTitle}</span>

          <span className={`ft-sz-18 ${multiBtn ? '' : 'hide-v'}`} onClick={()=>updateState("multiBtn2")}>{multiBtn2}</span>

        </nav>
    );
};

export default GameTopBar;