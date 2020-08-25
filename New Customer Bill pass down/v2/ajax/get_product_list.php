<?php
  require_once("../inc_load.php");
  global $nlldb;
	$searchTerm = $_GET['term'];
  $user_no=$_SESSION['UserID'];
  $data=array();
  $arr_where=array();
  $str_where="";
  
  //$sql_mapping="SELECT user_client_mapping FROM usermst WHERE user_no='$user_no' LIMIT 1";
  //$result_mapping=$nlldb->executeQuery($sql_mapping);
  //list($client_mapping)=$nlldb->fetchRow($result_mapping);
  //if($client_mapping!=''){
  //  $arr_where[]="tc_no IN ($client_mapping)";
  //}
  
  $arr_where[]="(prdt_no LIKE '%".$searchTerm."%' OR prdt_name1 LIKE '%".$searchTerm."%')";
  //$arr_where[]="dbtr_flag='1'";
  
  if(count($arr_where)>0){
    $str_where=" WHERE ".implode(" AND ",$arr_where);
  }
  
  $sql_query="SELECT prdt_no AS `no`, prdt_name1 AS `label`, CONCAT(prdt_no,' : ',prdt_name1) AS `desc`
    FROM productmst 
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
