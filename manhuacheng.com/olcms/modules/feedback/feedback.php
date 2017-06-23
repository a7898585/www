<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class feedback extends admin {
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('feedback_model');
	}

	public function init() {		
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo('',$order = 'updatetime DESC',$page, $pages = '9');
		$pages = $this->db->pages;
		$big_menu = array('?m=feedback&c=feedback', L('feedback'));
		//$big_menu = '';
		include $this->admin_tpl('feedback_list');
	}
	/**
	 * 删除友情链接  
	 * @param	intval	$sid	友情链接ID，递归删除
	 */
	public function delete() {
  		if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $id_arr) {
 					//批量删除友情链接
					$this->db->delete(array('id'=>$id_arr));
				}
				showmessage(L('operation_success'),'?m=feedback&c=feedback');
			}else{
				$id = intval($_GET['id']);
				if($id < 1) return false;
				//删除友情链接
				$result = $this->db->delete(array('id'=>$id));
				
				if($result){
					showmessage(L('operation_success'),'?m=feedback&c=feedback');
				}else {
					showmessage(L("operation_failure"),'?m=feedback&c=feedback');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	 
	/**
	 * 投票模块配置
	 */
	public function setting() {
		if(isset($_POST['dosubmit'])) {
			setcache('link', $_POST['setting'], 'commons'); //设置缓存
			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			$setting = array2string($_POST['setting']);
			$m_db->update(array('setting'=>$setting), array('module'=>ROUTE_M));
			showmessage(L('setting_updates_successful'), '?m=link&c=link&a=init');
		} else {
			@extract($this->M);
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=link&c=link&a=add\', title:\''.L('link_add').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('link_add'));
 			include $this->admin_tpl('setting');
		}
	}
	
	/**
	 * 
	 * 审核申请 ...
	 */
	public function check_register(){
		if(isset($_POST['dosubmit'])) {
			if((!isset($_GET['linkid']) || empty($_GET['linkid'])) && (!isset($_POST['linkid']) || empty($_POST['linkid']))) {
				showmessage(L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($_POST['linkid'])){
					foreach($_POST['linkid'] as $linkid_arr) {
						//批量审核通过友情链接
						$this->db->update(array('passed'=>1),array('linkid'=>$linkid_arr));
					}
					showmessage(L('operation_success'),'?m=link&c=link');
				}else{
					$linkid = intval($_GET['linkid']);
					if($linkid < 1) return false;
					//删除友情链接
					$result = $this->db->update(array('passed'=>1),array('linkid'=>$linkid));
					if($result){
						showmessage(L('operation_success'),'?m=link&c=link');
					}else {
						showmessage(L("operation_failure"),'?m=link&c=link');
					}
				}
			}
		}else {
			//读取未审核列表
			$where = array('passed'=>0);
			$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
			$infos = $this->db->listinfo($where,'linkid DESC',$page, $pages = '9');
			$pages = $this->db->pages;
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=link&c=link&a=add\', title:\''.L('link_add').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('link_add'));
			include $this->admin_tpl('check_register_list');
		}
		
	}
	
	/**
	 * 单个审核通过
	 * Enter description here ...
	 */
	public function check(){
		if((!isset($_GET['linkid']) || empty($_GET['linkid'])) && (!isset($_POST['linkid']) || empty($_POST['linkid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['linkid'])){
				foreach($_POST['linkid'] as $linkid_arr) {
					//批量审核通过友情链接
					$this->db->update(array('passed'=>1),array('linkid'=>$linkid_arr));
				}
				showmessage(L('operation_success'),'?m=link&c=link');
			}else{
				$linkid = intval($_GET['linkid']);
				if($linkid < 1) return false;
				//删除友情链接
				$result = $this->db->update(array('passed'=>1),array('linkid'=>$linkid));
				if($result){
					showmessage(L('operation_success'),'?m=link&c=link');
				}else {
					showmessage(L("operation_failure"),'?m=link&c=link');
				}
			}
		}
	}

    
	
	/**
	 * 说明:对字符串进行处理
	 * @param $string 待处理的字符串
	 * @param $isjs 是否生成JS代码
	 */
	function format_js($string, $isjs = 1){
		$string = addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
		return $isjs ? 'document.write("'.$string.'");' : $string;
	}
 
 
	
}
?>