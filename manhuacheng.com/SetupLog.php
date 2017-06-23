<?php
require_once 'MHinclude/common.inc.php';

if ($SN && $ParentID){
	if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] ))
		$ip = $_SERVER ['HTTP_CLIENT_IP'];
	else if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))
		$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
	else
		$ip = $_SERVER ['REMOTE_ADDR'];
	
	$LoginTime = date ( "Y-m-d H:i:s" );
	$sql = "insert into mh_setuplog(IP,Type,ParentID,SN,LogTime) values('$ip','$Type','$ParentID','$SN','$LoginTime')";
	$db = new db ( $db_config );
	$db->ExecuteNoneQuery ( $sql );
	if ($getID = mysql_insert_id ())
		echo 'ok';
}else echo 'false';
?>