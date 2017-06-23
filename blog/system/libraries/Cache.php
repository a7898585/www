<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Cache 扩展库  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('cache');
| $this->cache->set('oo__pp__gg', 'I love you!');
|--------------------------------------------------------------------------
*/


class CI_Cache
{

    var $memcache;
    var $dbcache;
    var $configs;


    function CI_Cache()
    {
        # 取得配置信息
		$this->CI = & get_instance();

		$this->configs   = $this->CI->config->item('cache');

        #开启memcache(只针对test.passport.cnfol.com)
        if($this->configs['is_memcache'])
        {
            $this->memcache = & load_class('Memcache');
            $this->memcache->addServer();
        }
        if($this->configs['is_cache'])
        {
            $this->memcache = & load_class('Memcache');
            $this->memcache->addServer();
        }
        #开启dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache = & load_class('Dbcache');
        }
    }


    /**
     * 设置数据到cache
     *
     */
    function set($key, $value, $expire)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }

        #设置memcache
        $this->memcache->set($key, $value, $expire);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $this->dbcache->set($key, $value, $expire);
        }
        return true;
    }

    /**
     * 新增数据到cache
     *
     */
    function add($key, $value, $expire)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $this->memcache->add($key, $value, $expire);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $this->dbcache->add($key, $value, $expire);
        }
        return true;
    }

    /**
     * 替换到cache信息
     *
     */
    function replace($key, $value, $expire)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $this->memcache->replace($key, $value, $expire);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $this->dbcache->replace($key, $value, $expire);
        }
        return true;
    }

    /**
     * 增加cache信息的值，$value为整型，默认为1
     */
    function increment($key, $value=1)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $this->memcache->increment($key, $value);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $this->dbcache->increment($key, $value);
        }
        return true;
    }

    /**
     * 取得cache信息
     * 参数为单个key值
     */
    function get_all($key)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $result = $this->memcache->get_all($key);
        #获取memcache失败且有开启dbcache时，才从dbcache取值
        if($result===false && $this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $result = $this->dbcache->get_all($key);
        }
        return $result;
    }
    /**
     * 取得cache的info/settime信息
     * 参数为单个key值
     */
    function get($key,$k='info')
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $result = $this->memcache->get($key,$k);
        #获取memcache失败且有开启dbcache时，才从dbcache取值
        if($result===false && $this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $result = $this->dbcache->get($key,$k);
        }
        return $result;
    }
    /**
     * 取得cache信息
     * 参数为数组，返回也为数组
     */
    function get_multi_all($arr)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $result = $this->memcache->get_multi_all($arr);
        #获取memcache失败且有开启dbcache时，才从dbcache取值
        if($result===false && $this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $result = $this->dbcache->get_multi_all($arr);
        }
        return $result;
    }

    /**
     * 取得cache的info信息
     * 参数为数组，返回也为数组
     */
	function get_multi($arr,$k='info')
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $result = $this->memcache->get_multi($arr,$k);
        #获取memcache失败且有开启dbcache时，才从dbcache取值
        if($result===false && $this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $result = $this->dbcache->get_multi($arr,$k);
        }
        return $result;
    }

    /**
     * 取得cache的settime信息
     * 参数为数组，返回也为数组
     */
	function get_multi_settime($arr)
    {
        #不使用缓存，直接返回false
        if($this->configs['is_cache']===false)
        {
            return false;
        }
        #设置memcache
        $result = $this->memcache->get_multi_settime($arr);
        #获取memcache失败且有开启dbcache时，才从dbcache取值
        if($result===false && $this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $result = $this->dbcache->get_multi_settime($arr);
        }
        return $result;
    }

	/**
     * 删除cache信息的值
     *
     */
    function delete($key)
    {
        #设置memcache
        $this->memcache->delete($key);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->addServer();
            $this->dbcache->delete($key);
        }
        return true;
    }

    /**
     * 设置Key前缀
     *
     */
    function set_prefix_key($prefix)
    {
        #设置memcache
        $this->memcache->set_prefix_key($prefix);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->set_prefix_key($prefix);
        }
    }

    /**
     * 设置是否压缩
     *
     */
    function set_compress($compress)
    {
        #设置memcache
        $this->memcache->set_compress($compress);
        #有开启dbcache时，设置dbcache
        if($this->configs['is_dbcache'])
        {
            $this->dbcache->set_compress($compress);
        }
    }

}

// END Memcache Class

/* End of file Memcache.php */
/* Location: ./system/libraries/Memcache.php */
