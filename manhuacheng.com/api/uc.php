<?php
/*
 * 客户端被动接收uc服务端通知
 * 服务端通知内容:同步登陆、退出，同步积分设置、对换比率，同步添加、删除用户、修改用户密码，测试通信状态
 * 
 */
define('UC_CLIENT_VERSION', '1.5.1');
define('UC_CLIENT_RELEASE', '20100501');
define('API_DELETEUSER', 1);
define('API_RENAMEUSER', 1);
define('API_GETTAG', 1);
define('API_SYNLOGIN', 1);
define('API_SYNLOGOUT', 1);
define('API_UPDATEPW', 1);
define('API_UPDATEBADWORDS', 1);
define('API_UPDATEHOSTS', 1);
define('API_UPDATEAPPS', 1);
define('API_UPDATECLIENT', 1);
define('API_UPDATECREDIT', 1);
define('API_GETCREDIT', 1);
define('API_GETCREDITSETTINGS', 1);
define('API_UPDATECREDITSETTINGS', 1);
define('API_ADDFEED', 1);
define('UC_API', 1);
define('TIME', time());
define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '1');
define('CURSCRIPT', 'api');
include '../olcms/base.php';

include '../uc_client/client.php';
$db = pc_base::load_model('member_model');
$system = pc_base::load_config('system');
$ps_api_id = $system['uc_appid'];
$ps_api_url = $system['uc_api_url'];	//接口地址
$ps_auth_key = $system['uc_auth_key'];	//加密密钥
$ps_version = $system['uc_version'];
define ( 'UC_APPID', $ps_api_id );
$arr = $post = array();
$code = $_REQUEST['code'];
$code = @$_GET['code'];

parse_str(uc_authcode($code, 'DECODE', $ps_auth_key), $arr);
if(time() - $arr['time'] > 3600) {
	exit('Authracation has expiried');
}

if(empty($arr)) {
	exit('Invalid Request');
}

if(in_array($arr['action'], array('test', 'deleteuser', 'adduser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings', 'addfeed'))) {
	$uc_note = new uc_note();
	echo $uc_note->$arr['action']($arr, $post);
	exit();
} else {
	exit(API_RETURN_FAILED);
}

class uc_note {

	var $dbconfig = '';
	var $db = '';
	var $tablepre = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once OLCMS_PATH.'./uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}
	function __construct() {
		$this->db = pc_base::load_model('member_model');
	}
	function uc_note() {
		
	}

	function test($arr, $post) {
		return API_RETURN_SUCCEED;
	}
	/**
	 * 添加用户
	 */
	function adduser($arr, $post) {
		$userinfo['ucuid'] = isset($arr['uid']) ? $arr['uid'] : exit('0');
		$userinfo['encrypt'] = isset($arr['random']) ? $arr['random'] : exit('0');
		$userinfo['username'] = isset($arr['username']) ? $arr['username'] : exit('0');
		$userinfo['password'] = isset($arr['password']) ? $arr['password'] : exit('0');
		$userinfo['email'] = isset($arr['email']) ? $arr['email'] : '';
		$userinfo['regip'] = isset($arr['regip']) ? $arr['regip'] : '';
		$userinfo['regdate'] = $userinfo['lastdate'] = SYS_TIME;
		$userinfo['modelid'] = 10;
		$userinfo['groupid'] = 6;

		$userid =  $this->db->insert($userinfo, 1);
		if($userid) {
			exit('1');
		} else {
			exit('0');
		}
	}
	function deleteuser($arr, $post) {
		$uidarr = $arr['ids'];
		$where = "ucuid in ($uidarr)";
		$status = $this->db->delete($where);
		if($status) {
			exit('1');
		} else {
			exit('0');
		}
		return API_RETURN_SUCCEED;
	}

	function renameuser($get, $post) {
		global $_G;

		if(!API_RENAMEUSER) {
			return API_RETURN_FORBIDDEN;
		}
		$tables = array(
			'common_block' => array('id' => 'uid', 'name' => 'username'),
			'common_invite' => array('id' => 'fuid', 'name' => 'fusername'),
			'common_member' => array('id' => 'uid', 'name' => 'username'),
			'common_member_verify_info' => array('id' => 'uid', 'name' => 'username'),
			'common_mytask' => array('id' => 'uid', 'name' => 'username'),
			'common_report' => array('id' => 'uid', 'name' => 'username'),

			'forum_thread' => array('id' => 'authorid', 'name' => 'author'),
			'forum_post' => array('id' => 'authorid', 'name' => 'author'),
			'forum_activityapply' => array('id' => 'uid', 'name' => 'username'),
			'forum_groupuser' => array('id' => 'uid', 'name' => 'username'),
			'forum_pollvoter' => array('id' => 'uid', 'name' => 'username'),
			'forum_postcomment' => array('id' => 'authorid', 'name' => 'author'),
			'forum_ratelog' => array('id' => 'uid', 'name' => 'username'),

			'home_album' => array('id' => 'uid', 'name' => 'username'),
			'home_blog' => array('id' => 'uid', 'name' => 'username'),
			'home_clickuser' => array('id' => 'uid', 'name' => 'username'),
			'home_docomment' => array('id' => 'uid', 'name' => 'username'),
			'home_doing' => array('id' => 'uid', 'name' => 'username'),
			'home_feed' => array('id' => 'uid', 'name' => 'username'),
			'home_feed_app' => array('id' => 'uid', 'name' => 'username'),
			'home_friend' => array('id' => 'fuid', 'name' => 'fusername'),
			'home_friend_request' => array('id' => 'fuid', 'name' => 'fusername'),
			'home_notification' => array('id' => 'authorid', 'name' => 'author'),
			'home_pic' => array('id' => 'uid', 'name' => 'username'),
			'home_poke' => array('id' => 'fromuid', 'name' => 'fromusername'),
			'home_share' => array('id' => 'uid', 'name' => 'username'),
			'home_show' => array('id' => 'uid', 'name' => 'username'),
			'home_specialuser' => array('id' => 'uid', 'name' => 'username'),
			'home_visitor' => array('id' => 'vuid', 'name' => 'vusername'),

			'portal_article_title' => array('id' => 'uid', 'name' => 'username'),
			'portal_comment' => array('id' => 'uid', 'name' => 'username'),
			'portal_topic' => array('id' => 'uid', 'name' => 'username'),
			'portal_topic_pic' => array('id' => 'uid', 'name' => 'username'),
		);

		foreach($tables as $table => $conf) {
			DB::query("UPDATE ".DB::table($table)." SET `$conf[name]`='$get[newusername]' WHERE `$conf[id]`='$get[uid]' AND `$conf[name]`='$get[oldusername]'");
		}
		return API_RETURN_SUCCEED;
	}

	function gettag($get, $post) {
		global $_G;
		if(!API_GETTAG) {
			return API_RETURN_FORBIDDEN;
		}
		return $this->_serialize(array($get['id'], array()), 1);
	}

	function synlogin($arr, $post) {
		if(!isset($arr['uid'])) exit('0');
					
		$ucuid = $arr['uid'];
		$userinfo = $this->db->get_one(array('ucuid'=>$ucuid));
				
		if (!$userinfo) {
			//插入会员
			exit;
			$ps_userinfo = uc_get_user($ucuid);
			if ($ps_userinfo['uid'] > 0) {
				$arr_member['touserid'] = $ps_userinfo['uid'];
				$arr_member['registertime'] = TIME;
				$arr_member['lastlogintime'] = TIME;
				$arr_member['username'] = $ps_userinfo['username'];
				$arr_member['password'] = md5(PASSWORD_KEY.$password) ;
				$arr_member['email'] = $ps_userinfo['email'];
				$arr_member['modelid'] = 10;
				$this->db->insert($arr_member);
				$userinfo = $this->db->get_one(array('username' => $arr['username']));
				$userid = $userinfo['userid'];
			}

			$username = $ps_userinfo['username'];
		} else {
			$username = $userinfo['username'];
		}
		//执行本系统登陆操作
		$userid = $userinfo['userid'];
		$groupid = $userinfo['groupid'];
		$username = $userinfo['username'];
		$password = $userinfo['password'];
		$this->db->update(array('lastip'=>ip(), 'lastdate'=>SYS_TIME), array('userid'=>$userid));
		pc_base::load_sys_class('param', '', 0);
		
		if(!$cookietime) $get_cookietime = param::get_cookie('cookietime');
		$_cookietime = $cookietime ? intval($cookietime) : ($get_cookietime ? $get_cookietime : 0);
		$cookietime = $_cookietime ? TIME + $_cookietime : 0;
		
		$olcms_auth_key = md5(pc_base::load_config('system', 'auth_key').$_SERVER['HTTP_USER_AGENT']);
		$olcms_auth = sys_auth($userid."\t".$password, 'ENCODE', $olcms_auth_key);
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');	
		param::set_cookie('auth', $olcms_auth, $cookietime);
		param::set_cookie('_userid', $userid, $cookietime);
		param::set_cookie('_username', $username, $cookietime);
		param::set_cookie('_groupid', $groupid, $cookietime);
		param::set_cookie('cookietime', $_cookietime, $cookietime);
		exit('1');
	}

	function synlogout($get, $post) {
		global $_G;

		if(!API_SYNLOGOUT) {
			return API_RETURN_FORBIDDEN;
		}

		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

		dsetcookie('auth', '', -31536000);
	}

	function updatepw($get, $post) {
		global $_G;

		if(!API_UPDATEPW) {
			return API_RETURN_FORBIDDEN;
		}

		$username = $get['username'];
		$newpw = md5(time().rand(100000, 999999));
		DB::query("UPDATE ".DB::table('common_member')." SET password='$newpw' WHERE username='$username'");

		return API_RETURN_SUCCEED;
	}

	function updatebadwords($get, $post) {
		global $_G;

		if(!API_UPDATEBADWORDS) {
			return API_RETURN_FORBIDDEN;
		}

		$data = array();
		if(is_array($post)) {
			foreach($post as $k => $v) {
				$data['findpattern'][$k] = $v['findpattern'];
				$data['replace'][$k] = $v['replacement'];
			}
		}
		$cachefile = DISCUZ_ROOT.'./uc_client/data/cache/badwords.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'badwords\'] = '.var_export($data, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return API_RETURN_SUCCEED;
	}

	function updatehosts($get, $post) {
		global $_G;

		if(!API_UPDATEHOSTS) {
			return API_RETURN_FORBIDDEN;
		}

		$cachefile = DISCUZ_ROOT.'./uc_client/data/cache/hosts.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return API_RETURN_SUCCEED;
	}

	function updateapps($get, $post) {
		global $_G;

		if(!API_UPDATEAPPS) {
			return API_RETURN_FORBIDDEN;
		}

		$UC_API = '';
		if($post['UC_API']) {
			$UC_API = $post['UC_API'];
			unset($post['UC_API']);
		}

		$cachefile = DISCUZ_ROOT.'./uc_client/data/cache/apps.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'apps\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		if($UC_API && is_writeable(DISCUZ_ROOT.'./config/config_ucenter.php')) {
			if(preg_match('/^https?:\/\//is', $UC_API)) {
				$configfile = trim(file_get_contents(DISCUZ_ROOT.'./config/config_ucenter.php'));
				$configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;
				$configfile = preg_replace("/define\('UC_API',\s*'.*?'\);/i", "define('UC_API', '".addslashes($UC_API)."');", $configfile);
				if($fp = @fopen(DISCUZ_ROOT.'./config/config_ucenter.php', 'w')) {
					@fwrite($fp, trim($configfile));
					@fclose($fp);
				}
			}
		}
		return API_RETURN_SUCCEED;
	}

	function updateclient($get, $post) {
		global $_G;

		if(!API_UPDATECLIENT) {
			return API_RETURN_FORBIDDEN;
		}

		$cachefile = DISCUZ_ROOT.'./uc_client/data/cache/settings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return API_RETURN_SUCCEED;
	}

	function updatecredit($get, $post) {
		global $_G;

		if(!API_UPDATECREDIT) {
			return API_RETURN_FORBIDDEN;
		}

		$credit = $get['credit'];
		$amount = $get['amount'];
		$uid = $get['uid'];
		if(!DB::result_first("SELECT count(*) FROM ".DB::table('common_member')." WHERE uid='$uid'")) {
			return API_RETURN_SUCCEED;
		}

		updatemembercount($uid, array($credit => $amount));
		DB::insert('common_credit_log', array('uid' => $uid, 'operation' => 'ECU', 'relatedid' => $uid, 'dateline' => time(), 'extcredits'.$credit => $amount));

		return API_RETURN_SUCCEED;
	}

	function getcredit($get, $post) {
		global $_G;

		if(!API_GETCREDIT) {
			return API_RETURN_FORBIDDEN;
		}
		$uid = intval($get['uid']);
		$credit = intval($get['credit']);
		$_G['uid'] = $uid;
		return getuserprofile('extcredits'.$credit);
	}

	function getcreditsettings($get, $post) {
		global $_G;

		if(!API_GETCREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}

		$credits = array();
		foreach($_G['setting']['extcredits'] as $id => $extcredits) {
			$credits[$id] = array(strip_tags($extcredits['title']), $extcredits['unit']);
		}

		return $this->_serialize($credits);
	}

	function updatecreditsettings($get, $post) {
		global $_G;

		if(!API_UPDATECREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}

		$outextcredits = array();
		foreach($get['credit'] as $appid => $credititems) {
			if($appid == UC_APPID) {
				foreach($credititems as $value) {
					$outextcredits[$value['appiddesc'].'|'.$value['creditdesc']] = array(
						'appiddesc' => $value['appiddesc'],
						'creditdesc' => $value['creditdesc'],
						'creditsrc' => $value['creditsrc'],
						'title' => $value['title'],
						'unit' => $value['unit'],
						'ratiosrc' => $value['ratiosrc'],
						'ratiodesc' => $value['ratiodesc'],
						'ratio' => $value['ratio']
					);
				}
			}
		}
		$tmp = array();
		foreach($outextcredits as $value) {
			$key = $value['appiddesc'].'|'.$value['creditdesc'];
			if(!isset($tmp[$key])) {
				$tmp[$key] = array('title' => $value['title'], 'unit' => $value['unit']);
			}
			$tmp[$key]['ratiosrc'][$value['creditsrc']] = $value['ratiosrc'];
			$tmp[$key]['ratiodesc'][$value['creditsrc']] = $value['ratiodesc'];
			$tmp[$key]['creditsrc'][$value['creditsrc']] = $value['ratio'];
		}
		$outextcredits = $tmp;

		$cachefile = DISCUZ_ROOT.'./uc_client/data/cache/creditsettings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'creditsettings\'] = '.var_export($outextcredits, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

		return API_RETURN_SUCCEED;
	}

	function addfeed($get, $post) {
		global $_G;

		if(!API_ADDFEED) {
			return API_RETURN_FORBIDDEN;
		}
		return API_RETURN_SUCCEED;
	}
}