<?php
/**
 * 域名注册接口，对接WEBNIC
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-05
 */

namespace Common\Extend;


use Think\Log;

class DomainApi{
    private $handler;
    public function __construct($driver='webnic'){
        $driver = $driver ? : 'webnic';
        $class = 'Common\\Extend\\Domain\\Driver\\'.ucfirst(strtolower($driver));
        $this->handler = new $class();
    }
    public function __call($method,$args){
        if(method_exists($this->handler, $method)){
            return call_user_func_array(array($this->handler,$method), $args);
        }else{
            E(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }
}