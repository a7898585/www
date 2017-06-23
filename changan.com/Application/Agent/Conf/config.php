<?php

return array(
    'ERROR_MESSAGE' => '页面错误！请稍后再试～',
    'SHOW_ERROR_MSG' => true,
    'TMPL_ACTION_ERROR' => THINK_PATH . 'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => THINK_PATH . 'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => THINK_PATH . 'Tpl/think_exception.tpl', // 异常页面的模板文件
    'URL_MODEL' => 1,
    'URL_ROUTER_ON' => true, // 是否开启URL路由
    'URL_ROUTE_RULES' => array(), // 默认路由规则 针对模块
    'URL_MAP_RULES' => array(), // URL映射定义规则
    'LAYOUT_ON' => false, // 是否启用布局
    'APP_DEBUG' => true,
    'LOG_RECORD' => true,
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR',
//    'DEFAULT_CONTROLLER' => 'Index',
    'LAYOUT_ON' => true, // 是否启用布局
    'order_package' => array('1' => array('data' => array('1' => 1000, '2' => 1600, '3' => 2500), 'name' => '套餐1'),
        '2' => array('data' => array('1' => 3000, '2' => 4600, '3' => 6500), 'name' => '套餐2'),
        '3' => array('data' => array('1' => 5000, '2' => 8600, '3' => 12500), 'name' => '套餐3'))
);