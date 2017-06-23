<?php

if (!defined('ROOT'))
    exit('access deny!');

global $config;
//定义socket
$config['blog_socket'] = array(
    'key' => str_pad('', 32),
    //'host' => 'test.gw.cnfol.net',
    //'host' => '172.20.1.235',
    //'host' => '172.20.2.198', 


    'host' => 'gw.blog.cnfol.net',
    'port' => 6666


        //'port' => 443
);
//定义队列文件的存放位置
$config['queepath'] = ROOT . '/cache/';
$config['blogstat'] = $config['queepath'] . 'blogstat.dat';
$config['artvote'] = $config['queepath'] . 'artvote.dat';

//头像基本地址
$config['userhead'] = 'http://head.cnfolimg.com/';
$config['passport'] = 'http://passport.cnfol.com/';
$config['domain'] = 'new.blog.cnfol.com';
$config['newblog'] = 'http://' . $config['domain'] . '/';
//memcache的配置
$config['cache']['server'] = array(
    array('host' => 'memcache7.cache.cnfol.com', 'port' => 11211),
    array('host' => 'memcache8.cache.cnfol.com', 'port' => 11211)
        //array('host'=>'192.168.15.107','port'=>11211)
);
$config['cache']['compress'] = false;
$config['cache']['expire'] = 86400;     //15分钟过期
$config['cache']['prefix'] = 'CNfol_Blog_';

//点击数比例
srand(time());
$config['clickrank'] = rand(1, 3);

//博客访客记录数
$config['vBlogTlCnt'] = 8;

//用户最新的访问博客记录
$config['vBlogToTlCnt'] = 8;

//用户最新的访问文章记录
$config['vArtToTlCnt'] = 10;

//文章访客记录数
$config['vArtTlCnt'] = 8;

//点击数比例
$config['voterank'] = rand(1, 9);
//缓存Key的设置规则
//投票统计
$config['keys']['articlevote'] = 'vote_article_stat_articleid';
//博客统计
$config['keys']['blogclick'] = 'stat_click_memberid';
//文章点击统计
$config['keys']['articleclick'] = 'article_click_articleid';


//博客访客
$config['keys']['blogvisitor'] = 'visitor_blog_memberid';
//文章访客
$config['keys']['articlevisitor'] = 'visitor_article_articleid';


//用户访问过的博客
$config['keys']['blogvisitorto'] = 'visitorto_blog_userid';
//用户访问过的文章
$config['keys']['artvisitorto'] = 'visitorto_art_userid';

//文章状态	
$config['keys']['articlestat'] = 'Blogarticle_getBlogArticleStatByID_{MemberID}_{ArticleID}';
?>
