<?php
return array(
//网站路径
'web_path' => '/',
//Session配置
'session_storage' => 'mysql',
'session_ttl' => 1800,
'session_savepath' => CACHE_PATH.'sessions/',
'session_n' => 0,
//Cookie配置
'cookie_domain' => '', //Cookie 作用域
'cookie_path' => '/', //Cookie 作用路径
'cookie_pre' => 'ibCpE_', //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
'cookie_ttl' => 0, //Cookie 生命周期，0 表示随浏览器进程
//模板相关配置
'tpl_root' => 'templates/', //模板保存物理路径
'tpl_name' => 'default', //当前模板方案目录
'tpl_css' => 'default', //当前样式目录
'tpl_referesh' => 1,
'tpl_edit'=>1,//是否允许在线编辑模板

//附件相关配置
'upload_path' => OLCMS_PATH.'uploadfile/',
'upload_url' => '/uploadfile/', //附件路径
'attachment_stat' => '1',//是否记录附件使用状态 0 统计 1 统计， 注意: 本功能会加重服务器负担

'js_path' => '/statics/js/', //CDN JS
'css_path' => '/statics/css/', //CDN CSS
'img_path' => '/statics/images/', //CDN img
'not_allow' => '0,10,11', //禁止显示的漫画分类id
'app_path' => '/',//动态域名配置地址

'charset' => 'utf-8', //网站字符集
'timezone' => 'Etc/GMT-8', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
'debug' => 1, //是否显示调试信息
'admin_log' => 1, //是否记录后台操作日志
'errorlog' => 1, //1、保存错误日志到 cache/error_log.php | 0、在页面直接显示
'gzip' => 1, //是否Gzip压缩后输出
'auth_key' => 'e8sSEVMXN1W2BswlWC5e', //密钥
'lang' => 'zh-cn',  //网站语言包
'lock_ex' => '1',  //写入缓存时是否建立文件互斥锁定（如果使用nfs建议关闭）

'admin_founders' => '1', //网站创始人ID，多个ID逗号分隔
'execution_sql' => 0, //EXECUTION_SQL

'uc' => '0',	//是否使用uc
'uc_appid' => '1',	//应用id	
'uc_api_url' => 'http://www.manhuacheng.com:81/ucenter',	//接口地址
'uc_auth_key' => 'MZUurzbP4Lf7tkc7qgFO2g1BT8x6zZmF', //加密密钥
'uc_version' => '1', //uc版本

'html_root' => '/html',//生成静态文件路径

'connect_enable' => '0',	//是否开启外部通行证
'gather_img' => '1',	//是否开启突破图片防采集
'content_manage' => '1',	//内容管理模式
'delayviewcount' => '0',
'setting_add' => 'a:3:{i:0;a:2:{s:7:"varname";s:8:"MIIBEIAN";s:5:"value";s:24:"闽ICP备10018231号-232";}i:1;a:2:{s:7:"varname";s:9:"COPYRIGHT";s:5:"value";s:105:"CopyRight @ 2011-03-16    详细  <a href="/" target="_blank">www.manhuacheng.com</a> All Rights Reserved";}i:2;a:2:{s:7:"varname";s:7:"COMPANY";s:5:"value";s:39:"厦门漫画城网络科技有限公司";}}',
'updatehost' => 'http://test.com',		//升级地址
);
?>