<?php
class search_tag {
	private $db;
	public function __construct() {
		$this->content_db = pc_base::load_model('content_model');
	}
	public function sphinx($get) {
		$get = new_stripslashes ( $get );
		$setting = getcache ( 'search','search' );
		//$q 过滤
		$arr = array ('m', 'c', 'a', 'q', 'catid', 'modelid', 'order', 'leaf', 'source', 'limit' );
		foreach ( $get as $k => $v ) {
			if (in_array ( $k, $arr ) || empty ( $v ))
				continue;
			$condition [$k] = trim ( $v );
		}
		$page = max( intval ( $get ['leaf'] ) , 1);
		$pagesize = isset ( $get ['limit'] ) ? intval ( $get ['limit'] ) : 10;
		if($get ['catid']) $catid = intval($get ['catid']);
		if($get ['modelid']) $modelid = intval($get ['modelid']);
		if($get ['source']) $source = trim($get ['source']);
		$search_q = "";
		if(!$modelid && !$source) showmessage(L('error'));
		if (! empty ( $get ['q'] )) {
			$q = safe_replace ( trim ( $get ['q'] ) );
			$q = htmlspecialchars ( strip_tags ( $q ) );
			$q = str_replace ( '%', '', $q ); //过滤'%'，用户全文搜索
			$search_q = "@title $q"; //搜索原内容
		}
		
		if (! empty ( $get ['order'] )) {
			$order = safe_replace ( trim ( $get ['order'] ) );
			$order = htmlspecialchars ( strip_tags ( $order ) );
			$order = str_replace ( '%', '', $order ); //过滤'%'，用户全文搜索
			$order = '@'.$order . ' desc';
		}

		$search_types = getcache ( 'search_types','search' );
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
			$data['words'] = isset ( $res['words'] ) ? $res['words'] : '';
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

			$sphinx_data = $this->content_db->listinfo ( $where, '', '', $pagesize, 'id' );

			$pages = pages ( $totalnums, $page, $pagesize );
		}
		foreach ( $res ['matches'] as $v ) {
			$datas [] = $sphinx_data [$v ['id']];
		}
		
		unset ( $sphinx_data,$res );
		$pages = isset ( $pages ) ? $pages : '';
		$totalnums = isset ( $totalnums ) ? $totalnums : 0;
		$data['data'] = isset ( $datas ) ? $datas : '';
		$data['page'] = isset ( $pages ) ? $pages : '';
		return $data;
	}
	
}
?>