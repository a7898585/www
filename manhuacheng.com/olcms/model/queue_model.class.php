<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class queue_model extends model {
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'queue';
		parent::__construct();
	}
		
	/**
	 * 添加同步队列
	 * @param string $type  操作类型{add:添加,edit:修改,del:删除}
	 * @param string $path 文档地址
	 */
	final public function add_queue($type = 'add', $path) {
		$sites = pc_base::load_app_class('sites', 'admin');
		$site = siteinfo();

	}
	
}
?>