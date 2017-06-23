<?php
return array(
	//'配置项'=>'配置值'
	'APP_SUB_DOMAIN_DEPLOY'		=> true,
	'APP_SUB_DOMAIN_RULES'      => array('www.dev'=>'Home', 'member.dev'=>'Member', 'jansen.dev'=>'Admin', 'task.dev'=>'Task', 'port.dev'=>'Port'),
	/* 数据库设置 */
	'DB_TYPE'           =>  'mysql',     	// 数据库类型
	'DB_HOST'           =>  '117.25.148.51', 	// 服务器地址
	'DB_NAME'           =>  'db_dev_micang', // 数据库名
	'DB_USER'           =>  'db_dev_micang',     	// 用户名
	'DB_PWD'            =>  'gbEq6xn1tAhpjhbh939u',     		// 密码
	'DB_PORT'           =>  '3399',     	// 端口
	'DB_PREFIX'         =>  'mc_',      	// 数据库表前缀
	'DB_DEBUG'  		=>  true, 			// 数据库调试模式 开启后可以记录SQL日志
	'SHOW_PAGE_TRACE'   =>	false,   		// 显示页面Trace信息
	'LOAD_EXT_CONFIG'   =>  array('extend'),


);