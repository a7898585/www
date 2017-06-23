<?php 
/**
 *  member pc标签
 *
 * @copyright			(C) 2005-2010 OLCMS
 * @license				
 * @lastmodify			2010-8-3
 */

defined('IN_OLCMS') or exit('No permission resources.');

class member_tag {
	private $db, $favorite_db;
	
	public function __construct() {
		$this->db = pc_base::load_model('member_model');
		$this->favorite_db = pc_base::load_model('favorite_model');
	}
	
	public function favoritelist($data) {
		$userid = intval($data['userid']);
		$limit = $data['limit'];
		$order = $data['order'];
		$favoritelist = $this->favorite_db->select(array('userid'=>$userid), "*", $limit, $order);
		return $favoritelist;
	}

	public function count($data) {
		$userid = intval($data['userid']);
		return $this->favorite_db->count(array('userid'=>$userid));
	}
	
	public function memberdetail($data) {
		$membermodelid = $data['membermodelid'];
		$userid = $data['userid'];
		$this->db->set_model($membermodelid);
		return $this->db->get_one(array('userid'=>$userid));
	}
	
	public function memberdetail_list($data) {
		$modelid = intval($data['modelid']);
		$limit = $data['limit'];
		$order = $data['order'];
		$this->db->set_model($modelid);
		return $this->db->select('','*',"$limit","$order");
	}
	
	public function pc_tag() {
		return array(
			'action'=>array('favoritelist'=>L('favorite_list', '', 'member')),
			'favoritelist'=>array(
				'userid'=>array('name'=>L('uid'),'htmltype'=>'input'),
			),
		);
	}
}
?>