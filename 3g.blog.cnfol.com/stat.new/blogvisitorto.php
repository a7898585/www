<?php
/*----------------------------------------------------------
	获取博客的最近访客信息，通过Ajax访问
 ------------------------------------------------------------*/
	define('ROOT',	dirname(__FILE__));
	require_once ROOT.'/inc/config.inc.php';
	require_once ROOT.'/cls/CMemcache.php';
	require_once ROOT.'/cls/CSocket.php';
	require_once ROOT.'/func/socket.func.php';
	require_once ROOT.'/func/commen.func.php';
	header('Content-Type: text/html; charset=utf-8');

	$_debug     = isset($_GET['debug'])? 1:0;
	if(!is_numeric($_GET['uid']))
	{
		exit("<p>您至今还访问过其他博客</p>");
	}
	//初始化Memcache
	$memcache  = new CMemcache;

	$userid   = $_GET['uid'];
	$data = get_uservisitto($userid, 'blog', $_debug);   
	$visivor    = isset($data['VBlogs'])? unserialize($data['VBlogs']):array();
	
	if(!is_array($visivor) || count($visivor) < 1)
	{
		exit('<div class="tjwzcz01">该博客暂时没有任何访问记录<br></div>');
	}

	//$output  = '<div class="zjfksz"><div class="zjfksz02">';
	$output  = '<div class="zjfksz02">';
	$i = 0;
	foreach($visivor as $user)
	{
		$i++;
		//$output .= '<div class="zjfksz04"><div class="zjfksz05"><img height="48" border="0" width="48" src="'.getuserhead($user['userid']).'" onerror="this.onerror=\'\';this.src=\''.$config['userhead'].'man_48.png\';" style="cursor: pointer;" onload="javascript:this.style.display=\'\'" ;="" onclick="showuserinfo(\''.$user['userid'].'\',this,event)" title="点击查看个人名片"></div><a href="'.$config['newblog'].$user['domain'].'" target="_blank">'.utf8_str(strtolower($user['blogname']),8,'false').'</a></div>';
		$output .= '<div class="zjfksz04"><div class="zjfksz05"><img height="48" border="0" width="48" src="'.getuserhead($user['userid']).'" onerror="this.onerror=\'\';this.src=\''.$config['userhead'].'man_48.png\';" style="cursor: pointer;" class="refid"  refid="'.$user['userid'].'" ></div><a href="'.$config['newblog'].$user['domain'].'" target="_blank">'.utf8_str(strtolower($user['blogname']),8,'false').'</a></div>';
		if($i >= $config['vBlogToTlCnt'])
		{
			break;
		}
	}
	//$output .= '<div style="clear: both;"></div></div></div>';
	$output .= '<div style="clear: both;"></div></div>';

	echo $output;
?>
