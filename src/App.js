import React,{useState} from 'react'
import {BrowserRouter as Router, Routes, Route} from 'react-router-dom';
import './App.css';
import './MainStyle.css';
import Home from './components/screens/Home';
import Invest from './components/screens/Invest';
import Share from './components/screens/Share';
import Rank from './components/screens/Rank';
import Ads from './components/screens/Ads';
import Recharge from './components/screens/Recharge';
import Mine from './components/screens/Mine';
import Team from './components/screens/Team';
import Prizes from './components/screens/Prizes';
import Login from './components/screens/Login';
import Register from './components/screens/Register';
import ForgotPassword from './components/screens/ForgotPassword';
import ChangePassword from './components/screens/ChangePassword';
import HelpSupport from './components/screens/HelpSupport';
import Transactions from './components/screens/Transactions';
import AllBankCards from './components/screens/AllBankCards';
import AddBankCard from './components/screens/AddBankCard';
import Withdraw from './components/screens/Withdraw';
import Shopping from './components/screens/Shopping';
import WithdrawRecords from './components/screens/WithdrawRecords';
import RechargeRecords from './components/screens/RechargeRecords';
import UnderMaintenance from './components/screens/UnderMaintenance';
import DailyAdsIncome from './components/screens/DailyAdsIncome';
import LevelDailyIncome from './components/screens/LevelDailyIncome';
import ReferalIncome from './components/screens/ReferalIncome';
import ViewAllMembers from './components/screens/ViewAllMembers';
import AutopoolIncome from './components/screens/AutopoolIncome';
import RoyaltyIncome from './components/screens/RoyaltyIncome';
import SpecialBonus from './components/screens/SpecialBonus';

function App() {

  return <>
  <Router>

    <Routes>
      <Route path='/home' element={<Home/>} />
      <Route path='/ads' element={<Ads/>} />
      {/* <Route path='/invest' element={<Invest/>} /> */}
      <Route path='/recharge' element={<Recharge/>} />
      <Route path='/team' element={<Team/>} />
      <Route path='/mine' element={<Mine/>} />
      <Route path='/prize' element={<Prizes/>} />
      <Route path='/transactions' element={<Transactions/>} />
      <Route path='/viewallmembers' element={<ViewAllMembers/>} />
      <Route path='/autopoolincome' element={<AutopoolIncome/>} />
      <Route path='/royaltyincome' element={<RoyaltyIncome/>} />
      <Route path='/LG' element={<Login/>} />
      <Route path='/RG' element={<Register/>} />
      <Route path='/change-password' element={<ChangePassword/>} />
      <Route path='/forgot-password' element={<ForgotPassword/>} />
      <Route path='/help-support' element={<HelpSupport/>} />
      <Route path='/shopping' element={<Shopping/>} />
      <Route path='/daily-ads-income' element={<DailyAdsIncome/>} />
      <Route path='/level-daily-income' element={<LevelDailyIncome/>} />
      <Route path='/referal-income' element={<ReferalIncome/>} />
      <Route path='/specialbonus' element={<SpecialBonus/>} />
      <Route path='/share' element={<Share/>} />
      <Route path='/rank' element={<Rank/>} />
      <Route path='/withdraw' element={<Withdraw/>} />
      <Route path='/bankcards' element={<AllBankCards/>} />
      <Route path='/addbankcard' element={<AddBankCard/>} />
      <Route path='/withdrawrecords' element={<WithdrawRecords/>} />
      <Route path='/rechargerecords' element={<RechargeRecords/>} />

      <Route path='/um' element={<UnderMaintenance/>} />

      {/* default page */}
      <Route path='/' element={<Login/>} />
    </Routes>

  </Router>
  </>;
}

export default App;
