<?php 
class cache_memcache {

	private $memcache = null;

	public function __construct() {
		$this->memcache = new Memcache;
		$this->memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT, MEMCACHE_TIMEOUT);
	}

	public function memcache() {
		$this->__construct();
	}

	public function get($name) {
		$value = $this->memcache->get($name);
		return $value;
	}

	public function set($name, $value, $ttl = 0, $ext1='', $ext2='') {
		return $this->memcache->set($name, $value, false, $ttl);
	}

	public function delete($name) {
		return $this->memcache->delete($name);
	}

	public function flush() {
		return $this->memcache->flush();
	}

	//检测对应值是否有效
     public function cacheinfo($name) {
          $value=$this->get($name);
          if(empty($value)){
               $info['filemtime']=0;
          }else{
               $info['filemtime']=SYS_TIME;
          }
          return $info;
     }
}
?>