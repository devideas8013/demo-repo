<style>
    .toast-dialog-view{
        position: fixed;
        height: 100vh;
        width: 100%;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.5);
    }
    .toast-view {
  min-width: 50px;
  min-height: 25px;
  border-radius: 15px;
  padding: 5px 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #FFFFFF;
}

</style>
<div class="toast-dialog-view hide_view">
    <div class="toast-view">
        <p>This is toast</p>
    </div>
</div>

<script>
function showToast(message) {
    let toast_dialog_view = document.querySelector(".toast-dialog-view");
    let toast_dialog_tv = document.querySelector(".toast-dialog-view p");

    toast_dialog_tv.innerHTML = message;
    toast_dialog_view.classList.remove("hide_view");
    setTimeout(dismissToast, 1600);

    function dismissToast() {
        toast_dialog_view.classList.add("hide_view");
    }
}
</script>