<?php

/* ----------------------------------------------------------------------------------
  ------------------------获取博客点击信息----------------------------------
  ----------------------------------------------------------------------------------- */
if (!class_exists('Memcache')) {
    echo("Unable to load the requested base class: Memcache");
    exit(-1);
}
//修改博客点击统计信息
define('ROOT', dirname(__FILE__));
require_once ROOT . '/inc/config.inc.php';
require_once ROOT . '/cls/CMemcache.php';
require_once ROOT . '/cls/CSocket.php';
require_once ROOT . '/func/socket.func.php';
require_once ROOT . '/func/commen.func.php';

$_debug = isset($_GET['debug']) ? 1 : 0;
$type = $_GET['type'];
$ids = explode(',', $_GET['ids']);
$ids = is_array($ids) ? $ids : array(0 => $ids);

$newids = array();
foreach ($ids as $id) {
    if (is_numeric($id)) {
        $newids[] = intval($id);
    }
}
if (empty($newids)) {
    exit('parameters error!');
}

//初始化Memcache
$memcache = new CMemcache;
$str = '<script language="javascript">';
switch ($type) {
    //博客点击
    case 'blog':
        $memberids = join(',', $newids);
        $data = get_blogstat($memberids, $_debug);
        if (isset($_GET['r'])) {
            $str .= '$("#s_mtclick").html("' . $data['RealTotalClick'] . '");';
            $str .= '$("#s_mdclick").html("' . $data['RealTodayClick'] . '");';
        } else {
            $str .= '$("#s_mtclick").html("' . $data['TotalClick'] . '");';
            $str .= '$("#s_mdclick").html("' . $data['TodayClick'] . '");';
        }
        break;

    //文章点击
    case 'art':
        if (count($newids) == 1) {
            $clickartids = join(',', $newids);
            $data = get_articlestat($clickartids, $_debug);
            $str .= '$("#atonclick_' . $clickartids . '").html("' . $data['TotalClick'] . '");';
            $str .= '$("#adonclick_' . $clickartids . '").html("' . $data['TodayClick'] . '");';
        } else {
            $clickartids = $newids;
            $data = get_articlestatlist($clickartids, $_debug);
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $str .= '$("#atonclick_' . $k . '").html("' . $v['TotalClick'] . '");';
                }
            }
        }
        break;
    default:
        echo '<!-- no act -->';
        exit;
}
$str .= '</script>';
echo $str;
?>