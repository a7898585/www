<?php
/*
 * author:xiezhihai
 * 合作的控制器类
 */
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
pc_base::load_app_class ( 'admin', 'admin', 0 );

class admin_cooprate extends admin {
	
	private $db;
	public $username;
	public function __construct() {
		parent::__construct ();
		$this->username = param::get_cookie ( 'admin_username' );
		$this->db = pc_base::load_model ( 'cooprate_model' );
	}
	
	public function init() {
		$sql = '';
		$page = max ( intval ( $_GET ['page'] ), 1 );
		$data = $this->db->listinfo ( $sql, '`cooid`,`topdomain`,`dlfilename`', $page );
		$big_menu = array ('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=cooprate&c=admin_cooprate&a=add\', title:\'' . L ( 'cooprate_add' ) . '\', width:\'500\', height:\'200\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L ( 'cooprate_add' ) );
		
		include $this->admin_tpl ( 'addcooprate_list' );
	}
	
	/*
	 * 增加数据
	 *
	 */
	
	public function add() {
		if (isset ( $_POST ['dosubmit'] )) {
			if ($this->db->insert ( $_POST ['cooprate'] ))
				showmessage ( L ( 'cooprate_successful_added' ), HTTP_REFERER, '', 'add' );
		} else {
			pc_base::load_app_func ( 'global', 'admin' );
			$template_list = template_list ( 0 );
			$info = siteinfo ();
			foreach ( $template_list as $k => $v ) {
				$template_list [$v ['dirname']] = $v ['name'] ? $v ['name'] : $v ['dirname'];
				unset ( $template_list [$k] );
			}
			$show_header = $show_validator = $show_scroll = 1;
			pc_base::load_sys_class ( 'form', '', 0 );
			include $this->admin_tpl ( 'addcooprate_add' );
		}
	}
	
	/**
	 * 编辑某条数据
	 */
	public function edit() {
		$_GET ['cooid'] = intval ( $_GET ['cooid'] );
		if (! $_GET ['cooid'])
			showmessage ( L ( 'illegal_operation' ) );
		if (isset ( $_POST ['dosubmit'] )) {
			$_POST ['topdomain'] = $this->check ( $_POST ['topdomain'], 'edit' );
			if ($this->db->update ( $_POST ['topdomain'], array ('aid' => $_GET ['aid'] ) ))
				showmessage ( L ( 'cooprate_a' ), HTTP_REFERER, '', 'edit' );
		} else {
			$where = array ('cooid' => $_GET ['cooid'] );
			$an_info = $this->db->get_one ( $where );
			pc_base::load_sys_class ( 'form', '', 0 );
			pc_base::load_app_func ( 'global', 'admin' );
			$template_list = template_list ( 0 );
			foreach ( $template_list as $k => $v ) {
				$template_list [$v ['dirname']] = $v ['name'] ? $v ['name'] : $v ['dirname'];
				unset ( $template_list [$k] );
			}
			$show_header = $show_validator = $show_scroll = 1;
			include $this->admin_tpl ( 'cooprate_edit' );
		}
	}
	public function setdefaulturl() {
		$file = OLCMS_PATH."caches/set_cooprate.cache";
		if(isset($_POST['defaulturl'])&&!empty($_POST['defaulturl'])){
			$array = array ("url" => $_POST['defaulturl']);
			file_put_contents ( $file, serialize ( $array ) );
			showmessage ( L('setsuccess'),'/index.php?m=cooprate&c=admin_cooprate&a=init&menuid=1539&s=1');
	
		}else{
			$array = array ("url" => "");
			file_put_contents ( $file, serialize ( $array ) );
			showmessage ( L('setsuccess'),'/index.php?m=cooprate&c=admin_cooprate&a=init&menuid=1539&s=1');
		}
		
	}
	public function set() {
			include $this->admin_tpl ( 'addcooprate_set' );
		
		
	}
	
	/*
   * 删除数据
   */
	public function delete($cooid = 0) {
		
		if ((! isset ( $_GET ['cooid'] ) || empty ( $_GET ['cooid'] )) && ! $cooid) {
			showmessage ( L ( 'illegal_operation' ) );
		} else {
			if (is_array ( $_GET ['cooid'] ) && ! $cooid) {
				array_map ( array ($this, 'delete' ), $_GET ['cooid'] );
				showmessage ( L ( 'cooprate_deleted' ), HTTP_REFERER );
			} elseif (! $cooid) {
				
				$cooid = intval ( $_GET ['cooid'] );
				$this->db->delete ( array ('cooid' => $cooid ) );
				showmessage ( L ( 'cooprate_deleted' ), HTTP_REFERER );
			}
		}
	}

}
?>