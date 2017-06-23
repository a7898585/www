<?php
/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */
namespace Task\Controller;
use Think\Controller;
//任务服务公共基类模块
class TaskCommonController extends Controller {
    protected function _initialize(){

    }
    // 空方法
	final public function _empty(){
        send_http_status(404);
        exit();
    }

}



