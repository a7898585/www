<?php
return array(
	'ERROR_MESSAGE'         => '页面错误！请稍后再试～',
    'SHOW_ERROR_MSG'        => true,
    'TMPL_ACTION_ERROR'     => THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   => THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
    'URL_MODEL'             => 1,
    'URL_ROUTER_ON'         => true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       => array(), // 默认路由规则 针对模块
    'URL_MAP_RULES'         => array(), //
    'TUIJIAN_TYPE'=>1800,
    'HOT_TYPE'=>1801,
    'LAYOUT_ON'             =>  false, // 是否启用布局
);