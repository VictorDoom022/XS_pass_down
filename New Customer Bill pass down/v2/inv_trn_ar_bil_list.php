<?php
  require_once("inc_load.php");
  
  global $nlldb;
  $s_document_type=$_GET['s_document_type'];
  $s_customer=$_GET['s_customer'];
  $s_customer_display=$_GET['s_customer_display'];
  $s_store=$_GET['s_store'];
  $s_store_display=$_GET['s_store_display'];
  $s_date_from=$_GET['s_date_from'];
  $s_date_to=$_GET['s_date_to'];
  $s_product=$_GET['s_product'];
  $s_product_display=$_GET['s_product_display'];
  $s_description=$_GET['s_description'];
  $s_importStatus=$_GET['s_importStatus'];
  $arr_s_document_type=array();
  
  if($s_date_from==''){
    $s_date_from=date('d/m/Y', strtotime('today - 30 days'));
  }

  $sql_s_document_type="SELECT bilctrl_name AS listValue,bilctrl_desc AS listText FROM bill_control WHERE bilctrl_doc='AR-BIL' AND bilctrl_userdefine4='1' ORDER BY bilctrl_order, bilctrl_desc";
  $result_s_document_type=$nlldb->executeQuery($sql_s_document_type);
  while($row_s_document_type=$nlldb->fetchAssoc($result_s_document_type)) {
    $arr_s_document_type[]=$row_s_document_type;
  }
  $nlldb->freeResult($result_s_document_type);

  $arr_s_importStatus = array(
    array("listValue" => "0" , "listText" => "Not Imported"),
    array("listValue" => "1" , "listText" => "Imported")
  );

  $arrTag['s_document_type']=$s_document_type;
  $arrTag['s_customer']=$s_customer;
  $arrTag['s_customer_display']=$s_customer_display;
  $arrTag['s_store']=$s_store;
  $arrTag['s_store_display']=$s_store_display;
  $arrTag['s_date_from']=$s_date_from;
  $arrTag['s_date_to']=$s_date_to;
  $arrTag['s_product']=$s_product;
  $arrTag['s_product_display']=$s_product_display;
  $arrTag['s_description']=$s_description;
  $arrTag['s_importStatus']=$s_importStatus;

  $objTemplate=new clsBSTemplate();
  $objTemplate->setTemplate("master_template.html","");
  $objTemplate->setTemplate("head_inv_trn_ar_bil_list.html","head");
  $objTemplate->setTemplate("body_inv_trn_ar_bil_list.html","body");
  $objTemplate->setTag($arrTag);
  $objTemplate->setListbox("s_document_type",$arr_s_document_type,$s_document_type);
  $objTemplate->setListbox("s_importStatus",$arr_s_importStatus,$s_importStatus);
  $Output=$objTemplate->Generate();
  
  echo $Output;
?>