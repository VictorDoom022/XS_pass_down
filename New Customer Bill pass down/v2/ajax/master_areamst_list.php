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

$s_state_list=urldecode($_GET['s_state_list']);
$s_area_code=urldecode($_GET['s_area_code']);
$s_area_name1=urldecode($_GET['s_area_name1']);
$s_area_userdefine1=urldecode($_GET['s_area_userdefine1']);

$arr_data=array();
$arr_where=array();

if($s_state_list!=''){
  $arr_where[]="area_state='".$s_state_list."'";
}
if($s_area_code!=''){
  $arr_where[]="area_code='".$s_area_code."'";
}
if($s_area_name1!=''){
  $arr_where[]="area_name1='".$s_area_name1."'";
}
if($s_area_userdefine1!=''){
  $arr_where[]="area_userdefine1='".$s_area_userdefine1."'";
}

if(count($arr_where)>0){
    $str_where=" ".implode(" AND ",$arr_where);
}

if(count($arr_where)>0){
  $whereVar="where";
}else{
  $whereVar="";
}
  
$sql_final="SELECT *, 
IF(area_status = '1','Active','Inactive') AS area_status,
IF(area_flag = '1','Active' ,'Inactive') AS area_flag
FROM areamst LEFT JOIN statemst ON 
areamst.area_state = statemst.stte_no  
".$whereVar." ".$str_where."
GROUP BY area_no";


$fp = fopen('c:/debug.txt', 'a');
fwrite($fp, $sql_final);
fclose($fp);

$rs_final=$nlldb->executeQuery($sql_final);
while($row_final=$nlldb->fetchAssoc($rs_final)){
  $arr_data[]=$row_final;
}
$nlldb->freeResult($rs_final);
echo json_encode($arr_data);
exit;
?>