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

  $sql_final="SELECT hd_transno, hd_type1, hd_type2, hd_status, hd_date, hd_customer, hd_refno, hd_amount, hd_link_no, hd_link_type1, hd_link_type2,
  hd_currency, hd_yourno, hd_sys_add_user, hd_invdocstat, hd_sys_last_print_user,
  hd_ourno, st_code, st_no, hd_dono, hd_deldate, hd_refno2, hd_refno3, hd_sys_last_mod_user, 
  IF(hd_status = 'BX','Posted', 'Unpost') AS posted_status, 
     hd_doctype, FROM_UNIXTIME(LEFT(hd_sys_add_date, 10),'%d/%m/%Y %H:%i:%s') AS hd_sys_add_date, hd_delto, 
     hd_print_no, hd_sys_add_date AS hd_cre_date_id, hd_area, CONCAT(area_code, ':', area_name1)  AS area_name, bilctrl_type,    
  IF(hd_status_stock = '1','Posted','Unpost') AS hd_status_stock, hd_remark, hd_sys_last_mod_date, hd_db_consolidate_flag, 
     hd_vat_group, hd_vat_amt, hd_vat_based, hd_o_tel, hd_userdefine1, hd_userdefine2, hd_userdefine3, hd_temp_refno, hd_source AS hd_source, 
     hd_job AS hd_job, hd_task AS hd_task, crdt_code, crdt_name1,
     create_user.user_login AS create_user_name, FROM_UNIXTIME(LEFT(hd_sys_add_date, 10),'%d/%m/%Y %H:%i:%s') AS create_user_date
  FROM (((bill_header 
  INNER JOIN creditormst ON 
     bill_header.hd_customer = creditormst.crdt_no) 
  INNER JOIN storemst ON
     bill_header.hd_store = storemst.st_no) 
  INNER JOIN bill_control ON 
     bill_header.hd_doc = bill_control.bilctrl_doc AND bill_header.hd_type2 = bill_control.bilctrl_name) 
  LEFT JOIN areamst ON 
     bill_header.hd_area = areamst.area_no
  LEFT JOIN usermst create_user ON create_user.user_no=hd_sys_add_user   
  WHERE hd_type1='AP' ".$str_where."
  ORDER BY hd_type2 DESC";
  
  $rs_final=$nlldb->executeQuery($sql_final);
while($row_final=$nlldb->fetchAssoc($rs_final)){
  $arr_data[]=$row_final;
}
$nlldb->freeResult($rs_final);
echo json_encode($arr_data);
exit;
?>