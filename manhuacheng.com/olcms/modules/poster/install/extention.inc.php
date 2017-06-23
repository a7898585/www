<?php
error_reporting(E_ALL);
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'poster', 'parentid'=>29, 'm'=>'poster', 'c'=>'space', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_space', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_space', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'del_space', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'poster_list', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'add_poster', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_poster', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'del_poster', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'poster_stat', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'poster', 'a'=>'stat', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'poster_setting', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'setting', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'create_poster_js', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'create_js', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'poster_template', 'parentid'=>$parentid, 'm'=>'poster', 'c'=>'space', 'a'=>'poster_template', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$pdb = pc_base::load_model('poster_model');
$sql = "INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(1, 'banner', 1, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/2.png'',\n    ''alt'' => '''',\n  ),\n)', 1285813808, 1446249600, 1285813833, 0, 1, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(2, 'olcms', 2, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./statics/images/beta/ad_login.jpg'',\n    ''alt'' => ''olcms专业建站系统'',\n  ),\n)', 1285816298, 1446249600, 1285816310, 0, 1, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(3, 'olcms下载推荐', 3, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/3.png'',\n    ''alt'' => ''olcms官方'',\n  ),\n)', 1286504815, 1446249600, 1286504865, 0, 1, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(4, 'olcms广告', 4, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/4.gif'',\n    ''alt'' => ''olcms官方'',\n  ),\n)', 1286505141, 1446249600, 1286505178, 0, 0, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(5, 'olcms下载', 5, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/5.gif'',\n    ''alt'' => ''官方'',\n  ),\n)', 1286509363, 1446249600, 1286509401, 0, 0, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(6, 'olcms下载推荐1', 6, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/5.gif'',\n    ''alt'' => ''官方'',\n  ),\n)', 1286510183, 1446249600, 1286510227, 0, 0, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(7, 'olcms下载详情', 7, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/5.gif'',\n    ''alt'' => ''官方'',\n  ),\n)', 1286510314, 1446249600, 1286510341, 0, 0, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(8, 'olcms下载页', 8, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/1.jpg'',\n    ''alt'' => ''官方站'',\n  ),\n)', 1286522084, 1446249600, 1286522125, 0, 0, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES
(9, 'olcms 广告', 9, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/4.gif'',\n    ''alt'' => '''',\n  ),\n)', 1287041759, 1446249600, 1287041804, 0, 0, 0, 0);
INSERT INTO `olcms_poster` (`id`, `name`, `spaceid`, `type`, `setting`, `startdate`, `enddate`, `addtime`, `hits`, `clicks`, `listorder`, `disabled`) VALUES (10, 'olcms', 10, 'images', 'array (\n  1 => \n  array (\n    ''linkurl'' => ''http://www.olcms.cn'',\n    ''imageurl'' => ''./uploadfile/poster/6.jpg'',\n    ''alt'' => ''olcms官方'',\n  ),\n)', 1289270509, 1446249600, 1289270541, 1, 0, 0, 0);";
$sql = str_replace(array("olcms_", './uploadfile/poster/', './statics/images'), array($pdb->db_tablepre, pc_base::load_config('system','upload_url').'poster/', APP_PATH.'statics/images'), $sql);
$sqls = explode(";", trim($sql));
unset($sql);
$sqls = array_filter($sqls);
if (is_array($sqls)) {
	foreach($sqls as $s) {
		$pdb->query($s);
	}
}
unset($pdb);

$language = array('poster'=>'广告', 'add_space'=>'添加版位', 'edit_space'=>'编辑版位', 'del_space'=>'删除版位', 'poster_list'=>'广告列表', 'add_poster'=>'添加广告', 'edit_poster'=>'编辑广告', 'del_poster'=>'删除广告', 'poster_stat'=>'广告统计', 'poster_setting'=>'模块配置', 'create_poster_js'=>'重新生成js', 'poster_template'=>'广告模板设置');
?>