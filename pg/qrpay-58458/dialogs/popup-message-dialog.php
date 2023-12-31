<style>
.primary-btn,.secondary-btn{
    width: 100%;
    padding: 10px 16px;
    border: none;
    outline: none;
    cursor: pointer;
    font-size: 17px;
    color: var(--white-color);
    border-radius: 20px;
    background: rgba(0,0,0,0.1) !important;
}

  .primary-color-back{
    /* background: #3F51B5 !important; */
    background: var(--primary-color) !important;
  }

  .primary-btn:active{
    transform: scale(0.9);
    transition: 0.1s;
}

    .popup-dialog-view{
        position: fixed;
        height: 100vh;
        width: 100%;
        z-index: 100;
        background: rgba(0,0,0,0.5);
    }
    .popup-dialog-view .dialog-view{
        position: absolute;
        top: 50%;
        left: 50%;
        min-height: 200px;
        width: 350px;
        background: #FFFFFF;
        border-radius: 15px;
        transform: translate(-50%,-50%);
    }
    .popup-dialog-view .dialog-topicon{
        position: relative;
        height: 80px;
        width: 80px;
        top: -20px;
        left: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #FFFFFF;
        transform: translateX(-50%);
    }
    .popup-dialog-view .dialog-topicon i{
        color: #E74C3C;
        font-size: 2.5em;
    }

    .popup-dialog-view .green-icon i{
        color: #28B463 !important;
    }

    .popup-dialog-view .dialog-view .dialog-contents{
        text-align: center;
        padding: 15px;
    }

    .popup-dialog-view .dialog-view .dialog-contents p:first-child{
        font-size: 22px;
    }

    .popup-dialog-view .dialog-view .dialog-contents p:last-child{
        margin-top: 10px;
    }

    .popup-dialog-view .dialog-view .active-btn{
        background: rgba(0,0,0,0.05);
    }

    .popup-dialog-view .dialog-view .options-action-btns{
        padding: 5px 15px;
    }

    .popup-dialog-view dialog-view .dialog-option:active{
        transform: scale(0.9);
        transition: 0.1s;
    }

    .popup-dialog-view .dialog-transaction{
        padding: 0 10px;
    }


    @media (max-width: 550px) {
      .popup-dialog-view .dialog-view{
        width: 90%;
     }
    }
</style>
<div class="popup-dialog-view hide_view">
   <div class="dialog-view">
      <div class="dialog-topicon green-icon">
         <i class='bx bxs-check-circle'></i>  
      </div>
      <div class="dialog-contents">
         <p id="pop-dialog-title-tv"></p>
         <p id="pop-dialog-message-tv"></p>
      </div>
      <div class="dialog-transaction hide_view">
      </div>
      </br>
      <div class="options-action-btns">
         <button id="confirm-popup-btn" class="primary-btn primary-color-back">Ok</button>
      </div>
      </br>
   </div>
</div>

<script>
    let confirm_popup_btn = document.querySelector("#confirm-popup-btn");
    let popup_dialog_view = document.querySelector(".popup-dialog-view");
    let dialog_contents = document.querySelector(".popup-dialog-view .dialog-contents");
    let dialog_transaction = document.querySelector(".popup-dialog-view .dialog-transaction");
    let dialog_topicon = document.querySelector(".popup-dialog-view .dialog-topicon");
    let pop_dialog_title_tv = document.querySelector(".popup-dialog-view #pop-dialog-title-tv");
    let pop_dialog_message_tv = document.querySelector(".popup-dialog-view #pop-dialog-message-tv");

    function showPopUpDialog(title,message,type,extradata){
        dialog_topicon.classList.add("green-icon");
        confirm_popup_btn.innerHTML = "Ok";

        if(type=="success-dialog"){
            dialog_topicon.innerHTML = "<i class='bx bx-check-circle' ></i>";
            popUpCancelType("dismiss");
        }else if(type=="rejected"){
            dialog_topicon.innerHTML = "<i class='bx bx-x-circle'></i>";
            dialog_topicon.classList.remove("green-icon");
            popUpCancelType("dismiss");
        }else if(type=="rejected-back"){
            dialog_topicon.innerHTML = "<i class='bx bx-x-circle'></i>";
            dialog_topicon.classList.remove("green-icon");
            popUpCancelType("back");
        }else{
            popUpCancelType("dismiss");
        }

        pop_dialog_title_tv.innerHTML = title;
        pop_dialog_message_tv.innerHTML = message;
        popup_dialog_view.classList.remove("hide_view");
    }

    function popUpCancelType(cancelType){
        if(cancelType=="dismiss"){

          popup_dialog_view.addEventListener("click", (e)=>{
            if(e.target.className=="popup-dialog-view"){
              popup_dialog_view.classList.add("hide_view");
            }
          })

          confirm_popup_btn.addEventListener("click", ()=> {
            popup_dialog_view.classList.add("hide_view");
          })

        }else if(cancelType=="back"){
          confirm_popup_btn.addEventListener("click", ()=> {
            window.history.back();
          })   
        }
    }

</script>