<?php
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'link', 'parentid'=>29, 'm'=>'link', 'c'=>'link', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_link', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_link', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'edit', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_link', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'link_setting', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'setting', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'add_type', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'add_type', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'list_type', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'list_type', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'check_register', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'check_register', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$type_db = pc_base::load_model('type_model');
$typeid = $type_db->insert(array('module'=>'link','name'=>'默认分类','description'=>'默认分类'),true);
if(!$typeid) return FALSE;

$language = array('link'=>'友情链接', 'add_link'=>'添加友情链接', 'edit_link'=>'编辑友情链接', 'delete_link'=>'删除友情链接', 'link_setting'=>'模块配置', 'add_type'=>'添加类别', 'list_type'=>'分类管理', 'check_register'=>'审核申请');
?>