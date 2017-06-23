<?php
/**
 * 翻译
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-09
 */

namespace Common\Extend;


class Translate{
    private $handler;
    public function __construct($driver, array $config=array()){
        $this->setDriver($driver, $config);
    }
    public function run($content){
        return $this->handler->run($content);
    }
    private function setDriver($driver=null, array $config=array()){
        $driver = $driver ? : C('TRANSLATE_DRIVER_TYPE');
        $class = 'Common\\Extend\\Translate\\Driver\\'.ucfirst(strtolower($driver));
        $this->handler = new $class($config);
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