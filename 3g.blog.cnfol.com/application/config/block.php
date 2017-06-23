<?php

/*
  -----------------------------------------------------------------------------
  |
  |    频道模板的设定
  |
  -----------------------------------------------------------------------------
 */

define('DEFAULT_PATH', '/home/www/html/'); //modify apache's DocumentRoot
define('UPLOAD_FILE_PATH', DEFAULT_PATH . 'img.cnfol.com/upload'); //本地附件路径/home/httpd/3g.blog.cnfol.com/shtml/ 
//用户中心地址  线上 
define('DEFAULT_CNFOL_URL', 'http://3g.cnfol.com/');
$config['usercenter_url'] = DEFAULT_CNFOL_URL . 'usercenter/views/user_login.html?url=';
$config['3g_cnfol_url'] = DEFAULT_CNFOL_URL . 'index.php?r=Navigation';

$config['shtml_path'] = DEFAULT_PATH . 'newblog/shtml/';
$config['allrank_path'] = dirname(APPPATH) . '/allrank/';
$config['filter_path'] = dirname(APPPATH) . '/expansion/';
$config['tmpupload'] = dirname(APPPATH) . '/userupload/';
$config['attached'] = dirname(APPPATH) . '/attached/';
$config['views_path'] = APPPATH . 'views/';
$config['common_path'] = $config['views_path'] . 'common/';
$config['weiboapi_url'] = "http://t.cnfol.com/ajax/publicweibo";
//手机博客
$config['block']['commonheader'] = $config['common_path'] . 'common_header.shtml';
$config['block']['commonfooter'] = $config['common_path'] . 'common_footer.shtml';
$config['block']['personalhead'] = $config['common_path'] . 'personal_header.shtml'; //我的主页头部导航
$config['block']['mybloghead'] = $config['common_path'] . 'myblog_header.shtml';  //我的博客 头部个人信息
$config['block']['otherpersonhead'] = $config['common_path'] . 'otherperson_header.shtml';  //他人 头部个人信息


$config['block']['articleaddtitle'] = '添加文章';
$config['block']['articleedittitle'] = '编辑文章';
$config['block']['draftboxtitle'] = '草稿箱列表';
$config['block']['articlemyfavorites'] = '我的收藏列表';
$config['block']['articlecommentlist'] = '文章评论列表';
$config['block']['myblog_index'] = '我的动态';
$config['block']['articlelist'] = '文章列表';
$config['block']['myfavorites'] = '我的收藏';
?>
