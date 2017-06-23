<?php

return array(
    #数据库配置
    'DB_TYPE' => 'mysql',
    'DB_CONFIG' => 'mysql://lijian:LJ@258.com@127.0.0.1:43061/changan',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'changan',
    'DB_USER' => 'lijian',
    'DB_PWD' => 'LJ@258.com',
    'DB_PORT' => 43061,
    'DB_PREFIX' => 'bx_',
    'DB_FIELDTYPE_CHECK' => false, // 是否进行字段类型检查
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO' => '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE' => false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE' => 'file', // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度
    'DB_SQL_LOG' => false, // SQL执行日志记录
    'DB_BIND_PARAM' => false, // 数据库写入数据自动参数绑定
);
