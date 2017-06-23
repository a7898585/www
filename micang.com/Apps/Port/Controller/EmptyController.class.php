<?php
/*
 * 空控制器
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */
namespace Port\Controller;
use Think\Controller;
class EmptyController extends Controller {
	public function index(){
        send_http_status(404);
        exit();
    }
}



