<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class tagindex_model extends model {
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'tagindex';
		parent::__construct();
	}
}
?>