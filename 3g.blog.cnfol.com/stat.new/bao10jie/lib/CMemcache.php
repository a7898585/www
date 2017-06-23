<?php if(!defined('ROOT')) exit('access deny!');
/*
|--------------------------------------------------------------------------
| Memcache 扩展库 Ver0.1
|--------------------------------------------------------------------------
*/
class CMemcache extends Memcache 
{
	var $mem;
	var $key_prefix;
	var $compress;

	function CMemcache()
	{
		global $config;
		foreach($config['cache']['server'] as $m)
		{
			$this->addServer($m['host'],$m['port']);
		}
		# 设置key前缀
		$this->key_prefix = $config['cache']['prefix'];
		# 是否压缩存储
		$this->compress   = $config['cache']['compress']; 
	}

	/**
	 * 保存到memcache信息
	 */
	function set($key, $var, $expire=3600)
	{
        return parent::set($this->key_prefix.$key, $var, $this->compress, $expire);
	}

	/**
	 * 取得memcache信息
	 */
	function get($key)
	{
		$result = parent::get($this->key_prefix.$key);
        return $result;
	}

	/**
	 * 保存到memcache信息
	 */
	function add($key, $var, $expire=3600)
	{
		return parent::add($this->key_prefix.$key, $var, $this->compress, $expire);
	}

    /**
	 * 增加memcache信息的值
	 */
	function increment($key, $var)
	{
		return parent::increment($this->key_prefix.$key, $var);
	}

	/**
	 * 删除缓存
	 */
	function delete($key, $timeout=0)
	{
        return parent::delete($this->key_prefix.$key, $timeout);
    }

	/*
	 *  析构
	*/
	function __destruct()
	{
		parent::close();
	}
}
/* End */
