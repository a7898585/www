<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class feedback_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'feedback';
		parent::__construct();
	} 
}
?>