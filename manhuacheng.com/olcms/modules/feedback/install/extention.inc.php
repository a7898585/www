<?php
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'feedback', 'parentid'=>29, 'm'=>'feedback', 'c'=>'feedback', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);

$language = array('feedback'=>'用户留言',  'delete_feedback'=>'删除友情链接');
?>