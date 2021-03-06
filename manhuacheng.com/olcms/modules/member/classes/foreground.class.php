<?php

class foreground {
	public $db, $memberinfo;
	private $_member_modelinfo;
	
	public function __construct() {
		$this->db = pc_base::load_model('member_model');
		//ajax验证信息不需要登录
		if(substr(ROUTE_A, 0, 7) != 'public_') {
			self::check_member();
		}
	}
	
	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_member() {
		$olcms_auth = param::get_cookie('auth');
		if(ROUTE_M =='member' && ROUTE_C =='index' && in_array(ROUTE_A, array('login', 'register', 'mini'))) {
			if ($olcms_auth && ROUTE_A != 'mini') {
				showmessage(L('login_success', '', 'member'), '?m=member&c=index');
			} else {
				return true;
			}
		} else {
			//判断是否存在auth cookie
			if ($olcms_auth) {
				$auth_key = md5(pc_base::load_config('system', 'auth_key').str_replace('7.0' ,'8.0',$_SERVER['HTTP_USER_AGENT']));
				list($userid, $password) = explode("\t", sys_auth($olcms_auth, 'DECODE', $auth_key));
				//验证用户，获取用户信息
				$this->memberinfo = $this->db->get_one(array('userid'=>$userid));
				//获取用户模型信息
				$this->db->set_model($this->memberinfo['modelid']);

				$this->_member_modelinfo = $this->db->get_one(array('userid'=>$userid));
				$this->_member_modelinfo = $this->_member_modelinfo ? $this->_member_modelinfo : array();
				$this->db->set_model();
				if(is_array($this->memberinfo)) {
					$this->memberinfo = array_merge($this->_member_modelinfo,$this->memberinfo);
				}
				
				if($this->memberinfo && $this->memberinfo['password'] === $password) {
					
					if($this->memberinfo['groupid'] == 1) {
						param::set_cookie('auth', '');
						param::set_cookie('_userid', '');
						param::set_cookie('_username', '');
						param::set_cookie('_groupid', '');
						showmessage(L('userid_banned_by_administrator', '', 'member'), '?m=member&c=index&a=login');
					} elseif($this->memberinfo['groupid'] == 7) {
						param::set_cookie('auth', '');
						param::set_cookie('_userid', '');
						param::set_cookie('_groupid', '');
						param::set_cookie('email', $this->memberinfo['email']);
						showmessage(L('need_emial_authentication', '', 'member'), '?m=member&c=index&a=register&t=2');
					}
				} else {
					param::set_cookie('auth', '');
					param::set_cookie('_userid', '');
					param::set_cookie('_username', '');
					param::set_cookie('_groupid', '');
				}
				unset($userid, $password, $olcms_auth, $auth_key);
			} else {
				$forward= isset($_GET['forward']) ?  urlencode($_GET['forward']) : urlencode(get_url());
				showmessage(L('please_login', '', 'member'), '?m=member&c=index&a=login&forward='.$forward);
			}
		}
	}
	
}