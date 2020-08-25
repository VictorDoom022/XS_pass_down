<?php
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=file.xls" );
$input=stripslashes($_REQUEST['input']);
echo $input;
?>