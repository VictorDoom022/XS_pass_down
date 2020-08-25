function pageRedirect(input){
  window.location.href=input;
}

function open_window(url){
  popup=window.open(url,"_blank","WIDTH="+(screen.width)+",HEIGHT="+(screen.height)+",left=0,top=0,scrollbars=yes,resizable=yes");
  popup.resizeTo(window.screen.availWidth, window.screen.availHeight);
  popup.moveTo(0,0);
}