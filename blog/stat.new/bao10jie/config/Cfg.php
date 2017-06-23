<?php

define('HL_APPID', '711'); //客户标识appid，需要根据客户帐号信息配置
define('HL_UKEY', '8e73db2b546179cc0db22c43a2e98904c31d0e41'); //客户私钥ukey，需要根据客户帐号信息配置
define('HL_VERSION', '2.0'); //请求版本v
define('HL_URLPRE', 'http://api.bao10jie.net/rest/purify/predict'); //净化服务内容识别接口地址
define('HL_URLTRA', 'http://api.bao10jie.net/rest/purify/train'); //净化服务训练（反馈）接口地址
define('HL_URLNTC', 'http://api.bao10jie.net/rest/purify/notice'); //净化服务通知接口地址
define('HL_URLGIR', 'http://api.bao10jie.net/rest/purify/getIndexResult'); //净化服务批量标引通知接口地址
define('HL_TIMEOUT', '10'); //在请求时的执行的超时时间
define('HL_RETRIES', '3'); //post请求失败(网络)时，重试的阀值
//define('HL_RTOUT','10');//readtimeout,在请求时的发送超时时间
define('HL_LPATH', '/home/www/html/logs/bao10jie'); //logpath,日志存放的路径设定
define('HL_DEBUG', '0'); //配置调试日志开关，鉴于影响效率，默认为关闭（非0-开 0-关）
