<?php
defined('IN_OLCMS') or exit('No permission resources.');
if (!module_exists('comment')) showmessage(L('module_not_exists'));
class comment_api {
	private $db;
	function __construct() {
		$this->db = pc_base::load_model('content_model');
	}
	
	/**
	 * 获取评论信息
	 * @param $module      模型
	 * @param $contentid   文章ID
	 */
	function get_info($module, $contentid) {
		list($module, $catid) = explode('_', $module);
		if (empty($contentid) || empty($catid)) {
			return false;
		}
		$this->db->set_catid($catid);
		$r = $this->db->get_one(array('catid'=>$catid, 'id'=>$contentid), '`title`');
		
		$category = getcache('category_content', 'commons');
		$model = getcache('model', 'commons');
		
		$cat = $category[$catid];
		$data_info = array();
		if ($cat['type']==0 && $model[$cat['modelid']]['type']==0) {
			$this->db->table_name = $this->db->db_tablepre.$model[$cat['modelid']]['tablename'].'_data';
			$data_info = $this->db->get_one(array('id'=>$contentid));
		}elseif($cat['type']==3){
			$data_info = $this->db->get_one(array('id'=>$contentid));
		}
		
		if ($r) {
			return array('title'=>$r['title'], 'url'=>go($catid, $contentid, 0), 'allow_comment'=>(isset($data_info['allow_comment']) ? $data_info['allow_comment'] : 1));
		} else {
			return false;
		}
	}
}