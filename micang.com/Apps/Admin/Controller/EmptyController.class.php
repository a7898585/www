<?php
/*
 * 空控制器
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */
namespace Admin\Controller;
use Think\Controller;
//总管理后台模块
class EmptyController extends Controller {
	public function index(){
        send_http_status(404);
        $this->display('Public/404');
        exit();
    }
}



