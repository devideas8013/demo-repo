export function setCookie(cname,cvalue,exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
  
export function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}
  
export function deleteCookie(exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires=" + d.toUTCString();

  var allCookies = document.cookie.split(';');

  for (var i = 0; i < allCookies.length; i++){
    var cookieName = allCookies[i].split('=')[0];
    document.cookie = cookieName + "=;"+ expires + ";path=/";
  }

  return true;
}

export function deleteSpecificCookie(cookie,exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires=" + d.toUTCString();

  var allCookies = document.cookie.split(';');

  for (var i = 0; i < allCookies.length; i++){
    var cookieName = allCookies[i].split('=')[0];
    var cookieValue = allCookies[i].split('=')[1];
    if(cookie==cookieName){
      document.cookie = cookieName + "=;"+ expires + ";path=/";
    }else{
      document.cookie = cookieName + "="+cookieValue+";"+ expires + ";path=/";
    }
  }

  return true;
}