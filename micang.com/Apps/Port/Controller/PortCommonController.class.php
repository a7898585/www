<?php
/**
 * PortCommonController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-18
 */
namespace Port\Controller;
use Think\Controller;

class PortCommonController extends Controller{
    protected function _initialize(){

    }
    final public function _empty(){
        send_http_status(404);
        exit();
    }
}