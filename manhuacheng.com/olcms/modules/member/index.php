<?php
/**
 * 会员前台管理中心、账号管理、收藏操作类
 */
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
pc_base::load_app_class ( 'foreground' );
pc_base::load_sys_class ( 'format', '', 0 );
pc_base::load_sys_class ( 'form', '', 0 );

class index extends foreground {
	
	private $times_db;
	
	function __construct() {
		parent::__construct ();
		$this->http_user_agent = str_replace('7.0' ,'8.0',$_SERVER['HTTP_USER_AGENT']);
		$this->ucopen = pc_base::load_config ( 'system', 'uc' );
		if ($this->ucopen) {
			$this->uc_api_url = $this->_init_uc ();
			$ucuid = $this->memberinfo ['ucuid'];
		}else{
			$ucuid = $this->memberinfo ['userid'];
		}
		$this->avatar = get_memberavatar ( $ucuid );
	}
	
	public function init() {
		$memberinfo = $this->memberinfo;
		$grouplist = getcache ( 'grouplist' );
		$avatar = $this->avatar;
		$memberinfo ['groupname'] = $grouplist [$memberinfo ['groupid']] ['name'];
		include template ( 'member', 'index' );
	}
	
	public function register() {
		$this->_session_start ();
		//加载用户模块配置
		$member_setting = getcache ( 'member_setting' );
		if ( $member_setting ['allowregister']) {
			showmessage ( L ( 'deny_register' ), 'index.php?m=member&c=index&a=login' );
		}
		
		header ( "Cache-control: private" );
		if (isset ( $_POST ['dosubmit'] )) {
			if ($_SESSION ['code'] != strtolower ( $_POST ['code'] )) {
				showmessage ( L ( 'code_error' ) );
			}
			
			$userinfo ['encrypt'] = create_randomstr ( 6 );
			$userinfo ['username'] = isset ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : exit ( '0' );
			$userinfo ['nickname'] = isset ( $_POST ['nickname'] ) ? trim ( $_POST ['nickname'] ) : '';
			$userinfo ['password'] = isset ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : exit ( '0' );
			$userinfo ['email'] = isset ( $_POST ['email'] ) ? trim ( $_POST ['email'] ) : exit ( '0' );
			$userinfo ['modelid'] = isset ( $_POST ['modelid'] ) ? intval ( $_POST ['modelid'] ) : $member_setting ['default_modelid'];
			$userinfo ['regip'] = ip ();
			$userinfo ['point'] = $member_setting ['defualtpoint'] ? $member_setting ['defualtpoint'] : 0;
			$userinfo ['amount'] = $member_setting ['defualtamount'] ? $member_setting ['defualtamount'] : 0;
			$userinfo ['regdate'] = $userinfo ['lastdate'] = SYS_TIME;
			
			if ($member_setting ['enablemailcheck']) { //是否需要邮件验�?
				$userinfo ['groupid'] = 7;
			} elseif ($member_setting ['registerverify']) { //是否需要管理员审核
				$userinfo ['modelinfo'] = isset ( $_POST ['info'] ) ? array2string ( $_POST ['info'] ) : '';
				$this->verify_db = pc_base::load_model ( 'member_verify_model' );
				unset ( $userinfo ['lastdate'] );
				$this->verify_db->insert ( $userinfo );
				showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index&a=register&t=3' );
			} else {
				$userinfo ['groupid'] = $this->_get_usergroup_bypoint ( $userinfo ['point'] );
			}
			if ($this->ucopen) {
				$status = uc_user_register ( $userinfo ['username'], $userinfo ['password'], $userinfo ['email'], '', '', $userinfo ['regip'] );
				if (isset($status) && !empty($status)) {
					
					$userinfo ['ucuid'] = $status;
					//传入uc为明文密码，加密后存入olcms
					$userinfo ['password'] = password ( $userinfo ['password'], $userinfo ['encrypt'] );
					
					$userid = $this->db->insert ( $userinfo, 1 );
					if ($member_setting ['choosemodel']) { //如果开启选择模型
						//通过模型获取会员信息					
						require_once CACHE_MODEL_PATH . 'member_input.class.php';
						require_once CACHE_MODEL_PATH . 'member_update.class.php';
						$member_input = new member_input ( $userinfo ['modelid'] );
						$user_model_info = $member_input->get ( $_POST ['info'] );
						$user_model_info ['userid'] = $userid;
						
						//插入会员模型数据
						$this->db->set_model ( $userinfo ['modelid'] );
						$this->db->insert ( $user_model_info );
					}
					
					if ($userid > 0) {
						//执行登陆操作
						if (! $cookietime)
							$get_cookietime = param::get_cookie ( 'cookietime' );
						$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
						$cookietime = $_cookietime ? TIME + $_cookietime : 0;
						
						if ($userinfo ['groupid'] == 7) {
							param::set_cookie ( '_username', $userinfo ['username'], $cookietime );
							param::set_cookie ( 'email', $userinfo ['email'], $cookietime );
						} else {
							$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
							$olcms_auth = sys_auth ( $userid . "\t" . $userinfo ['password'], 'ENCODE', $olcms_auth_key );
							
							param::set_cookie ( 'auth', $olcms_auth, $cookietime );
							param::set_cookie ( '_userid', $userid, $cookietime );
							param::set_cookie ( '_username', $userinfo ['username'], $cookietime );
							param::set_cookie ( '_nickname', $userinfo ['nickname'], $cookietime );
							param::set_cookie ( '_groupid', $userinfo ['groupid'], $cookietime );
							param::set_cookie ( 'cookietime', $_cookietime, $cookietime );
						}
					}
					//如果需要邮箱认�?
					if ($member_setting ['enablemailcheck']) {
						$siteinfo = siteinfo();
						pc_base::load_sys_func ( 'mail' );
						$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
						$code = sys_auth ( $userid, 'ENCODE', $olcms_auth_key );
						$url = $siteinfo['domain'] . "index.php?m=member&c=index&a=register&code=$code&verify=1";
						$message = $member_setting ['registerverifymessage'];
						$message = str_replace ( array ('{click}', '{url}' ), array ('<a href="' . $url . '">' . L ( 'please_click' ) . '</a>', $url ), $message );
						
						sendmail ( $userinfo ['email'], L ( 'reg_verify_email' ), $message );
						showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index&a=register&t=2' );
					} else {
						//如果不需要邮箱认证、直接登录其他应�?
						$synloginstr = uc_user_synlogin ( $userinfo ['ucuid'] );
						showmessage ( L ( 'operation_success' ) . $synloginstr, 'index.php?m=member&c=index&a=init' );
					}
				
				} else {
					showmessage ( L ( 'please_check_uc' ), HTTP_REFERER );
				}
			} else {
				$userinfo ['password'] = password ( $userinfo ['password'], $userinfo ['encrypt'] );
				$userid = $this->db->insert ( $userinfo, 1 );
				if ($member_setting ['choosemodel'] || $member_setting['default_modelid']) { //如果开启选择模型
					//通过模型获取会员信息					
					require_once CACHE_MODEL_PATH . 'member_input.class.php';
					require_once CACHE_MODEL_PATH . 'member_update.class.php';
					$member_input = new member_input ( $userinfo ['modelid'] );
					$user_model_info = $member_input->get ( $_POST ['info'] );
					$user_model_info ['userid'] = $userid;
					$userinfo ['modelid'] = $userinfo ['modelid'] ? $userinfo ['modelid'] : $member_setting['default_modelid']; 
					//插入会员模型数据
					$this->db->set_model ( $userinfo ['modelid'] );
					$this->db->insert ( $user_model_info );
				}
				
				if ($userid > 0) {
					//执行登陆操作
					if (! $cookietime)
						$get_cookietime = param::get_cookie ( 'cookietime' );
					$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
					$cookietime = $_cookietime ? TIME + $_cookietime : 0;
					
					if ($userinfo ['groupid'] == 7) {
						param::set_cookie ( '_username', $userinfo ['username'], $cookietime );
						param::set_cookie ( 'email', $userinfo ['email'], $cookietime );
					} else {
						$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
						$olcms_auth = sys_auth ( $userid . "\t" . $userinfo ['password'], 'ENCODE', $olcms_auth_key );
						
						param::set_cookie ( 'auth', $olcms_auth, $cookietime );
						param::set_cookie ( '_userid', $userid, $cookietime );
						param::set_cookie ( '_username', $userinfo ['username'], $cookietime );
						param::set_cookie ( '_nickname', $userinfo ['nickname'], $cookietime );
						param::set_cookie ( '_groupid', $userinfo ['groupid'], $cookietime );
						param::set_cookie ( 'cookietime', $_cookietime, $cookietime );
					}
				}
				//如果需要邮箱认�?
				if ($member_setting ['enablemailcheck']) {
					pc_base::load_sys_func ( 'mail' );
					$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
					$code = sys_auth ( $userid, 'ENCODE', $olcms_auth_key );
					$url = APP_PATH . "index.php?m=member&c=index&a=register&code=$code&verify=1";
					$message = $member_setting ['registerverifymessage'];
					$message = str_replace ( array ('{click}', '{url}' ), array ('<a href="' . $url . '">' . L ( 'please_click' ) . '</a>', $url ), $message );
					
					sendmail ( $userinfo ['email'], L ( 'reg_verify_email' ), $message );
					showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index&a=register&t=2' );
				} else {
					//如果不需要邮箱认证、直接登录其他应�?
					showmessage ( L ( 'operation_success' ) . $synloginstr, 'index.php?m=member&c=index&a=init' );
				}
			
			}
			showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
		} else {
			if (! empty ( $_GET ['verify'] )) {
				$code = isset ( $_GET ['code'] ) ? trim ( $_GET ['code'] ) : showmessage ( L ( 'operation_failure' ), 'index.php?m=member&c=index' );
				$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
				$userid = sys_auth ( $code, 'DECODE', $olcms_auth_key );
				$userid = is_numeric ( $userid ) ? $userid : showmessage ( L ( 'operation_failure' ), 'index.php?m=member&c=index' );
				
				$this->db->update ( array ('groupid' => $this->_get_usergroup_bypoint () ), array ('userid' => $userid ) );
				showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index' );
			} elseif (! empty ( $_GET ['protocol'] )) {
				
				include template ( 'member', 'protocol' );
			} else {
				//过滤非当前站点会员模�?
				$modellist = getcache ( 'member_model', 'commons' );
				if (empty ( $modellist )) {
					showmessage ( L ( 'site_have_no_model' ) . L ( 'deny_register' ), HTTP_REFERER );
				}
				//是否开启选择会员模型选项
				if ($member_setting ['choosemodel']) {
					$first_model = array_pop ( array_reverse ( $modellist ) );
					$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : $first_model ['modelid'];
					
					if (array_key_exists ( $modelid, $modellist )) {
						//获取会员模型表单
						require CACHE_MODEL_PATH . 'member_form.class.php';
						$member_form = new member_form ( $modelid );
						$this->db->set_model ( $modelid );
						$forminfos = $forminfos_arr = $member_form->get ();
						
						//万能字段过滤
						foreach ( $forminfos as $field => $info ) {
							if ($info ['isomnipotent']) {
								unset ( $forminfos [$field] );
							} else {
								if ($info ['formtype'] == 'omnipotent') {
									foreach ( $forminfos_arr as $_fm => $_fm_value ) {
										if ($_fm_value ['isomnipotent']) {
											$info ['form'] = str_replace ( '{' . $_fm . '}', $_fm_value ['form'], $info ['form'] );
										}
									}
									$forminfos [$field] ['form'] = $info ['form'];
								}
							}
						}
						
						$formValidator = $member_form->formValidator;
					}
				}
				$description = $modellist [$modelid] ['description'];
				include template ( 'member', 'register' );
			}
		}
	}
	
	public function account_manage() {
		$memberinfo = $this->memberinfo;
		$grouplist = getcache ( 'grouplist' );
		$member_model = getcache ( 'member_model', 'commons' );
		$avatar = $this->avatar;
		//获取用户模型数据
		$this->db->set_model ( $this->memberinfo ['modelid'] );
		$member_modelinfo_arr = $this->db->get_one ( array ('userid' => $this->memberinfo ['userid'] ) );
		$model_info = getcache ( 'model_field_' . $this->memberinfo ['modelid'], 'model' );
		foreach ( $model_info as $k => $v ) {
			if ($v ['formtype'] == 'omnipotent')
				continue;
			if ($v ['formtype'] == 'image') {
				$member_modelinfo [$v ['name']] = "<a href='$member_modelinfo_arr[$k]' target='_blank'><img src='$member_modelinfo_arr[$k]' height='40' widht='40' onerror=\"this.src='$this->uc_api_url/statics/images/member/nophoto.gif'\"></a>";
			} elseif ($v ['formtype'] == 'datetime' && $v ['fieldtype'] == 'int') { 
				$member_modelinfo [$v ['name']] = format::date ( $member_modelinfo_arr [$k], $v ['format'] == 'Y-m-d H:i:s' ? 1 : 0 );
			} elseif ($v ['formtype'] == 'images') {
				$tmp = string2array ( $member_modelinfo_arr [$k] );
				$member_modelinfo [$v ['name']] = '';
				if (is_array ( $tmp )) {
					foreach ( $tmp as $tv ) {
						$member_modelinfo [$v ['name']] .= " <a href='$tv[url]' target='_blank'><img src='$tv[url]' height='40' widht='40' onerror=\"this.src='$this->uc_api_url/statics/images/member/nophoto.gif'\"></a>";
					}
					unset ( $tmp );
				}
			} elseif ($v ['formtype'] == 'box') { //box字段，获取字段名称和值的数组
				$tmp = explode ( "\n", $v ['options'] );
				if (is_array ( $tmp )) {
					foreach ( $tmp as $boxv ) {
						$box_tmp_arr = explode ( '|', trim ( $boxv ) );
						if (is_array ( $box_tmp_arr ) && isset ( $box_tmp_arr [1] ) && isset ( $box_tmp_arr [0] )) {
							$box_tmp [$box_tmp_arr [1]] = $box_tmp_arr [0];
							$tmp_key = intval ( $member_modelinfo_arr [$k] );
						}
					}
				}
				if (isset ( $box_tmp [$tmp_key] )) {
					$member_modelinfo [$v ['name']] = $box_tmp [$tmp_key];
				} else {
					$member_modelinfo [$v ['name']] = $member_modelinfo_arr [$k];
				}
				unset ( $tmp, $tmp_key, $box_tmp, $box_tmp_arr );
			} elseif ($v ['formtype'] == 'linkage') { //如果为联动菜�?
				$tmp = string2array ( $v ['setting'] );
				$tmpid = $tmp ['linkageid'];
				$linkagelist = getcache ( $tmpid, 'linkage' );
				$fullname = $this->_get_linkage_fullname ( $member_modelinfo_arr [$k], $linkagelist );
				
				$member_modelinfo [$v ['name']] = substr ( $fullname, 0, - 1 );
				unset ( $tmp, $tmpid, $linkagelist, $fullname );
			} else {
				$member_modelinfo [$v ['name']] = $member_modelinfo_arr [$k];
			}
		}
		include template ( 'member', 'account_manage' );
	}
	
	public function account_manage_avatar() {
		$memberinfo = $this->memberinfo;
		$avatar = $this->avatar;
		if ($this->ucopen) {
			$ucuid = $this->memberinfo ['ucuid'];
			//返回头像上传html代码
			$avatar_html = uc_avatar ( $ucuid, 'real', 1 );
		} else {
			$ucuid = $this->memberinfo ['userid'];
			pc_base::load_app_func('global');
			$auth_data = auth_data(array('userid'=>$this->memberinfo['userid']));
			$upurl = base64_encode('/index.php?m=member&c=index&a=uploadavatar&auth_data='.$auth_data);
		}
		include template ( 'member', 'account_manage_avatar' );
	}
	
	public function account_manage_security() {
		$memberinfo = $this->memberinfo;
		include template ( 'member', 'account_manage_security' );
	}
	
	public function account_manage_info() {
		if (isset ( $_POST ['dosubmit'] )) {
			//更新用户昵称
			$nickname = isset ( $_POST ['nickname'] ) && trim ( $_POST ['nickname'] ) ? trim ( $_POST ['nickname'] ) : '';
			if ($nickname) {
				$this->db->update ( array ('nickname' => $nickname ), array ('userid' => $this->memberinfo ['userid'] ) );
				if (! isset ( $cookietime )) {
					$get_cookietime = param::get_cookie ( 'cookietime' );
				}
				$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
				$cookietime = $_cookietime ? TIME + $_cookietime : 0;
				param::set_cookie ( '_nickname', $nickname, $cookietime );
			}
			require_once CACHE_MODEL_PATH . 'member_input.class.php';
			require_once CACHE_MODEL_PATH . 'member_update.class.php';
			$member_input = new member_input ( $this->memberinfo ['modelid'] );
			$modelinfo = $member_input->get ( $_POST ['info'] );
			
			$this->db->set_model ( $this->memberinfo ['modelid'] );
			$membermodelinfo = $this->db->get_one ( array ('userid' => $this->memberinfo ['userid'] ) );
			if (! empty ( $membermodelinfo )) {
				$this->db->update ( $modelinfo, array ('userid' => $this->memberinfo ['userid'] ) );
			} else {
				$modelinfo ['userid'] = $this->memberinfo ['userid'];
				$this->db->insert ( $modelinfo );
			}
			
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$memberinfo = $this->memberinfo;
			//获取会员模型表单
			require CACHE_MODEL_PATH . 'member_form.class.php';
			$member_form = new member_form ( $this->memberinfo ['modelid'] );
			$this->db->set_model ( $this->memberinfo ['modelid'] );
			
			$membermodelinfo = $this->db->get_one ( array ('userid' => $this->memberinfo ['userid'] ) );
			$forminfos = $forminfos_arr = $member_form->get ( $membermodelinfo );
			
			//万能字段过滤
			foreach ( $forminfos as $field => $info ) {
				if ($info ['isomnipotent']) {
					unset ( $forminfos [$field] );
				} else {
					if ($info ['formtype'] == 'omnipotent') {
						foreach ( $forminfos_arr as $_fm => $_fm_value ) {
							if ($_fm_value ['isomnipotent']) {
								$info ['form'] = str_replace ( '{' . $_fm . '}', $_fm_value ['form'], $info ['form'] );
							}
						}
						$forminfos [$field] ['form'] = $info ['form'];
					}
				}
			}
			
			$formValidator = $member_form->formValidator;
			
			include template ( 'member', 'account_manage_info' );
		}
	}
	
	public function account_manage_password() {
		if (isset ( $_POST ['dosubmit'] )) {
			if (! is_password ( $_POST ['info'] ['password'] )) {
				showmessage ( L ( 'password_format_incorrect' ), HTTP_REFERER );
			}
			if ($this->memberinfo ['password'] != password ( $_POST ['info'] ['password'], $this->memberinfo ['encrypt'] )) {
				showmessage ( L ( 'old_password_incorrect' ), HTTP_REFERER );
			}
			//修改会员邮箱
			if ($this->memberinfo ['email'] != $_POST ['info'] ['email'] && is_email ( $_POST ['info'] ['email'] )) {
				$email = $_POST ['info'] ['email'];
				$updateinfo ['email'] = $_POST ['info'] ['email'];
			} else {
				$email = '';
			}
			$newpassword = password ( $_POST ['info'] ['newpassword'], $this->memberinfo ['encrypt'] );
			$updateinfo ['password'] = $newpassword;
			
			$this->db->update ( $updateinfo, array ('userid' => $this->memberinfo ['userid'] ) );
			if ($this->ucopen) {
				$res = uc_user_edit ( $this->memberinfo ['username'], $_POST ['info'] ['password'], $_POST ['info'] ['newpassword'], $email, 1 );
			}
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$show_validator = true;
			$memberinfo = $this->memberinfo;
			include template ( 'member', 'account_manage_password' );
		}
	}
	
	public function account_manage_upgrade() {
		$memberinfo = $this->memberinfo;
		$grouplist = getcache ( 'grouplist' );
		if (empty ( $grouplist [$memberinfo ['groupid']] ['allowupgrade'] )) {
			showmessage ( L ( 'deny_upgrade' ), HTTP_REFERER );
		}
		if (isset ( $_POST ['dosubmit'] )) {
			$groupid = isset ( $_POST ['groupid'] ) ? intval ( $_POST ['groupid'] ) : showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			
			$upgrade_type = isset ( $_POST ['upgrade_type'] ) ? intval ( $_POST ['upgrade_type'] ) : showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			$upgrade_date = ! empty ( $_POST ['upgrade_date'] ) ? intval ( $_POST ['upgrade_date'] ) : showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			
			//消费类型，包年、包月、包日，价格
			$typearr = array ($grouplist [$groupid] ['price_y'], $grouplist [$groupid] ['price_m'], $grouplist [$groupid] ['price_d'] );
			//消费类型，包年、包月、包日，时间
			$typedatearr = array ('366', '31', '1' );
			//消费的价�?
			$cost = $typearr [$upgrade_type] * $upgrade_date;
			//购买时间
			$buydate = $typedatearr [$upgrade_type] * $upgrade_date * 86400;
			$overduedate = $memberinfo ['overduedate'] > SYS_TIME ? ($memberinfo ['overduedate'] + $buydate) : (SYS_TIME + $buydate);
			
			if ($memberinfo ['amount'] >= $cost) {
				$this->db->update ( array ('groupid' => $groupid, 'overduedate' => $overduedate, 'vip' => 1 ), array ('userid' => $memberinfo ['userid'] ) );
				//消费记录
				pc_base::load_app_class ( 'spend', 'pay', 0 );
				spend::amount ( $cost, L ( 'allowupgrade' ), $memberinfo ['userid'], $memberinfo ['username'] );
				showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index&a=init' );
			} else {
				showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			}
		
		} else {
			$groupid = isset ( $_GET ['groupid'] ) ? intval ( $_GET ['groupid'] ) : '';
			$memberinfo ['groupname'] = $grouplist [$memberinfo [groupid]] ['name'];
			$memberinfo ['grouppoint'] = $grouplist [$memberinfo [groupid]] ['point'];
			unset ( $grouplist [$memberinfo ['groupid']] );
			include template ( 'member', 'account_manage_upgrade' );
		}
	}
	
	public function login() {
		$this->_session_start ();
		
		if (isset ( $_POST ['dosubmit'] )) {
			if ($_SESSION ['code'] != strtolower ( $_POST ['code'] )) {
				showmessage ( L ( 'code_error' ) );
			}
			$username = isset ( $_POST ['username'] ) && trim ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : showmessage ( L ( 'username_empty' ), HTTP_REFERER );
			$password = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : showmessage ( L ( 'password_empty' ), HTTP_REFERER );
			$synloginstr = ''; //同步登陆js代码
			if ($this->ucopen) {
				$memberinfo = uc_get_user ( $username );
				if (isset ( $memberinfo ['0'] )) {
					//查询帐号
					$r = $this->db->get_one ( array ('ucuid' => $memberinfo ['0'] ) );
					if (! $r) {
						$password = password ( $password );
						$memberinfo ['2'] = $password ['password'];
						$memberinfo ['random'] = $password ['encrypt'];
						//插入会员详细信息，会员不存在 插入会员
						$member_setting = getcache ( 'member_setting' );
						$info = array ('ucuid' => $memberinfo ['0'], 'username' => $memberinfo ['1'], 'password' => $memberinfo ['2'], 'encrypt' => $memberinfo ['random'], 'email' => $memberinfo ['3'], 'regip' => $memberinfo ['4'], 'regdate' => $memberinfo ['regdate'], 'lastip' => $memberinfo ['lastip'], 'lastdate' => $memberinfo ['lastdate'], 'groupid' => $this->_get_usergroup_bypoint (), //会员默认�?
'modelid' => $member_setting['default_modelid'] ); //默认会员
						$this->db->insert ( $info );
						unset ( $info );
						$r = $this->db->get_one ( array ('ucuid' => $memberinfo ['0'] ) );
					}
					$password = $r ['password'];
					$synloginstr = uc_user_synlogin ( $r ['ucuid'] );
				} else {
					if ($memberinfo == - 1) { //用户不存�?
						showmessage ( L ( 'user_not_exist' ), 'index.php?m=member&c=index&a=login' );
					} elseif ($memberinfo == - 2) { //密码错误
						showmessage ( L ( 'password_error' ), 'index.php?m=member&c=index&a=login' );
					} else {
						showmessage ( L ( 'login_failure' ), 'index.php?m=member&c=index&a=login' );
					}
				}
			
			} else {
				//密码错误剩余重试次数
				$this->times_db = pc_base::load_model ( 'times_model' );
				$rtime = $this->times_db->get_one ( array ('username' => $username ) );
				if ($rtime ['times'] > 4) {
					$minute = 60 - floor ( (SYS_TIME - $rtime ['logintime']) / 60 );
					showmessage ( L ( 'wait_1_hour', array ('minute' => $minute ) ) );
				}
				//查询帐号
				$r = $this->db->get_one ( array ('username' => $username ) );
				if (! $r)
					showmessage ( L ( 'user_not_exist' ), 'index.php?m=member&c=index&a=login' );
				$password = md5 ( md5 ( trim ( $password ) ) . $r ['encrypt'] );
				if ($r ['password'] != $password) {
					$ip = ip ();
					if ($rtime && $rtime ['times'] < 5) {
						$times = 5 - intval ( $rtime ['times'] );
						$this->times_db->update ( array ('ip' => $ip, 'times' => '+=1' ), array ('username' => $username ) );
					} else {
						$this->times_db->insert ( array ('username' => $username, 'ip' => $ip, 'logintime' => SYS_TIME, 'times' => 1 ) );
						$times = 5;
					}
					showmessage ( L ( 'password_error', array ('times' => $times ) ), 'index.php?m=member&c=index&a=login', 3000 );
				}
				$this->times_db->delete ( array ('username' => $username ) );
			}
			
			//如果用户被锁�?
			if ($r ['islock']) {
				showmessage ( L ( 'user_is_lock' ) );
			}
			$userid = $r ['userid'];
			$groupid = $r ['groupid'];
			$username = $r ['username'];
			$nickname = empty ( $r ['nickname'] ) ? $username : $r ['nickname'];
			$updatearr = array ('lastip' => ip (), 'lastdate' => SYS_TIME );
			//vip过期，更新vip和会员组
			if ($r ['overduedate'] < SYS_TIME) {
				$updatearr ['vip'] = 0;
			}
			
			//检查用户积分，更新新用户组，除去邮箱认证、禁止访问、游客组用户
			if ($r ['point'] >= 0 && ! in_array ( $r ['groupid'], array ('1', '7', '8' ) )) {
				$grouplist = getcache ( 'grouplist' );
				
				foreach ( $grouplist as $k => $v ) {
					$grouppointlist [$k] = $v ['point'];
				}
				arsort ( $grouppointlist );
				
				//如果超出用户组积分设置则为积分最高的用户�?
				if ($r ['point'] > max ( $grouppointlist )) {
					foreach ( $grouppointlist as $k => $v ) {
						$check_groupid = $k;
						break;
					}
				} else {
					foreach ( $grouppointlist as $k => $v ) {
						if ($r ['point'] >= $v) {
							$check_groupid = $tmp_k;
							break;
						}
						$tmp_k = $k;
					}
				}
				
				if ($check_groupid != $r ['groupid']) {
					$updatearr ['groupid'] = $groupid = $check_groupid;
				}
			}
			
			$this->db->update ( $updatearr, array ('userid' => $userid ) );
			
			if (! isset ( $cookietime )) {
				$get_cookietime = param::get_cookie ( 'cookietime' );
			}
			$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
			$cookietime = $_cookietime ? TIME + $_cookietime : 0;
			
			$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
			$olcms_auth = sys_auth ( $userid . "\t" . $password, 'ENCODE', $olcms_auth_key );
			
			param::set_cookie ( 'auth', $olcms_auth, $cookietime );
			param::set_cookie ( '_userid', $userid, $cookietime );
			param::set_cookie ( '_username', $username, $cookietime );
			param::set_cookie ( '_groupid', $groupid, $cookietime );
			param::set_cookie ( '_nickname', $nickname, $cookietime );
			param::set_cookie ( 'cookietime', $_cookietime, $cookietime );
			$forward = isset ( $_POST ['forward'] ) && ! empty ( $_POST ['forward'] ) ? urldecode ( $_POST ['forward'] ) : 'index.php?m=member&c=index';
			showmessage ( L ( 'login_success' ) . $synloginstr, $forward );
		} else {
			$setting = pc_base::load_config ( 'system' );
			$forward = isset ( $_GET ['forward'] ) && trim ( $_GET ['forward'] ) ? urlencode ( $_GET ['forward'] ) : '';
			include template ( 'member', 'login' );
		}
	}
	
	public function logout() {
		$setting = pc_base::load_config ( 'system' );
			$synlogoutstr = ''; //同步退出js代码
			if ($this->ucopen) {
				$synlogoutstr = uc_user_synlogout ();
			}
			param::set_cookie ( 'auth', '' );
			param::set_cookie ( '_userid', '' );
			param::set_cookie ( '_username', '' );
			param::set_cookie ( '_groupid', '' );
			param::set_cookie ( 'cookietime', '' );
			$forward = isset ( $_GET ['forward'] ) && trim ( $_GET ['forward'] ) ? $_GET ['forward'] : 'index.php?m=member&c=index&a=login';
			showmessage ( L ( 'logout_success' ) . $synlogoutstr, $forward );
	}
	
	/**
	 * 我的收藏
	 * 
	 */
	public function favorite() {
		$this->favorite_db = pc_base::load_model ( 'favorite_model' );
		$memberinfo = $this->memberinfo;
		if (isset ( $_GET ['id'] ) && trim ( $_GET ['id'] )) {
			$this->favorite_db->delete ( array ('userid' => $memberinfo ['userid'], 'id' => intval ( $_GET ['id'] ) ) );
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$page = isset ( $_GET ['page'] ) && trim ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$favoritelist = $this->favorite_db->listinfo ( array ('userid' => $memberinfo ['userid'] ), 'id DESC', $page, 10 );
			$pages = $this->favorite_db->pages;
			include template ( 'member', 'favorite_list' );
		}
	}
	
	/**
	 * 我的好友
	 */
	public function friend() {
		$memberinfo = $this->memberinfo;
		$this->friend_db = pc_base::load_model ( 'friend_model' );
		if (isset ( $_GET ['friendid'] )) {
			$this->friend_db->delete ( array ('userid' => $memberinfo ['userid'], 'friendid' => intval ( $_GET ['friendid'] ) ) );
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			if ($this->ucopen) {
				$ucuid = $this->memberinfo ['ucuid'];
			}
			//我的好友列表userid
			$page = isset ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$friendids = $this->friend_db->listinfo ( array ('userid' => $memberinfo ['userid'] ), '', $page, 10 );
			$pages = $this->friend_db->pages;
			foreach ( $friendids as $k => $v ) {
				$friendlist [$k] ['friendid'] = $v ['friendid'];
				if ($this->ucopen){
					$friendlist [$k] ['avatar'] = $this->public_getavatar ( $v['ucuid'] );
				}else{
					$friendlist [$k] ['avatar'] = $this->public_getavatar ( $v['userid'] );
				}
					$friendlist [$k] ['is'] = $v ['is'];
			}
			include template ( 'member', 'friend_list' );
		}
	}
	
	/**
	 * 积分兑换
	 * Enter description here ...
	 */
	public function change_credit() {
		$memberinfo = $this->memberinfo;
		//加载用户模块配置
		$member_setting = getcache ( 'member_setting' );
		showmessage ( '暂时关闭积分兑换', HTTP_REFERER );
		
		if (isset ( $_POST ['dosubmit'] )) {
			//本系统积分兑换数
			$fromvalue = intval ( $_POST ['fromvalue'] );
			//本系统积分类�?
			$from = $_POST ['from'];
			$toappid_to = explode ( '_', $_POST ['to'] );
			//目标系统appid
			$toappid = $toappid_to [0];
			//目标系统积分类型
			$to = $toappid_to [1];
			if ($from == 1) {
				if ($memberinfo ['point'] < $fromvalue) {
					showmessage ( L ( 'need_more_point' ), HTTP_REFERER );
				}
			} elseif ($from == 2) {
				if ($memberinfo ['amount'] < $fromvalue) {
					showmessage ( L ( 'need_more_amount' ), HTTP_REFERER );
				}
			} else {
				showmessage ( L ( 'credit_setting_error' ), HTTP_REFERER );
			}
			
			$status = $this->client->ps_changecredit ( $memberinfo ['ucuid'], $from, $toappid, $to, $fromvalue );
			if ($status == 1) {
				if ($from == 1) {
					$this->db->update ( array ('point' => "-=$fromvalue" ), array ('userid' => $memberinfo ['userid'] ) );
				} elseif ($from == 2) {
					$this->db->update ( array ('amount' => "-=$fromvalue" ), array ('userid' => $memberinfo ['userid'] ) );
				}
				showmessage ( L ( 'operation_success' ), HTTP_REFERER );
			} else {
				showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			}
		} elseif (isset ( $_POST ['buy'] )) {
			if (! is_numeric ( $_POST ['money'] )) {
				showmessage ( L ( 'money_error' ), HTTP_REFERER );
			} else {
				$money = intval ( $_POST ['money'] );
			}
			
			if ($memberinfo ['amount'] < $money) {
				showmessage ( L ( 'short_of_money' ), HTTP_REFERER );
			}
			//此处比率读取用户配置
			$point = $money * $member_setting ['rmb_point_rate'];
			$this->db->update ( array ('amount' => "-=$money", 'point' => "+=$point" ), array ('userid' => $memberinfo ['userid'] ) );
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$credit_list = pc_base::load_config ( 'credit' );
			
			include template ( 'member', 'change_credit' );
		}
	}
	
	//mini登陆�?
	public function mini() {
		$_username = param::get_cookie ( '_username' );
		$_userid = param::get_cookie ( '_userid' );
		include template ( 'member', 'mini' );
	}
	
	/**
	 * 初始化uc
	 * about uc, include client and client configure
	 * @return string uc_api_url uc地址
	 */
	private function _init_uc() {
		$uc_api_url = pc_base::load_config ( 'system', 'uc_api_url' );
		define ( 'UC_APPID', pc_base::load_config ( 'system', 'uc_appid' ) );
		define ( 'UC_API', $uc_api_url );
		include OLCMS_PATH . 'uc_client/client.php';
		return $uc_api_url;
	}
	
	private function _checkname($username) {
		$username = trim ( $username );
		if ($this->db->get_one ( array ('username' => $username ) )) {
			return false;
		}
		return true;
	}
	
	private function _session_start() {
		$session_storage = 'session_' . pc_base::load_config ( 'system', 'session_storage' );
		pc_base::load_sys_class ( $session_storage );
	}
	
	/*
	 * 通过linkageid获取名字路径
	 */
	private function _get_linkage_fullname($linkageid, $linkagelist) {
		$fullname = '';
		if ($linkagelist ['data'] [$linkageid] ['parentid'] != 0) {
			$fullname = $this->_get_linkage_fullname ( $linkagelist ['data'] [$linkageid] ['parentid'], $linkagelist );
		}
		//所在地区名�?
		$return = $fullname . $linkagelist ['data'] [$linkageid] ['name'] . '>';
		return $return;
	}
	
	/**
	 *根据积分算出用户�?
	 * @param $point int 积分�?
	 */
	private function _get_usergroup_bypoint($point = 0) {
		$groupid = 2;
		if (empty ( $point )) {
			$member_setting = getcache ( 'member_setting' );
			$point = $member_setting ['defualtpoint'] ? $member_setting ['defualtpoint'] : 0;
		}
		$grouplist = getcache ( 'grouplist' );
		
		foreach ( $grouplist as $k => $v ) {
			$grouppointlist [$k] = $v ['point'];
		}
		arsort ( $grouppointlist );
		
		//如果超出用户组积分设置则为积分最高的用户�?
		if ($point > max ( $grouppointlist )) {
			$groupid = key ( $grouppointlist );
		} else {
			$tmp_k = '';
			foreach ( $grouppointlist as $k => $v ) {
				if ($point >= $v) {
					$groupid = $tmp_k;
					break;
				}
				$tmp_k = $k;
			}
		}
		return $groupid;
	}
	
	/**
	 * 检查用户名
	 * @param string $username	用户�?
	 * @return $status {-4：用户名禁止注册;-1:用户名已经存�?;1:成功}
	 */
	public function public_checkname_ajax() {
		$username = isset ( $_GET ['username'] ) && trim ( $_GET ['username'] ) ? trim ( $_GET ['username'] ) : exit ( 0 );
		if (CHARSET != 'utf-8') {
			$username = iconv ( 'utf-8', CHARSET, $username );
			$username = trim ( $username );
		}
		if ($this->ucopen) {
			$status = uc_get_user ( $username );
			if ($status === '') {
				exit ( '1' );
			} else {
				exit ( '0' );
			}
		} else {
			$this->member_db = pc_base::load_model ( 'member_model' );
			$this->verify_db = pc_base::load_model ( 'member_verify_model' );
			if ($this->member_db->get_one ( array ('username' => $username ), 'userid' )) {
				exit ( '0' );
			} elseif ($this->verify_db->get_one ( array ('username' => $username, 'status' => 0 ), 'userid' )) {
				exit ( '0' );
			} else {
				exit ( '1' );
			}
		}
	}
	
	/**
	 * 检查用户昵�?
	 * @param string $nickname	昵称
	 * @return $status {0:已存�?1:成功}
	 */
	public function public_checknickname_ajax() {
		$nickname = isset ( $_GET ['nickname'] ) && trim ( $_GET ['nickname'] ) ? trim ( $_GET ['nickname'] ) : exit ( '0' );
		if (CHARSET != 'utf-8') {
			$nickname = iconv ( 'utf-8', CHARSET, $nickname );
			$nickname = addslashes ( $nickname );
		}
		$this->verify_db = pc_base::load_model ( 'member_verify_model' );
		if ($this->verify_db->get_one ( array ('nickname' => $nickname, 'status' => 0 ), 'userid' )) {
			exit ( '0' );
		}
		if ($this->db->get_one ( array ('nickname' => $nickname ), 'userid' )) {
			exit ( '0' );
		} else {
			exit ( '1' );
		}
	}
	
	/**
	 * 检查邮�?
	 * @param string $email
	 * @return $status {-1:email已经存在 ;-5:邮箱禁止注册;1:成功}
	 */
	public function public_checkemail_ajax() {
		$email = isset ( $_GET ['email'] ) && trim ( $_GET ['email'] ) ? trim ( $_GET ['email'] ) : exit ( 0 );
		if ($this->ucopen) {
			$status = uc_user_checkemail ( $email );
			if ($status == - 5) { //禁止注册
				exit ( '0' );
			} elseif ($status == - 1) { //用户名已存在，但是修改用户的时候需要判断邮箱是否是当前用户�?
				if (isset ( $_GET ['ucuid'] )) { //修改用户传入ucuid
					$status = uc_user_checkemail ( $email );
					if ($status) {
						$status = unserialize ( $status ); //接口返回序列化，进行判断
						if (isset ( $status ['uid'] ) && $status ['uid'] == intval ( $_GET ['ucuid'] )) {
							exit ( '1' );
						} else {
							exit ( '0' );
						}
					} else {
						exit ( '0' );
					}
				} else {
					exit ( '0' );
				}
			} else {
				exit ( '1' );
			}
		} else {
			$this->verify_db = pc_base::load_model ( 'member_verify_model' );
			if ($this->verify_db->get_one ( array ('email' => $email, 'status' => 0 ), 'userid' )) {
				exit ( '0' );
			}
			$this->member_db = pc_base::load_model ( 'member_model' );
			if ($this->member_db->get_one ( array ('email' => $email ), 'userid' )) {
				exit ( '0' );
			} else {
				exit ( '1' );
			}
		}
	}
	
	/**
	 * 找回密码
	 */
	public function public_forget_password() {
		$this->_session_start ();
		$member_setting = getcache ( 'member_setting' );
		if (isset ( $_POST ['dosubmit'] )) {
			if ($_SESSION ['code'] != strtolower ( $_POST ['code'] )) {
				showmessage ( L ( 'code_error' ), HTTP_REFERER );
			}
			
			$memberinfo = $this->db->get_one ( array ('email' => $_POST ['email'] ) );
			if (! empty ( $memberinfo ['email'] )) {
				$email = $memberinfo ['email'];
			} else {
				showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index&a=login' );
			}
			
			pc_base::load_sys_func ( 'mail' );
			$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
			
			$code = sys_auth ( $memberinfo ['userid'] . "\t" . SYS_TIME, 'ENCODE', $olcms_auth_key );
			
			$url = APP_PATH . "index.php?m=member&c=index&a=public_forget_password&code=$code";
			$message = $member_setting ['forgetpassword'];
			$message = str_replace ( array ('{click}', '{url}' ), array ('<a href="' . $url . '">' . L ( 'please_click' ) . '</a>', $url ), $message );
			//获取站点名称
			$sitelist = getcache ( 'sitelist', 'commons' );
			
			if (isset ( $sitelist ['name'] )) {
				$sitename = $sitelist ['name'];
			} else {
				$sitename = 'OLCMS_MAIL';
			}
			sendmail ( $email, L ( 'forgetpassword' ), $message, '', '', $sitename );
			showmessage ( L ( 'operation_success' ), 'index.php?m=member&c=index&a=login' );
		} elseif ($_GET ['code']) {
			$olcms_auth_key = md5 ( pc_base::load_config ( 'system', 'auth_key' ) . $this->http_user_agent );
			$hour = date ( 'y-m-d h', SYS_TIME );
			$code = sys_auth ( $_GET ['code'], 'DECODE', $olcms_auth_key );
			$code = explode ( "\t", $code );
			
			if (is_array ( $code ) && is_numeric ( $code [0] ) && date ( 'y-m-d h', SYS_TIME ) == date ( 'y-m-d h', $code [1] )) {
				$memberinfo = $this->db->get_one ( array ('userid' => $code [0] ) );
				
				if (empty ( $memberinfo ['ucuid'] )) {
					showmessage ( L ( 'operation_failure' ), 'index.php?m=member&c=index&a=login' );
				}
				
				$password = random ( 8 );
				$updateinfo ['password'] = password ( $password, $memberinfo ['encrypt'] );
				
				$this->db->update ( $updateinfo, array ('userid' => $code [0] ) );
				if ($hits->ucopen) {
					uc_user_edit ( '', '', $password, $email, 1 );
				}
				showmessage ( L ( 'operation_success' ) . L ( 'newpassword' ) . ':' . $password );
			} else {
				showmessage ( L ( 'operation_failure' ), 'index.php?m=member&c=index&a=login' );
			}
		} else {
			include template ( 'member', 'forget_password' );
		}
	}
	
	//查看用户信息
	public function public_user_info() {
		$_username = trim ( $_GET ['username'] );
		if (! isset ( $_username )) {
			showmessage ( '此用户不存在', HTTP_REFERER );
		}
		$memberinfo = $this->db->get_one ( array ('username' => $_username ) );
		if (! $memberinfo) {
			showmessage ( '此用户不存在', HTTP_REFERER );
		}
		$avatar = get_memberavatar ( $memberinfo['userid'] );
		$CATEGORYS = getcache ( 'category_content', 'commons' );
		$siteurl = siteurl ();
		$pagesize = 10;
		$page = max ( intval ( $_GET ['page'] ), 1 );
		$workflows = getcache ( 'workflow', 'commons' );
		$this->content_check_db = pc_base::load_model ( 'content_check_model' );
		$infos = $this->content_check_db->listinfo ( array ('username' => $_username ), 'inputtime DESC', $page );
		$datas = array ();
		foreach ( $infos as $_v ) {
			$arr_checkid = explode ( '-', $_v ['checkid'] );
			$_v ['id'] = $arr_checkid [1];
			$_v ['url'] = $_v ['status'] == 99 ? go ( $_v ['catid'], $_v ['id'] ) : APP_PATH . 'index.php?m=content&c=index&a=show&catid=' . $_v ['catid'] . '&id=' . $_v ['id'];
			if (! isset ( $setting [$_v ['catid']] ))
				$setting [$_v ['catid']] = string2array ( $CATEGORYS [$_v ['catid']] ['setting'] );
			$workflowid = $setting [$_v ['catid']] ['workflowid'];
			$_v ['flag'] = $workflows [$workflowid] ['flag'];
			$datas [] = $_v;
		}
		$pages = $this->content_check_db->pages;
		if ($_GET ['catid']) {
			include template ( 'member', 'user_published' );
		} else {
			include template ( 'member', 'user_info' );
		}
	}

	
	//头像上传
	public function uploadavatar() {
		$auth_key = pc_base::load_config('system','auth_key');
		if (isset ( $_GET ) && is_array ( $_GET ) && count ( $_GET ) > 0) {
			foreach ( $_GET as $k => $v ) {
				if (! in_array ( $k, array ('m', 'c', 'a' ) )) {
					$_POST [$k] = $v;
				}
			}
		}
		
		if (isset ( $_POST ['data'] )) {
			parse_str ( sys_auth ( $_POST ['data'], 'DECODE', $auth_key ), $this->data );
			if (get_magic_quotes_gpc ()) {
				$this->data = new_stripslashes ( $this->data );
			}
			if (! is_array ( $this->data )) {
				exit ( '0' );
			}
		} else {
			exit ( '0' );
		}
		if(isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
			$this->data['avatardata'] = $GLOBALS['HTTP_RAW_POST_DATA'];
		}
		//根据用户id创建文件�?
		if(isset($this->data['userid']) && isset($this->data['avatardata'])) {
			$this->userid = $this->data['userid'];
			$this->avatardata = $this->data['avatardata'];
		} else {
			exit('0');
		}
		
		$dir1 = ceil($this->userid / 10000);
		$dir2 = ceil($this->userid % 10000 / 1000);
		
		//创建图片存储文件�?
		$avatarfile = pc_base::load_config('system', 'upload_path').'avatar/';
		$dir = $avatarfile.$dir1.'/'.$dir2.'/'.$this->userid.'/';
		if(!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		
		//存储flashpost图片
		$filename = $dir.$this->userid.'.zip';
		file_put_contents($filename, $this->avatardata);
		
		//解压缩文�?
		pc_base::load_app_class('pclzip', '', 0);
		$archive = new PclZip($filename);
		if ($archive->extract(PCLZIP_OPT_PATH, $dir) == 0) {
			die("Error : ".$archive->errorInfo(true));
		}
		//判断文件安全，删除压缩包和非jpg图片
		$avatararr = array('180x180.jpg', '30x30.jpg', '45x45.jpg', '90x90.jpg');
		if($handle = opendir($dir)) {
		    while(false !== ($file = readdir($handle))) {
				if($file !== '.' && $file !== '..') {
					if(!in_array($file, $avatararr)) {
						@unlink($dir.$file);
					} else {
						$info = @getimagesize($dir.$file);
						if(!$info || $info[2] !=2) {
							@unlink($dir.$file);
						}
					}
				}
		    }
		    closedir($handle);    
		}
		$this->db->update(array('avatar'=>1), array('userid'=>$this->userid));
		exit('1');
	}
}
?>