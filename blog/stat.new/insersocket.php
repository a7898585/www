<?php

/* -------------------------------------------------------------------
  --------------功能描述------------
  ----1、集合每个博客出现的初次更新博客点击统计
  ----2、集合每个博客出现的用户信息更新博客最近访客
  ----3、集合每个博客出现的用户更新用户最近访问的博客记录
  ----4、集合文章id信息更新博客的文章点击统计
  ----5、集合文章的点击信息更新博客文章最近访客
  ----6、集合用户访问的文章信息更新用户最近访问过的博客文章
  ------------------------------------------------------------------- */
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);
define('ROOT', dirname(__FILE__));
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/script.func.php';

echo date('Y-m-d H:i:s');
echo '	';
$lockfile = $config['queepath'].'newlock.lock';

clearstatcache();
//保证单进程跑
if (file_exists($lockfile)) {
    exit('  new_blog has run!');
}
touch($lockfile);

//没有要处理的记录直接退出
if (!file_exists($config['blogstat'])) {
    unlink($lockfile);
    echo '  new_blog no date';
    exit(-1);
}

$prefix = '.bak';
$new_filename = $config['blogstat'] . $prefix;
if (file_exists($new_filename)) {
    unlink($new_filename);
}
//重命名
rename($config['blogstat'], $new_filename);

//变量的定义
$blogStat = array();     //博客点击统计
$artStat = array();     //博客文章点击统计

$blogUserVisitOr = array();     //博客最近访客
$ArtUserVisitOr = array();     //文章最近访客

$userBlogVisitTo = array();     //用户最近访问博客
$userArtVisitTo = array();     //用户最近访问文章
//打开文件
$fp = fopen($new_filename, "r");
//遍历文件内容
while ($str = fgets($fp)) {
    if (empty($str) || trim($str) == '') {
        continue;
    }
    $data = explode(',', $str);

    $vuid = trim($data[0]);
    $duid = trim($data[1]);
    $mid = trim($data[2]);
    $artid = trim($data[3]);
    $domain = trim($data[4]);
    $vusname = trim($data[5]);
    $vnkname = trim($data[6]);
    $blogname = trim($data[7]);
    $big = intval($data[8]);

    //开始记录统计
    if ($mid < 1) {
        continue;
    }

    if ($vuid > 1) {
        //博客访客记录
        if (isset($blogUserVisitOr[$mid][$vuid])) {
            unset($blogUserVisitOr[$mid][$vuid]);
        }
        $blogUserVisitOr[$mid][$vuid] = array('userid' => $vuid, 'username' => $vusname, 'nickname' => $vnkname);
        //用户访问过的博客
        if (isset($userBlogVisitTo[$vuid][$mid])) {
            unset($userBlogVisitTo[$vuid][$mid]);
        }
        $userBlogVisitTo[$vuid][$mid] = array('userid' => $duid, 'memberid' => $mid, 'blogname' => $blogname, 'domain' => $domain);

        if ($artid > 0) {
            //文章访客
            /*
              if(isset($ArtUserVisitOr[$artid][$vuid]))
              {
              unset($ArtUserVisitOr[$artid][$vuid]);
              }
             */
            $ArtUserVisitOr[$artid][$vuid] = array('userid' => $vuid, 'username' => $vusname, 'nickname' => $vnkname);
            //用户访问过的文章
            /*
              if(isset($userArtVisitTo[$vuid][$artid]))
              {
              unset($userArtVisitTo[$vuid][$artid]);
              }
             */
            $userArtVisitTo[$vuid][$artid] = array('userid' => $duid, 'memberid' => $mid, 'blogname' => $blogname, 'domain' => $domain, 'articleid' => $artid);
        }
    }
    //博客统计
    $blogStat[$mid][0] = isset($blogStat[$mid][0]) ? ($blogStat[$mid][0] + (1 * $big)) : (1 * $big);
    $blogStat[$mid][1] = isset($blogStat[$mid][1]) ? ($blogStat[$mid][1] + 1) : 1;

    if ($artid > 0) {
        /*
          if(isset($artStat[$artid]))
          {
          $artStat[$artid] = array();
          }
         */
        $artStat[$artid]['stat'][0] = isset($artStat[$artid]['stat'][0]) ? ($artStat[$artid]['stat'][0] + (1 * $big)) : (1 * $big);
        $artStat[$artid]['stat'][1] = isset($artStat[$artid]['stat'][1]) ? ($artStat[$artid]['stat'][1] + 1) : 1;
        $artStat[$artid]['memid'] = $mid;
    }
}
fclose($fp);
$memcache = new CMemcache;

//更新博客统计
if (!empty($blogStat)) {
    update_sockblogstat($blogStat);
}

//更新文章统计
if (!empty($artStat)) {
    update_sockartstat($artStat);
}

//更新博客访客
if (!empty($blogUserVisitOr)) {
    update_sockblogvisitor($blogUserVisitOr);
}

//更新文章访客
if (!empty($ArtUserVisitOr)) {
    update_sockartvisitor($ArtUserVisitOr);
}

//更新用户访问过的博客
if (!empty($userBlogVisitTo)) {
    update_sockuserblogvisitto($userBlogVisitTo);
}

//更新用户访问过的文章
if (!empty($userArtVisitTo)) {
    update_sockuserartvisitto($userArtVisitTo);
}

$message = date('Y-m-d H:i:s') . " \r\n";
$message .= serialize($blogStat) . " \r\n";
$message .= serialize($artStat) . " \r\n";
$message .= serialize($blogUserVisitOr) . " \r\n";
$message .= serialize($ArtUserVisitOr) . " \r\n";
$message .= serialize($userBlogVisitTo) . " \r\n";
$message .= serialize($userArtVisitTo) . " \r\n";
$logname = '/home/www/html/logs/blogview_' . date('Ymd') . '.log';
error_log($message, 3, $logname);
unlink($lockfile);
clearstatcache();
echo '  new_blog	finish	' . PHP_EOL;
?>
