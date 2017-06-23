<?php
defined('IN_OLCMS') or exit('No permission resources.');
class index {
	public function __construct(){
		$this->db = pc_base::load_model('tags_model');
		$this->db_content = pc_base::load_model('tags_content_model');
	}
	public function init(){
		$tag = $_GET['tag'];
		$models = getcache('model', 'commons');
		$sitelist = getcache('sitelist', 'commons');
		$i=0;
		$siteid = intval($_GET['siteid']);
		$modelid = intval($_GET['modelid']);
		$orderby = intval($_GET['orderby']);
		foreach($models as $model_v){
			$model_arr .= 'model_arr['.$i++.'] = new Array("'.$model_v['modelid'].'","'.$model_v['name'].'","'.$model_v['siteid'].'");'."\n";
		}
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;


		if($tag){
			if($this->db->get_one(array('tag'=>$tag))){
				$sql_arr = array('tag'=>$tag);
				if($siteid){
					$sql_arr['siteid'] = $siteid;

				}
				if($modelid){
					$sql_arr['modelid'] = $modelid;
				}
				if($orderby){
					$sql_ord = 'updatetime desc';
				}else{
					$sql_ord = 'updatetime asc';
				}
				$tagdata = $this->db_content->listinfo($sql_arr,$sql_ord, $page, 40);
				$pages = $this->db_content->pages;
				$total = $this->db_content->number;
			}else{
				showmessage('标签不存在！');
			}
			$CATEGORYS = getcache('category_content_'.$siteid,'commons');
			include template('tags', 'tag');
		}else{
			$tagdata = $this->db->listinfo('','tagid desc', $page, 100);
			$pages = $this->db->pages;
			$total = $this->db->number;
			include template('tags', 'index');
			
		}
		
	}
}