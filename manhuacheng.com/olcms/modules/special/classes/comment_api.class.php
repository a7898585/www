<?php
/**
 * 获取专题评论类
 */

defined('IN_OLCMS') or exit('No permission resources.');
if (!module_exists('comment')) showmessage(L('module_not_exists'));
class comment_api {
	private $db;
	function __construct() {
		$this->db = pc_base::load_model('special_model');
	}
	
	/**
	 * 获取评论信息
	 * @param $module      模型
	 * @param $contentid   文章ID
	 */
	function get_info($module, $contentid) {
		if ($module=='special') {
			$r = $this->db->get_one(array('id'=>$contentid), 'title, url');
			return array('title'=>$r['title'], 'url'=>$r['url']);
		} elseif ($module=='special_content') {
			$this->db = pc_base::load_model('special_content_model');
			$r = $this->db->get_one(array('id'=>$contentid), 'title, url');
			if ($r) {
				return array('title'=>$r['title'], 'url'=>$r['url']);
			} else {
				return false;
			}
		}
	}
}