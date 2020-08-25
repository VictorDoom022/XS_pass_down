<?php
Class clsBSTemplate{
  protected $arrTag;
  protected $isList;
  protected $TemplatePath;
  protected $TemplateFile;
  protected $TemplateContent;
  protected $TemplateContentList;
  protected $ResultSet;
  protected $RecordPerPage=20;
  protected $CurrentPage;
  protected $TotalPage;
  protected $TotalRecord;
  protected $BeforeDetailRowSpecific1;
  protected $AfterDetailRowSpecific1;
  
  function clsBSTemplate(){
    global $nlldb;
    $this->arrTag=array();
    $this->isList="0";
    $this->CurrentPage=$_GET['Page'];
    $this->TotalPage=1;
    $this->TotalRecord=0;
    $this->TemplatePath="template/";
    $this->DetailRowSpecific1=0;
    $this->DetailRowSpecific1Content="";
    $this->DB=$nlldb;
    
    if($this->CurrentPage==''){
      $this->CurrentPage=1;
    }
  }

  function Generate(){
    global $authorization;
    global $multilanguage;
    $this->ReplaceSystem();
    
    if($this->isList=="1"){
      $this->ReplaceDataSearch();
      $this->getDetailRowSpecific1();
      $this->ReplaceDataList();
      $this->ReplaceData();
    }
    else{
      $this->ReplaceData();
    }
    
    
    $this->TemplateContent=$multilanguage->convert($this->TemplateContent);
    $this->ReplaceDatajqxListAuthorization();
    $this->TemplateContent=$authorization->setAuthorization($this->TemplateContent,'v2');
    
    $this->Clear();
    return $this->TemplateContent;
  }
  
  function Clear(){
    $pattern="/{([a-zA-Z0-9_]+)}/";
    $this->TemplateContent=preg_replace($pattern,"",$this->TemplateContent);
  }
  
  function ReplaceData(){
    global $nlldb;
    if($this->TotalRecord>0){
      $nlldb->dataSeek($this->ResultSet,0);
      while($rowResult=$nlldb->fetchAssoc($this->ResultSet)){
        $V1=$rowResult;
        foreach($V1 as $Key=>$Value){
          if(strpos($this->TemplateContent,"{".$Key."_sorter}")!==false){
            $arrGet=array();
            $OrderByASC='ASC';
            $OrderByASCIcon='';
            foreach($_GET as $GetKey=>$GetValue){
              if($GetKey=='OrderBy'){
                if($GetValue==$Key){
                  if($_GET['OrderByASC']=='ASC'){
                    $OrderByASC='DESC';
                    $OrderByASCIcon='<i class="glyphicon glyphicon-triangle-top"></i>';
                  }
                  else{
                    $OrderByASCIcon='<i class="glyphicon glyphicon-triangle-bottom"></i>';
                  }
                }
              }
              elseif($GetKey!='OrderBy' && $GetKey!='OrderByASC'){
                $arrGet[]=$GetKey.'='.$GetValue;
              }
            }
            $arrGet[]='OrderBy='.$Key;
            $arrGet[]='OrderByASC='.$OrderByASC;
            $strGet=implode("&",$arrGet);
            $DataKey="{".$Key."_sorter}";
            $this->TemplateContent=str_replace($DataKey,$strGet,$this->TemplateContent);
            $DataKeyIcon="{".$Key."_sortericon}";
            $this->TemplateContent=str_replace($DataKeyIcon,$OrderByASCIcon,$this->TemplateContent);
            
          }
          if(strpos($this->TemplateContent,"{".$Key."_check}")===false){
            $DataKey="{".$Key."_txt}";
            $this->TemplateContent=str_replace($DataKey,$Value,$this->TemplateContent);
            $DataKey="{".$Key."}";
            $this->TemplateContent=str_replace($DataKey,nl2br($Value),$this->TemplateContent);
          }
          else{
            if($Value=='1'){
              $DataKey="{".$Key."_check}";
              $this->TemplateContent=str_replace($DataKey,"checked",$this->TemplateContent);
            }
          }
        }
      }
    }

    foreach($this->arrTag as $Key=>$Value){
      $DataKey="{".$Key."}";
      $this->TemplateContent=str_replace($DataKey,$Value,$this->TemplateContent);
    }
  }
  
  function ReplaceDataList(){
    global $nlldb;
    $DetailRowStartLength=strlen('<!-- Detail Row Start -->');
    $DetailRowEndLength=strlen('<!-- Detail Row End -->');
    $PositionStart=strpos($this->TemplateContent,'<!-- Detail Row Start -->')+$DetailRowStartLength;
    $PositionEnd=strpos($this->TemplateContent,'<!-- Detail Row End -->');
    $PositionLength=$PositionEnd-$PositionStart;
    $ListTemplate=substr($this->TemplateContent,$PositionStart,$PositionLength);
    $RecordStart=($this->CurrentPage-1)*$this->RecordPerPage;
    $RecordEnd=($this->CurrentPage*$this->RecordPerPage)-1;
    
    if($RecordEnd>($this->TotalRecord-1)){
      $RecordEnd=($this->TotalRecord-1);
    }
    
    $iRow=0;
    while($rowResult=$nlldb->fetchAssoc($this->ResultSet)){
      $iRow++;
      $BeforeRowSpecific1='';
      $AfterRowSpecific1='';
      
      if($this->BeforeDetailRowSpecific1==$iRow){
        $BeforeRowSpecific1=$this->DetailRowSpecific1Content;
      }
      if($this->AfterDetailRowSpecific1==$iRow){
        $AfterRowSpecific1=$this->DetailRowSpecific1Content;
      }
      
      $ListResult=$BeforeRowSpecific1.$ListTemplate.$AfterRowSpecific1;
      foreach($rowResult as $Key=>$Value){
        if(strpos($ListResult,"{".$Key."_check}")===false){
          $DataKey="{".$Key."_txt}";
          $ListResult=str_replace($DataKey,$Value,$ListResult);
          $DataKey="{".$Key."}";
          $ListResult=str_replace($DataKey,nl2br($Value),$ListResult);
        }
        else{
          if($Value=='1'){
            $DataKey="{".$Key."_check}";
            $ListResult=str_replace($DataKey,"checked",$ListResult);
          }
          else{
            $DataKey="{".$Key."_check}";
            $ListResult=str_replace($DataKey,"",$ListResult);
          }
        }
      }
      
      if(function_exists('funcBeforeShowRow')){
        $ListResult=funcBeforeShowRow($ListResult,$rowResult);
      }
      
      $this->TemplateContentList.=$ListResult;
    }
    $ListBefore=substr($this->TemplateContent,0,($PositionStart-$DetailRowStartLength));
    $ListAfter=substr($this->TemplateContent,($PositionEnd+$DetailRowEndLength));
    $this->TemplateContent=$ListBefore.$this->TemplateContentList.$ListAfter;
  }
  
  function ReplaceDatajqxListAuthorization(){
    global $nlldb;
    global $authorization;
    $DetailRowStartLength=strlen('/* jqxList-Start */');
    $DetailRowEndLength=strlen('/* jqxList-End */');
    $PositionStart=strpos($this->TemplateContent,'/* jqxList-Start */')+$DetailRowStartLength;
    $PositionEnd=strpos($this->TemplateContent,'/* jqxList-End */');
    $PositionLength=$PositionEnd-$PositionStart;
    $ListTemplate=substr($this->TemplateContent,$PositionStart,$PositionLength);
    
    $authorization->JQXListStart();
    $this->TemplateContentList=$authorization->setAuthorization($ListTemplate);
    $authorization->JQXListEnd();
    
    
    
    //$RecordStart=($this->CurrentPage-1)*$this->RecordPerPage;
    //$RecordEnd=($this->CurrentPage*$this->RecordPerPage)-1;
    //
    //if($RecordEnd>($this->TotalRecord-1)){
    //  $RecordEnd=($this->TotalRecord-1);
    //}
    //
    //$iRow=0;
    //while($rowResult=$nlldb->fetchAssoc($this->ResultSet)){
    //  $iRow++;
    //  $BeforeRowSpecific1='';
    //  $AfterRowSpecific1='';
    //  
    //  if($this->BeforeDetailRowSpecific1==$iRow){
    //    $BeforeRowSpecific1=$this->DetailRowSpecific1Content;
    //  }
    //  if($this->AfterDetailRowSpecific1==$iRow){
    //    $AfterRowSpecific1=$this->DetailRowSpecific1Content;
    //  }
    //  
    //  $ListResult=$BeforeRowSpecific1.$ListTemplate.$AfterRowSpecific1;
    //  foreach($rowResult as $Key=>$Value){
    //    if(strpos($ListResult,"{".$Key."_check}")===false){
    //      $DataKey="{".$Key."_txt}";
    //      $ListResult=str_replace($DataKey,$Value,$ListResult);
    //      $DataKey="{".$Key."}";
    //      $ListResult=str_replace($DataKey,nl2br($Value),$ListResult);
    //    }
    //    else{
    //      if($Value=='1'){
    //        $DataKey="{".$Key."_check}";
    //        $ListResult=str_replace($DataKey,"checked",$ListResult);
    //      }
    //      else{
    //        $DataKey="{".$Key."_check}";
    //        $ListResult=str_replace($DataKey,"",$ListResult);
    //      }
    //    }
    //  }
    //  
    //  if(function_exists('funcBeforeShowRow')){
    //    $ListResult=funcBeforeShowRow($ListResult,$rowResult);
    //  }
    //  
    //  $this->TemplateContentList.=$ListResult;
    //}
    $ListBefore=substr($this->TemplateContent,0,($PositionStart-$DetailRowStartLength));
    $ListAfter=substr($this->TemplateContent,($PositionEnd+$DetailRowEndLength));
    $this->TemplateContent=$ListBefore.$this->TemplateContentList.$ListAfter;
  }
  
  function ReplaceDataSearch(){
  }
  
  function ReplaceSystem(){
    ob_start();
    include("inc_head.php");
    $incHead=ob_get_clean();
    ob_end_clean();
    
    ob_start();
    include("inc_head2.php");
    $incHead2=ob_get_clean();
    ob_end_clean();
    
    $this->TemplateContent=str_replace("{sysHead}",$incHead,$this->TemplateContent);
    $this->TemplateContent=str_replace("{hidReferer}",$_SERVER['HTTP_REFERER'],$this->TemplateContent);
    $this->TemplateContent=str_replace("{sysHead2}",$incHead2,$this->TemplateContent);
    
    $strNavigator=$this->getNavigator();
    $this->TemplateContent=str_replace("{ListNavigator}",$strNavigator,$this->TemplateContent);
  }
  
  function getNavigator(){
    $Output="";
    $arrQuery=array();
    $File=$_SERVER['PHP_SELF'];
    $this->TotalPage=ceil($this->TotalRecord/$this->RecordPerPage);
    
    foreach($_GET as $key=>$value){
      if($key!='Page'){
        $arrQuery[$key]=$key.'='.$value;
      }
    }
    
    $strQuery=implode("&",$arrQuery);
    $Previous='<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
    $Next='<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
    if($strQuery!=''){
      if($this->CurrentPage>1){
        $Previous='<li><a href="'.$File.'?'.$strQuery.'&Page='.($this->CurrentPage-1).'"><span aria-hidden="true">&laquo;</span></a></li>';
      }
      if($this->CurrentPage<$this->TotalPage){
        $Next='<li><a href="'.$File.'?'.$strQuery.'&Page='.($this->CurrentPage+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
      }
      if(($this->CurrentPage-1)>0){
        $Previous1='<li><a href="'.$File.'?'.$strQuery.'&Page='.($this->CurrentPage-1).'">'.($this->CurrentPage-1).'</a></li>';
      }
      if(($this->CurrentPage-2)>0){
        $Previous2='<li><a href="'.$File.'?'.$strQuery.'&Page='.($this->CurrentPage-2).'">'.($this->CurrentPage-2).'</a></li>';
      }
      if(($this->CurrentPage+1)<=$this->TotalPage){
        $Next1='<li><a href="'.$File.'?'.$strQuery.'&Page='.($this->CurrentPage+1).'">'.($this->CurrentPage+1).'</a></li>';
      }
      if(($this->CurrentPage+2)<=$this->TotalPage){
        $Next2='<li><a href="'.$File.'?'.$strQuery.'&Page='.($this->CurrentPage+2).'">'.($this->CurrentPage+2).'</a></li>';
      }
      $Current='<li class="active"><a href="#">'.$this->CurrentPage.'<span class="sr-only">(current)</span></a></li>';
    }
    else{
      if($this->CurrentPage>1){
        $Previous='<li><a href="'.$File.'?Page='.($this->CurrentPage-1).'"><span aria-hidden="true">&laquo;</span></a></li>';
      }
      if($this->CurrentPage<$this->TotalPage){
        $Next='<li><a href="'.$File.'?Page='.($this->CurrentPage+1).'"><span aria-hidden="true">&raquo;</span></a></li>';
      }
      if(($this->CurrentPage-1)>0){
        $Previous1='<li><a href="'.$File.'?Page='.($this->CurrentPage-1).'">'.($this->CurrentPage-1).'</a></li>';
      }
      if(($this->CurrentPage-2)>0){
        $Previous2='<li><a href="'.$File.'?Page='.($this->CurrentPage-2).'">'.($this->CurrentPage-2).'</a></li>';
      }
      if(($this->CurrentPage+1)<=$this->TotalPage){
        $Next1='<li><a href="'.$File.'?Page='.($this->CurrentPage+1).'">'.($this->CurrentPage+1).'</a></li>';
      }
      if(($this->CurrentPage+2)<=$this->TotalPage){
        $Next2='<li><a href="'.$File.'?Page='.($this->CurrentPage+2).'">'.($this->CurrentPage+2).'</a></li>';
      }
      $Current='<li class="active"><a href="#">'.$this->CurrentPage.'<span class="sr-only">(current)</span></a></li>';
    }
    
    $Output='<nav aria-label="Page navigation"><ul class="pagination">'.$Previous.$Previous2.$Previous1.$Current.$Next1.$Next2.$Next.'</ul></nav>';
    return $Output;
  }
  
  function getDetailRowSpecific1(){
    $DetailRowStartLength=strlen('<!-- Detail Row Specific1 Start -->');
    $DetailRowEndLength=strlen('<!-- Detail Row Specific1 End -->');
    $PositionStart=strpos($this->TemplateContent,'<!-- Detail Row Specific1 Start -->')+$DetailRowStartLength;
    $PositionEnd=strpos($this->TemplateContent,'<!-- Detail Row Specific1 End -->');
    $PositionLength=$PositionEnd-$PositionStart;
    $ListTemplate=substr($this->TemplateContent,$PositionStart,$PositionLength);
    $this->DetailRowSpecific1Content=$ListTemplate;
    
    $ListBefore=substr($this->TemplateContent,0,($PositionStart-$DetailRowStartLength));
    $ListAfter=substr($this->TemplateContent,($PositionEnd+$DetailRowEndLength));
    $this->TemplateContent=$ListBefore.$ListAfter;
  }
  
  function setTag($Input){
    $this->arrTag=$Input;
  }
  
  function setList($Input){
    $this->isList=$Input;
  }
  
  function setListbox($ListKey,$ListOption,$DefaultValue=""){
    $Output="";
    foreach($ListOption as $List){
      if($DefaultValue!='' && $DefaultValue==$List['listValue']){
        $Output.='<option value="'.$List['listValue'].'" selected>'.$List['listText'].'</option>';
      }
      elseif($DefaultValue=='' && $this->ResultSet[0][$ListKey]==$List['listValue']){
        $Output.='<option value="'.$List['listValue'].'" selected>'.$List['listText'].'</option>';
      }
      else{
        $Output.='<option value="'.$List['listValue'].'">'.$List['listText'].'</option>';
      }
    }
    $ListKey="{".$ListKey."_option}";
    $this->TemplateContent=str_replace($ListKey,$Output,$this->TemplateContent);
  }
  
  function setMultipleListbox($ListKey,$ListOption,$arrDefaultValue=array()){
    $Output="";
    $countDefaultValue=count($arrDefaultValue);
    foreach($ListOption as $List){
      if($countDefaultValue>0 && in_array($List['listValue'],$arrDefaultValue)){
        $Output.='<option value="'.$List['listValue'].'" selected>'.$List['listText'].'</option>';
      }
      elseif($countDefaultValue==0 && $this->ResultSet[0][$ListKey]==$List['listValue']){
        $Output.='<option value="'.$List['listValue'].'" selected>'.$List['listText'].'</option>';
      }
      else{
        $Output.='<option value="'.$List['listValue'].'">'.$List['listText'].'</option>';
      }
    }
    $ListKey="{".$ListKey."_option}";
    $this->TemplateContent=str_replace($ListKey,$Output,$this->TemplateContent);
  }
  
  function setResultSet($Input){
    $this->ResultSet=$Input;
    $this->TotalRecord=$this->DB->getNumRows($this->ResultSet);
  }
  
  function setFinalSQL($FinalSQL,$FinalSQLCount){
    global $nlldb;
    $RecordStart=($this->CurrentPage-1)*$this->RecordPerPage;
    $RecordPerPage=$this->RecordPerPage;
    
    if($RecordStart==''){
      $RecordStart="0";
    }

    if($RecordStart!="" && $RecordPerPage!=''){
      $sqlLimit="LIMIT ".$RecordStart.",".$RecordPerPage;
      $FinalSQL=str_replace("{sqlLimit}",$sqlLimit,$FinalSQL);
    }
    $rsFinal=$nlldb->executeQuery($FinalSQL);
    $rsFinalCount=$nlldb->executeQuery($FinalSQLCount);
    $this->ResultSet=$rsFinal;
    
    list($FinalCount)=$nlldb->fetchRow($rsFinalCount);
    $this->TotalRecord=$FinalCount;
  }
  
  function setBeforeDetailRowSpecific1($Input){
    $this->BeforeDetailRowSpecific1=$Input;
  }
  
  function setAfterDetailRowSpecific1($Input){
    $this->AfterDetailRowSpecific1=$Input;
  }
  
  function setTemplate($Input,$ReplaceTag){
    $this->TemplateFile=$Input;
    if($ReplaceTag==''){
      $this->TemplateContent=file_get_contents($this->TemplatePath.$this->TemplateFile);
    }
    else{
      $incContentSub=file_get_contents($this->TemplatePath.$this->TemplateFile);
      $this->TemplateContent=str_replace("{".$ReplaceTag."}",$incContentSub,$this->TemplateContent);
    }
  }
}
  
?>