import React from 'react'
import {BrowserRouter as Router, Routes, Route} from 'react-router-dom';

import './App.css';
import Ludo from './components/Ludo';
import Navbar from './components/Navbar';

function AppRouter() {
    return <>
  
    <Navbar title="MoneyMall" />
    <Router>
  
      <Routes>
        <Route path='/app' element={<Ludo/>} />
        <Route path='/ludo' element={<Ludo/>} />
      </Routes>
  
    </Router>
    </>;
  }
  
  export default AppRouter;