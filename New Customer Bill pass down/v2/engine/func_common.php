<?php
function funcRedirect(){
  echo '<script language="JavaScript">';
  echo 'try{';
  echo 'var Redirect=parent.document.getElementById("hidReferer").value;';
  echo 'if(Redirect!=""){';
  echo 'parent.document.location.href=Redirect;';
  echo '}';
  echo '}catch(err){';
  echo 'parent.document.location.reload();';
  echo '}';
  echo '</script>';
}
?>