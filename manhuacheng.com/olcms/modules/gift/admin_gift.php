<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class admin_gift extends admin {
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('change_gift_record_model');
		
	}

	public function init() {
	$where='';
//		if($_GET['typeid'] && intval($_GET['typeid'])>0){
//			$where='typeid='.$_GET['typeid'];
//		}
//		if($_GET['type'] && $_GET['keyword']){
//			$keyword=trim($_GET['keyword']);
//			$where .= $where?' and ':'';
//			$where .= $_GET['type']=='1'?'`name` like \'%'.$keyword.'%\'':'`url` like \'%'.$keyword.'%\'';
//		}
	$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
	$infos = $this->db->listinfo($where,$order = 'id DESC',$page, $pages = '9');
foreach($infos as $k=>$info){
	$sql="select title,free from tt_gifts where id='".$info['giftid']."'";
	$giftinfos=$this->db->select2($sql);
	foreach($giftinfos as $gift){
	$infos[$k]['giftname']=$gift['title'];
	$infos[$k]['free']=$gift['free'];
}
}

	$pages = $this->db->pages;
//		//提取分类名称
//		$type_arr = array ();
//		//$types 是个二维的数组,为提取真正有效的数据,需重新组合一个新数组.以便在列表中使用
//		foreach($types as $typeid=>$type){
//			$type_arr[$type['typeid']] = $type['name'];
//		}
//		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=link&c=link&a=add\', title:\''.L('link_add').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('link_add'));
//		include $this->admin_tpl('gift_list');


include $this->admin_tpl('gift_list');
	}

	/*
	 *判断标题重复和验证 
	 */
	public function public_name() {
		$link_title = isset($_GET['link_name']) && trim($_GET['link_name']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['link_name'])) : trim($_GET['link_name'])) : exit('0');
			
		$linkid = isset($_GET['linkid']) && intval($_GET['linkid']) ? intval($_GET['linkid']) : '';
		$data = array();
		if ($linkid) {

			$data = $this->db->get_one(array('linkid'=>$linkid), 'name');
			if (!empty($data) && $data['name'] == $link_title) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$link_title), 'linkid')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	

	public function edit() {
		if(isset($_POST['dosubmit'])){
		$id = intval($_GET['id']);
			if($id < 1) return false;
			if(!is_array($_POST['gift']) || empty($_POST['gift'])) return false;
			if((!$_POST['gift']['name']) || empty($_POST['gift']['name'])) return false;
			$this->db->update($_POST['gift'],array('id'=>$id));

			showmessage(L('operation_success'),'?m=gift&c=admin_gift&a=init','', 'edit');
//			
		}else{
			
			$show_validator = $show_scroll = $show_header = true;
//			pc_base::load_sys_class('form', '', 0);
//			$types = $this->db2->listinfo(array('module'=> ROUTE_M),$order = 'typeid DESC',$page, $pages = '10');
//			$type_arr = array ();
//			foreach($types as $typeid=>$type){
//				$type_arr[$type['typeid']] = $type['name'];
//			}
//			//解出链接内容
			$info = $this->db->get_one(array('id'=>$_GET['id']));
			if(!$info) showmessage(L('link_exit'));
		extract($info); 
		 			include $this->admin_tpl('gift_edit');
	}

	}
	
	
	/**
	 * 删除
	 * @param	intval	$sid	友情链接ID，递归删除
	 */
	public function delete() {
  		
	if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $giftid_arr) {
 					//批量删除礼物
					$this->db->delete(array('id'=>$giftid_arr));
					//更新附件状态
					
				}
				showmessage(L('operation_success'),'?m=gift&c=admin_gift');
			}else{
				$id = intval($_GET['id']);
				if($id < 1) return false;
				//删除友情链接
				$result = $this->db->delete(array('id'=>$id));
				//更新附件状态
		
				if($result){
					showmessage(L('operation_success'),'?m=gift&c=admin_gift');
				}else {
					showmessage(L("operation_failure"),'?m=gift&c=admin_gift');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	
	

 
	
}
?>