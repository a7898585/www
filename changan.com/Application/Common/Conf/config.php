<?php

return array(
    'DOMAIN' => 'changan.com',
    'LOAD_EXT_CONFIG' => 'changan,db',
    //'配置项'=>'配置值'
    'APP_SUB_DOMAIN_DEPLOY' => true,
    'APP_SUB_DOMAIN_RULES' => array('www' => 'Home', 'port' => 'Port', 'admin' => 'Admin', 'm' => 'Mobile', 'daili' => 'Daili', 'member' => 'Member', 'agent' => 'Agent', '*' => 'Site'),

    'LAYOUT_ON' => true, // 是否启用布局
    'DEFAULT_FILTER' => 'htmlspecialchars',
    #SESSION用户配置信息
    'LOGIN_KEY' => "changan_key",
    'SESSION_OPTIONS' => array(
        'domain' => 'changan.com'
    ),
    /** 微博QQ登陆接口* */
    'OAUTH' => array(
        'QQ_APPKEY' => '100xxxx',
        'QQ_APPSECRETKEY' => 'xxxxxxxxxxxxxxxxxxxxxxx',
        'QQ_SCOPE' => '',
        'QQ_CALLBACK' => 'http://jizhihuwai.com/user/auth/qq',
        'WEIBO_APPKEY' => '742966044',
        'WEIBO_APPSECRETKEY' => '904e34da60735200e3b6c3d5878f31d0',
        'WEIBO_SCOPE' => '',
        'WEIBO_CALLBACK' => 'http://beta.changan.com/auth/auth?type=weibo',
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
    'YOU_PAI_YUN' => 'http://baoxianla.b0.upaiyun.com',
    'FILE_UPLOAD_TYPE' => 'Upyun',
    'UPLOAD_TYPE_CONFIG' => array(
        'host' => 'v0.api.upyun.com', //又拍云服务器
        'username' => 'lijian4baoxian', //又拍云用户
        'password' => 'li#JIAN5(Boaxia', //又拍云密码
        'bucket' => 'baoxianla', //空间名称
        'timeout' => 90, //超时时间
    ),
    //邮件配置
    'THINK_EMAIL' => array(
        'smtp_host' => 'smtp.exmail.qq.com', //SMTP服务器
        'smtp_port' => '25', //SMTP服务器端口
        'smtp_user' => 'kefu@changan.com', //SMTP服务器用户名
        'smtp_pass' => 'Changan258', //SMTP服务器密码
        'from_email' => 'kefu@changan.com', //发件人EMAIL
        'from_name' => '常安保险', //发件人名称
        'reply_email' => '', //回复EMAIL（留空则为发件人EMAIL）
        'reply_name' => '', //回复名称（留空则为发件人名称）
    ),
    'DATA_CACHE_TYPE' => 'file',
    'DATA_CACHE_TIME' => '3600',
    'THINK_SDK_WEIBO' => array(
        'APP_KEY' => '3593850693', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '3e1eea33ce9514075584f20629f8cc4', //应用注册成功后分配的KEY
        'CALLBACK' => 'http://beta.changan.com/auth/auth/type/weibo', //注册应用填写的callback
    ),
    #BRA域名  
    'BRA_DOMAIN_HOST' => 'http://sapi.niucha.com',
    #BRM域名
    'BRM_DOMAIN_HOST' => 'http://brm.niucha.com',
    #BRC域名
    'BRC_DOMAIN_HOST' => 'http://brc.niucha.com',
    #PDC域名
    'PDC_DOMAIN_HOST' => 'http://pdc.258.com',
    #SSO域名
    'SSO_DOMAIN_HOST' => 'http://sso.niucha.com',
    'BRA_OLD_DOMAIN_HOST' => 'http://bra.niucha.com',
    #钱多多在BRM中的产品ID
    'PROJECT_ID' => 1067,
    #展业通登录接口
    'ZYT_LOGIN_URL' => 'http://zyt.changan.com/Home/User/login_zyt/login_key/',
);