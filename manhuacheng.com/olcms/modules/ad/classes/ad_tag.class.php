<?php 
/**
 * 
 * 公告类
 *
 */

defined('IN_OLCMS') or exit('No permission resources.');

class ad_tag {
	private $db;
	
	public function __construct() {
		$this->db = pc_base::load_model('ad_model');
	}
	
	/**
	 * 广告列表方法
	 * @param array $data 传递过来的参数
	 * @param return array 数据库中取出的数据数组
	 */
	public function lists($data) {
		$where = '1';
		$where .= ' AND `passed`=\'1\' AND (`endtime` > \''.date('Y-m-d').'\' or `endtime`=\'0000-00-00\')';
		return $this->db->select($where, '*', $data['limit'], 'aid DESC');
	}
	
	public function count() {
		
	}
	
	/**
	 * pc标签初始方法
	 */
	public function pc_tag() {
		$result = getcache('special', 'commons');
		if(is_array($result)) {
			$specials = array(L('please_select', '', 'ad'));
			foreach($result as $r) {
				$specials[$r['id']] = $r['title'];
			}
		}
	}
}
?>