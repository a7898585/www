<?php
defined('IN_OLCMS') or exit('No permission resources.');
if(!defined('CACHE_MODEL_PATH')) define('CACHE_MODEL_PATH',OLCMS_PATH.'caches'.DIRECTORY_SEPARATOR.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_sys_class('model', '', 0);
class member_model extends model {
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'member';
		parent::__construct();
	}

	/**
	 * 重置模型操作表
	 * @param string $modelid 模型id
	 */
	public function set_model($modelid = '') {
		if($modelid) {
			$model = getcache('member_model', 'commons');
			$this->table_name = $this->db_tablepre.$model[$modelid]['tablename'];
		} else {
			$this->table_name = $this->db_tablepre.'member';
		}
	}
	
	/**
	 * 删除会员操作
	 * @param string 会员userid数组
	 * @param 		在没有开启ucenter的情况下,是否删除本地头像
	 */
	public function del_member($uidsarr,$avator=0) {
		$uidsarr = (array)$uidsarr;
		if(!$uidsarr) {
			return false;
		}
		$uids = implode(',',$uidsarr);
		$arr = $this->select("userid IN ($uids)",'vip,userid,modelid');
		foreach((array)$arr as $k=>$member) {
			if($member['vip']==1) continue;
			$u_modelid[$member['modelid']][] = $member['userid'];
		}
		foreach ($u_modelid as $k=>$v){
		$uidstr = implode(',',$v);
		if($uidstr) {
			$this->delete("userid IN($uidstr)");
			$this->set_model($k);
			$this->delete("userid IN($uidstr)");
			if($avator)	$this->delete_useravatar($uidsarr);
			return true;
		} else {
			return false;
		}
		}
	}

	function delete_useravatar($uidsarr) {
		$uidsarr = (array)$uidsarr;
		if(pc_base::load_config ( 'system', 'uc' )==0){
		foreach((array)$uidsarr as $uid) {
			file_exists($avatar_file = get_memberavatar($uid,1,'30')) && unlink($avatar_file);
			file_exists($avatar_file = get_memberavatar($uid,1,'45')) && unlink($avatar_file);
			file_exists($avatar_file = get_memberavatar($uid,1,'90')) && unlink($avatar_file);
			file_exists($avatar_file = get_memberavatar($uid,1,'180')) && unlink($avatar_file);
			}		
		}
	}

}
?>