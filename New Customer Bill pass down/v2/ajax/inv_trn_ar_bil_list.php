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
session_start();
global $nlldb;
if(!$nlldb){
  $nlldb=new nllDbAccessExecution();
}

$s_document_type=urldecode($_GET['s_document_type']);
$s_customer=urldecode($_GET['s_customer']);
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
  $arr_where[]="hd_type2 NOT IN ('POS','POR')";
}
if($s_customer!=''){
  $arr_where[]="hd_customer='".$s_customer."'";
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

$arr_where[] = "
 (
  ((idocstat_view_groups_allow REGEXP '^$user_role$|,$user_role$|^$user_role,|,$user_role,'  OR  idocstat_view_groups_allow IS NULL OR idocstat_view_groups_allow='') 
  OR 
  (idocstat_view_users_allow REGEXP '^$user_id$|,$user_id$|^$user_id,|,$user_id,' OR idocstat_view_users_allow IS NULL OR idocstat_view_users_allow='' ))
  AND 
  ( idocstat_view_users_deny NOT REGEXP '^$user_id$|,$user_id$|^$user_id,|,$user_id,'  OR idocstat_view_users_deny IS NULL OR idocstat_view_users_deny='' )
)";
  
if(count($arr_where)>0){
  $str_where="AND ".implode(" AND ",$arr_where);
}

  
$sql_final="SELECT CONCAT_WS('-',hd_type1,hd_type2,hd_transno) AS hd_id, 
  hd_type1, hd_type2, hd_transno, hd_refno, hd_refno2, hd_refno3, hd_ourno, hd_yourno, hd_date, DATE_FORMAT(hd_date,'%d/%m/%Y') AS hd_date_display, hd_amount,
  hd_vat_group, hd_store, hd_customer, dt_prdcode, dt_desc, 
  hd_rate,crcy_code,hd_xamount,
  IF(hd_status = 'BX','Posted','Unpost') AS posted_status,
  dbtr_code AS debtor_code, dbtr_name1 AS debtor_name,
  st_code AS store_code,
  idocstat_code AS document_status,
  create_user.user_login AS create_user_name, FROM_UNIXTIME(LEFT(hd_sys_add_date, 10),'%d/%m/%Y %H:%i:%s') AS create_user_date
  FROM bill_header a
  INNER JOIN bill_detail b ON a.hd_type1=b.dt_type1 AND a.hd_transno=b.dt_transno AND a.hd_type2= b.dt_type2 
  INNER JOIN debtormst ON dbtr_no=hd_customer
  INNER JOIN storemst ON st_no=hd_store
  INNER JOIN invdoc_status ON idocstat_id=hd_invdocstat
  INNER JOIN currencymst ON crcy_no=hd_currency
  LEFT JOIN usermst create_user ON create_user.user_no=hd_sys_add_user
  WHERE hd_type1='AR' ".$str_where."  
  ORDER BY hd_type2 DESC, hd_date DESC, hd_refno DESC, hd_transno DESC";

$rs_final=$nlldb->executeQuery($sql_final);
while($row_final=$nlldb->fetchAssoc($rs_final)){
  $arr_data[]=$row_final;
}
$nlldb->freeResult($rs_final);
echo json_encode($arr_data);
exit;
?>