<?php
/**
 * Created by PhpStorm.
 * User: jianglw
 * Date: 2017/5/11
 * Time: 17:41
 */

include_once dirname(dirname(__FILE__)) . '/amct/amct.init.php';

$get = stripslashes_array($_GET);
if (!$get['user_id']) {
	echo json_encode(array('status' => 0, 'message' => '用户不存在!'));
	exit();
}
$limit = $get['limit'] ? $get['limit'] : 1000;
$start = $get['p'] ? ($get['p']-1)*$limit : 0;
$db = DB::getInstance();
$data = $db->getAll("SELECT * FROM `schedule` where user_id=" . intval($get['user_id']) ." order by ctime desc limit ".$start.",".$limit);
if (!empty($data)) {
	$res = array();
	foreach ($data as $val) {
		$info['name'] = $val['name'];
		$log = $db->getAll('select * from schedule_log where sid='.$val['id'].' order by log_time desc limit 3');
		$info['log'] = array();
		if (!empty($log)) {
			foreach ($log as $v) {
				$logArr['time'] = date('m月d日',$v['log_time']);
				$logArr['s_page'] =  $v['s_page'];
				$logArr['e_page'] =  $v['e_page'];
				$info['log'][] = $logArr;
			}
		}
		$res[] = $info;
	}
	echo json_encode(array('status' => 200, 'data' => $res));
	exit();
} else {
	echo json_encode(array('status' => 0, 'message' => '无记录!'));
	exit();
}