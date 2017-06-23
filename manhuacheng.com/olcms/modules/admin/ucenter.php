<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class ucenter extends admin {
	function __construct() {
		parent::__construct();
	}
	
	function menu() {
	}
	
	
	function public_menu_left() {
		$setting = pc_base::load_config('system');

		include $this->admin_tpl('ucenter');
	}
}
?>