<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class pay_payment_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'pay_payment';
		parent::__construct();
	}
}
?>