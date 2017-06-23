<?php

/* ----------------------------------------------------------
  设置博客文章的投票信息，通过Ajax访问
  ------------------------------------------------------------ */
define('ROOT', dirname(__FILE__));
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/commen.func.php';
header('Content-Type: text/html; charset=utf-8');

if (strpos($_GET['artid'], '-') === false) {
    $data['ArticleID'] = $_GET['artid'];
} else {
    $temp = explode('-', $_GET['artid']);
    $data['AppearTime'] = date("Y-m-d H:i:s", $temp[0]);
    $data['ArticleID'] = $temp[1];
}
$data['MemberID'] = intval($_GET['memid']);
$loginuserid = intval($_POST['userid']);

if ($data['ArticleID'] < 1 || getVerifyStr($data['ArticleID']) != $_POST['code']) {
    echo json_encode(array('errno' => 'failed', 'error' => '投票请求失败'));
    exit;
} elseif (checkHasVote($data['ArticleID'])) {
    echo json_encode(array('errno' => 'failed', 'error' => '你已经赞过了'));
    exit;
} else {
    //初始化Memcache
    $memcache = new CMemcache;
    //保存成功
    if (!isMobile()) {
        $name = 'blogartvote' . $data['ArticleID'];
        $pre = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
        $isuservote = $memcache->get($name . $pre);
        if ($isuservote) {
            echo json_encode(array('errno' => 'failed', 'error' => '你已经赞过了'));
            exit;
        }
    }
    $flag = set_articlevote($data);
    if ($flag) {
        //清除文章缓存
        $ckey = str_replace('{ArticleID}', $data['ArticleID'], $config['keys']['articlestat']);
        $ckey = str_replace('{MemberID}', $data['MemberID'], $ckey);
        $memcache->delete($ckey);
        //清除文章缓存
        //删除我的博客首页1-15条缓存
        //$indexckeynum = str_replace('{MemberID}', $loginuserid, $config['keys']['K1076']);
        //$flag1=$memcache->delete($indexckeynum);

        $indexckey = str_replace('{MemberID}', $loginuserid, $config['keys']['K1077']);
        $flag2 = $memcache->delete($indexckey);
        //error_log($_POST['userid'].'|'.$flag1.'|'.$flag2.'|'.$indexckeynum,3,'/home/httpd/logs/a1111111.log');
        //删除我的博客首页1-15条缓存

        $myindexckey = str_replace('{MemberID}', $data['MemberID'], $config['keys']['K1015']);
        $memcache->delete($myindexckey);

        //记录统计队列
        echo json_encode(array('errno' => 'succ', 'error' => ''));
        exit;
    }
    //记录统计队列
    echo json_encode(array('errno' => 'failed', 'error' => '投票请求失败'));
    exit;
}
?>
