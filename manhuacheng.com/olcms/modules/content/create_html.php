<?php
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );

pc_base::load_app_class ( 'admin', 'admin', 0 );
pc_base::load_sys_class ( 'form', '', 0 );

class create_html extends admin {
	public function __construct() {
		parent::__construct ();
		$this->db = pc_base::load_model ( 'content_model' );
		$this->categorys = getcache ( 'category_content', 'commons' );
		foreach ( $_GET as $k => $v ) {
			$_POST [$k] = $v;
		}
	}
	public function batch_update() {
		
		include $this->admin_tpl ( 'batch_update' );
	}
	
	public function update_urls() {
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			$this->url = pc_base::load_app_class ( 'url' );
			$modelid = intval ( $_POST ['modelid'] );
			if ($modelid) {
				$this->db->set_model ( $modelid );
				$table_name = $this->db->table_name;

				if($type == 'lastinput') {
					$offset = 0;
				} else {
					$page = max(intval($page), 1);
					$offset = $pagesize*($page-1);
				}
				$where = '';
				$order = 'ASC';
				
				if(!isset($first) && is_array($catids) && $catids[0] > 0)  {
					setcache('url_show_'.$_SESSION['userid'], $catids,'content');
					$catids = implode(',',$catids);
					$where .= "catid IN($catids) ";
					$first = 1;
				} elseif($first) {
					$catids = getcache('url_show_'.$_SESSION['userid'],'content');
					$catids = implode(',',$catids);
					$where .= "catid IN($catids) ";
				} else {
					$first = 0;
				}

				if($type == 'lastinput' && $number) {
					$offset = 0;
					$pagesize = $number;
					$order = 'DESC';
				} elseif($type == 'date') {
					if($fromdate) {
						$fromtime = strtotime($fromdate.' 00:00:00');
						$where .= " AND `inputtime`>=$fromtime ";
					}
					if($todate) {
						$totime = strtotime($todate.' 23:59:59');
						$where .= " AND `inputtime`<=$totime ";
					}
				} elseif($type == 'id') {
					$fromid = intval($fromid);
					$toid = intval($toid);
					if($fromid) $where .= " AND `id`>=$fromid ";
					if($toid) $where .= " AND `id`<=$toid ";
				}
				
				if (! isset ( $total ) && $type != 'lastinput') {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "*", "$offset,$pagesize" );
				foreach ( $data as $r ) {
					if ($r ['islink'])
						continue;
					$this->urls ( $r ['id'], $r ['catid'], $r ['inputtime'] );
				}
				
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					
					$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=update_urls&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&modelid=$modelid" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					delcache ( 'url_show_' . $_SESSION ['userid'], 'content' );
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=update_urls';
				}
				showmessage ( $message, $forward, 200 );
			} else {
				//当没有选择模型时，需要按照栏目来更新
				if (! isset ( $set_catid )) {
					if ($catids [0] != 0) {
						$update_url_catids = $catids;
					} else {
						foreach ( $this->categorys as $catid => $cat ) {
							if ($cat ['child'] || $cat ['type'] != 0)
								continue;
							$update_url_catids [] = $catid;
						}
					}
					setcache ( 'update_url_catid' . '-' . $_SESSION ['userid'], $update_url_catids, 'content' );
					$message = L ( 'start_update_urls' );
					$forward = "?m=content&c=create_html&a=update_urls&set_catid=1&pagesize=$pagesize&dosubmit=1";
					showmessage ( $message, $forward, 200 );
				}
				$catid_arr = getcache ( 'update_url_catid' . '-' . $_SESSION ['userid'], 'content' );
				$autoid = $autoid ? intval ( $autoid ) : 0;
				if (! isset ( $catid_arr [$autoid] ))
					showmessage ( L ( 'create_update_success' ), '?m=content&c=create_html&a=update_urls', 200 );
				$catid = $catid_arr [$autoid];
				
				$modelid = $this->categorys [$catid] ['modelid'];
				//设置模型数据表名
				$this->db->set_model ( $modelid );
				$table_name = $this->db->table_name;
				
				$page = max ( intval ( $page ), 1 );
				$offset = $pagesize * ($page - 1);
				$where = "status=99 AND catid='$catid'";
				$order = 'ASC';
				
				if (! isset ( $total )) {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "*", "$offset,$pagesize" );
				foreach ( $data as $r ) {
					if ($r ['islink'])
						continue;
						$this->urls ( $r ['id'], $r ['catid'], $r ['inputtime'] );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					$message = '【' . $this->categorys [$catid] ['catname'] . '】 ' . L ( 'have_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=update_urls&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&autoid=$autoid&set_catid=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					$autoid ++;
					$message = L ( 'updating' ) . $this->categorys [$catid] ['catname'] . " ...";
					$forward = "?m=content&c=create_html&a=update_urls&set_catid=1&pagesize=$pagesize&dosubmit=1&autoid=$autoid";
				}
				showmessage ( $message, $forward, 200 );
			}
		
		} else {
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'])
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				$r ['disabled'] = $r ['child'] ? 'disabled' : '';
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'update_urls' );
		}
	}
	
	private function urls($id, $catid = 0, $inputtime = 0, $prefix = '') {
		$urls = $this->url->show ( $id, 0, $catid, $inputtime, $prefix, '', 'edit' );
		$url = $urls [0];
		$this->db->update ( array ('url' => $url ), array ('id' => $id ) );
		return $urls;
	}
	/**
	 * 生成内容页
	 */
	public function show() {
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			$this->html = pc_base::load_app_class ( 'html' );
			var_dump($this->html);exit;
			$modelid = intval ( $_POST ['modelid'] );
		
			if ($modelid) {
				$this->db->set_model ( $modelid );
				$table_name = $this->db->table_name;
				
				if ($type == 'lastinput') {
					$offset = 0;
				} else {
					$page = max ( intval ( $page ), 1 );
					$offset = $pagesize * ($page - 1);
				}
				$where = 'status=99 ';
				$order = 'ASC';
				
				if (! isset ( $first ) && is_array ( $catids ) && $catids [0] > 0) {
					setcache ( 'html_show_' . $_SESSION ['userid'], $catids, 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
					$first = 1;
				} elseif ($first) {
					$catids = getcache ( 'html_show_' . $_SESSION ['userid'], 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
				} else {
					$first = 0;
				}
				if (count ( $catids ) == 1 && $catids [0] == 0) {
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=show';
					showmessage ( $message, $forward );
				}
				if ($type == 'lastinput' && $number) {
					$offset = 0;
					$pagesize = $number;
					$order = 'DESC';
				} elseif ($type == 'date') {
					if ($fromdate) {
						$fromtime = strtotime ( $fromdate . ' 00:00:00' );
						$where .= " AND `inputtime`>=$fromtime ";
					}
					if ($todate) {
						$totime = strtotime ( $todate . ' 23:59:59' );
						$where .= " AND `inputtime`<=$totime ";
					}
				} elseif ($type == 'id') {
					$fromid = intval ( $fromid );
					$toid = intval ( $toid );
					if ($fromid)
						$where .= " AND `id`>=$fromid ";
					if ($toid)
						$where .= " AND `id`<=$toid ";
				}
				if (! isset ( $total ) && $type != 'lastinput') {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "*", "$offset,$pagesize" );
				$tablename = $this->db->table_name . '_data';
				foreach ( $data as $r ) {
					if ($r ['islink'])
						continue;
					$this->db->table_name = $tablename;
					$r2 = $this->db->get_one ( array ('id' => $r ['id'] ) );
					if ($r)
						$r = array_merge ( $r, $r2 );
						$this->html->show ( $r ['url'], $r, 0, 'edit' );
				}
				
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					
					$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=show&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&modelid=$modelid" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					delcache ( 'html_show_' . $_SESSION ['userid'], 'content' );
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=show';
				}
				showmessage ( $message, $forward, 200 );
			} else {
				//当没有选择模型时，需要按照栏目来更新
				if (! isset ( $set_catid )) {
					if ($catids [0] != 0) {
						$update_url_catids = $catids;
					} else {
						foreach ( $this->categorys as $catid => $cat ) {
							if ($cat ['child'] || $cat ['type'] != 0)
								continue;
							$setting = string2array ( $cat ['setting'] );
							if (! $setting ['content_ishtml'])
								continue;
							$update_url_catids [] = $catid;
						}
					}
					setcache ( 'update_html_catid' . '-' . $_SESSION ['userid'], $update_url_catids, 'content' );
					$message = L ( 'start_update' );
					$forward = "?m=content&c=create_html&a=show&set_catid=1&pagesize=$pagesize&dosubmit=1";
					showmessage ( $message, $forward, 200 );
				}
				if (count ( $catids ) == 1 && $catids [0] == 0) {
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=show';
					showmessage ( $message, $forward, 200 );
				}
				$catid_arr = getcache ( 'update_html_catid' . '-' . $_SESSION ['userid'], 'content' );
				$autoid = $autoid ? intval ( $autoid ) : 0;
				if (! isset ( $catid_arr [$autoid] ))
					showmessage ( L ( 'create_update_success' ), '?m=content&c=create_html&a=show', 200 );
				$catid = $catid_arr [$autoid];
				
				$modelid = $this->categorys [$catid] ['modelid'];
				//设置模型数据表名
				$this->db->set_model ( $modelid );
				$table_name = $this->db->table_name;
				
				$page = max ( intval ( $page ), 1 );
				$offset = $pagesize * ($page - 1);
				$where = "status=99 AND catid='$catid'";
				$order = 'ASC';
				
				if (! isset ( $total )) {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "*", "$offset,$pagesize" );
				$tablename = $this->db->table_name . '_data';
				foreach ( $data as $r ) {
					if ($r ['islink'])
						continue;
					$this->url = pc_base::load_app_class ( 'url' );
					$this->db->table_name = $tablename;
					$r2 = $this->db->get_one ( array ('id' => $r ['id'] ) );
					if ($r2)
						$r = array_merge ( $r, $r2 );
					
					$urls = $this->url->show ( $r ['id'], '', $r ['catid'], $r ['inputtime'] );
					$this->html->show ( $urls [1], $r, 0, 'edit' );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					$message = '【' . $this->categorys [$catid] ['catname'] . '】 ' . L ( 'have_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=show&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&autoid=$autoid&set_catid=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					$autoid ++;
					$message = L ( 'start_update' ) . $this->categorys [$catid] ['catname'] . " ...";
					$forward = "?m=content&c=create_html&a=show&set_catid=1&pagesize=$pagesize&dosubmit=1&autoid=$autoid";
				}
				showmessage ( $message, $forward, 200 );
			}
		} else {
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'] == 1)
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				if ($r ['child'] == 0) {
					$setting = string2array ( $r ['setting'] );
					if (! $setting ['content_ishtml'])
						continue;
				}
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'create_html_show' );
		}
	
	}
	/**
	 * 生成栏目页
	 */
	public function category() {
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			$this->html = pc_base::load_app_class ( 'html' );
			$referer = isset ( $referer ) ? urlencode ( $referer ) : '';
			
			$modelid = intval ( $_POST ['modelid'] );
			if (! isset ( $set_catid )) {
				if ($catids [0] != 0) {
					$update_url_catids = $catids;
				} else {
					foreach ( $this->categorys as $catid => $cat ) {
						if ($cat ['type'] == 2 || ! $cat ['ishtml'])
							continue;
						if ($modelid && ($modelid != $cat ['modelid']))
							continue;
						$update_url_catids [] = $catid;
					}
				}
				setcache ( 'update_html_catid' . '-' . $_SESSION ['userid'], $update_url_catids, 'content' );
				$message = L ( 'start_update_category' );
				$forward = "?m=content&c=create_html&a=category&set_catid=1&pagesize=$pagesize&dosubmit=1&modelid=$modelid&referer=$referer";
				
				showmessage ( $message, $forward );
			}
			
			$catid_arr = getcache ( 'update_html_catid' . '-' . $_SESSION ['userid'], 'content' );
			
			$autoid = $autoid ? intval ( $autoid ) : 0;
			
			if (! isset ( $catid_arr [$autoid] )) {
				if (! empty ( $referer ) && $this->categorys [$catid_arr [0]] ['type'] != 1) {
					showmessage ( L ( 'create_update_success' ), '?m=content&c=content&a=init&catid=' . $catid_arr [0], 200 );
				} else {
					showmessage ( L ( 'create_update_success' ), '?m=content&c=create_html&a=category', 200 );
				}
			}
			$catid = $catid_arr [$autoid];
			$page = $page ? $page : 1;
			$j = 1;
			do {
				$this->html->category ( $catid, $page );
				$page ++;
				$j ++;
				$total_number = isset ( $total_number ) ? $total_number : PAGES;
			} while ( $j <= $total_number && $j < $pagesize );
			if ($page <= $total_number) {
				$endpage = intval ( $page + $pagesize );
				$message = L ( 'updating' ) . $this->categorys [$catid] ['catname'] . L ( 'start_to_end_id', array ('page' => $page, 'endpage' => $endpage ) );
				$forward = "?m=content&c=create_html&a=category&set_catid=1&pagesize=$pagesize&dosubmit=1&autoid=$autoid&page=$page&total_number=$total_number&modelid=$modelid&referer=$referer";
			} else {
				$autoid ++;
				$message = $this->categorys [$catid] ['catname'] . L ( 'create_update_success' );
				$forward = "?m=content&c=create_html&a=category&set_catid=1&pagesize=$pagesize&dosubmit=1&autoid=$autoid&modelid=$modelid&referer=$referer";
			}
			showmessage ( $message, $forward, 200 );
		} else {
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'] == 2)
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				if ($r ['child'] == 0) {
					if (! $r ['ishtml'])
						continue;
				}
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'create_html_category' );
		}
	
	}
	//生成首页
	public function public_index() {
		$this->html = pc_base::load_app_class ( 'html' );
		$size = $this->html->index ();
		showmessage ( L ( 'index_create_finish', array ('size' => sizecount ( $size ) ) ) );
	}
	/**
	 * 批量生成内容页
	 */
	public function batch_show() {
		if (isset ( $_POST ['dosubmit'] )) {
			$catid = intval ( $_GET ['catid'] );
			if (! $catid)
				showmessage ( L ( 'missing_part_parameters' ) );
			$modelid = $this->categorys [$catid] ['modelid'];
			$setting = string2array ( $this->categorys [$catid] ['setting'] );
			$content_ishtml = $setting ['content_ishtml'];
			if ($content_ishtml) {
				$this->url = pc_base::load_app_class ( 'url' );
				$this->db->set_model ( $modelid );
				if (empty ( $_POST ['ids'] ))
					showmessage ( L ( 'you_do_not_check' ) );
				$this->html = pc_base::load_app_class ( 'html' );
				$ids = implode ( ',', $_POST ['ids'] );
				$rs = $this->db->select ( "catid='$catid' AND id IN ($ids)" );
				$tablename = $this->db->table_name . '_data';
				foreach ( $rs as $r ) {
					if ($r ['islink'])
						continue;
					$this->db->table_name = $tablename;
					$r2 = $this->db->get_one ( array ('id' => $r ['id'] ) );
					if ($r2)
						$r = array_merge ( $r, $r2 );
					$urls = $this->url->show ( $r ['id'], '', $r ['catid'], $r ['inputtime'] );
					$this->html->show ( $urls [1], $r, 0, 'edit' );
				}
				showmessage ( L ( 'operation_success' ), HTTP_REFERER );
			}
		}
	}
	
	public function update_desc() {
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			$modelid = intval ( $_POST ['modelid'] );
			if ($modelid) {
				//设置模型数据表名
				$this->db->set_model ( $modelid );
				$table_name = $this->db->table_name;
				
				if ($type == 'lastinput') {
					$offset = 0;
				} else {
					$page = max ( intval ( $page ), 1 );
					$offset = $pagesize * ($page - 1);
				}
				$where = '';
				$order = 'ASC';
				
				if (! isset ( $first ) && is_array ( $catids ) && $catids [0] > 0) {
					setcache ( 'update_desc_' . $_SESSION ['userid'], $catids, 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
					$first = 1;
				} elseif ($first) {
					$catids = getcache ( 'update_desc_' . $_SESSION ['userid'], 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
				} else {
					$first = 0;
				}
				
				if ($type == 'lastinput' && $number) {
					$offset = 0;
					$pagesize = $number;
					$order = 'DESC';
				} elseif ($type == 'date') {
					if ($fromdate) {
						$fromtime = strtotime ( $fromdate . ' 00:00:00' );
						$where .= " AND `inputtime`>=$fromtime ";
					}
					if ($todate) {
						$totime = strtotime ( $todate . ' 23:59:59' );
						$where .= " AND `inputtime`<=$totime ";
					}
				} elseif ($type == 'id') {
					$fromid = intval ( $fromid );
					$toid = intval ( $toid );
					if ($fromid)
						$where .= " AND `id`>=$fromid ";
					if ($toid)
						$where .= " AND `id`<=$toid ";
				}
				
				if (! isset ( $total ) && $type != 'lastinput') {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$table_name_data = $table_name . '_data';
				$data = $this->db->select ( $where, "id", "$offset,$pagesize" );
				foreach ( $data as $r ) {
					$rs = $this->db->query ( "select `$field` from `$table_name_data` where id={$r[id]}" );
					$m = $this->db->fetch_array ( $rs );
					$content = stripslashes ( $m [0] [$field] );
					$introcude_length = intval ( $stringlength );
					$description = str_cutword ( str_replace ( array ("\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;' ), '', strip_tags ( $content ) ), $introcude_length );
					$description = addslashes ( $description );
					$this->db->update ( array ('description' => $description ), array ('id' => $r ['id'] ) );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					
					$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=update_desc&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&stringlength=$stringlength&field=$field&modelid=$modelid" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					delcache ( 'update_desc_' . $_SESSION ['userid'], 'content' );
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=update_desc';
				}
				showmessage ( $message, $forward, 200 );
			} else {
				
				//当没有选择模型时，需要按照栏目来更新
				if (! isset ( $set_catid )) {
					if ($catids [0] != 0) {
						$update_desc_catids = $catids;
					} else {
						foreach ( $this->categorys as $catid => $cat ) {
							if ($cat ['child'] || $cat ['type'] != 0)
								continue;
							$update_desc_catids [] = $catid;
						}
					}
					setcache ( 'update_desc_catid' . '-' . $_SESSION ['userid'], $update_desc_catids, 'content' );
					$message = L ( 'start_update_urls' );
					$forward = "?m=content&c=create_html&a=update_desc&set_catid=1&pagesize=$pagesize&stringlength=$stringlength&field=$field&dosubmit=1";
					showmessage ( $message, $forward, 200 );
				}
				$catid_arr = getcache ( 'update_desc_catid' . '-' . $_SESSION ['userid'], 'content' );
				$autoid = $autoid ? intval ( $autoid ) : 0;
				if (! isset ( $catid_arr [$autoid] ))
					showmessage ( L ( 'create_update_success' ), '?m=content&c=create_html&a=update_desc', 200 );
				$catid = $catid_arr [$autoid];
				
				$modelid = $this->categorys [$catid] ['modelid'];
				//设置模型数据表名
				$this->db->set_model ( $modelid );
				$table_name = $this->db->table_name;
				
				$page = max ( intval ( $page ), 1 );
				$offset = $pagesize * ($page - 1);
				$where = "catid='$catid'";
				if (! isset ( $total )) {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "id", "$offset,$pagesize" );
				$table_name_data = $table_name . '_data';
				foreach ( $data as $r ) {
					$rs = $this->db->query ( "select `$_GET[field]` from `$table_name_data` where id={$r[id]}" );
					$m = $this->db->fetch_array ( $rs );
					$content = stripslashes ( $m [0] [$field] );
					$introcude_length = intval ( $stringlength );
					$description = str_cut ( str_replace ( array ("\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;' ), '', strip_tags ( $content ) ), $introcude_length );
					$description = addslashes ( $description );
					$this->db->update ( array ('description' => $description ), array ('id' => $r ['id'] ) );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					$message = '【' . $this->categorys [$catid] ['catname'] . '】 ' . L ( 'have_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=update_desc&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&autoid=$autoid&stringlength=$stringlength&field=$field&set_catid=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					$autoid ++;
					$message = L ( 'updating' ) . $this->categorys [$catid] ['catname'] . " ...";
					$forward = "?m=content&c=create_html&a=update_desc&set_catid=1&pagesize=$pagesize&stringlength=$stringlength&field=$field&dosubmit=1&autoid=$autoid";
				}
				showmessage ( $message, $forward, 200 );
			}
		
		} else {
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'])
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				$r ['disabled'] = $r ['child'] ? 'disabled' : '';
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'update_desc' );
		}
	}
	public function batch_hits() {
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			$modelid = intval ( $_POST ['modelid'] );
			$this->hits_db = pc_base::load_model ( "hits_model" );
			$table_name = $this->hits_db->table_name;
			if ($modelid) {
				//设置模型数据表名
				if ($type == 'lastinput') {
					$offset = 0;
				} else {
					$page = max ( intval ( $page ), 1 );
					$offset = $pagesize * ($page - 1);
				}
				$where = 'status=99 ';
				$order = 'ASC';
				
				if (! isset ( $first ) && is_array ( $catids ) && $catids [0] > 0) {
					setcache ( 'update_hits_' . $_SESSION ['userid'], $catids, 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
					$first = 1;
				} elseif ($first) {
					$catids = getcache ( 'update_hits_' . $_SESSION ['userid'], 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
				} else {
					$first = 0;
				}
				
				if ($type == 'lastinput' && $number) {
					$offset = 0;
					$pagesize = $number;
					$order = 'DESC';
				} elseif ($type == 'date') {
					if ($fromdate) {
						$fromtime = strtotime ( $fromdate . ' 00:00:00' );
						$where .= " AND `inputtime`>=$fromtime ";
					}
					if ($todate) {
						$totime = strtotime ( $todate . ' 23:59:59' );
						$where .= " AND `inputtime`<=$totime ";
					}
				} elseif ($type == 'id') {
					$fromid = intval ( $fromid );
					$toid = intval ( $toid );
					if ($fromid)
						$where .= " AND `id`>=$fromid ";
					if ($toid)
						$where .= " AND `id`<=$toid ";
				}
				$this->db = pc_base::load_model ( 'content_model' );
				$this->db->set_model ( $modelid );
				if (! isset ( $total ) && $type != 'lastinput') {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "id,catid", "$offset,$pagesize" );
				foreach ( $data as $r ) {
					$hitsid = 'c-' . $modelid . '-'. $r ['catid'] .'-' . $r ['id'];
					$sys_time = SYS_TIME;
					$rs = $this->hits_db->query ( "insert IGNORE into $table_name (hitsid,updatetime) values ('$hitsid',$sys_time)" );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					
					$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=batch_hits&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&modelid=$modelid" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					delcache ( 'update_hits_' . $_SESSION ['userid'], 'content' );
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=batch_hits';
				}
				showmessage ( $message, $forward, 200 );
			} else {
				//当没有选择模型时，需要按照栏目来更新
				if (! isset ( $set_catid )) {
					if ($catids [0] != 0) {
						$update_desc_catids = $catids;
					} else {
						foreach ( $this->categorys as $catid => $cat ) {
							if ($cat ['child'] || $cat ['type'] != 0)
								continue;
							$update_desc_catids [] = $catid;
						}
					}
					setcache ( 'update_hits_catid' . '-' . $_SESSION ['userid'], $update_desc_catids, 'content' );
					$message = L ( 'start_update_urls' );
					$forward = "?m=content&c=create_html&a=batch_hits&set_catid=1&pagesize=$pagesize&dosubmit=1";
					showmessage ( $message, $forward, 200 );
				}
				$catid_arr = getcache ( 'update_hits_catid' . '-' . $_SESSION ['userid'], 'content' );
				$autoid = $autoid ? intval ( $autoid ) : 0;
				if (! isset ( $catid_arr [$autoid] ))
					showmessage ( L ( 'create_update_success' ), '?m=content&c=create_html&a=batch_hits', 200 );
				$catid = $catid_arr [$autoid];
				
				$modelid = $this->categorys [$catid] ['modelid'];
				//设置模型数据表名
				$this->db->set_model ( $modelid );
				$page = max ( intval ( $page ), 1 );
				$offset = $pagesize * ($page - 1);
				$where = "status=99 AND catid='$catid'";
				$order = 'ASC';
				
				if (! isset ( $total )) {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "id,catid", "$offset,$pagesize" );
				foreach ( $data as $r ) {
					$hitsid = 'c-' . $modelid . '-'. $r ['catid'] .'-' . $r ['id'];
					$sys_time = SYS_TIME;
					$rs = $this->hits_db->query ( "insert IGNORE into $table_name (hitsid,updatetime) values ('$hitsid',$sys_time)" );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					$message = '【' . $this->categorys [$catid] ['catname'] . '】 ' . L ( 'have_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=batch_hits&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&autoid=$autoid&stringlength=$stringlength&field=$field&set_catid=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					$autoid ++;
					$message = L ( 'updating' ) . $this->categorys [$catid] ['catname'] . " ...";
					$forward = "?m=content&c=create_html&a=batch_hits&set_catid=1&pagesize=$pagesize&stringlength=$stringlength&field=$field&dosubmit=1&autoid=$autoid";
				}
				showmessage ( $message, $forward, 200 );
			}
		
		} else {
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'])
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				$r ['disabled'] = $r ['child'] ? 'disabled' : '';
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'batch_hits' );
		}
	}
	
	public function batch_relation() {
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			$modelid = intval ( $_POST ['modelid'] );
			$this->db = pc_base::load_model ( 'content_model' );
			$this->db->set_model ( $modelid );
			$tab = $this->db->table_name . '_data';
			$sphinxenable = getcache ( 'search', 'search' );
			if ($sphinxenable ['sphinxenable']) {
				$sphinx = pc_base::load_app_class ( 'search_interface', 'search', 0 );
				$sphinx = new search_interface ();
			}
			if ($modelid) {
				if ($type == 'lastinput') {
					$offset = 0;
				} else {
					$page = max ( intval ( $page ), 1 );
					$offset = $pagesize * ($page - 1);
				}
				$where = 'status=99 ';
				$order = 'ASC';
				if (! isset ( $first ) && is_array ( $catids ) && $catids [0] > 0) {
					setcache ( 'update_relation_' . $_SESSION ['userid'], $catids, 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
					$first = 1;
				} elseif ($first) {
					$catids = getcache ( 'update_relation_' . $_SESSION ['userid'], 'content' );
					$catids = implode ( ',', $catids );
					$where .= " AND catid IN($catids) ";
				} else {
					$first = 0;
				}
				if ($type == 'lastinput' && $number) {
					$offset = 0;
					$pagesize = $number;
					$order = 'DESC';
				} elseif ($type == 'date') {
					if ($fromdate) {
						$fromtime = strtotime ( $fromdate . ' 00:00:00' );
						$where .= " AND `inputtime`>=$fromtime ";
					}
					if ($todate) {
						$totime = strtotime ( $todate . ' 23:59:59' );
						$where .= " AND `inputtime`<=$totime ";
					}
				} elseif ($type == 'id') {
					$fromid = intval ( $fromid );
					$toid = intval ( $toid );
					if ($fromid)
						$where .= " AND `id`>=$fromid ";
					if ($toid)
						$where .= " AND `id`<=$toid ";
				}
				
				if (! isset ( $total ) && $type != 'lastinput') {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "id,title", "$offset,$pagesize" ,"$order");
				foreach ( $data as $r ) {
					$q = safe_replace ( trim ( $r ['title'] ) );
					$q = htmlspecialchars ( strip_tags ( $q ) );
					if ($sphinxenable ['sphinxenable']) {
						$res = $sphinx->search ( $q );
					} else {
						$res = $this->search ( $modelid, $q );
					}
					$totalnums = $res ['total'];
					$result = array ();
					//如果结果不为空
					if (! empty ( $res ['matches'] )) {
						$result = $res ['matches'];
					}
					
					foreach ( $result as $v ) {
						$rel .= '|' . $v ['attrs'] ['id'];
					}
					$relation = substr ( $rel, 1 );
					unset ( $rel );
					$this->db->query ( "update $tab set `relation`='$relation' where `id`=$r[id]" );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					
					$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=batch_relation&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&modelid=$modelid" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					delcache ( 'update_relation_' . $_SESSION ['userid'], 'content' );
					$message = L ( 'create_update_success' );
					$forward = '?m=content&c=create_html&a=batch_relation';
				}
				showmessage ( $message, $forward, 200 );
			} else {
				//当没有选择模型时，需要按照栏目来更新
				if (! isset ( $set_catid )) {
					if ($catids [0] != 0) {
						$update_desc_catids = $catids;
					} else {
						foreach ( $this->categorys as $catid => $cat ) {
							if ($cat ['child'] || $cat ['type'] != 0)
								continue;
							$update_desc_catids [] = $catid;
						}
					}
					setcache ( 'update_relation_catid' . '-' . $_SESSION ['userid'], $update_desc_catids, 'content' );
					$message = L ( 'start_update' );
					$forward = "?m=content&c=create_html&a=batch_relation&set_catid=1&pagesize=$pagesize&dosubmit=1";
					showmessage ( $message, $forward, 200 );
				}
				$catid_arr = getcache ( 'update_relation_catid' . '-' . $_SESSION ['userid'], 'content' );
				$autoid = $autoid ? intval ( $autoid ) : 0;
				if (! isset ( $catid_arr [$autoid] ))
					showmessage ( L ( 'create_update_success' ), '?m=content&c=create_html&a=batch_relation', 200 );
				$catid = $catid_arr [$autoid];
				
				$modelid = $this->categorys [$catid] ['modelid'];
				//设置模型数据表名
				$this->db->set_model ( $modelid );
				$page = max ( intval ( $page ), 1 );
				$offset = $pagesize * ($page - 1);
				$where = "status=99 AND catid='$catid'";
				$order = 'ASC';
				
				if (! isset ( $total )) {
					$total = $this->db->count ( "$where" );
					$pages = ceil ( $total / $pagesize );
					$start = 1;
				}
				$data = $this->db->select ( $where, "id", "$offset,$pagesize" );
				foreach ( $data as $r ) {
					$q = safe_replace ( trim ( $r ['title'] ) );
					$q = htmlspecialchars ( strip_tags ( $q ) );
					if ($sphinxenable ['sphinxenable']) {
						$res = $sphinx->search ( $q );
					} else {
						$res = $this->search ( $modelid, $q );
					}
					$totalnums = $res ['total'];
					//如果结果不为空
					if (! empty ( $res ['matches'] )) {
						$result = $res ['matches'];
					}
					foreach ( $result as $v ) {
						$rel .= '|' . $v ['attrs'] ['id'];
					}
					$relation = substr ( $rel, 1 );
					unset ( $rel );
					$this->db->update ( "update $tab set `relation`='$relation' where `id`=$r[id]" );
				}
				if ($pages > $page) {
					$page ++;
					$http_url = get_url ();
					$creatednum = $offset + count ( $data );
					$percent = round ( $creatednum / $total, 2 ) * 100;
					$message = '【' . $this->categorys [$catid] ['catname'] . '】 ' . L ( 'have_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
					$forward = $start ? "?m=content&c=create_html&a=batch_relation&type=$type&dosubmit=1&first=$first&fromid=$fromid&toid=$toid&fromdate=$fromdate&todate=$todate&pagesize=$pagesize&page=$page&pages=$pages&total=$total&autoid=$autoid&stringlength=$stringlength&field=$field&set_catid=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
				} else {
					$autoid ++;
					$message = L ( 'updating' ) . $this->categorys [$catid] ['catname'] . " ...";
					$forward = "?m=content&c=create_html&a=batch_relation&set_catid=1&pagesize=$pagesize&stringlength=$stringlength&field=$field&dosubmit=1&autoid=$autoid";
				}
				showmessage ( $message, $forward, 200 );
			}
		
		} else {
			
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'])
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				$r ['disabled'] = $r ['child'] ? 'disabled' : '';
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'batch_relation' );
		}
	}
	
	public function search($modelid, $q, $page = '') {
		$this->search_db = pc_base::load_model ( 'search_model' );
		$page = max ( 1, $page );
		pc_base::load_sys_class ( 'segment', '', 0 );
		$segment = new segment ();
		//分词结果
		$segment_q = $segment->get_keyword ( $segment->split_result ( $q ) );
		//如果分词结果为空
		if (! empty ( $segment_q )) {
			$sql = "`modelid` = '$modelid'  AND MATCH (`data`) AGAINST ('$segment_q' IN BOOLEAN MODE)";
		} else {
			$sql = "`modelid` = '$modelid'  AND `data` like '%$q%'";
		}
		return $result = $this->search_db->listinfo ( $sql, 'searchid DESC', $page, 10 );
	}
	
	//更新查看次数
	function batch_views() {
		if ($_POST ['dosubmit']) {
			$modelids = intval ( $_POST ['modelid'] );
			$this->model = getcache ( 'model', 'commons' );
			if ($modelids === 0) {
				$data = array_keys ( $this->model );
			} elseif ($modelids > 0) {
				$data = array ($modelids );
			}
			foreach ( $data as $modelid ) {
				$logfile = './caches/caches_content/caches_data/' . $modelid . '.log';
				$viewlog = $viewarray = array ();
				$contentdb = pc_base::load_model ( 'content_model' );
				$contentdb->set_model ( $modelid );
				$newlog = OLCMS_PATH . $logfile . random ( 6 );
				if (@rename ( OLCMS_PATH . $logfile, $newlog )) {
					$viewlog = file ( $newlog );
					unlink ( $newlog );
					if (is_array ( $viewlog ) && ! empty ( $viewlog )) {
						$viewlog = array_count_values ( $viewlog );
						foreach ( $viewlog as $contentid => $views ) {
							$viewarray [$views] .= ($contentid > 0) ? ',' . intval ( $contentid ) : '';
						}
						foreach ( $viewarray as $views => $ids ) {
							$contentdb->update ( array ('views' => '+=' . $views ), 'id IN (0' . $ids . ')' );
						}
					}
				}
			}
			showmessage ( L ( 'success' ), HTTP_REFERER );
		} else {
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : 0;
			
			$tree = pc_base::load_sys_class ( 'tree' );
			$tree->icon = array ('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ' );
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array ();
			foreach ( $this->categorys as $catid => $r ) {
				if ($r ['type'])
					continue;
				if ($modelid && $modelid != $r ['modelid'])
					continue;
				$r ['disabled'] = $r ['child'] ? 'disabled' : '';
				$categorys [$catid] = $r;
			}
			$str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
			
			$tree->init ( $categorys );
			$string .= $tree->get_tree ( 0, $str );
			include $this->admin_tpl ( 'batch_views' );
		}
	}
	/*
	function batch_comic() {  //关联采集下来的漫画与漫画内容 Add By SouL_Z 2013-4-22 11:28
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			//$modelid = intval ( $_POST ['modelid'] );
			if (! isset ($first)) {
				$message = L ( 'start_relevance_comic' );
				$forward = "?m=content&c=create_html&a=batch_comic&first=1&pagesize=$pagesize&dosubmit=1";
				showmessage ( $message, $forward, 200 );
			}
			//$this->db = pc_base::load_model ( 'content_model' );
			$this->db->table_name = 'tt_CartoonDetail_data';
			$page = max ( intval ( $page ), 1 );
			$offset = $pagesize * ($page - 1);
			$where = "cdd.is_down = '1' AND cdd.is_new = '1' AND cd.manhuaid = '0'";
			if (! isset ( $total )) {
				$num_query =  $this->db->query("select COUNT(*) as num from tt_CartoonDetail as cd left join tt_CartoonDetail_data as cdd ON cd.id =cdd.id where $where");
				$num = $this->db->fetch_array($num_query);
				$total = $num[0]['num'];

				$pages = ceil ( $total / $pagesize );
				$start = 1;
			}
			$query = $this->db->query("select cd.id from tt_CartoonDetail as cd left join tt_CartoonDetail_data as cdd ON cd.id =cdd.id where $where limit $offset,$pagesize");
			while($list = $this->db->fetch_array($query)) {
				$data = $list;
			}
			foreach ( $data as $r ) {
				$this->db->query("UPDATE tt_CartoonDetail AS cd ,tt_Cartoon AS c SET cd.manhuaid = c.id WHERE cd.web_url=c.web_url AND cd.web_url != '' AND cd.id=$r[id]" );
			}
			if ($pages > $page) {
				$page ++;
				$http_url = get_url ();
				$creatednum = $offset + count ( $data );
				$percent = round ( $creatednum / $total, 2 ) * 100;
				$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
				$forward = $start ? "?m=content&c=create_html&a=batch_comic&dosubmit=1&pagesize=$pagesize&page=$page&pages=$pages&total=$total&first=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
			} else {
				$message = L ( 'create_update_success' );
				$forward = '?m=content&c=create_html&a=batch_comic';
			}
			showmessage ( $message, $forward, 200 );
		}else{
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			include $this->admin_tpl ( 'batch_comic' );
		}
	}


	function batch_cartoon() {  //关联采集下来的动漫与动漫内容 Add By SouL_Z 2013-5-20 11:28
		if (isset ( $_POST ['dosubmit'] )) {
			extract ( $_POST, EXTR_SKIP );
			//$modelid = intval ( $_POST ['modelid'] );
			if (! isset ($first)) {
				$message = L ( 'start_relevance_cartoon' );
				$forward = "?m=content&c=create_html&a=batch_cartoon&first=1&pagesize=$pagesize&dosubmit=1";
				showmessage ( $message, $forward, 200 );
			}
			//$this->db = pc_base::load_model ( 'content_model' );
			$this->db->table_name = 'tt_dongmanDetail';
			$page = max ( intval ( $page ), 1 );
			$offset = $pagesize * ($page - 1);
			$where = "CartoonID = '0' AND web_url != ''";
			if (! isset ( $total )) {
				$total = $this->db->count ( "$where" );

				$pages = ceil ( $total / $pagesize );
				$start = 1;
			}
//			$query = $this->db->query("select cd.id from tt_CartoonDetail as cd left join tt_CartoonDetail_data as cdd ON cd.id =cdd.id where $where limit $offset,$pagesize");
//			while($list = $this->db->fetch_array($query)) {
//				$data = $list;
//			}
			$data = $this->db->select ( $where, "id", "$offset,$pagesize" );
			foreach ( $data as $r ) {
				$this->db->query("UPDATE tt_dongmanDetail AS dd ,tt_dongman AS d SET dd.CartoonID = d.id WHERE dd.web_url=d.web_url AND dd.web_url != '' AND dd.id=$r[id]" );
			}
			if ($pages > $page) {
				$page ++;
				$http_url = get_url ();
				$creatednum = $offset + count ( $data );
				$percent = round ( $creatednum / $total, 2 ) * 100;
				$message = L ( 'need_update_items', array ('total' => $total, 'creatednum' => $creatednum, 'percent' => $percent ) );
				$forward = $start ? "?m=content&c=create_html&a=batch_cartoon&dosubmit=1&pagesize=$pagesize&page=$page&pages=$pages&total=$total&first=1" : preg_replace ( "/&page=([0-9]+)&pages=([0-9]+)&total=([0-9]+)/", "&page=$page&pages=$pages&total=$total", $http_url );
			} else {
				$message = L ( 'create_update_success' );
				$forward = '?m=content&c=create_html&a=batch_cartoon';
			}
			showmessage ( $message, $forward, 200 );
		}else{
			$show_header = $show_dialog = '';
			$admin_username = param::get_cookie ( 'admin_username' );
			include $this->admin_tpl ( 'batch_cartoon' );
		}
	}
	*/
}
?>