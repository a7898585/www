<?php
error_reporting(E_ALL ^ E_NOTICE);
define('IN_MHESITE', TRUE);
define('MHROOT', substr(dirname(__FILE__), 0, -9));
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
//载入全局函数
require_once MHROOT.'./MHinclude/gobal.fun.php';
//载入配置文件
require_once MHROOT.'./MHinclude/config.inc.php';
if(PHP_VERSION < '4.1.0') {
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}
foreach(array('_COOKIE', '_POST', '_GET') as $_request) {
	foreach($$_request as $_key => $_value) {
		$_key{0} != '_' && $$_key = daddslashes($_value);
	}
}
//设置时区
ini_set('date.timezone','Asia/Shanghai');
unset($_request, $_key, $_value);
//载入mysql文件
require_once MHROOT.'./MHinclude/mysql.class.php';
//打开数据库连接
?>