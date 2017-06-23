<?php
/*----------------------------------------------------------
	获取博客文章的投票信息，通过Ajax访问
 ------------------------------------------------------------*/
	define('ROOT',	dirname(__FILE__));
	require_once ROOT.'/inc/config.inc.php';
	header('Content-Type: text/html; charset=utf-8');

	$_debug     = isset($_GET['debug'])? 1:0;
	
    var_dump($_GET);
?>
