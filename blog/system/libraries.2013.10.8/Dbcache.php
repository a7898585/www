<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Dbcache 扩展库  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('Dbcache');
| $this->dbcache->set('oo__pp__gg', 'I love you!');
|--------------------------------------------------------------------------
*/
if(!class_exists('Memcache'))
{
    show_error("Unable to load the requested base class: Memcache");
}

class CI_Dbcache extends Memcache
{

    var $prefix_key;
    var $compress = false;
    var $expire;
    var $configs;
    var $cache_log = 'base/dbcache.log';

    function CI_Dbcache()
    {
        # 取得配置信息
		$this->CI = & get_instance();

		$config = $this->CI->config->item('cache');
        $this->configs = $config['dbcache'];
        unset($config);

        # 设置key前缀
        $this->set_prefix_key($this->configs['prefix']);

        # 设置过期时间
        $this->expire = $this->configs['expire'];

        # 是否压缩存储
        $this->set_compress($this->configs['compress']);

    }

    /**
     * 链接多台cache
     */
    function addServer()
    {
        # 添加服务器端
        foreach($this->configs['server'] as $m)
        {
            $flag = parent::addServer($m['host'],$m['port']);
            if($flag===false)
            {
                log_write("host为{$m['host']}",$this->cache_log,__METHOD__ ,'ERROR');
            }
        }
    }

    /**
     * 链接单台cache
     */
    function connect()
    {
        $flag = parent::connect($this->configs['server']['host'],$this->configs['server']['port']);
        if($flag===false)
        {
            log_write("host为{$this->configs['server']['host']}",$this->cache_log,__METHOD__ ,'ERROR');
        }
    }

    /**
     * 设置数据到cache
     *
     */
    function set($key, $value, $expire)
    {
        if($expire=='')
        {
            $expire = $this->expire;
        }
        $content = array('info'=>$value,'settime'=>time());
        $result = parent::set($this->prefix_key.$key, $content, $this->compress, $expire);
        if($result===false)
        {
            log_write("key为{$key}",$this->memcache_log,__METHOD__ ,'ERROR');
            return false;
        }
        return true;
    }

    /**
     * 新增数据到cache
     *
     */
    function add($key, $value, $expire)
    {
        if($expire=='')
        {
            $expire = $this->expire;
        }
        $content = array('info'=>$value,'settime'=>time());
        $result = parent::add($this->prefix_key.$key, $content,$this->compress, $expire);
        if($result===false)
        {
            log_write("key为{$key}",$this->cache_log,__METHOD__ ,'ERROR');
            return false;
        }
        return true;
    }

    /**
     * 替换到cache信息
     *
     */
    function replace($key, $value, $expire)
    {
        if($expire=='')
        {
            $expire = $this->expire;
        }
        $content = array('info'=>$value,'settime'=>time());
        $result = parent::replace($this->prefix_key.$key, $content,false, $expire);
        if($result===false)
        {
            log_write("key为{$key}",$this->cache_log,__METHOD__ ,'ERROR');
            return false;
        }
        return true;
    }

    /**
     * 增加cache信息的值，$value为整型，默认为1
     */
    function increment($key, $value=1)
    {
        $result = parent::increment($this->prefix_key.$key, $value);
        if($result===false)
        {
            log_write("key为{$key}",$this->cache_log,__METHOD__ ,'ERROR');
            return false;
        }
        return true;
    }

    /**
     * 取得cache信息
     * 参数为单个key值
     */
    function get_all($key)
    {
        $result = parent::get($this->prefix_key.$key);
        return $result;
    }

	 /**
     * 取得cache的info信息
     * 参数为单个key值
     */

    function get($key,$k='info')
    {
        $result = parent::get($this->prefix_key.$key);
 		$result= isset($result[$k]) ? $result[$k] : $result;
 		return $result;
    }

     
	/**
     * 取得cache信息
     * 参数为数组，返回也为数组
     */
    function get_multi_all($arr)
    {
        $arrnew = array();
        foreach($arr as $v)
        {
            $arrnew[] = $this->prefix_key.$v;
        }
        $result = parent::get($arrnew);
        return $result;
    }

    /**
     * 取得cache的info/settime信息
     * 参数为数组，返回也为数组
     */

    function get_multi($arr,$k='info')
    {
        $result = $this->get_multi_all($arr);
        foreach($result as $v)
        {
            $rs[] = $v[$k];
        }
        $result=$rs? $rs : $result;
        return $result;
     }

    /**
     * 删除cache信息的值
     *
     */
    function delete($key)
    {
        $result = parent::delete($this->prefix_key.$key);
        if($result===false)
        {
            log_write("key为{$key}",$this->cache_log,__METHOD__ ,'ERROR');
            return false;
        }
        return true;
    }

    /**
     * 设置Key前缀
     *
     */
    function set_prefix_key($prefix)
    {
        $this->prefix_key = $prefix;
    }

    /**
     * 设置是否压缩
     *
     */
    function set_compress($compress)
    {
        $this->compress = $compress ? MEMCACHE_COMPRESSED : $compress;
    }

}

// END Dbcache Class

/* End of file Dbcache.php */
/* Location: ./system/libraries/Dbcache.php */
