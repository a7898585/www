<?php

define('ROOT', dirname(__FILE__));
define('DEFAULT_PATH', '/home/httpd/');
define('BLOG_MANAGE_LOG', DEFAULT_PATH . 'logs/blog_manage/'); //后台日志   /home/httpd/logs/blog_manage/Sitemap 
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/commen.func.php';

require_once ROOT . '/library/ADODB/adodb.inc.php';
header('Content-Type: text/html; charset=utf-8');
global $mysqli;

global $gjc;
//$gjc = file(DEFAULT_PATH . 'gjc.txt');
$gjc = array('爱唯侦察','白咲舞');
error_log(date("Y-m-d H:i:s", time()) . " || ------start------|\r\n", 3, BLOG_MANAGE_LOG.'socket_keyfilter_load.log');
print_r($config);
$mysqli = NewADOConnection($config['blog_db']['type']);
if (!$mysqli->Connect($config['blog_db']['hostname'], $config['blog_db']['username'], $config['blog_db']['password'], $config['blog_db']['database'])) {
    error_log(date("Y-m-d H:i:s", time()) . " || ------MYSQL could not connect3------|\r\n", 3, BLOG_MANAGE_LOG.'socket_keyfilter_load.log');
    exit;
}
$mysqli->Execute("set names latin1");

/*
 * 	删除博客文章
 */

function delblogarticle($DelIDs) {
    $socket = new CSocket();
    $param['MemberID'] = $DelIDs['MemberID'];
    $param['IsDel'] = 4; //系统删除
    $param['ArticleIDs'] = $DelIDs['ArticleID'];
    $param['AppearTimes'] = $DelIDs['AppearTime'];
    $type = "B244";
    $rs = $socket->senddata($type, $param);
//    print_r($rs);
    if (isset($rs['Status']['Code']) && $rs['Status']['Code'] == '00') {
        error_log(date("Y-m-d H:i:s", time()) . " || GW：| 状态码：{$rs['Code']} | 参数：" . serialize($param) . "\r\n", 3, BLOG_MANAGE_LOG.'socket_keyfilter_suc.log');
//        echo '/-id：' . $DelIDs['ArticleID'] . '-succes--/';
    } else {
        error_log(date("Y-m-d H:i:s", time()) . " || GW：| 状态码：{$rs['Code']} | 参数：" . serialize($param) . "\r\n", 3, BLOG_MANAGE_LOG.'socket_keyfilter_error.log');
//        echo '/-id：' . $DelIDs['ArticleID'] . '-error--/';
    }
    return true;
}
