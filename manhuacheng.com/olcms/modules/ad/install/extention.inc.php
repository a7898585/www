<?php
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'ad', 'parentid'=>29, 'm'=>'ad', 'c'=>'admin_ad', 'a'=>'init', 'data'=>'s=1', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'ad_add', 'parentid'=>$parentid, 'm'=>'ad', 'c'=>'admin_ad', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_ad', 'parentid'=>$parentid, 'm'=>'ad', 'c'=>'admin_ad', 'a'=>'edit', 'data'=>'s=1', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'check_ad', 'parentid'=>$parentid, 'm'=>'ad', 'c'=>'admin_ad', 'a'=>'init', 'data'=>'s=2', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'overdue', 'parentid'=>$parentid, 'm'=>'ad', 'c'=>'admin_ad', 'a'=>'init', 'data'=>'s=3', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'del_ad', 'parentid'=>$parentid, 'm'=>'ad', 'c'=>'admin_ad', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$language = array('ad'=>'广告', 'ad_add'=>'添加广告', 'edit_ad'=>'编辑广告', 'check_ad'=>'审核广告', 'overdue'=>'过期广告', 'del_ad'=>'删除广告');
?>