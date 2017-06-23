<?php

/*
  -----------------------------------------------------------------------------
  |
  |    频道模板的设定
  |
  -----------------------------------------------------------------------------
 */

define('DEFAULT_PATH', '/home/www/html/'); //modify apache's DocumentRoot
define('UPLOAD_FILE_PATH', DEFAULT_PATH . 'img.cnfol.com/upload'); //本地附件路径/home/www/html/img.cnfol.com
define('UPLOAD_FILE_LOCALPATH', DEFAULT_PATH . 'newestblog/attached/'); //博客设置背景附件路径

define('BLOG_INDEX_LOG', DEFAULT_PATH . 'logs/blogindex/'); //前台日志 /home/www/html/logs/blogindex/article

$config['shtml_path'] = dirname(APPPATH) . '/shtml/';
$config['filter_path'] = dirname(APPPATH) . '/expansion/';
$config['tmpupload'] = dirname(APPPATH) . '/userupload/';
$config['attached'] = dirname(APPPATH) . '/attached/';
$config['views_path'] = APPPATH . 'views/';
$config['module_path'] = $config['views_path'] . 'module/';

$config['weiboapi_url'] = "http://t.cnfol.com/ajax/publicweibo";

$config['block']['common_login'] = $config['module_path'] . 'common_login.shtml'; //登录条
$config['block']['channalhead'] = $config['module_path'] . 'channal_header.shtml';
$config['block']['channalfoot'] = $config['module_path'] . 'channal_footer.shtml';
$config['block']['channalgoldenfoot'] = $config['module_path'] . 'channal_golden_footer.shtml';
$config['block']['personalhead'] = $config['module_path'] . 'personal_header.shtml';
$config['block']['repersonalhead'] = $config['module_path'] . 'repersonal_header.shtml';
$config['block']['personalfoot'] = $config['module_path'] . 'personal_footer.shtml';
$config['block']['logintool'] = $config['module_path'] . 'common_login.shtml';
//登录条+导航
$config['block']['devpersonallogin'] = $config['module_path'] . 'devpersonal_login.shtml';
//我的博客 头部
$config['block']['devmyblogloginheader'] = $config['module_path'] . 'myblog_login_header.shtml';
//我的博客 内容右边公共页
$config['block']['devmyblogcommonright'] = $config['views_path'] . 'manage/common/myblog_right_common.html';
$config['block']['devmyblogcommonfooter'] = $config['views_path'] . 'manage/common/myblog_common_footer.html'; //我的博客 内容底部公共页

$config['block']['channalgoldenhead'] = $config['module_path'] . 'channal_golden_header.shtml';

//频道页菜单
$config['block']['categorymenu'] = $config['module_path'] . 'channal_categorymenu.shtml';

//美酒博客头，尾
$config['block']['mjbk_channalhead'] = $config['module_path'] . 'channal_header_mjbk.shtml';
$config['block']['mjbk_channalfoot'] = $config['module_path'] . 'channal_footer_mjbk.shtml';

$config['block']['register_home'] = 'register_home.shtml';
$config['block']['registerindex'] = 'register_index.shtml';
$config['block']['registerindex1'] = 'register_index1.shtml';
//博客设置
$config['block']['configcache'] = 'config/manage_configcache.shtml';
$config['block']['configcachetitle'] = '缓存管理';
$config['block']['configedit'] = 'config/devmanage_configEdit.shtml';
$config['block']['configedittitle'] = '编辑常规配置';
$config['block']['affichedit'] = 'config/manage_affichedit.shtml';
$config['block']['affichedittitle'] = '编辑公告';
$config['block']['advanced'] = 'config/manage_advanced.shtml';
$config['block']['advancedtitle'] = '高级设置';
$config['block']['tindexcss'] = 'config/manage_addcss.shtml';
$config['block']['tindexcsstitle'] = '高级模板设置';
$config['block']['blogstat'] = 'config/manage_blogstat.shtml';
$config['block']['blogstattitle'] = '访问统计';
$config['block']['blackuser'] = 'config/manage_blackuser.shtml';
$config['block']['blackusertitle'] = '黑名单管理';
$config['block']['managedefine'] = 'config/manage_define.shtml';
$config['block']['definetitle'] = '自定义模板';
$config['block']['manageaddcss'] = 'config/manage_addcss.shtml';
$config['block']['manageaddcsstitle'] = '添加CSS样式';
$config['block']['managedelcsstitle'] = '编辑CSS样式';
$config['block']['mysharelist'] = 'config/manage_sharelist.shtml';
$config['block']['mysharelisttitle'] = '共享列表';

//文章设置
$config['block']['articleadd'] = 'article/manage_articleadd.shtml';
$config['block']['articleaddtitle'] = '添加文章';
$config['block']['articlelist'] = 'article/devmanage_articlelist.shtml';
$config['block']['articlelisttitle'] = '文章列表';
$config['block']['articleedit'] = 'article/manage_articleedit.shtml';
$config['block']['articleedittitle'] = '编辑文章';
$config['block']['commentarticlelist'] = 'article/devmanage_articlecomment.shtml';
$config['block']['commentarticlelisttitle'] = '评论文章列表';
//草稿箱列表
$config['block']['draftboxtitle'] = '草稿箱列表';
$config['block']['draftboxlist'] = 'article/draftbox_list.shtml';
//实名认证
$config['block']['usersettitle'] = '实名认证';
$config['block']['usersetpage'] = 'user/edit_realuser.shtml';

$config['block']['articlecommentlist'] = 'article/devmanage_commentlist.shtml';
$config['block']['articlecommentlisttitle'] = '文章评论列表';

//文章分类
$config['block']['articlesortadd'] = 'article/manage_articlesortadd.shtml';
$config['block']['articlesortaddtitle'] = '添加文章分类';
$config['block']['articlesortlist'] = 'article/devmanage_articlesortlist.shtml';
$config['block']['articlesortlisttitle'] = '文章分类列表';

$config['block']['articletaglist'] = 'article/manage_articletaglist.shtml';
$config['block']['articletaglisttitle'] = '文章标签管理';
//友情链接
$config['block']['addlinksort'] = 'link/manage_addlinksort.shtml';
$config['block']['addlinksorttitle'] = '添加友情链接分类';
$config['block']['flinklistsort'] = 'link/manage_flinklistsort.shtml';
$config['block']['flinklistsorttitle'] = '友情链接分类列表';
$config['block']['flinkadd'] = 'link/manage_flinkadd.shtml';
$config['block']['flinkaddtitle'] = '添加友情链接';
$config['block']['flinklist'] = 'link/manage_flinklist.shtml';
$config['block']['flinklisttitle'] = '友情链接列表';
$config['block']['flinkmanage'] = 'link/manage_flinkmanage.shtml';
//博客直播
$config['block']['onlinesublist'] = 'online/manage_onlinelist.shtml';
$config['block']['onlinesublisttitle'] = '直播主题列表';


$config['block']['addarticle'] = $config['module_path'] . 'editor.html'; //添加文章
$config['block']['devpersonalhead'] = $config['module_path'] . 'devpersonal_header.shtml';

$config['block']['showAffiche'] = $config['module_path'] . 'devshow_affiche.shtml';  //公告列表
$config['block']['recommend'] = $config['module_path'] . 'devshow_remmendarticle.shtml';  //推荐文章
$config['block']['showStat'] = $config['module_path'] . 'devshow_stat.shtml';  //访问统计
$config['block']['articlesort'] = $config['module_path'] . 'devshow_articlesort.shtml';  //文章分类
$config['block']['devaddGroup'] = 'config/devmanage_addgroup.shtml';
$config['block']['archive'] = $config['module_path'] . 'devshow_archive.shtml';  //文章分类
$config['block']['guesteverbrowse'] = $config['module_path'] . 'devshow_guesteverbrowse.shtml';  //浏览过该文章的人还浏览过
$config['block']['taglist'] = $config['module_path'] . 'devshow_taglist.shtml'; //我的标签
$config['block']['fanslist'] = $config['module_path'] . 'devshow_fanslist.shtml'; //我的粉丝
$config['block']['show_renewvisitor'] = $config['module_path'] . 'devshow_renewvisitor.shtml'; //个人博客页面最近访客
$config['block']['show_newestcomment'] = $config['module_path'] . 'devshow_newestcomment.shtml'; //个人博客页面最新评论
$config['block']['blogInfo'] = $config['module_path'] . 'devshow_blogInfo.shtml';//单独的博客信息
$config['block']['show_publisharticle'] = 'article/devmanage_articleadd.html'; //发表文章

$config['block']['show_draft'] = $config['module_path'] . 'show_draft.shtml';
$config['block']['jointly'] = $config['module_path'] . 'show_jointly.shtml'; //共同关注


//关键词过滤
$config['keyword']['keywordlist'] = array('烛光晚会', '六四', '联名信', '穿纪念衫', '快闪', '点蜡烛'); //关键词过滤
$config['keyword']['keywordclose'] = array('****', '**', '***', '****', '**', '***'); //关键词过滤
?>
