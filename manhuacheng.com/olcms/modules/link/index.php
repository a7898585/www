<?php
defined('IN_OLCMS') or exit('No permission resources.');
class index {
	function __construct() {
		pc_base::load_app_func('global');
	}
	
	public function init() {	
 		$setting = getcache('link', 'commons');
		//SEO设置 
		$SEO = seo('', L('link'), '', '');
		include template('link', 'index');
	}
	
	 /**
	 *	友情链接列表页
	 */
	public function list_type() {
  		$type_id = trim(urldecode($_GET['type_id']));
		$type_id = intval($type_id);
  		if($type_id==""){
 			$type_id ='0';
 		}
  		$setting = getcache('link', 'commons');
		$SEO = seo('', L('link'), '', '');
  		include template('link', 'list_type');
	} 
 	
	 /**
	 *	申请友情链接 
	 */
	public function register() { 
 		if(isset($_POST['dosubmit'])){
 			if($_POST['name']==""){
 				showmessage(L('sitename_noempty'),"?m=link&c=index&a=register");
 			}
 			if($_POST['url']==""){
 				showmessage(L('siteurl_not_empty'),"?m=link&c=index&a=register");
 			}
 			if(!in_array($_POST['linktype'],array('0','1'))){
 				$_POST['linktype'] = '0';
 			}
 			$link_db = pc_base::load_model(link_model);
 			$_POST['logo'] =new_html_special_chars($_POST['logo']);
 			
 			if($_POST['linktype']=='0'){
 				$sql = array('typeid'=>$_POST['typeid'],'linktype'=>$_POST['linktype'],'name'=>$_POST['name'],'url'=>$_POST['url']);
 			}else{
 				$sql = array('typeid'=>$_POST['typeid'],'linktype'=>$_POST['linktype'],'name'=>$_POST['name'],'url'=>$_POST['url'],'logo'=>$_POST['logo']);
 			}
 			$link_db->insert($sql);
 			showmessage(L('add_success'), "?m=link&c=index");
 		}else {
 			$setting = getcache('link', 'commons');
 			if($setting['is_post']=='0'){
 				showmessage(L('suspend_application'), HTTP_REFERER);
 			}
 			$this->type = pc_base::load_model('type_model');
 			$types = $this->type->get_types();
 			pc_base::load_sys_class('form', '', 0);
 			$setting = getcache('link', 'commons');
			$SEO = seo('', L('application_links'), '', '');
   			include template('link', 'register');
 		}
	} 
	
}
?>
