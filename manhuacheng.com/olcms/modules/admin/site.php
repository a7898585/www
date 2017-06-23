<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class site extends admin {
	private $db;
	public function __construct() {
		$this->db = pc_base::load_model('site_model');
		parent::__construct();
	}
	
	public function edit() {
			if (isset($_POST['dosubmit'])) {
				$name = isset($_POST['name']) && trim($_POST['name']) ? trim($_POST['name']) : showmessage(L('site_name').L('empty'));
				$domain = isset($_POST['domain']) && trim($_POST['domain']) ? trim($_POST['domain']) : '';
				$site_title = isset($_POST['site_title']) && trim($_POST['site_title']) ? trim($_POST['site_title']) : '';
				$keywords = isset($_POST['keywords']) && trim($_POST['keywords']) ? trim($_POST['keywords']) : '';
				$description = isset($_POST['description']) && trim($_POST['description']) ? trim($_POST['description']) : '';
				$template = isset($_POST['template']) && !empty($_POST['template']) ? $_POST['template'] : showmessage(L('please_select_a_style'));
				$default_style = isset($_POST['default_style']) && !empty($_POST['default_style']) ? $_POST['default_style'] : showmessage(L('please_choose_the_default_style'));	
				if (!empty($template) && is_array($template)) {
					$template = implode(',', $template);
				} else {
					$template = '';
				}
				$_POST['setting']['watermark_img'] = 'statics/images/water/'.$_POST['setting']['watermark_img'];
				$setting = trim(array2string($_POST['setting']));
				$sql = array('name'=>$name, 'domain'=>$domain, 'site_title'=>$site_title, 'keywords'=>$keywords, 'description'=>$description, 'template'=>$template, 'setting'=>$setting, 'default_style'=>$default_style);
				if ($this->db->update($sql, array('siteid'=>1))) {
					$class_site = pc_base::load_app_class('sites');
					$class_site->set_cache();
					showmessage(L('operation_success'));
				} else {
					showmessage(L('operation_failure'));
				}
			} else {
				$show_validator = true;
				$show_header = true;
				$show_scroll = true;
				$template_list = template_list();
				$data = siteinfo();
				$setting = string2array($data['setting']);
				$setting['watermark_img'] = str_replace('statics/images/water/','',$setting['watermark_img']);
				include $this->admin_tpl('site_edit');
			}
		}
		
	//GD库功能检测
	private function check_gd() {
		if(!function_exists('imagepng') && !function_exists('imagejpeg') && !function_exists('imagegif')) {
			$gd = L('gd_unsupport');
		} else {
			$gd = L('gd_support');
		}
		return $gd;
	}
}