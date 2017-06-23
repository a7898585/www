<?php

define('AMCT_ROOTPATH', dirname(__FILE__));
include AMCT_ROOTPATH.'/config.php';
include_once dirname(__FILE__) . '/amct_mysql.php';

amct_mysql::$config = $configDatabase;

class DB extends amct_mysql {

    protected function __config() {
        $this->connectId = 'book';
    }

    private static $_instance;
    //覆盖__clone()方法，禁止克隆
    private function __clone() {

    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
function stripslashes_array(&$array) {
	while(list($key,$var) = each($array)) {
		if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) {
			if (is_string($var)) {
				$array[$key] = stripslashes($var);
			}
			if (is_array($var))  {
				$array[$key] = stripslashes_array($var);
			}
		}
	}
	return $array;
}