<?php
/**
 * Created by PhpStorm.
 * User: jianglw
 * Date: 2017/5/11
 * Time: 17:18
 */

include_once dirname(dirname(__FILE__)) . '/amct/amct.init.php';


$db = DB::getInstance();
//$_POST = array('name' => '我看的书本', 'user_id' => '33', 's_page' => '2', 'e_page' => '33','id'=>2);
$post = stripslashes_array($_POST);
if (!empty($post)) {
	if (!$post['name'] && !$post['id']) {
		echo json_encode(array('status' => 0, 'message' => '书名不能为空!'));
		exit();
	}
	if (!$post['user_id']) {
		echo json_encode(array('status' => 0, 'message' => '用户不存在!'));
		exit();
	}
	if (!$post['s_page']) {
		echo json_encode(array('status' => 0, 'message' => '开始页数不能为空!'));
		exit();
	}
	if (!$post['e_page']) {
		echo json_encode(array('status' => 0, 'message' => '结束页数不能为空!'));
		exit();
	}
	if ($post['id']) {
		$info = $db->getOne('select * from schedule where id='.intval($post['id']).'  limit 1');
		if(empty($info)){
			echo json_encode(array('status' => 0, 'message' => '不存在书本!'));
			exit();
		}
		$logData['sid'] = intval($post['id']);
	} else {
		$data['name'] = $post['name'];
		$data['user_id'] = $post['user_id'];
		$data['ctime'] = time();
		$db->insertArray('schedule', $data);
		$logData['sid'] = $db->last_insert_id();
	}
	$logData['log_time'] = time();
	$logData['s_page'] = $post['s_page'];
	$logData['e_page'] = $post['e_page'];
	if ($db->insertArray('schedule_log', $logData)) {
		echo json_encode(array('status' => 200, 'message' => '添加成功!'));
		exit();
	} else {
		echo json_encode(array('status' => 0, 'message' => '添加失败!'));
		exit();
	}

} else {
	echo json_encode(array('status' => 0, 'message' => '参数错误!'));
	exit();
}
