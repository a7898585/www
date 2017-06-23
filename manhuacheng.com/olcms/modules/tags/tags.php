<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class tags extends admin {
	private $db;
	public function __construct() {
		$this->db = pc_base::load_model('tags_model');
		parent::__construct();
	}
	public function init() {
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$data = $this->db->listinfo('','tagid desc', $page, 20);
		$pages = $this->db->pages;
		include $this->admin_tpl('tags_list');
	}
	public function create(){
		if(isset($_POST['dosubmit'])){
			//$siteid = get_siteid();
			$this->db_content = pc_base::load_model('tags_content_model');
			$modelid = intval($_POST['modelid']);
			//$siteid = intval($_POST['siteid']);
			//if($siteid){
			//	$this->db_content->delete(array('siteid'=>$siteid));
			//}else{
				$this->db_content->query("TRUNCATE TABLE `tt_tags_content`");
				$this->db->query("TRUNCATE TABLE `tt_tags`");
			//}
			$models = getcache('model', 'commons');
			foreach($models as $models_v){
				//if($siteid && $models_v['siteid']!=$siteid)continue;
				if($modelid && $models_v['modelid']!=$modelid)continue;
				$keywords = $this->db->query("SELECT `keywords`,`url`,`id`,`title`,`catid`,`updatetime` FROM `tt_".$models_v[tablename]."` where `keywords` !=''");
				$keywords = $this->db->fetch_array();
				set_time_limit (0);
				foreach($keywords as $keyword){
					$key = strpos($keyword['keywords'], ',') !==false ? explode(',', $keyword['keywords']) : explode(' ', $keyword['keywords']);
					foreach($key as $key_v){
						$key_v = addslashes($key_v);
						if($this->db->get_one("`tag`='$key_v'", 'tagid')){
							$this->db->query("UPDATE `tt_tags` SET `usetimes`=usetimes+1 WHERE tag='$key_v'");
						}else{
							$this->db->query("INSERT INTO `tt_tags`(`tag`,`usetimes`,`lastusetime`,`lasthittime`)VALUES('$key_v',1,".SYS_TIME.",".SYS_TIME.")");
						}
						$sql .= ",('$key_v','$keyword[url]','$keyword[title]',1,$models_v[modelid],$keyword[id],$keyword[catid],$keyword[updatetime])\n";
					}
				}
			}
			if(!$sql)showmessage('没有需要导入的数据', '?m=tags&c=tags&a=init');
			$sql = "INSERT INTO `tt_tags_content` (`tag`,`url`,`title`,`siteid`,`modelid`,`contentid`,`catid`,`updatetime`) VALUES ".substr($sql, 1);
			$this->db->query($sql);
			showmessage('重建成功！', '?m=tags&c=tags&a=init');
		
		}else{
			include $this->admin_tpl('tags_create');
		}
	}
	public function edit(){
		if(isset($_POST['dosubmit'])){
			$_POST['info']['lastusetime'] = strtotime($_POST['info']['lastusetime']);
			$_POST['info']['lasthittime'] = strtotime($_POST['info']['lasthittime']);
			$this->db->update($_POST['info'], array('tagid'=>$_GET['tagid']));
			showmessage('更新成功！');
		
		}else{
			$data = $this->db->get_one("`tagid` = '$_GET[tagid]'");
			if(!$data)showmessage('信息不存在或者已被删除！！', '?m=tags&c=tags&a=init');
			pc_base::load_sys_class('form','',0);
			include $this->admin_tpl('tags_edit');
		}
	}
	public function delete(){
		if($_GET['tagid']){
			if(is_array($_GET['tagid'])){
				$_GET['tagid'] = implode(',', $_GET['tagid']);
				$this->db->query("DELETE FROM `tt_tags` WHERE `tagid` in ($_GET[tagid])");
			}else{
				$this->db->query("DELETE FROM `tt_tags` WHERE `tagid` in ($_GET[tagid])");
			}
			showmessage('操作成功', '?m=tags&c=tags&a=init');
		}else{
			showmessage('参数不正确', '?m=tags&c=tags&a=init');
		}
	}
	public function listorder(){
		$tagid = $_GET['tagid'];
		if($tagid){
			foreach($tagid as $n=>$id){
				if(!$id)continue;
				$this->db->update('`listorder`='.intval($_GET['listorder'][$n]), array('tagid'=>$id));

			}
			showmessage('更新成功！', '?m=tags&c=tags&a=init');
		}else{
			showmessage('参数不正确', '?m=tags&c=tags&a=init');
		}
	}
}
?>