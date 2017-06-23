<?php
defined('IN_OLCMS') or exit('No permission resources.');
pc_base::load_sys_class('form','',0);
pc_base::load_sys_class('format','',0);
class index {
	function __construct() {
		$this->db = pc_base::load_model('search_model');
		$this->content_db = pc_base::load_model('content_model');
	}
	
	/**
	 * 关键词搜索
	 */
	public function init() {
		//搜索配置
		$setting = getcache('search');
                $search['search'] = $_GET['q'];
                
                $search['times'] = date("Y-m-d H:i:s" , time());
                $search['from'] = 1;
                $search['type'] = 9;
                
		$search_model = getcache('search_model');
		$type_module = getcache('type_module');

		if(isset($_GET['q'])) {
			if(trim($_GET['q'])=='') {
				showmessage(L('entera_content'));
				exit;
			}
			$typeid = empty($_GET['typeid']) ? 48 : intval($_GET['typeid']);
			$time = 'all';//empty($_GET['time']) ? 'all' : trim($_GET['time']);
			$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
			$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 5;
			$q = safe_replace(trim($_GET['q']));
			$q = htmlspecialchars(strip_tags($q));
			$q = str_replace('%', '', $q);	//过滤'%'，用户全文搜索
			$search_q = $q;	//搜索原内容
			$search_type = !empty($_GET['search_type']) ? intval($_GET['search_type']) : 1;

			//按时间搜索
			if($time == 'day') {
				$search_time = SYS_TIME - 86400;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif($time == 'week') {
				$search_time = SYS_TIME - 604800;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif($time == 'month') {
				$search_time = SYS_TIME - 2592000;
				$sql_time = ' AND adddate > '.$search_time;
			} elseif($time == 'year') {
				$search_time = SYS_TIME - 31536000;
				$sql_time = ' AND adddate > '.$search_time;
			} else {
				$search_time = 0;
				$sql_time = '';
			}
//			if($page==1 && !$setting['sphinxenable']) {
//				//精确搜索
//				$commend = $this->db->get_one("`typeid` = '$typeid' $sql_time AND `data` like '%$q%'");
//			} else {
//				$commend = '';
//			}
			//如果开启sphinx
			if($setting['sphinxenable']) {
				$sphinx = pc_base::load_app_class('search_interface', '', 0);
				$sphinx = new search_interface();
				
				$offset = $pagesize*($page-1);
				$res = $sphinx->search($q, array($typeid), array($search_time, SYS_TIME), $offset, $pagesize, '@weight desc');
				$totalnums = $res['total'];
				//如果结果不为空
				if(!empty($res['matches'])) {
					$result = $res['matches'];
				}
			} else {
				//pc_base::load_sys_class('segment', '', 0);
				//$segment = new segment();
				//分词结果
				//$segment_q = $segment->get_keyword($segment->split_result($q));
				//如果分词结果为空
				//if(!empty($segment_q)) {
				//	$sql = "`typeid` = '$typeid' $sql_time AND MATCH (`data`) AGAINST ('$segment_q' IN BOOLEAN MODE)";
				//} else {
				//	$sql = "`typeid` = '$typeid' $sql_time AND `data` like '%$q%'";
				//}

				if($typeid == 18){
					//$this->db->table_name = $this->db->db_tablepre . 'Cartoon';
					if($search_type == 2){
						$this->db->table_name = $this->db->db_tablepre . 'Cartoon_data';
						$sql = "Author LIKE '%".$q."%'";	
						$result = $this->db->listinfo($sql, 'id desc', $page, $pagesize);
					}else{
						
					//	$this->Cartoon_db = pc_base::load_model('Cartoon_model');
						$this->db->table_name = $this->db->db_tablepre . 'Cartoon';
						$sql = "title LIKE '%".$q."%'  AND status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";	
						$result = $this->db->listinfo($sql, 'id desc', $page, $pagesize);
					}
				}elseif($typeid == 21 && $search_type == 2){
					$this->db->table_name = $this->db->db_tablepre . 'dongman_data';
					$sql = "author LIKE '%".$q."%'";	
					$result = $this->db->listinfo($sql, 'id desc', $page, $pagesize);
				}else{
					$sql = "`typeid` = '$typeid' $sql_time AND `data` like '%$q%'";
					$result = $this->db->listinfo($sql, 'adddate DESC', $page, $pagesize);
				}
			}
			//echo $page;
			//print_r($result);
			//关键词处理
			if(empty($_GET['iframe'])){
				//如果关键词长度在8-16之间，保存关键词作为relation search
				$this->keyword_db = pc_base::load_model('search_keyword_model');
				if(strlen($q) < 17 && strlen($q) > 5) {
					$res = $this->keyword_db->get_one(array('keyword'=>$q,'search_type'=>$search_type));
					if($res) {
						//关键词搜索数+1
						$this->keyword_db->update(array('searchnums'=>'+=1'), array('keyword'=>$q,'search_type'=>$search_type));
					} else {
						//关键词转换为拼音
						pc_base::load_sys_func('iconv');
						$pinyin = gbk_to_pinyin($q);
						if(is_array($pinyin)) {
							$pinyin = implode('', $pinyin);
						}
						$this->keyword_db->insert(array('keyword'=>$q, 'searchnums'=>1, 'data'=>$segment_q, 'pinyin'=>$pinyin,'search_type'=>$search_type));
					}
				}
				

				$keywords = $this->keyword_db->select('search_type = 1', 'keyword','30','searchnums desc');
				$manhua = array();
				$dongman = array();
				$limit = 1;
				
				//热搜漫画
				foreach($keywords as $v){
					if(count($manhua) >= 10){
						break;
					}else{
						$this->db->table_name = $this->db->db_tablepre . 'Cartoon';
						$sql = "title like '%".$v['keyword']."%'  AND status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";	
						$manhua_r = $this->db->select($sql, 'id,title',$limit, 'views desc');
						foreach($manhua_r as $val){
							if(count($manhua) < 10 && empty($manhua[$val['id']])){
								$manhua[$val['id']] = $val;
							}
						}
					}
				}

				//热搜动漫
				foreach($keywords as $v){
					if(count($dongman) >= 10){
						break;
					}else{
						$this->db->table_name = $this->db->db_tablepre . 'dongman';
						$sql = "title like '%".$v['keyword']."%'  AND status = '99'";	
						$dongman_r = $this->db->select($sql, 'id,title',$limit, 'views desc');
						foreach($dongman_r as $val){
							if(count($dongman) < 10 && empty($dongman[$val['id']])){
								$dongman[$val['id']] = $val;
							}
						}
					}
				}
			}
                        

			//如果开启相关搜索功能
			if($setting['relationenble']) {
				//相关搜索
				if(!empty($segment_q)) {
					$relation_q = str_replace(' ', '%', $segment_q);
				} else {
					$relation_q = $q;
				}
				$relation = $this->keyword_db->select("MATCH (`data`) AGAINST ('%$relation_q%' IN BOOLEAN MODE)", '*', $pagesize, 'adddate DESC');
			}
				
			//如果结果不为空
			  if(!empty($result) || !empty($commend['id'])) {
				//开启sphinx后文章id取法不同
				if($setting['sphinxenable']) {
					foreach($result as $_v) {
						$sids[] = $_v['attrs']['id'];
					}
				} else {
					foreach($result as $_v) {
						$sids[] = $_v['id'];
					}
				}
				if(!empty($commend['id'])) {
					$sids[] = $commend['id'];
				}
			//	$sids = array_unique($sids);
				$where = to_sqls($sids, '', 'id');
				//获取模型id
				$model_type_cache = getcache('type_model','search');
				$model_type_cache = array_flip($model_type_cache);
				$modelid = $model_type_cache[$typeid];

				//是否读取其他模块接口
				if($modelid) {
					$this->content_db->set_model($modelid);
	
					if($setting['sphinxenable']) {
						$data = $this->content_db->listinfo($where, 'id DESC', 1, $pagesize);
						$pages = pages($totalnums, $page, $pagesize);
					} else {
						$data = $this->content_db->select($where, '*','','id desc');
						$pages = $this->db->pages;
						$totalnums = $this->db->number;
					}
					//如果分词结果为空
					if(!empty($segment_q)) {
						$replace = explode(' ', $segment_q);
						foreach($replace as $replace_arr_v) {
							$replace_arr[] =  '<font color=red>'.$replace_arr_v.'</font>';
						}
						foreach($data as $_k=>$_v) {
							$data[$_k]['title'] = str_replace($replace, $replace_arr, $_v['title']);
							$data[$_k]['description'] = str_replace($replace, $replace_arr, $_v['description']);
						}
					} else {
						foreach($data as $_k=>$_v) {
							$data[$_k]['title'] = str_replace($q, '<font color=red>'.$q.'</font>', $_v['title']);
							$data[$_k]['description'] = str_replace($q, '<font color=red>'.$q.'</font>', $_v['description']);
						}
					}
				} else {
					//读取专辑搜索接口
					$special_api = pc_base::load_app_class('search_api', 'special');

					$data = $special_api->get_search_data($sids);
				}
			}
			$execute_time = execute_time();
			$pages = isset($pages) ? $pages : '';
			$totalnums = isset($totalnums) ? $totalnums : 0;
			$data = isset($data) ? $data : '';
                        
			if(!empty($_GET['iframe'])){
				include	template('search','iframe_list');
              
			}else{
				include	template('search','list');
			}
		} else {
			showmessage(L('entera_content'));
			exit;
		}
                if($data){
                    foreach ((array)$data as $key => $value) {
                        $s[] = $value['id'];
                        $search['sonid'] = implode('|', $s);
                        
                    }
                }
                $db =  pc_base::load_model('tj_model');
                if($_GET['typeid'] == 18){
                    $datas = $db->insert($search);
                }
	}

	/*
	public function public_get_suggest_keyword() {
		$url = $_GET['url'].'&q='.$_GET['q'];
		
		$res = @file_get_contents($url);
		if(CHARSET != 'utf-8') {
			$res = iconv('utf-8', CHARSET, $res);
		}
		echo $res;
	}*/
	
	/**
	 * 提示搜索接口
	 * TODO 暂时未启用，用的是google的接口
	 */
	public function public_suggest_search() {
		//关键词转换为拼音
		pc_base::load_sys_func('iconv');
		$pinyin = gbk_to_pinyin($q);
		if(is_array($pinyin)) {
			$pinyin = implode('', $pinyin);
		}
		$this->keyword_db = pc_base::load_model('search_keyword_model');
		$suggest = $this->keyword_db->select("pinyin like '$pinyin%'", '*', 10, 'searchnums DESC');
		
		foreach($suggest as $v) {
			echo $v['keyword']."\n";
		}
	}
	
	/**
	 * sphinx关键词多级筛选搜索
	 */
	/**
	 * sphinx关键词多级筛选搜索
	 */
	public function sphinx() {
		$get = new_stripslashes ( $_GET );
		$setting = getcache ( 'search' );
		//$q 过滤
		$arr = array ('m', 'c', 'a', 'q', 'catid', 'modelid', 'order', 'page', 'source', 'limit' );
		foreach ( $get as $k => $v ) {
			if (in_array ( $k, $arr ) || empty ( $v ))
				continue;
			$condition [$k] = trim ( $v );
		}
		
		$page = isset ( $get ['page'] ) ? intval ( $get ['page'] ) : 1;
		$pagesize = isset ( $get ['limit'] ) ? intval ( $_GET ['limit'] ) : 10;
		if($get ['catid']) $catid = intval($get ['catid']);
		if($get ['modelid']) $modelid = intval($get ['modelid']);
		if($get ['source']) $source = trim($get ['source']);
		$search_q = "";
		if(!$modelid && !$source) showmessage(L('error'));
		if (! empty ( $get ['q'] )) {
			$q = safe_replace ( trim ( $get ['q'] ) );
			$q = htmlspecialchars ( strip_tags ( $q ) );
			$q = str_replace ( '%', '', $q ); //过滤'%'，用户全文搜索
			$search_q = "@title $q "; //搜索原内容
		}else{
			showmessage(L('entera_content'));
		}
		
		if (! empty ( $get ['order'] )) {
			$order = safe_replace ( trim ( $get ['order'] ) );
			$order = htmlspecialchars ( strip_tags ( $order ) );
			$order = str_replace ( '%', '', $order ); //过滤'%'，用户全文搜索
			$order = $order . ' desc';
		}
		$search_types = getcache ( 'search_types' );
		if($modelid){
				$this->content_db->set_model ( $modelid );
				foreach ($search_types as $k){
					if($modelid==$k['modelid']){
						$source = $k['description'];
						break;
					}
				}
			}else{				
				foreach ($search_types as $k){
					if($source==$k['description']){
						$this->content_db->set_model ( $k['modelid'] );
						break;
					}
				}
			}
		if (! empty ( $source )) {
			$source = safe_replace ( trim (  $source ) );
			$source = htmlspecialchars ( strip_tags ( $source ) );
			$source = str_replace ( '%', '', $source ); //过滤'%'，用户全文搜索
		}
		//如果开启sphinx
		if ($setting ['sphinxenable']) {
			$sphinx = pc_base::load_app_class ( 'search_interface', 'search', 0 );
			$sphinx = new search_interface ();
			$offset = $pagesize * ($page - 1);
			$res = $sphinx->sphinx_search ( $search_q, $condition, '', $offset, $pagesize, $order, $source );
			$totalnums = $res ['total'];
			if (! empty ( $res ['matches'] )) {
				$result = $res ['matches'];
			}
		}
		if (! empty ( $result )) {
			if ($setting ['sphinxenable']) {
				foreach ( $result as $_v ) {
					$sids [] = $_v ['id'];
				}
			}
			$sids = array_unique ( $sids );
			$where = to_sqls ( $sids, '', 'id' );
			$sphinx_data = $this->content_db->listinfo ( $where, 'id DESC', 1, $pagesize, 'id' );
			$pages = pages ( $totalnums, $page, $pagesize );
		}
		foreach ( $res ['matches'] as $v ) {
			$data [] = $sphinx_data [$v ['id']];
		}
		unset ( $sphinx_data );
		$pages = isset ( $pages ) ? $pages : '';
		$totalnums = isset ( $totalnums ) ? $totalnums : 0;
		$data = isset ( $data ) ? $data : '';
		include template ( 'search', 'sphinx_lists' );
	}
}
?>