<?php
defined('IN_OLCMS') or exit('No permission resources.');
class vote_tag {
	//数据库连接
	public function __construct() {
		$this->subject_db = pc_base::load_model('vote_subject_model');
		$this->option_db = pc_base::load_model('vote_option_model');
	}
	
	/**
	 * 显示
	 * @param  $data 
	 */
	public function show($data) {
		$subjectid = $data['subjectid'];//投票ID
		if($subjectid){
			if(is_int($subjectid)) return false;
			$sql = array('subjectid'=>$subjectid);
		}else {
			$sql = '';
		}
		return $this->subject_db->select($sql, '*', $data['limit']);
	}
	
	/**
	 * 其它投票
	 * @param  $data 
	 */
	public function other_vote($data) {
		return $this->subject_db->select('', '*', $data['limit'], $data['order']);
	} 
	
	/**
	 * 投票热度排行 
	 * @param  $data 传入的数组参数
	 */
	public function hits($data) {
		$enabled = $data['enabled']?$data['enabled'] : 1;//状态:是否启用
		switch ($enabled) {
			case all://不限
				$sql = ''; 
				break; 
			default://默认按选择项
				$sql = array('enabled'=>$enabled); 
		}
		return $this->subject_db->select($sql, '*', $data['limit'], 'votenumber '.$data['order']);
	}
		
	/**
	 * 
	 * 投票列表
	 * @param $data 数组参数
	 */
	public function lists($data) {
		$enabled = $data['enabled']?$data['enabled'] : 1;//状态:是否启用
		$order = $data['order']?$data['order'] : 'subjectid desc';//状态:是否启用
		switch ($enabled) {
			case all://不限
				$sql = ''; 
				break; 
			default://默认按选择项
				$sql = array('enabled'=>$enabled); 
		}
 		return $this->subject_db->select($sql, '*', $data['limit'], $order);
	}	

	/**
	 * 投票概况
	 */
	public function get_vote($data) {
		$subjectid = intval($data['subjectid']);
		if (empty($subjectid)) return false;
  		return  $this->subject_db->get_one(array('subjectid'=>$subjectid));
	}
	
	/**
	 * 计数
	 */
	public function count($data) {
		if(isset($data['where'])) {
			$sql = $data['where'];
		} else {
				$enabled = $data['enabled']?$data['enabled'] : 1;//状态:是否启用
				switch ($enabled) {
					case all://不限
						$sql = ''; 
						break; 
					default://默认按选择项
						$sql = array('enabled'=>$enabled); 
				}
		 		return $this->subject_db->count($sql); 
		}		 		
	}
	 
	/**
	 * pc 标签调用
	 */
	public function pc_tag() {
		return array(
			'action'=>array('lists'=>L('list','', 'comment'),'get_vote'=>L('vote_overview','','vote')),
			'lists'=>array(
 						'enabled'=>array('name'=>L('vote_status','','vote'), 'htmltype'=>'select', 'data'=>array('all'=>L('vote_Lockets','','vote'),'1'=>L('vote_use','','vote'), '0'=>L('vote_lock','','vote'))),
						'order'=>array('name'=>L('sort', '', 'comment'), 'htmltype'=>'select','data'=>array('subjectid desc'=>L('subjectid_desc', '', 'vote'), 'subjectid asc'=>L('subjectid_asc', '', 'vote'))),
					),
		    'get_vote'=>array(
						'subjectid'=>array('name'=>L('vote_voteid','','vote'),'htmltype'=>'input', 'validator'=>array('min'=>1)), 
					),
		);
	}
}