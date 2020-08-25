<?php
require_once("inc_load.php");
$sql = "SELECT glob_var_value FROM globalsetting WHERE glob_grp_id='100' AND glob_no='69' AND glob_var_name='Multicurrency Priority';";
$result=$nlldb->executeQuery($sql);
list($docstat_close) = $nlldb->fetchRow($result);
echo json_encode($docstat_close);
?>