export const WEBSITE_NAME = "Ads Exchange";
export const WEBSITE_URL = "https://adsexchanges.com/";
export const API_ACCESS_URL = "https://api.adsexchanges.com/";
export const PG_ACCESS_URL = "https://adsexchanges.com/pg/";
export const LOGIN_REDIRECT_URL = WEBSITE_URL+"LG";
export const HOME_REDIRECT_URL = WEBSITE_URL+"home";

export function redirectTo(url) {
    window.location.replace(url);
}

export function openNewPage(url) {
    window.location.href =url;
}

export function getURLParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

export function getInternetStatus(){
    if(navigator.onLine){
        return true;
    }else{
        return false;
    }
}

export function generateReferalURL(USER_ID){
    return WEBSITE_URL+"RG?C="+USER_ID;
}

export function copyText(text){

    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.opacity = "0"; 
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      document.execCommand('copy');
      document.body.removeChild(textArea);
    } catch (err) {
      return true;
    }

    return true;
}