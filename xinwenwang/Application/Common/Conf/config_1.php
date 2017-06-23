<?php

return array(
    'APP_SUB_DOMAIN_DEPLOY' => true,
    'APP_SUB_DOMAIN_RULES' => array('port' => 'Port', 'www' => 'Home', 'admin' => 'Admin', 'm' => 'Mobile',),
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '117.25.148.102',
    'DB_NAME' => 'db_xinwenwang',
    'DB_USER' => 'jiangliwei',
    'DB_PWD' => 'ff7ho07fibj0peXxff7G',
//    'DB_HOST' => '127.0.0.1',
//    'DB_NAME' => 'db_xinwenwang',
//    'DB_USER' => 'db_xinwenwang',
//    'DB_PWD' => 'db_xinwenwang134256',
    'DB_PORT' => 3306,
    'DB_PREFIX' => 'xw_',
    'DB_FIELDTYPE_CHECK' => false, // 是否进行字段类型检查
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO' => '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE' => true, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE' => 'file', // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度
    'DB_SQL_LOG' => false, // SQL执行日志记录
    'DB_BIND_PARAM' => false, // 数据库写入数据自动参数绑定
//SESSION用户配置信息

    'LAYOUT_ON' => true, // 是否启用布局
    'LOGIN_KEY' => "xinwenwang",
    'SESSION_OPTIONS' => array(
        'domain' => 'xinwenwang.com'
    ),
    /* 默认设定 */
    'DEFAULT_M_LAYER' => 'Model', // 默认的模型层名称
    'DEFAULT_C_LAYER' => 'Controller', // 默认的控制器层名称
    'DEFAULT_V_LAYER' => 'View', // 默认的视图层名称
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'DEFAULT_THEME' => '', // 默认模板主题名称
    'DEFAULT_MODULE' => 'Home', // 默认模块
    'DEFAULT_CONTROLLER' => 'Index', // 默认控制器名称
    'DEFAULT_ACTION' => 'index', // 默认操作名称
    'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
    'DEFAULT_AJAX_RETURN' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_JSONP_HANDLER' => 'jsonpReturn', // 默认JSONP格式返回的处理方法
    'DEFAULT_FILTER' => 'htmlspecialchars', // 默认参数过滤方法 用于I函数...

    'LOG_RECORD' => true,
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR',
    'SHOW_RUN_TIME' => false,
    'SHOW_PAGE_TRACE' => false,
    'YOU_PAI_YUN' => 'http://xinwenwang.b0.upaiyun.com',
    'FILE_UPLOAD_TYPE' => 'Upyun',
    'UPLOAD_TYPE_CONFIG' => array(
        'host' => 'v0.api.upyun.com', //又拍云服务器
        'username' => 'xinwenwang', //又拍云用户
        'password' => 'XWW@258.com', //又拍云密码
        'bucket' => 'xinwenwang', //空间名称
        'timeout' => 90, //超时时间
    ),
    //邮件配置
    'THINK_EMAIL' => array(
        'smtp_host' => 'mail.258.com', //SMTP服务器
        'smtp_port' => '25', //SMTP服务器端口
        'smtp_user' => 'jiangliwei@258.com', //SMTP服务器用户名
        'smtp_pass' => 'Jlw199026', //SMTP服务器密码
        'from_email' => 'jiangliwei@258.com', //发件人EMAIL
        'from_name' => '保险啦', //发件人名称
        'reply_email' => '', //回复EMAIL（留空则为发件人EMAIL）
        'reply_name' => '', //回复名称（留空则为发件人名称）
    ),
    'DATA_CACHE_TYPE' => 'file',
    'DATA_CACHE_TIME' => '3600',
    'MEMCACHE_ON' => false,
//MEMCACHE配置
    'MEMCACHE' => array(
        'type' => 'memcache',
        'host' => '127.0.0.1',
        'port' => '11211',
        'prefix' => 'xinwenwang_',
    ),
);
