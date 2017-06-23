<?php

/* ----------------------------------------------------------
  获取博客的最近访客信息，通过Ajax访问
  ------------------------------------------------------------ */
define('ROOT', dirname(__FILE__));
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/commen.func.php';
header('Content-Type: text/html; charset=utf-8');

$_debug = isset($_GET['debug']) ? 1 : 0;
if (!is_numeric($_GET['mid'])) {
    exit("<p>还没有登陆用户访问过此篇博客...</p>");
}
//初始化Memcache
$memcache = new CMemcache;

$memberid = $_GET['mid'];
$data = get_blogvisitor($memberid, $_debug);
$visivor = isset($data['VUsers']) ? unserialize($data['VUsers']) : array();

if (!is_array($visivor) || count($visivor) < 1) {
    exit('<div class="tjwzcz01">暂无最近访客信息<br></div>');
}

//$output  = '<div class="zjfksz"><div class="zjfksz02">';
$output = '<div class="zjfksz02">';

foreach ($visivor as $user) {
    //$output .= '<div class="zjfksz04"><div class="zjfksz05"><img height="48" border="0" width="48" src="'.getuserhead($user['userid']).'" onerror="this.onerror=\'\';this.src=\''.$config['userhead'].'man_48.png\';" style="cursor: pointer;" onload="javascript:this.style.display=\'\'" ;="" onclick="showuserinfo(\''.$user['userid'].'\',this,event)" title="点击查看个人名片"></div><a href="'.$config['passport'].'member/profile/'.$user['userid'].'" target="_blank">'.utf8_str($user['nickname'],8,'false').'</a></div>';
    $output .= '<div class="zjfksz04"><div class="zjfksz05"><img height="48" border="0" width="48" class="refid"  refid="' . $user['userid'] . '" src="' . getuserhead($user['userid']) . '" onerror="this.onerror=\'\';this.src=\'' . $config['userhead'] . 'man_48.png\';" style="cursor: pointer;" ></div><a href="' . $config['passport'] . 'otherpersoninfo' . $user['userid'] . '.html" target="_blank">' . utf8_str($user['nickname'], 8, 'false') . '</a></div>';
}
//$output .= '<div style="clear: both;"></div></div><div style="clear: both;"></div></div>';
$output .= '<div style="clear: both;"></div></div><div style="clear: both;"></div>';

echo $output;
?>
