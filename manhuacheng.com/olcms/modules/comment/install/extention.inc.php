<?php
defined('IN_OLCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'comment', 'parentid'=>'29', 'm'=>'comment', 'c'=>'comment_admin', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'comment_check', 'parentid'=>'821', 'm'=>'comment', 'c'=>'check', 'a'=>'checks', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'comment_list', 'parentid'=>$parentid, 'm'=>'comment', 'c'=>'comment_admin', 'a'=>'lists', 'data'=>'', 'listorder'=>0, 'display'=>'0'));

$language = array('comment'=>'评论', 'comment_check'=>'评论审核', 'comment_list'=>'评论列表');
?>