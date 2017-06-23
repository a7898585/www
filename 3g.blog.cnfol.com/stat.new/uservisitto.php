<?php
/*-------------------------------------------------------------------
	--------------功能描述------------
		----1、获取指定用户最近访问过的博客或者文章
 -------------------------------------------------------------------*/

	define('ROOT',dirname(__FILE__));
	require_once ROOT.'/inc/config.inc.php';
	require_once ROOT.'/cls/CMemcache.php';
	require_once ROOT.'/cls/CSocket.php';
	require_once ROOT.'/func/socket.func.php';
	require_once ROOT.'/func/commen.func.php';
	header('Content-Type: text/html; charset=utf-8');

	$data   = array();
	$Str    = '';
    $_debug = isset($_GET['debug'])? 1:0;
	$type	= $_GET['type'];
	$userid = intval($_GET['uid']);
	//初始化Memcache
	$memcache  = new CMemcache;
	switch($type)
	{
		case 'blog':
			//获取最近访问过的博客
			$data = get_uservisitto($userid, $type, $_debug);
			$data = unserialize($data['VBlogs']);
			if(empty($data))
			{
				echo '您暂时没有访问过任何博客';
				exit(-1);
			}
			foreach($data as $vblog)
			{
				$Str .= '<a target="_blank" href="'.$config['newblog'].$vblog['domain'].'">'.$config['newblog'].$vblog['domain'].'</a>（<a target="_blank" href="'.$config['newblog'].$vblog['domain'].'">'.$vblog['blogname'].'</a>）<br />';
			}
			break;
		case 'article':
			//获取最近访问过的文章
			$data = get_uservisitto($userid, $type, $_debug);  
			$varts= unserialize($data['VArticles']);
			if(empty($varts))
			{
				echo '您暂时没有访问过任何博客文章';
				exit(-1);
			}
			foreach($varts as $vart)
			{
				$Str .= '<a target="_blank" href="'.$config['newblog'].$vart['domain'].'/article/'.$vart['articleid'].'.html">';
				$Str .= $config['newblog'].$vart['domain'].'/article/'.$vart['articleid'].'.html';
				$Str .= '</a><br />';
			}
			break;
		default:
			echo '<!--请选择 操作-->';
			exit(-1);
	}
	echo $Str;
?>