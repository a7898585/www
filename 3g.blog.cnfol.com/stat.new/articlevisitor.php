<?php

/* ----------------------------------------------------------
  获取博客文章的最近访客信息，通过Ajax访问
  ------------------------------------------------------------ */
define('ROOT', dirname(__FILE__));
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/commen.func.php';
header('Content-Type: text/html; charset=utf-8');

$_debug = isset($_GET['debug']) ? 1 : 0;

if (!is_numeric($_GET['aid'])) {
    exit('<p>&nbsp;&nbsp;&nbsp;&nbsp;该文章暂无访客信息记录</p>');
}

//初始化Memcache
$memcache = new CMemcache;

$articleid = $_GET['aid'];
$uid = $_GET['uid'];
$data = get_articlevisit($articleid, $_debug);

$visivor = isset($data['VUsers']) ? unserialize($data['VUsers']) : array();

if (!is_array($visivor) || count($visivor) < 1) {
    exit('<p>&nbsp;&nbsp;&nbsp;&nbsp;还没有登录用户访问过此篇文章!</p>');
}

$userid = isset($_COOKIE['cookie']['passport']['userId']) ? $_COOKIE['cookie']['passport']['userId'] : 0;
session_start();
if (($userid == $uid) && ($userid > 0)) {
    $isowner = true;
} elseif ($_SESSION['ViewAdmin'] == 'admin') {
    $isowner = true;
} else {
    $btnstr = false;
}
$i = 0;
$output = '<ul class="PicLst">';
foreach ($visivor as $user) {
    if ($isowner) {
        $btnstr = '<li onmouseover="document.getElementById(\'vbtn' . $user['userid'] . '\').style.display=\'block\'" onmouseout="document.getElementById(\'vbtn' . $user['userid'] . '\').style.display=\'none\'">';
        $btnstr .= '<code title="删除" onclick="javascript:delvisitor(' . $articleid . ',' . $user['userid'] . ');" style="cursor:pointer;float:right;position:relative;display:none;width:15px;height:15px;left:14px;" id="vbtn' . $user['userid'] . '" ><img src="http://img.cnfol.com/newblog/Version2/images/x2.png" alt="删除" /></code>';
    } else {
        $btnstr = '<li>';
    }
    $output .= $btnstr . '<a href="#"><img class="refid"  refid="' . $user['userid'] . '"  onload="javascript:this.style.display=\'\'" style="cursor: pointer;" onerror="this.onerror=\'\';this.src=\'' . $config['userhead'] . 'man_48.png\';" src="' . getuserhead($user['userid']) . '" /></a>';
    $output .= '<p><a href="http://blog.cnfol.com/returnbolg/' . $user['userid'] . '.html" target="_blank" >' . utf8_str($user['nickname'], 8, 'false') . '</a></p>';
    $output .='</li>';
    $i++;
}
$output .= '</ul>';
if (!isset($_COOKIE['cookie']['passport']['keys'])) {
    $output .= '<p class="LoginTip"><a href="javascript:;" onclick=showiframe("http://blog.cnfol.com/index.php/widget/login",500,300,"",false); >登录</a>后，您的头像将出现在这里。 </p>';
}

echo $output;
?>
