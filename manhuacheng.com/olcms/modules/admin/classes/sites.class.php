<?php
/**
 * 站点对外接口
 * @author chenzhouyu
 *
 */
class sites {
	//数据库连接
	private $db;
	public function __construct() {
		$this->db = pc_base::load_model('site_model');
	}
	
	/**
	 * 设置站点缓存
	 */
	public function set_cache() {
		$data = $this->db->get_one();
		setcache('sitelist', $data, 'commons');
	}


}