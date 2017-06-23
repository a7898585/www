<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Memcache 配置
|--------------------------------------------------------------------------
|
| 此文件放于system/application/config/下
|
| host: 服务器
| port: 端口
| 
| 使用方法:
| 自动载入--在autoload.php中,把memcache添加入$autoload['config'],如:$autoload['config'] = array('memcache');
| 手动载入--$this->config->load('memcache');
|--------------------------------------------------------------------------
|
| 调用:$this->config->item('memcache');
|
*/
$config['memcache']['server'][0]['host'] = 'memcache.cache.cnfol.com';
$config['memcache']['server'][0]['port'] = 11211;
$config['memcache']['server'][1]['host'] = 'memcache2.cache.cnfol.com';
$config['memcache']['server'][1]['port'] = 11211;
$config['memcache']['server'][2]['host'] = 'memcache3.cache.cnfol.com';
$config['memcache']['server'][2]['port'] = 11211;
$config['memcache']['server'][3]['host'] = 'memcache4.cache.cnfol.com';
$config['memcache']['server'][3]['port'] = 11211;

# Memcache的Key前缀
$config['memcache']['prefix'] ='';

# 是否压缩存储
$config['memcache']['compress'] = false;

# cache file保存的根路径
$config['memcache']['cache_root'] = dirname(dirname(__FILE__)).'/cache';

# 路径分隔符
# 如果file_path不为空,则根据分隔符,把key分成多层目录,保存在根目录下,文件名data.cache
# 默认为"__"
$config['memcache']['separator'] = '__';

# 是否保存成cache file的开关
# true=保存,false=不保存
$config['memcache']['save_file'] = true;

# 默认缓存时间
$config['memcache']['cache_time'] = 70;
?>