<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class setting extends admin {
	private $db;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('module_model');
		$this->config_db = pc_base::load_model('system_config_model');
		pc_base::load_app_func('global');
	}
	
	/**
	 * 配置信息
	 */
	public function init() {
		$show_validator = true;
		$setconfig = pc_base::load_config('system');
		extract($setconfig);
		$group = $this->config_db->select();
		if(!function_exists('ob_gzhandler')) $gzip = 0;
		$info = $this->db->get_one(array('module'=>'admin'));
		extract(string2array($info['setting']));
		$show_header = true;
		$show_validator = 1;
		include $this->admin_tpl('setting');
	}
	
	/**
	 * 保存配置信息
	 */
	public function save() {
		
		$setting['admin_email'] = is_email($_POST['setting']['admin_email']) ? trim($_POST['setting']['admin_email']) : showmessage(L('email_illegal'),HTTP_REFERER);
		//$setting['adminaccessip'] = intval($_POST['setting']['adminaccessip']);
		$setting['maxloginfailedtimes'] = intval($_POST['setting']['maxloginfailedtimes']);
		//$setting['maxiplockedtime'] = intval($_POST['setting']['maxiplockedtime']);
		$setting['minrefreshtime'] = intval($_POST['setting']['minrefreshtime']);
		$setting['mail_type'] = intval($_POST['setting']['mail_type']);		
		$setting['mail_server'] = trim($_POST['setting']['mail_server']);	
		$setting['mail_port'] = intval($_POST['setting']['mail_port']);	
		$setting['mail_user'] = trim($_POST['setting']['mail_user']);
		$setting['mail_auth'] = intval($_POST['setting']['mail_auth']);		
		$setting['mail_from'] = trim($_POST['setting']['mail_from']);		
		$setting['mail_password'] = trim($_POST['setting']['mail_password']);
		$setting['errorlog_size'] = trim($_POST['setting']['errorlog_size']);
		$setting = array2string($setting);
		$this->db->update(array('setting'=>$setting), array('module'=>'admin')); //存入admin模块setting字段
		
		foreach($_POST['setconfig'] as $k=>$v){
			$this->config_db->update(array('value'=>$v), array('varname'=>$k)); //存入system_config表
		}
		$result['setting_add'] = $this->config_db->select(array('tofinal'=>1),'varname,value');
		set_configarray($result);
		set_config($_POST['setconfig']);	 //保存进config文件
		$this->setcache();
		showmessage(L('setting_succ').$snda_error, HTTP_REFERER);
	}
	/**
	 * Q-保存新参数-20101121
	 */
	public function add() {
		$setting['varname'] = trim($_POST['add']['varname']);	
		$setting['value'] = trim($_POST['add']['varvalue']);
		$setting['type'] = trim($_POST['add']['vartype']);		
		$setting['info'] = trim($_POST['add']['varmsg']);		
		//$setting['groupid'] = intval($_POST['add']['vargroup']);
		$setting['tofinal'] = intval($_POST['add']['tofinal']);
		if($setting['varname'] && isset($setting['value'])) {
		$this->config_db->insert($setting, 'system_config');
		showmessage(L('setting_succ').$snda_error, HTTP_REFERER);
		}else{
		showmessage(L('setting_fail').$snda_error, HTTP_REFERER);
		}		
	}
	
	/**
	 * Q-删除参数-20101121
	 */
	public function vardelete() {
		$varname = trim($_GET['varname']);	
		if(isset($varname) && !empty($varname)) {
		$this->config_db->delete("varname='$varname'"); 
		showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		}		
	}
	/*
	 * 测试邮件配置
	 */
	public function public_test_mail() {
		pc_base::load_sys_func('mail');
		$subject = 'olcms test mail';
		$message = 'this is a test mail from olcms team';
		$mail= Array (
			'mailsend' => 2,
			'maildelimiter' => 1,
			'mailusername' => 1,
			'server' => $_POST['mail_server'],
			'port' => intval($_POST['mail_port']),
			'mail_type' => intval($_POST['mail_type']),
			'auth' => intval($_POST['mail_auth']),
			'from' => $_POST['mail_from'],
			'auth_username' => $_POST['mail_user'],
			'auth_password' => $_POST['mail_password']
		);	
		
		if(sendmail($_GET['mail_to'],$subject,$message,$_POST['mail_from'],$mail)) {
			echo L('test_email_succ').$_GET['mail_to'];
		} else {
			echo L('test_email_faild');
		}	
	}
	
	/**
	 * 设置缓存
	 * Enter description here ...
	 */
	private function setcache() {
		$result = $this->db->get_one(array('module'=>'admin'));
		$setting = string2array($result['setting']);
		setcache('common', $setting,'commons');
	}
}
?>