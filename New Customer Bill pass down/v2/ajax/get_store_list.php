<?php
  require_once("../inc_load.php");
  global $nlldb;
	$searchTerm = $_GET['term'];
  $user_no=$_SESSION['UserID'];
  $data=array();
  $arr_where=array();
  $str_where="";
  
  $userid=$_SESSION['UserID'];
  $mapping_sql ="select mv_target_key1 from mapping_value where mv_link=4 and mv_source_key1='$userid';";
  $result = $nlldb->executeQuery("$mapping_sql");
  list($storeStr) = $nlldb->fetchRow($result);

  if($storeStr)
    $arr_where[] = "st_no ='$storeStr'";

  
  $arr_where[]="(st_code LIKE '%".$searchTerm."%' OR st_name1 LIKE '%".$searchTerm."%')";
  //$arr_where[]="dbtr_flag='1'";
  
  if(count($arr_where)>0){
    $str_where=" WHERE ".implode(" AND ",$arr_where);
  }
  
  $sql_query="SELECT st_no AS `no`, st_name1 AS `label`, CONCAT(st_code,' : ',st_name1) AS `desc`
    FROM storemst 
    $str_where
    ORDER BY `desc`";
  $exec_query=$nlldb->query($sql_query);
  while ($row = $nlldb->fetchAssoc($exec_query)){
    $data[] = $row;
  }
  if(count($data)==0){
    $data[]=array("no"=>"","label"=>"No records","desc"=>"No records");
  }
  echo json_encode($data);
?>
