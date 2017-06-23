<?php
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'cooprate', 'parentid'=>29, 'm'=>'cooprate', 'c'=>'admin_cooprate', 'a'=>'init', 'data'=>'s=1', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'cooprate_add', 'parentid'=>$parentid, 'm'=>'cooprate', 'c'=>'admin_cooprate', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_cooprate', 'parentid'=>$parentid, 'm'=>'cooprate', 'c'=>'admin_cooprate', 'a'=>'edit', 'data'=>'s=1', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'check_cooprate', 'parentid'=>$parentid, 'm'=>'cooprate', 'c'=>'admin_cooprate', 'a'=>'init', 'data'=>'s=2', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'overdue', 'parentid'=>$parentid, 'm'=>'cooprate', 'c'=>'admin_cooprate', 'a'=>'init', 'data'=>'s=3', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'del_cooprate', 'parentid'=>$parentid, 'm'=>'cooprate', 'c'=>'admin_cooprate', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$language = array('dlfilename'=>'下载文件目录','topdomain'=>'客户顶级域名','cooprate_delete'=>'删除合作站点','cooprate'=>'合作站点', 'cooprate_add'=>'添加合作站点', 'edit_cooprate'=>'编辑合作站点','del_cooprate'=>'删除合作站点');
?>
