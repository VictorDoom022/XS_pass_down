<?php
/**
*
*		
*/

//Include Common Files
define("RelativePath", "../..");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");

include_once(RelativePath."/common/nll_classes_common_db.php");
include_once(RelativePath."/common/nll_common_function.php");
include_once(RelativePath."/common/nll_global_include_classes.php");
//END Common Files
global $nlldb;
session_start();
if(!$nlldb){
  $nlldb=new nllDbAccessExecution();
}

$s_document_type=urldecode($_GET['s_document_type']);
$s_store=urldecode($_GET['s_store']);
$s_date_from=urldecode($_GET['s_date_from']);
$s_date_to=urldecode($_GET['s_date_to']);
$s_product=urldecode($_GET['s_product']);
$s_description=urldecode($_GET['s_description']);

$arr_data=array();
$arr_where=array();



if($s_document_type!=''){
  $arr_where[]="hd_type2='".$s_document_type."'";
}
else{
  // $arr_where[]="hd_type2 NOT IN ('POS','POR')";
}
if($s_store!=''){
  $arr_where[]="hd_store='".$s_store."'";
}
if($s_date_from!=''){
  $arr_where[]="hd_date>='".$s_date_from."'";
}
if($s_date_to!=''){
  $arr_where[]="hd_date<='".$s_date_to."'";
}
if($s_product!=''){
  $arr_where[]="dt_prdcode='".$s_product."'";
}
if($s_description!=''){
  $arr_where[]="dt_desc LIKE '%".$s_description."%'";
}

/*40946 yinru*/
$user_id=$_SESSION['UserID'];
$user_role  = $_SESSION['UserRole'];
$mapping_sql ="select mv_target_key1, mv_target_key2 from mapping_value where mv_link=4 and mv_source_key1='$user_id';";
$result = $nlldb->executeQuery("$mapping_sql");
list($storeStr,$sourceStr) = $nlldb->fetchRow($result);

if($sourceStr)
  $arr_where[] = "hd_source ='$sourceStr'";

if($storeStr)
  $arr_where[] = "hd_store ='$storeStr'";

// $arr_where[] = "
//  (
//   ((idocstat_view_groups_allow REGEXP '^$user_role$|,$user_role$|^$user_role,|,$user_role,'  OR  idocstat_view_groups_allow IS NULL OR idocstat_view_groups_allow='') 
//   OR 
//   (idocstat_view_users_allow REGEXP '^$user_id$|,$user_id$|^$user_id,|,$user_id,' OR idocstat_view_users_allow IS NULL OR idocstat_view_users_allow='' ))
//   AND 
//   ( idocstat_view_users_deny NOT REGEXP '^$user_id$|,$user_id$|^$user_id,|,$user_id,'  OR idocstat_view_users_deny IS NULL OR idocstat_view_users_deny='' )
// )";
  
if(count($arr_where)>0){
  $str_where="AND ".implode(" AND ",$arr_where);
}

  $sql_final="SELECT hd_transno, hd_type1, hd_type2, hd_date, hd_customer, hd_refno, hd_amount, hd_sys_add_user, hd_invdocstat, storemst.*, hd_sys_last_print_user, 
  CASE WHEN hd_sys_last_print_date != '0000-00-00 00:00:00' THEN DATE_FORMAT(hd_sys_last_print_date , '%d/%m/%Y %H:%i') ELSE '-' END AS hd_sys_last_print_date, 
  hd_store, CONCAT(store_tofrom.st_code,':',store_tofrom.st_name1) AS store_tofrom_st_code, CONCAT(storemst.st_code,':',storemst.st_name1) AS st_code_display, 
  hd_remark, hd_refno2, hd_refno3, user_login, FROM_UNIXTIME(LEFT(hd_sys_add_date, 10),'%d/%m/%Y %H:%i:%s') AS hd_sys_add_date1, 
  hd_status, hd_link_no, hd_link_type1, hd_link_type2, 
  IF(hd_status = 'BX','Posted', 'Unpost') AS posted_status, hd_print_no, 
  hd_sys_add_date, bilctrl_type, hd_sys_last_mod_date, hd_userdefine1, hd_userdefine2, hd_userdefine3, 
  hd_temp_refno, hd_source AS hd_source, hd_job AS hd_job, hd_task AS hd_task  
  FROM ((bill_header 
  INNER JOIN storemst ON bill_header.hd_customer = storemst.st_no) 
  INNER JOIN storemst store_tofrom ON bill_header.hd_store = store_tofrom.st_no) 
  INNER JOIN bill_control ON bill_header.hd_doc = bill_control.bilctrl_doc AND bill_header.hd_type2 = bill_control.bilctrl_name
  INNER JOIN usermst ON bill_header.hd_sys_last_mod_user = usermst.user_no ".$str_where."  
  ORDER BY hd_type2 DESC, hd_date DESC, hd_refno DESC, hd_transno DESC;";
  
  $rs_final=$nlldb->executeQuery($sql_final);
while($row_final=$nlldb->fetchAssoc($rs_final)){
  $arr_data[]=$row_final;
}
$nlldb->freeResult($rs_final);
echo json_encode($arr_data);
exit;
?>