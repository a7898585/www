<?php
/**
 * Created by PhpStorm.
 * User: jianglw
 * Date: 2017/5/11
 * Time: 17:56
 */

include_once dirname(dirname(__FILE__)) . '/amct/amct.init.php';

$get = stripslashes_array($_GET);
if (!$get['user_id']) {
	echo json_encode(array('status' => 0, 'message' => '用户不存在!'));
	exit();
}
if (!$get['sid']) {
	echo json_encode(array('status' => 0, 'message' => '请选择你的书!'));
	exit();
}
$limit = $get['limit'] ? $get['limit'] : 1000;
$start = $get['start'] ? $get['start'] : 0;
$db = DB::getInstance();
$data = $db->getAll("SELECT * FROM `schedule_log` where sid=" . intval($get['sid']) ." order by log_time desc limit ".$start.",".$limit);
if (!empty($data)) {
	$res = array();
	foreach ($data as $val) {
		$info['time'] = date('m月d日',$val['log_time']);
		$info['s_page'] = $val['s_page'];
		$info['e_page'] = $val['e_page'];
		$res[] = $info;
	}
	echo json_encode(array('status' => 200, 'data' => $res));
	exit();
} else {
	echo json_encode(array('status' => 0, 'message' => '无记录!'));
	exit();
}