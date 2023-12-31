import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import TopBar from '../other-components/TopBar';
import ToastDialog from '../dialogs/ToastDialog';
import '../../MainStyle.css';
import { API_ACCESS_URL,getURLParam } from '../modals/Constants';
import { getCookie } from '../modals/Cookie';

function AddBankCard(){
    const navigate = useNavigate();
    const [isInputValCorrect, setInValCorrect] = useState(false);
    const [isBankValCorrect, setInBankCorrect] = useState(false);

    const [pageConst, setConstants] = useState({
        pageTitle: "Add Bank Card",
        isLoadingShow: false,
        inAccountNum: "",
        inBeneficiary: "",
        inIFSCCode: "",
        inBankName: "",
        inBranchName: "",
        inPrimaryOption: "true",
        bankCardMethod: "",
        tabActiveReord: "upi",
        recordList: [],
    });

    const topBarClickAction = (data) =>{
    }

    const updateActiveTab = (data) => {
      setConstants(previousState => {
          return { ...previousState, tabActiveReord: data }
      });
    }

    const updateLoadingStatus = (data) => {
        setConstants(previousState => {
            return { ...previousState, isLoadingShow: data }
        });
    }

    const updateToastDialogState = (data,msg) => {
      setConstants(previousState => {
        return { ...previousState, toastDialogShow: data }
      });

      setConstants(previousState => {
        return { ...previousState, toastMessage: msg }
      });
    }

    const checkForInputVal = (beneficiary,accountNum,ifscCode) =>{
        if(beneficiary.length > 3 && accountNum.length > 5){
          if(pageConst.tabActiveReord!="upi" && ifscCode.length > 4){
            setInValCorrect(true);
          }else if(pageConst.tabActiveReord=="upi"){
            setInValCorrect(true);
          }else{
            setInValCorrect(false);
          }
        }else{
          setInValCorrect(false);
        }
    }
  
    const onInputValChange = (source,data) =>{
        if(source=="beneficiary"){
          checkForInputVal(data, pageConst.inAccountNum, pageConst.inIFSCCode);
  
          setConstants(previousState => {
            return { ...previousState, inBeneficiary: data }
          });
        }
  
        if(source=="accountNum"){
          checkForInputVal(pageConst.inBeneficiary, data, pageConst.inIFSCCode);
  
          setConstants(previousState => {
            return { ...previousState, inAccountNum: data }
          });
        }
  
        if(source=="ifscCode"){
          checkForInputVal(pageConst.inBeneficiary, pageConst.inAccountNum, data);
  
          setConstants(previousState => {
            return { ...previousState, inIFSCCode: data }
          });
        }
    }

    const setPrimaryOption = (data) =>{
      setConstants(previousState => {
        return { ...previousState, inPrimaryOption: data }
      });
    }


    const verifyIFSCCode = () =>{
        const fecthApiData = async (url) => {
            try {
              const res = await fetch(url);
              const data = await res.json();

              updateLoadingStatus(false);
              if(data!="Not Found" && data['BANK']!="" && data['BRANCH']!=""){
                setInBankCorrect(true);
                setConstants({...pageConst, inBankName: data['BANK'], inBranchName: data['BRANCH']});
                checkForInputVal(pageConst.inBeneficiary, pageConst.inAccountNum, pageConst.inIFSCCode);
              }else{
                setInBankCorrect(false);
                checkForInputVal(pageConst.inBeneficiary, pageConst.inAccountNum, pageConst.inIFSCCode);
                updateToastDialogState(true,"Invalid IFSC Code! Please try again!");
              }
            
            } catch (error) {
              updateLoadingStatus(false);
              updateToastDialogState(true,"There was a technical issue! Please try again!");
            }
        };

        if(pageConst.inIFSCCode.length > 4){
          updateLoadingStatus(true);
          fecthApiData("https://ifsc.razorpay.com/"+pageConst.inIFSCCode);
        }else{
          updateToastDialogState(true,"Please enter IFSC Code!");
        }
    }

    function addNewBankCard(){
        const fecthApiData = async (url) => {
            try {
              const res = await fetch(url);
              const data = await res.json();
              updateLoadingStatus(false);

              if(data.status_code=="success"){
                navigate(-1);
              }else if(data.status_code=="already_exist"){
                updateToastDialogState(true,"Account Number already exit! Please try again!");
              }else if(data.status_code=="limit_reached"){
                updateToastDialogState(true,"Limit reached! You can't add more bankcard!");
              }else{
                updateToastDialogState(true,"Something went wrong! Please try again!");
              }
            } catch (error) {
              updateLoadingStatus(false);
            }
        };

        if(isInputValCorrect){
          updateLoadingStatus(true);

          fecthApiData(API_ACCESS_URL+"add-bankcard.php?USER_ID="+getCookie("uid")+"&BENEFICIARY_NAME="+pageConst.inBeneficiary+"&USER_BANK_NAME="+pageConst.inBankName+
            "&USER_BANK_ACCOUNT="+pageConst.inAccountNum+"&USER_BANK_IFSC_CODE="+pageConst.inIFSCCode+
            "&IS_PRIMARY="+pageConst.inPrimaryOption+"&CARD_METHOD="+pageConst.tabActiveReord);
          
        }
    }

    useEffect(() => {
      if(getURLParam('M')!=null && getURLParam('M')!=""){

        setConstants(previousState => {
          return { ...previousState, bankCardMethod: getURLParam('M') }
        });
      }
    }, []);

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-tar-black">
          <TopBar intentData={pageConst} multiBtn={false} multiBtn1="" multiBtn2="" updateState={topBarClickAction}/>
          <ToastDialog intentData={pageConst} updateState={updateToastDialogState} />

          <div className="col-view mg-t-45 mg-b-15 pd-15">

            <div className="tab-slct-v w-100">
              <div className={`v-center tab-in-v pd-10 ft-sz-18 w-100 ${pageConst.tabActiveReord=="upi" ? 'active' : ''}`} onClick={()=>updateActiveTab('upi')}>UPI</div>
              <div className={`v-center tab-in-v pd-10 ft-sz-18 w-100 ${pageConst.tabActiveReord=="bank" ? 'active' : ''}`} onClick={()=>updateActiveTab('bank')}>Bank Account</div>
            </div>

            <div className={`col-view pd-18 br-10 mg-t-15 bg-white ${pageConst.tabActiveReord == 'upi' ? 'hide-v' : ''}`}>
                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13 mg-l-10'>Beneficiary Name:</span>
                    <input type='text' className='cutm-inp mg-t-5' placeholder='Please Input' onChange={e => onInputValChange('beneficiary',e.target.value)}></input>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13 mg-l-10'>Account Number:</span>
                    <input type='text' className='cutm-inp mg-t-5' placeholder='Please Input' onChange={e => onInputValChange('accountNum',e.target.value)}></input>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13 mg-l-10'>IFSC:</span>
                    <div className='row-view'>
                      <input type='text' className='cutm-inp mg-t-5' placeholder='Please Input' onChange={e => onInputValChange('ifscCode',e.target.value)} readOnly={isBankValCorrect ? true : false}></input>
                      <div className='w-65-p h-30-p v-center ft-sz-16 br-10 cl-white bg-green' onClick={() => verifyIFSCCode()}>Verify</div>
                    </div>
                    
                    <span className='ft-sz-13 mg-l-10 mg-t-15'>Bank: {pageConst.inBankName}</span>
                    <span className='ft-sz-13 mg-l-10 mg-t-5'>Branch: {pageConst.inBranchName}</span>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13'>Set as Primary:</span>
                    <select className='cutm-inp mg-t-10'>
                      <option value="Yes" onClick={() => setPrimaryOption("true")}>Yes</option>
                      <option value="No" onClick={() => setPrimaryOption("false")}>No</option>
                    </select>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='w-100 pd-5-15 mg-t-25'>
                    <div className={`w-100 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk ${isInputValCorrect ? (isBankValCorrect ? 'bg-blue' : 'bg-grey-2') : 'bg-grey-2'}`} onClick={() => addNewBankCard()}>
                      <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                      <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Add</span>
                    </div>
                  </div>

            </div>

            <div className={`col-view pd-18 br-10 mg-t-15 bg-white ${pageConst.tabActiveReord == 'bank' ? 'hide-v' : ''}`}>
                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13 mg-l-10'>Beneficiary Name:</span>
                    <input type='text' className='cutm-inp mg-t-5' placeholder='Please Input' onChange={e => onInputValChange('beneficiary',e.target.value)}></input>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13 mg-l-10'>UPI Id:</span>
                    <input type='text' className='cutm-inp mg-t-5' placeholder='Please Input' onChange={e => onInputValChange('accountNum',e.target.value)}></input>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='col-view w-100 pd-5-15 mg-t-20'>
                    <span className='ft-sz-13'>Set as Primary:</span>
                    <select className='cutm-inp mg-t-10'>
                      <option value="Yes" onClick={() => setPrimaryOption("true")}>Yes</option>
                      <option value="No" onClick={() => setPrimaryOption("false")}>No</option>
                    </select>
                  </div>

                  <div className='line-hv-grey mg-t-5'></div>

                  <div className='w-100 pd-5-15 mg-t-25'>
                    <div className={`w-100 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk ${isInputValCorrect ? 'bg-blue' : 'bg-grey-2'}`} onClick={() => addNewBankCard()}>
                      <img className={`smpl-btn-l spin-anim ${pageConst.isLoadingShow==false ? 'hide-v' : ''}`} src={require('../icons/loader-icon.png')} />
                      <span className={`${pageConst.isLoadingShow==true ? 'hide-v' : ''}`}>Add</span>
                    </div>
                  </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default AddBankCard;