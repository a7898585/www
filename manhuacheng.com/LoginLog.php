<?php

		echo 'ok';
		die();
// 以上代码只是临时处理成正确的返回值，，具体逻辑东霖尽快分析并修复！！！





/*
 * 用来记住登陆次数的，安装次数
 */
require_once 'MHinclude/common.inc.php';
$LoginCount = intval($_GET['LoginCount']);
$ParentID = intval($_GET['ParentID']);
$ParentID = intval($_GET['ParentID']);
$Type = intval($_GET['Type']);
if ($LoginCount && $ParentID) {
	//获取ip
	if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] ))
		$ip = $_SERVER ['HTTP_CLIENT_IP'];
	else if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))
		$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
	else
		$ip = $_SERVER ['REMOTE_ADDR'];
	$LoginTime = date ( "Y-m-d H:i:s" );
	$db = new db ( $db_config );
	$loginsql = "insert into mh_loginlog(`IP`,`LoginCount`,`Type`,`ParentID`,`LoginTime`) values('$ip','$LoginCount','$Type','$ParentID','$LoginTime')";
	$db->ExecuteNoneQuery ( $loginsql );
	if ($getID = mysql_insert_id ())
		echo 'ok';
		else echo 'false';
}
?>