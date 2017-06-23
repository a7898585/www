<?php

/* ----------------------------------------------------------
  获取博客点赞列表  bloggoldstatlist   1.150   1.210  6.151 服务器跑
  ------------------------------------------------------------ */
define('ROOT', dirname(__FILE__));
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/commen.func.php';
header('Content-Type: text/html; charset=utf-8');
$memcache = new CMemcache;
$data['nTimes'] = getnTimes();
$data['nStart'] = -1;
$res = getStatList($data);
$total = $res['UTopCnt'];
print_r($res);
if ($total > 0) {
    $data['nStart'] = 0;
    $data['nCount'] = 30;
    $data['FlagCode'] = $res['FlagCode'];
    $list = getStatList($data);
    if ($list['RetRecords'] == 1) {
        $list['Record'] = array(0 => $list['Record']);
    }
    print_r($list);
}


