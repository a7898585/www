<?php

/*
  ----------------------------------------------------------------------------------------------------------------------
  |
  |    css javascript image 的设置
  |
  ----------------------------------------------------------------------------------------------------------------------
 */
$config['estbase'] = 'http://img.cnfol.com/';
$config['estblog'] = $config['estbase'] . 'newblog/batev6.0';

$config['imgsite'] = 'http://img.cnfol.com';
$config['imagesite'] = 'http://images.cnfol.com';
$config['hssite'] = 'http://hs.cnfol.com';
$config['headurl'] = 'http://head.cnfolimg.com';

$config['buildcssurl'] = $config['imgsite'] . '/createcss/BuildCss.php';
$config['cssfileurl'] = $config['imgsite'] . '/newcss/blog';
$config['photourl'] = $config['imgsite'] . '/upload/album';
$config['uploadpath'] = '/album';
$config['spacesize'] = 50 * 1024 * 1024;  //相册大小 默认50M空间 目前暂时没用

$config['buildcssimg'] = $config['imgsite'] . '/createcss/shots.php?type=add&key=b7571e3e5f86bc516d6b&content=';

$config['cssimgloading'] = 'http://i1.cnfolimg.com/uploads/blog/madeimg.gif';
$config['cssimgcookiet'] = 120;
$config['csspreviewdomain'] = 'blog_help';
$config['buildcsskey'] = 'be034ce95af9b9acd17dcbba93b57e84';

$config['uploadbasepath'] = '/home/html/cnfol/img.cnfol.com';
$config['sync_caches_key'] = 'cnfol_simlife';
$config['sync_upload_url'] = "http://img.cnfol.com/FileList.php"; //附件同步到其他服务器包括删除

$config['logbasepath'] = '/home/www/html/logs/';

/*
  ----------------------------------------------------------------------------------------------------------------------
  |
  |    passport 的设置
  |
  ----------------------------------------------------------------------------------------------------------------------
 */
$config['passporturl'] = 'http://passport.cnfol.com/';
$config['logintry'] = true;  //后踢前开关

/*
  -----------------------------------------------------------------------------------------------------------------------
  |
  |    博客操作权限定义
  |
  -----------------------------------------------------------------------------------------------------------------------
 */

$config['accesskey']['TemplateAdvanced'] = '1193';      //进入高级模板
$config['accesskey']['TemplateDefine'] = '1194';      //自定义模板
$config['accesskey']['TemplateChoose'] = '1195';      //选择模板
$config['accesskey']['UseDefineStyle'] = '1196';  //使用自定义模板
$config['accesskey']['ShareDefineStyle'] = '1197';  //共享自定义样式
$config['accesskey']['DelPhotoComment'] = '1198';      //删除评论
$config['accesskey']['SetPhotoTag'] = '1199';      //给图片贴标签
$config['accesskey']['AddBlog'] = '1200';  //开通博客
$config['accesskey']['BlogStat'] = '1201';  //博客统计
$config['accesskey']['AddDefineStyle'] = '1202';  //添加自定义样式
$config['accesskey']['EditDefineStyle'] = '1203';  //编辑自定义样式
$config['accesskey']['EditPhoto'] = '1213';  //编辑相片
$config['accesskey']['DelPhoto'] = '1214';  //删除相片

$config['accesskey']['GetTagList'] = '1215';  //获取标签类表
$config['accesskey']['IsAddAlbum'] = '1216';  //是否开通相册
$config['accesskey']['DelTemPlate'] = '1233';  //删除模板
$config['accesskey']['AddAlbum'] = '1254';  //添加相册
$config['accesskey']['EditAlbum'] = '1255';  //编辑相册
$config['accesskey']['DelAlbum'] = '1256';  //删除相册
$config['accesskey']['AddArticleSort'] = '1083';  //添加文章分类
$config['accesskey']['EditArticleSort'] = '1084';  //编辑文章分类
$config['accesskey']['DelArticleSort'] = '1086';  //删除文章分类
$config['accesskey']['MangeComment'] = '1087';  //管理页查看所有评论
$config['accesskey']['AticleComment'] = '1088';  //文章评论
$config['accesskey']['DelComment'] = '1089';  //删除评论
$config['accesskey']['EditTag'] = '1093';  //编辑标签

$config['accesskey']['TagList'] = '1094';      //标签列表
$config['accesskey']['LinkSort'] = '1098';  //友情链接分类
$config['accesskey']['AddArticle'] = '2';    //发表文章
$config['accesskey']['EditArticle'] = '3';    //编辑文章
$config['accesskey']['DelArticle'] = '319';  //删除文章
$config['accesskey']['ArticleList'] = '271';  //文章列表
$config['accesskey']['ArticleSortList'] = '1085';     //文章分类列表
$config['accesskey']['DelLink'] = '1103';  //删除友情链接
$config['accesskey']['AfficheShow'] = '1106';  //显示公告
$config['accesskey']['TemplateShow'] = '1108';  //查看模板
$config['accesskey']['DelTrackBack'] = '1109';  //删除引用公告
$config['accesskey']['CannelLink'] = '1091';  //取消博客链接
$config['accesskey']['AddTag'] = '1092';  //添加标签

$config['accesskey']['DelTag'] = '1095';  //删除标签
$config['accesskey']['AddLinkSort'] = '1096';  //友情链接分类
$config['accesskey']['EditLinkSort'] = '1097';  //编辑友情链接分类
$config['accesskey']['DelLinkSort'] = '1099';  //删除友情链接分类
$config['accesskey']['AddLink'] = '1100';  //添加友情链接
$config['accesskey']['EditLink'] = '1101';  //编辑友情链接
$config['accesskey']['LinkList'] = '1102';  //友情链接列表
$config['accesskey']['ConfigShow'] = '1104';  //查看博客配置
$config['accesskey']['SaveConfig'] = '1105';  //保存博客配置
$config['accesskey']['EditAffiche'] = '1107';  //编辑公告
$config['accesskey']['PayArticlSort'] = '1191';  //付费栏目
$config['accesskey']['PayArticleShow'] = '1192';  //付费文章
$config['accesskey']['OnlineDisplay'] = '1331';  //博客直播的权限

/*
  -----------------------------------------------------------------------------------------------------------------------
  |
  |    博客系统模块定义
  |
  -----------------------------------------------------------------------------------------------------------------------
 */
$config['sysmodules'][1] = array('Mod_Affiche', '1', '公告', 'devshow_affiche.shtml');
$config['sysmodules'][2] = array('Mod_RemmentArticle', '2', '推荐文章', 'devshow_remmendarticle.shtml');
$config['sysmodules'][3] = array('Mod_Label', '3', '我的标签', 'devshow_taglist.shtml');
$config['sysmodules'][4] = array('Mod_MyFans', '4', '我的粉丝/我的关注', 'devshow_fanslist.shtml');
$config['sysmodules'][5] = array('Mod_Stat', '5', '访问统计', 'devshow_stat.shtml');
$config['sysmodules'][6] = array('Mod_NearVisitor', '6', '最近访客', 'devshow_renewvisitor.shtml');
$config['sysmodules'][7] = array('Mod_Comm', '7', '最新评论', 'devshow_newestcomment.shtml');
$config['sysmodules'][8] = array('Mod_ArticleSort', '8', '文章分类', 'devshow_articlesort.shtml');
$config['sysmodules'][9] = array('Mod_ArchiveList', '9', '文章存档', 'devshow_archive.shtml');
$config['sysmodules'][11] = array('Mod_Album', '10', '我的相册', 'devshow_album.shtml');
$config['sysmodules'][12] = array('Mod_Calendar', '11', '日历', 'devshow_calendar.shtml');
$config['sysmodules'][13] = array('Mod_Link', '12', '友情链接', 'devshow_friendlinks.shtml');

$config['sysmodules'][10] = array('Mod_ArtList', '10', '文章列表', 'devshow_articlelist.shtml');

$layoutbaseurl = $config['imgsite'] . '/newblog/js/new/';
$config['layout'][0] = array('100%', '0%', '0%', $layoutbaseurl . 'layout6.gif');
$config['layout'][1] = array('76%', '24%', '0%', $layoutbaseurl . 'layout2.gif');
$config['layout'][2] = array('24%', '76%', '0%', $layoutbaseurl . 'layout1.gif');
$config['layout'][3] = array('50%', '50%', '0%', $layoutbaseurl . 'layout5.gif');
$config['layout'][4] = array('30%', '46%', '24%', $layoutbaseurl . 'layout3.gif');
$config['layout'][5] = array('20%', '40%', '40%', $layoutbaseurl . 'layout4.gif');

$bgbaseurl = $config['imgsite'] . '/images/bg/';
$config['bgurl'][0] = array('13', '');
$config['bgurl'][1] = array('17', $bgbaseurl . 'bg4.jpg');
$config['bgurl'][2] = array('19', $bgbaseurl . 'bg5.jpg');
$config['bgurl'][3] = array('23', $bgbaseurl . 'bg10.jpg');
$config['bgurl'][4] = array('24', $bgbaseurl . 'bg11.jpg');
$config['bgurl'][5] = array('14', $bgbaseurl . 'bg3.jpg');
$config['bgurl'][6] = array('16', $bgbaseurl . 'bg12.jpg');
$config['bgurl'][7] = array('15', $bgbaseurl . 'bg6.jpg');
$config['bgurl'][8] = array('20', $bgbaseurl . 'bg8.jpg');
$config['bgurl'][9] = array('22', $bgbaseurl . 'bg9.jpg');

$config['cssimgurl'] = $config['imgsite'] . '/upload/newcss/blog';
$config['defaultcss']['layoutid'] = 2;
$config['defaultcss']['lmods'] = array('13', '8', '6', '2');
$config['defaultcss']['mmods'] = array('1', '4', '9');
$config['defaultcss']['rmods'] = array('5', '10', '11');
$config['defaultcss']['bgpic'] = '';
$config['defaultcss']['bgtype'] = '1';

$config['default']['linksort'] = array(5, '默认分类');
$config['default']['articlesort'] = array(18295, '默认分类');
$config['default']['articlemycollect'] = array(18296, '我的收藏');
$config['defaultgroup'] = '1'; //注册设置博客默认组

$config['sysTagList'] = array(
    1461 => '股市天地', 1445 => '基金', 1463 => '财经杂谈',
    1465 => '外汇', 1433 => '期指期货', 1447 => '理财消费', 1464 => '港股',
    1449 => '保险', 1451 => '银行', 1453 => '黄金', 1462 => '白银', 1457 => '债券',
    1455 => '汽车', 1469 => '权证', 1459 => '休闲区', 1470 => '广告区', 1471 => '美酒',
    1460 => '投资收藏', 1446 => '信托'
);

/*  $config['sysTagList']   = array(
  1461=>'股市天地',1445=>'基金',1463=>'财经杂谈',
  1465=>'外汇',1433=>'期指期货',1447=>'理财消费',1464=>'港股',
  1449=>'保险',1451=>'银行',1453=>'黄金',1457=>'债券',
  1455=>'汽车',1469=>'权证',1459=>'休闲区',1470=>'广告区',1471=>'美酒'
  ); */

//属于这些组的博客直接文章直接采用或者推荐
$config['recommendgroup'] = array('64' => 1, '65' => 2, '66' => 3);  //推荐，名家，高手
//属于采用组、高手看盘、名家看市组的用户浏览过的文章进行展示
$config['recommendgroupshow'] = array('63' => 1, '65' => 2, '66' => 3);  //推荐，名家，高手

$config['isuse'] = 63;       //采用组

$config['adgroup'] = 85;        //被定为发广告的组

$config['autoaudit'] = 105;       //已审组
//$config['online']		                 = 63;

$config['limitgroups'] = 23;       //禁止博客排行的组


$config['limittags'] = array('1459', '1470');  //系统标签属于灌水区和休息区的不做推荐和采用

$config['adgrouptagid'] = 1472;      //广告组文章分类ID

$config['offcommentid'] = 164;       //用户中心设置的禁止评论的组

$config['offrank'] = 112;       //禁止参与博客排行
//不受限制的IP
$config['accessip'] = array(
    '/218\.85\.72\.1/',
    '/172\.20\.[1|2]\.[0-9]+/',
    '/192\.168\.([10|15|16|17]+)\.([0-9]+)/',
    '/127\.0\.0\.1/'
);
/*
  -----------------------------------------------------------------------------------------------------------------------
  |
  |    手工编辑数据库设置  www8.cnfol.com
  |
  -----------------------------------------------------------------------------------------------------------------------
 */
$config['hw']['server'] = '172.20.1.153';
$config['hw']['username'] = 'cnfol_blog';
$config['hw']['password'] = 'JKJKFU787JJ3ljhf';
$config['hw']['dbname'] = 'cnfolv3_fol_admin';
$config['hw']['charset'] = 'utf8';

/*
 *   
 */
$config['showc']['articlelist'] = 30; //文章列表展示数目
$config['showc']['recommendlist'] = 10; //文章列表展示数目

//关键词过滤
$config['keywordlist'] = array('烛光晚会', '六四', '联名信', '穿纪念衫', '快闪', '点蜡烛'); //关键词过滤
$config['keywordclose'] = array('****', '**', '***', '****', '**', '***'); //关键词过滤

$config['statday'] = 06;       //贵金属点赞开始月分
?>
