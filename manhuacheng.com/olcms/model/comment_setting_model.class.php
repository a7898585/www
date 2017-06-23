<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class comment_setting_model extends model {
	public $table_name;
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'comment';
		$this->table_name = 'comment_setting';
		parent::__construct();
	}
	
	/**
	 * 按站点ID返回站点配置情况
	 */
	public function site() {
		return $this->get_one(array('id'=>1));
	}
}
?>