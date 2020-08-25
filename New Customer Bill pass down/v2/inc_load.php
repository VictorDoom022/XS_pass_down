<?php
  session_start();
  
  //define("RelativePath", "../..");
  if(file_exists("login.php")){
    define("RelativePath", ".");
  }
  elseif(file_exists("../login.php")){
    define("RelativePath", "..");
  }
  elseif(file_exists("../../login.php")){
    define("RelativePath", "../..");
  }
  elseif(file_exists("../../../login.php")){
    define("RelativePath", "../../..");
  }
  elseif(file_exists("../../../../login.php")){
    define("RelativePath", "../../../..");
  }
  include_once(RelativePath."/Common.php");
  include_once(RelativePath."/Template.php");
  include_once(RelativePath."/Sorter.php");
  include_once(RelativePath."/Navigator.php");
  include_once(RelativePath."/common/nll_classes_common_db.php");
  include_once(RelativePath."/common/nll_common_function.php");
  include_once(RelativePath."/v2/inc_engine.php");
  include_once(RelativePath."/v2/inc_constant.php");
  require_once(RelativePath."/class/authorization.php");
  
  $menu=$_GET['Menu'];
  $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
    if ($menu=='1' && (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0; rv:11.0') !== false))) {
    // do stuff for IE
    $page_url=$_SERVER['PHP_SELF'];
    $Redirect = "../menu_dynamic.php?menu_no=1010&menu_key=1l-2&menu_name={stxtMenuInventory}";
    
    /*echo "<script language='Javascript'>
      var ans =confirm('This page need to open in new window, proceed?');
      if(ans){
        window.open('$page_url','_blank','width=1400,height=1000,scrollbars=yes,resizable=yes');
        
      }
      window.location='$Redirect';
    </script>";*/
    echo "<script language='Javascript'>
     window.open('$page_url','_blank','width=1400,height=1000,scrollbars=yes,resizable=yes');
     window.location='$Redirect';
    </script>";
    die();
  }
  
  
  global $nlldb;
  global $authorization;
  $authorization=new Authorization();


  //if($PageLogin=="1"){
  //  if($_SESSION['MBMMID']==""){
  //    header("location:login.php");
  //    exit;
  //  }
  //}
?>