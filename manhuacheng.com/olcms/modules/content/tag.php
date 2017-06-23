<?php
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
//模型缓存路径
define ( 'CACHE_MODEL_PATH', OLCMS_PATH . 'caches' . DIRECTORY_SEPARATOR . 'caches_model' . DIRECTORY_SEPARATOR . 'caches_data' . DIRECTORY_SEPARATOR );

pc_base::load_app_func ( 'util', 'content' );
class tag {
	function __construct() {
		$this->db = pc_base::load_model ( 'content_model' );
		$this->categorys = getcache ( 'category_content', 'commons' );
	}
	/**
	 * 按照模型搜索
	 */
	public function init() {
		$modelid = intval ( $_GET ['modelid'] );
		if ($modelid < 1) {
			if (! isset ( $_GET ['catid'] ) && ! isset ( $this->categorys [$_GET ['catid']] ))
				showmessage ( L ( 'missing_part_parameters' ) );
			$catid = intval ( $_GET ['catid'] );
			if (isset ( $_GET ['info'] ['catid'] ) && $_GET ['info'] ['catid']) {
				$catid = intval ( $_GET ['info'] ['catid'] );
			} else {
				$_GET ['info'] ['catid'] = 0;
			}
			$modelid = $this->categorys [$catid] ['modelid'];
			$modelid = intval ( $modelid );
		}
		if (! $modelid)
			showmessage ( L ( 'illegal_parameters' ) );
		if (isset ( $_GET ['tag'] ) && trim ( $_GET ['tag'] ) != '') {
			$tag = safe_replace ( strip_tags ( $_GET ['tag'] ) );
		} else {
			showmessage ( L ( 'illegal_operation' ) );
		}
		$this->db->set_model ( $modelid );
		$tagindex_db = pc_base::load_model ( 'tagindex_model' );
		$tags_db = pc_base::load_model ( 'tags_model' );
		$page = $_GET ['page'];
		$tagrs = $tags_db->get_one ( array ('modelid' => $modelid, 'tag' => $tag ), 'id' );
		$rs = $tagindex_db->listinfo ( "tagid={$tagrs['id']}", 'aid DESC', $page, 20 );
		$datas = array ();
		if ($rs) {
			foreach ( $rs as $v ) {
				$ids [$v ['aid']] = $v ['aid'];
			}
			$datas = $this->db->listinfo ( '`id` in (' . implode ( ',', $ids ) . ')', 'id DESC' );
			unset ( $rs, $ids );
		}
		$total = $tagindex_db->number;
		if ($total > 0) {
			$pages = $tagindex_db->pages;
		}
		unset ( $tagindex_db, $tagindex_db );
		$SEO = seo ( 1, $catid, $tag );
		include template ( 'content', 'tag' );
	}
}
?>