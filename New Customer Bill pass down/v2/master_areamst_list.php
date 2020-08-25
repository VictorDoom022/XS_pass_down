<?php
  require_once("inc_load.php");
  
  global $nlldb;
  $s_state_list=$_GET['s_state_list'];
  $s_area_code=$_GET['s_area_code'];
  $s_area_name1=$_GET['s_area_name1'];
  $s_userdefine1=$_GET['s_userdefine1'];
  $s_store_display=$_GET['s_store_display'];
  $s_date_from=$_GET['s_date_from'];
  $s_date_to=$_GET['s_date_to'];
  $s_product=$_GET['s_product'];
  $s_product_display=$_GET['s_product_display'];
  $s_description=$_GET['s_description'];
  $arr_s_state_list=array();
  
  if($s_date_from==''){
    $s_date_from=date('d/m/Y', strtotime('today - 30 days'));
  }

  $sql_s_state_list="SELECT stte_no AS listValue, CONCAT(stte_code, ' : ', stte_name1) AS listText FROM statemst;";
  $result_s_state_list=$nlldb->executeQuery($sql_s_state_list);
  while($row_s_state_list=$nlldb->fetchAssoc($result_s_state_list)) {
    $arr_s_state_list[]=$row_s_state_list;
  }
  $nlldb->freeResult($result_s_state_list);

  $arrTag['s_state_list']=$s_state_list;
  $arrTag['s_area_code']=$s_area_code;
  $arrTag['s_area_name1']=$s_area_name1;
  $arrTag['s_area_userdefine1']=$s_area_userdefine1;

  $objTemplate=new clsBSTemplate();
  $objTemplate->setTemplate("master_template.html","");
  $objTemplate->setTemplate("head_master_areamst_list.html","head");
  $objTemplate->setTemplate("body_master_areamst_list.html","body");
  $objTemplate->setTag($arrTag);
  $objTemplate->setListbox("s_state_list",$arr_s_state_list,$s_state_list);
  $Output=$objTemplate->Generate();
  //var_dump($arrTag);
  echo $Output;
?>