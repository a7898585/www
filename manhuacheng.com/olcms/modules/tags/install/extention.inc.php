<?php
error_reporting(E_ALL);
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'tags', 'parentid'=>29, 'm'=>'tags', 'c'=>'tags', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'tags_create', 'parentid'=>$parentid, 'm'=>'tags', 'c'=>'tags', 'a'=>'create', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$language = array('tags'=>'tags管理', 'tags_create'=>'tags重建');
?>