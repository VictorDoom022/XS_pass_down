<?php
?>
<script language="Javascript">
if(typeof DateFormat=='undefined'){
  var DateFormat = new globalDateFormat('DDMMYYYY','/');
}
$(document).ready(function(){
  $('.form_date').datetimepicker({
    format: 'dd/mm/yyyy',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0
  });

  $('.form_date').change(function(){
    if($(this).val()!=""){
      DateFormat.dateCheck(this);
      $(this).datetimepicker('update');
    }
  });

  $('.form_date_from').change(function(){
    date_to_id=$(this).attr("date_to");
    if($(this).val()!="" && $('#'+date_to_id).val()!=""){
      DateFormat.chk_from_date(this,document.getElementById(date_to_id));
    }
  });
  
  $('.form_date_to').change(function(){
    date_from_id=$(this).attr("date_from");
    if($(this).val()!="" && $('#'+date_from_id).val()!=""){
      DateFormat.chk_to_date(this,document.getElementById(date_from_id));
    }
  });
  
  /*jqWidget Function (start)*/

  /*jqWidget Function (end)*/
});
</script>