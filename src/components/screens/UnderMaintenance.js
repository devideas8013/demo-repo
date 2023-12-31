import React from 'react';
import '../../MainStyle.css';

function UnderMaintainance(){

    return (
      <div className="v-center">
        <div className="h-100vh pr-v res-wth ovf-scrl-y hide-sb bg-white">

          <div className="col-view mg-t-45">

            <div className="col-view mg-b-15 bg-white">

               <div className='col-view v-center pd-5-15'>
                  <span className='ft-sz-25 mg-t-15'>We'll be back soon!</span>
                  <span className='ft-sz-16 mg-t-10'>*Currently we are under maintenance</span>

                  <div className={`w-100 mg-t-20 h-50-p ft-sz-20 v-center br-10 cl-white bx-shdw-blk bg-blue`}>
                    <span>Thanks for your patience</span>
                  </div>

                  <span className='ft-sz-13 mg-t-10'>Please follow our telegram channel for regular updates.</span>
                </div>

            </div>

          </div>
        </div>
      </div>
    );
}

export default UnderMaintainance;