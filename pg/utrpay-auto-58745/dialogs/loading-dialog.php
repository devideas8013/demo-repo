<style>
.loading-dialog-view{
  position: fixed;
  height: 100vh;
  width: 100%;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.5);
}
.loading-dialog-view .loading-view {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-radius: 50%;
  border-top: 4px solid rgba(0,0,0,0.05);
  -webkit-animation: spin 0.6s linear infinite; /* Safari */
  animation: spin 0.6s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<div class="loading-dialog-view hide_view">
    <div class="loading-view">
    </div>
</div>

<script>
let loading_dialog_view = document.querySelector(".loading-dialog-view");

function showLoadingDialog(){
  loading_dialog_view.classList.remove("hide_view");
}

function dismissLoadingDialog(){
  loading_dialog_view.classList.add("hide_view");
}
</script>