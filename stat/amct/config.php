<?php
/*
 * 是否开启调试模式
 */
define('AMCT_DEBUG', true);
// 设置默认时区
define('TIMEZONE', 'Asia/Shanghai');
// 设置加密密钥
define('AMCT_AUTH_KEY', '1qw23er45ty67ui89op0');
// 设置编码
define('AMCT_CHARSET', 'utf-8');
// 设置MYSQL数据库参数
$configDatabase['book'] = array(
	'host' => '127.0.0.1',
	'port' => '3306',
	'user' => 'root',
	'password' => '123456',
	'persistent' => false,
	'database' => 'book',
	'charset' => 'utf8',
	'autoReconnect' => true
);


