<?php
require_once 'MHinclude/common.inc.php';
if($BookID){
	$sql = "insert into mh_error(`BookID`) values('$BookID')";
	$db = new db ( $db_config );
	$db->ExecuteNoneQuery ( $sql );
	if ($getID = mysql_insert_id ())echo 'ok';
}
?>