<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class index extends admin {
	public function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('admin_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->panel_db = pc_base::load_model('admin_panel_model');
	}
	
	public function init () {

		$userid = $_SESSION['userid'];
		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$rolename = $roles[$_SESSION['roleid']];
		$site = pc_base::load_app_class('sites');
		$currentsite = siteinfo();
		/*管理员收藏栏*/
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
		include $this->admin_tpl('index');
	}
	
	public function login() {
		if(isset($_GET['dosubmit'])) {
			$username = isset($_POST['username']) ? trim($_POST['username']) : showmessage(L('nameerror'),HTTP_REFERER);
			
			//密码错误剩余重试次数
			$this->times_db = pc_base::load_model('times_model');
			$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
			$maxloginfailedtimes = getcache('common','commons');
			$maxloginfailedtimes = (int)$maxloginfailedtimes['maxloginfailedtimes'];

			if($rtime['times'] > $maxloginfailedtimes) {
				$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
				showmessage(L('wait_1_hour',array('minute'=>$minute)));
			}
			//查询帐号
			$r = $this->db->get_one(array('username'=>$username));
			if(!$r) showmessage(L('user_not_exist'),'?m=admin&c=index&a=login');
			$password = md5(md5(trim($_POST['password'])).$r['encrypt']);
			
			if($r['password'] != $password) {
				$ip = ip();
				if($rtime && $rtime['times'] < $maxloginfailedtimes) {
					$times = $maxloginfailedtimes-intval($rtime['times']);
					$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
				} else {
					$this->times_db->delete(array('username'=>$username,'isadmin'=>1));
					$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
					$times = $maxloginfailedtimes;
				}
				showmessage(L('password_error',array('times'=>$times)),'?m=admin&c=index&a=login',3000);
			}
			$this->times_db->delete(array('username'=>$username));
			
			$this->db->update(array('lastloginip'=>ip(),'lastlogintime'=>SYS_TIME),array('userid'=>$r['userid']));
			$_SESSION['userid'] = $r['userid'];
			$_SESSION['roleid'] = $r['roleid'];
			$_SESSION['lock_screen'] = 0;
			$cookie_time = SYS_TIME+86400*30;
			param::set_cookie('admin_username',$username,$cookie_time);
			param::set_cookie('userid', $r['userid'],$cookie_time);
			param::set_cookie('admin_email', $r['email'],$cookie_time);
			showmessage(L('login_success'),'?m=admin&c=index');
		} else {
			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('login');
		}
	}
	
	
	public function public_logout() {
		$_SESSION['userid'] = 0;
		$_SESSION['roleid'] = 0;
		param::set_cookie('admin_username','');
		param::set_cookie('userid',0);
		
		//退出uc
		$uc_api_url = pc_base::load_config('system', 'uc_api_url');
		$uc_logout = '<script type="text/javascript" src="'.$uc_api_url.'/api.php?op=logout" reload="1"></script>';
		
		showmessage(L('logout_success').$uc_logout,'?m=admin&c=index&a=login');
	}
	
	//左侧菜单
	public function public_menu_left() {
		$menuid = intval($_GET['menuid']);
		$datas = admin::admin_menu($menuid);
		if (isset($_GET['parentid']) && $parentid = intval($_GET['parentid']) ? intval($_GET['parentid']) : 10) {
			foreach($datas as $_value) {
	        	if($parentid==$_value['id']) {
	        		echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        		
	        	} else {
	        		echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        	}      	
	        }
		} else {
			include $this->admin_tpl('left');
		}
		
	}
	//当前位置
	public function public_current_pos() {
		echo admin::current_pos($_GET['menuid']);
		exit;
	}

	
	public function public_ajax_add_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$menuarr = $this->menu_db->get_one(array('id'=>$menuid));
		$url = '?m='.$menuarr['m'].'&c='.$menuarr['c'].'&a='.$menuarr['a'].'&'.$menuarr['data'];
		$data = array('menuid'=>$menuid, 'userid'=>$_SESSION['userid'], 'name'=>$menuarr['name'], 'url'=>$url, 'datetime'=>SYS_TIME);
		$this->panel_db->insert($data, '', 1);
		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url'].'&menuid='.$v['menuid']."'>".L($v['name'])."</a>  <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	
	public function public_ajax_delete_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$this->panel_db->delete(array('menuid'=>$menuid, 'userid'=>$_SESSION['userid']));

		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url']."'>".L($v['name'])."</a> <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	public function public_main() {
		pc_base::load_app_func('global');
		pc_base::load_app_func('admin');
		define('PC_VERSION', pc_base::load_config('version','pc_version'));
		define('PC_RELEASE', pc_base::load_config('version','pc_release'));		
		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$userid = $_SESSION['userid'];
		$rolename = $roles[$_SESSION['roleid']];
		$r = $this->db->get_one(array('userid'=>$userid));
		$logintime = $r['lastlogintime'];
		$loginip = $r['lastloginip'];
		$sysinfo = get_sysinfo();
		$sysinfo['mysqlv'] = mysql_get_server_info();
		$show_header = 1;
		/*检测框架目录可写性*/
		$pc_writeable = is_writable(PC_PATH.'base.php');
		$common_cache = getcache('common','commons');
		$logsize_warning = errorlog_size() > $common_cache['errorlog_size'] ? '1' : '0';
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), '*',20 , 'datetime');
		$architecture = base64_decode('6ZmI5ZGo55Gc');
		$products_council = base64_decode('6ZmI5ZGo55Gc44CB546L5Y+C5Yqg44CB546L6ZOB5oiQ');
		$programmer = base64_decode('546L5Y+C5Yqg44CB546L6ZOB5oiQ44CB6ZmI5ZGo55Gc44CB6ZmI5a2m5pe644CB6ZmI6bmP44CB546L5a6Y5bqG');
		$designer = base64_decode('6JGj6aOe6b6Z44CB5byg5LqM5by6');
		ob_start();
		include $this->admin_tpl('main');
		$data = ob_get_contents();
		ob_end_clean();
		system_information($data);
	}
	/**
	 * 维持 session 登陆状态
	 */
	public function public_session_life() {
		$userid = $_SESSION['userid'];
		return true;
	}
	/**
	 * 锁屏
	 */
	public function public_lock_screen() {
		$_SESSION['lock_screen'] = 1;
	}
	public function public_login_screenlock() {
		if(empty($_GET['lock_password'])) showmessage(L('password_can_not_be_empty'));
		//密码错误剩余重试次数
		$this->times_db = pc_base::load_model('times_model');
		$username = param::get_cookie('admin_username');
		$maxloginfailedtimes = getcache('common','commons');
		$maxloginfailedtimes = (int)$maxloginfailedtimes['maxloginfailedtimes'];
		
		$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
		if($rtime['times'] > $maxloginfailedtimes-1) {
			$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
			exit('3');
		}
		//查询帐号
		$r = $this->db->get_one(array('userid'=>$_SESSION['userid']));
		$password = md5(md5($_GET['lock_password']).$r['encrypt']);
		if($r['password'] != $password) {
			$ip = ip();
			if($rtime && $rtime['times']<$maxloginfailedtimes) {
				$times = $maxloginfailedtimes-intval($rtime['times']);
				$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
			} else {
				$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
				$times = $maxloginfailedtimes;
			}
			exit('2|'.$times);//密码错误
		}
		$this->times_db->delete(array('username'=>$username));
		$_SESSION['lock_screen'] = 0;
		exit('1');
	}
	
	//后台站点地图
	public function public_map() {
		 $array = admin::admin_menu(0);
		 $menu = array();
		 foreach ($array as $k=>$v) {
		 	$menu[$v['id']] = $v;
		 	$menu[$v['id']]['childmenus'] = admin::admin_menu($v['id']);
		 }
		 $show_header = true;
		 include $this->admin_tpl('map');
	}
}
?>