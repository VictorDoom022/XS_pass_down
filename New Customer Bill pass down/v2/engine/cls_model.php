<?php
Class Model{
  protected $Table;
  protected $Data=array();
  protected $Index=array();
  protected $DB;
  function Model($DB){
    $this->DB=$DB;
  }
  
  function Insert(){
    $arrSet=array();
    $strSet="";
    
    foreach($this->Data as $Key=>$Value){
      $arrSet[]=$Key."='".$Value."'";
    }
    
    if(count($arrSet)>0){
      $strSet=implode(",",$arrSet);
    }
    
    if($strSet!=""){
      $sql="INSERT INTO ".$this->Table." SET ".$strSet;
      $this->DB->Query($sql);
    }
  }
  
  function Update(){
    $arrSet=array();
    $arrWhere=array();
    $strSet="";
    $strWhere="";
    
    foreach($this->Data as $Key=>$Value){
      $arrSet[]=$Key."='".$Value."'";
    }
    foreach($this->Index as $Key=>$Value){
      $arrWhere[]=$Key."='".$Value."'";
    }
    
    if(count($arrSet)>0){
      $strSet=implode(",",$arrSet);
    }
    if(count($arrWhere)>0){
      $strWhere=implode(" AND ",$arrWhere);
    }
    
    if($strSet!="" && $strWhere!=""){
      $sql="UPDATE ".$this->Table." SET ".$strSet." WHERE ".$strWhere;
      $this->DB->Query($sql);
    }
  }
  
  function Delete(){
    $arrWhere=array();
    $strWhere="";
    foreach($this->Index as $Key=>$Value){
      $arrWhere[]=$Key."='".$Value."'";
    }
    if(count($arrWhere)>0){
      $strWhere=implode(" AND ",$arrWhere);
      $sql="DELETE FROM ".$this->Table." WHERE ".$strWhere;
      $this->DB->Query($sql);
    }
  }
  
  function Validation(){
    $arrWhere=array();
    $arrError=array();
    $strWhere="";
    $strError="";
    
    foreach($this->Index as $Key=>$Value){
      $arrWhere[]=$Key."='".$Value."'";
    }
    if(count($arrWhere)>0){
      $strWhere=implode(" AND ",$arrWhere);
    }
    
    foreach($this->Data as $Key=>$Value){
      $sqlValidation="SELECT ValFieldName,ValAction FROM stvalidation WHERE ValTable='".$this->Table."' AND ValField='".$Key."' AND ValActive='1'";
      $rsValidation=$this->DB->Query($sqlValidation);
      foreach($rsValidation as $rowValidation){
        $ValAction=$rowValidation['ValAction'];
        $ValFieldName=$rowValidation['ValFieldName'];
        if($ValAction=='1' && $Value==''){
          $arrError[]=$ValFieldName." is blank!";
        }
        elseif($ValAction=='2'){
          $sqlDuplication="SELECT COUNT(1) FROM ".$this->Table." WHERE !($strWhere) AND ".$Key."='".$Value."'";
          $rsDuplication=$this->DB->Query($sqlDuplication);
          if($rsDuplication[0][0]>0){
            $arrError[]=$ValFieldName." exist!";
          }
        }
      }
    }
    if(count($arrError)>0){
      $strError=implode('\n',$arrError);
      echo '<script language="JavaScript">';
      echo 'alert("'.$strError.'");';
      echo '</script>';
      exit;
    }
  }
  
  function setData($Key,$Value){
    $this->Data[$Key]=$Value;
  }
  
  function setIndex($Key,$Value){
    $this->Index[$Key]=$Value;
  }
  
  function setTable($Input){
    $this->Table=$Input;
  }
  
}
?>