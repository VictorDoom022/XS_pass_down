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

$s_type1=urldecode($_GET['s_type1']);
$s_type2=urldecode($_GET['s_type2']);
$s_transno=urldecode($_GET['s_transno']);
$arr_data=array();
$arr_where=array();

//if($s_document_type!=''){
//  $arr_where[]="hd_type2='".$s_document_type."'";
//}
//else{
//  $arr_where[]="hd_type2 NOT IN ('POS','POR')";
//}
//if($s_customer!=''){
//  $arr_where[]="hd_customer='".$s_customer."'";
//}
//if($s_store!=''){
//  $arr_where[]="hd_store='".$s_store."'";
//}
//if($s_date_from!=''){
//  $arr_where[]="hd_date>='".$s_date_from."'";
//}
//if($s_date_to!=''){
//  $arr_where[]="hd_date<='".$s_date_to."'";
//}
//
//if(count($arr_where)>0){
//  $str_where="AND ".implode(" AND ",$arr_where);
//}

$sql_final="SELECT hd_sys_last_mod_date,hd_sys_last_mod_user,
  hd_sys_last_print_user, hd_sys_last_print_date,
  hd_db_consolidate_flag
  FROM bill_header
  WHERE hd_transno='".$s_transno."' AND hd_type1='".$s_type1."' AND hd_type2='".$s_type2."' LIMIT 1";
$rs_final=$nlldb->executeQuery($sql_final);
list($hd_sys_last_mod_date,$hd_sys_last_mod_user,$hd_sys_last_print_user,$hd_sys_last_print_date,$hd_db_consolidate_flag)=$nlldb->fetchRow($rs_final);
$nlldb->freeResult($rs_final);

$arr_data['last_mod_date']=$hd_sys_last_mod_date;
$arr_data['last_print_date']=$hd_sys_last_print_date;

// Last Mod User
$sql_last_mod_user="SELECT user_login FROM usermst WHERE user_no='".$hd_sys_last_mod_user."' LIMIT 1";
$rs_last_mod_user=$nlldb->executeQuery($sql_last_mod_user);
list($last_mod_user)=$nlldb->fetchRow($rs_last_mod_user);
$nlldb->freeResult($rs_last_mod_user);
$arr_data['last_mod_user']=$last_mod_user;

// Last Print User
if($hd_sys_last_mod_user==$hd_sys_last_print_user){
  $last_print_user=$last_mod_user;
}
else{
  $sql_last_print_user="SELECT user_login FROM usermst WHERE user_no='".$hd_sys_last_print_user."' LIMIT 1";
  $rs_last_print_user=$nlldb->executeQuery($sql_last_print_user);
  list($last_print_user)=$nlldb->fetchRow($rs_last_print_user);
  $nlldb->freeResult($rs_last_print_user);
}
$arr_data['last_print_user']=$last_print_user;

// DB Consolidate Flag
if($hd_db_consolidate_flag=='1'){
  $db_consolidate_flag='Consolidated';
}
else{
  $db_consolidate_flag='';
}
$arr_data['db_consolidate_flag']=$db_consolidate_flag;

// Import Status
$sql_import="SELECT SUM(dt_backorder), SUM(dt_qty) FROM bill_detail WHERE dt_transno='".$s_transno."' AND dt_type1='".$s_type1."' AND dt_type2='".$s_type2."'";
$rs_import=$nlldb->executeQuery($sql_import);
list($dt_backorder,$dt_qty)=$nlldb->fetchRow($rs_import);
$nlldb->freeResult($rs_import);
if($dt_backorder!=$dt_qty){
  $arr_data['import_status']="Imported";
}
else{
  $arr_data['import_status']="-";
}

echo json_encode($arr_data);
exit;
?>