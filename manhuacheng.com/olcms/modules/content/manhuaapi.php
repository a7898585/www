<?php
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
//模型缓存路径
define ( 'CACHE_MODEL_PATH', CACHE_PATH . 'caches_model' . DIRECTORY_SEPARATOR . 'caches_data' . DIRECTORY_SEPARATOR );
define ('DOWNLOADURL','http://www.manhuacheng.com/download/MHCPoint/ManHuaCheng.rar');
pc_base::load_app_func ( 'util', 'content' );
class manhuaapi {
	private $db, $xmlstr, $page, $modelid;
	function __construct() {
		$this->xmlstr = '<?xml version="1.0" encoding="utf-8"?>';
		$this->db = pc_base::load_model ( 'content_model' );
		$this->categorys = getcache ( 'category_content', 'commons' );
		$this->page = intval ( $_GET ['page'] );
		$this->modelid = intval ( $_GET ['modelid'] );
		$this->sw_db = pc_base::load_model ( 'search_words_model');
		$this->db->set_model ( $this->modelid );
	}

	
public function charIndex() {
		$top_catid = '13';
		if (ord ( $_GET ['letter'] ) > 64 && ord ( $_GET ['letter'] ) < 91 || ord ( $_GET ['letter'] ) == 48) {
		$swdata=$this->sw_db->select('','*',100);
			$tablename = $this->db->table_name;
			//$this->db->table_name = $this->db->table_name . '_data';
			$where = "status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";	
			$char_where = $where." AND Char_Index='".$_GET['letter']."'";
			$num = $this->db->count ($char_where);
			$chardata = $this->db->listinfo ( $char_where , 'id DESC', $this->page );
		
		if ($chardata) {
				$ids = array ();
				
				foreach ( $chardata as $v ) {
					if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
						$ids [] = $v ['id'];
					} else {
						continue;
					}
				}
				
				//将附表数据也取出来
				if (! empty ( $ids )) {
					$this->db->table_name = $tablename. '_data';;
					$ids = implode ( '\',\'', $ids );
					$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
					
					if (! empty ( $r )) {
						foreach ( $chardata as $k => $v ) {
							if (isset ( $r [$v ['id']] ))
								$chardata [$k] = array_merge ( $r [$v ['id']], $chardata [$k] );
						}
					}
				}
				
			
			}
			
			
		
		
		$pages=pages($num,$this->page);
		}
		$letter = $_GET['letter'];
		include template ( 'content', 'char_bg' );
	
	}
	public function char() {
//		if (ord ( $_GET ['letter'] ) > 64 && ord ( $_GET ['letter'] ) < 91 || ord ( $_GET ['letter'] ) == 48) {
		
			$tablename = $this->db->table_name;
			$this->db->table_name = $this->db->table_name . '_data';
			for ($i=65; $i<=90; $i++){
			$chardata[$i-65] = $this->db->listinfo ( ' CharIndex=\'' . chr($i) . '\'', 'id DESC', $this->pages,'12' );
			
		}
		
		//渠道用的
			/*if ($_GET ['ParentID'] == 2||$_GET['ParentID']==6||$_GET['ParentID']==7) {
				$num = $this->db->count ( 'tid<11 and CharIndex=\'' . $_GET ['letter'] . '\'' );
				$chardata = $this->db->listinfo ( 'tid<11 and CharIndex=\'' . $_GET ['letter'] . '\'', 'id DESC', $this->pages,'12' );
			} else {
				$num = $this->db->count ( 'CharIndex=\'' . $_GET ['letter'] . '\'' );
				$chardata = $this->db->listinfo ( 'CharIndex=\'' . $_GET ['letter'] . '\'', 'id DESC', $this->pages,'12' );
			}*/
		$this->db->table_name = $tablename;
		foreach($chardata as $ck=>$cv){
			if ($cv) {
				$ids = array ();
				foreach ( $cv as $v ) {
					if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
						$ids [] = $v ['id'];
					} else {
						continue;
					}
				}
				//将主表数据也取出来
				if (! empty ( $ids )) {
					
					$ids = implode ( '\',\'', $ids );
					$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
					
					if (! empty ( $r )) {
						foreach ( $cv as $k => $v ) {
							if (isset ( $r [$v ['id']] ))
								$chardata [$ck][$k] = array_merge ( $r [$v ['id']], $cv [$k] );
						}
					}
				}
			
			}
		}
		setcache('charIndex',$chardata);
		echo 'success';
//			echo  json_encode($chardata);
		
	
	}
	//http://t.manhuacheng.com/index.php?m=content&c=MHapi&a=mhlist&UID=2&Page=0&type=11&modelid=12
	public function type() {
		$top_catid = '13';
		$table_name=$this->db->table_name;
		//$this->db->table_name = $this->db->table_name;
		if (intval ( $_GET ['type'] ) > - 1) {
			$where = "status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";	
			$num = $this->db->count ( ' status = \'99\' AND type_id=\'' . $_GET ['type'] . '\'' );
			$mhdata = $this->db->listinfo ( 'status = \'99\' AND type_id=\'' . $_GET ['type'] . '\'', ' updatetime desc', $this->page,'16' );
			$ids = array ();
			
			if ($mhdata) {
				foreach ( $mhdata as $v ) {
					if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
						$ids [] = $v ['id'];
					} else {
						continue;
					}
				}
				//将附表数据也取出来
				if (! empty ( $ids )) {
					$this->db->table_name = $table_name . '_data';
					$ids = implode ( '\',\'', $ids );
					$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
					
					if (! empty ( $r )) {
						foreach ( $mhdata as $k => $v ) {
							if (isset ( $r [$v ['id']] ))
								$mhdata [$k] = array_merge ( $r [$v ['id']], $mhdata [$k] );
						}
					}
				}
			
			}
			$pages=pages($num,$this->page);
			$typeid = $_GET ['type'];
			include template ( 'content', 'type_bg' );
		}
		/* elseif (intval ( $_GET ['type'] ) == 11 && $_GET ['UID']) {
			$num = $this->db->count ( 'tid=11' );
			$mhdata = $this->db->listinfo ( 'tid=11', '', $this->page );
			$ids = array ();
			
			if ($mhdata) {
				foreach ( $mhdata as $v ) {
					if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
						$ids [] = $v ['id'];
					} else {
						continue;
					}
				}
				//将附表数据也取出来
				if (! empty ( $ids )) {
					$this->db->table_name = $table_name;
					$ids = implode ( '\',\'', $ids );
					$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
					
					if (! empty ( $r )) {
						foreach ( $mhdata as $k => $v ) {
							if (isset ( $r [$v ['id']] ))
								$mhdata [$k] = array_merge ( $r [$v ['id']], $mhdata [$k] );
						}
					}
				}
				
			}
			
			$pages=pages($num,$this->pages);
//			include template ( 'content', 'type_bg' );
		} */
		
	}
	public function status(){
		$page=intval($_GET['page'])>0?intval($_GET['page']):1;
		$state=$_GET['state'];
		$where = "status = '99' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";	
		include template ( 'content', 'state_bg' );
	}

	public function dm_status(){
		$page=intval($_GET['page'])>0?intval($_GET['page']):1;
		$top_catid = '32';
		$state=$_GET['state'];		
		$where = '';
		if(isset($state)){
			$where = "state=".$state;
		}
		include template ( 'content', 'dm_state_bg' );
	}

	public function dm_area(){
		$top_catid = '32';
		$page=intval($_GET['page'])>0?intval($_GET['page']):1;
		$area=$_GET['area'];
		$areaname =  get_linkage($area,'3360');
		$where = '';
		if(!empty($area)){
			$where = 'CartoonArea = '.$area;
		}
		include template ( 'content', 'dm_area_bg' );
	}
	public function mh_area() {
		$table_name=$this->db->table_name;
		//$this->db->table_name = $this->db->table_name;
		$top_catid = '13';
		$area=$_GET['area'];
		$page=intval($_GET['page'])>0?intval($_GET['page']):1;
		if (intval ( $area ) > - 1) {
			$where = "status = '99' AND region = '".$area."' AND type_id NOT IN(".dimplode(dexplode(NOT_ALLOW)).")";	
			$num = $this->db->count ($where );
			$mhdata = $this->db->listinfo ($where, ' id desc', $page,'16' );
			$ids = array ();
			
			if ($mhdata) {
				foreach ( $mhdata as $v ) {
					if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
						$ids [] = $v ['id'];
					} else {
						continue;
					}
				}
				//将附表数据也取出来
				if (! empty ( $ids )) {
					$this->db->table_name = $table_name . '_data';
					$ids = implode ( '\',\'', $ids );
					$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
					
					if (! empty ( $r )) {
						foreach ( $mhdata as $k => $v ) {
							if (isset ( $r [$v ['id']] ))
								$mhdata [$k] = array_merge ( $r [$v ['id']], $mhdata [$k] );
						}
					}
				}
			
			}
			$pages=pages($num,$page);
			include template ( 'content', 'mh_area_bg' );
		}

	}
	
	public function search() {
		if ($_GET ['title'] && ! empty ( $_GET ['title'] )) {
			$tablename = $this->db->table_name;
			$title = iconv ( "gbk", "utf-8", $_GET ['title'] );
			if ($_GET ['type'] == 1) {
				$this->db->table_name = $this->db->table_name . '_data';
				if($_GET['ParentID']==2||$_GET['ParentID']==6||$_GET['ParentID']==7){
					$num = $this->db->count ( "tid<11 and Author like '%$title%'" );
					$searchdata = $this->db->listinfo ( "tid<11 and Author like '%$title%'", '', $this->pages );
				}else{
					$num = $this->db->count ( "Author like '%$title%'" );
					$searchdata = $this->db->listinfo ( "Author like '%$title%'", '', $this->pages );
				
				}
				if ($searchdata) {
					
					$this->db->table_name = $tablename;
					foreach ( $searchdata as $v ) {
						if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
							$ids [] = $v ['id'];
						} else {
							continue;
						}
					}
					//将附表数据也取出来
					if (! empty ( $ids )) {
						
						$ids = implode ( '\',\'', $ids );
						$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
						if (! empty ( $r )) {
							foreach ( $searchdata as $k => $v ) {
								if (isset ( $r [$v ['id']] ))
									$searchdata [$k] = array_merge ( $r [$v ['id']], $searchdata [$k] );
							}
						}
					}
					
					foreach ( $searchdata as $v ) {
						preg_match ( '/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/', $v ['thumb'], $matches );
						if ($matches [0])
						$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"/>';
					else
					$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . "http://manhuacheng.com".$v ['thumb'] . '"/>';
						
//						$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"><url></url></Book>';
					}
					echo $this->xmlstr . '<BookList Count="' . $num . '" Page="' . ($this->pages - 1) . '">' . $list . '</BookList>';
				
				} else
					echo $this->xmlstr . '<BookList></BookList>';
			} elseif ($_GET ['type'] == 0) {
					$num = $this->db->count ( "title like '%$title%'" );
					$searchdata = $this->db->listinfo ( "title like '%$title%'", '', $this->pages );
		
				if ($searchdata) {
					$this->db->table_name = $this->db->table_name . '_data';
					
					foreach ( $searchdata as $v ) {
						if (isset ( $v ['id'] ) && ! empty ( $v ['id'] )) {
							$ids [] = $v ['id'];
						} else {
							continue;
						}
					}
					//将附表数据也取出来
					if (! empty ( $ids )) {
						
						$ids = implode ( '\',\'', $ids );
						$r = $this->db->select ( "`id` IN ('$ids')", '*', '', '', '', 'id' );
						if (! empty ( $r )) {
							foreach ( $searchdata as $k => $v ) {
								if(($_GET['ParentID']==2||$_GET['ParentID']==6||$_GET['ParentID']==7)&&$r[$v['id']]['tid']==11){
									unset($searchdata [$k]);// = array_merge ( $r [$v ['id']], $nbdata [$k] );
								}else{
									$searchdata [$k] = array_merge ( $r [$v ['id']], $searchdata [$k] );
								}
//								if (isset ( $r [$v ['id']] ))
//									$searchdata [$k] = array_merge ( $r [$v ['id']], $searchdata [$k] );
							}
						}
					}
					foreach ( $searchdata as $v ) {
						preg_match ( '/[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/', $v ['thumb'], $matches );
						if ($matches [0])
							$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"><url></url></Book>';
						else
						$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . "http://manhuacheng.com".$v ['thumb'] . '"><url></url></Book>';
					//$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['title'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" State="' . $v ['state'] . '" Image="' . $v ['thumb'] . '"><url></url></Book>';
					}
					echo $this->xmlstr . '<BookList Count="' . $num . '" Page="' . ($this->pages - 1) . '">' . $list . '</BookList>';
				
				} else
					echo $this->xmlstr . '<BookList></BookList>';
			
			}
		}
	}
	public function manhua() {
		if ($_GET ['id']) {
			$id = intval ( $_GET ['id'] );
			$this->db->set_model ( 12 );
			$mhdata = $this->db->get_one ( 'id=' . $_GET ['id'] );
			
			if ($mhdata) {
				$this->db->table_name = $this->db->table_name . '_data';
				$r = $this->db->select ( "`id` = " . $_GET ['id'], '*', '', '', '', 'id' );
				
				if (! empty ( $r )) {
					if ($mhdata ['id'] == $r [$_GET ['id']] ['id'])
						$mhdata = array_merge ( $r [$_GET ['id']], $mhdata );
				}
				$this->db->set_model ( 13 );
				$r2 = $this->db->select ( "`manhuaid` = " . $mhdata ['id'], '*', '', 'id ASC', '', 'id' );
				
				//构造章节列表
				$l = '<Book Typeid="' . $mhdata ['tid'] . '" id="' . $mhdata ['id'] . '" Author="' . $mhdata ['Author'] . '" State="' . $mhdata ['state'] . '" Rem="' . $mhdata ['description'] . '" Name="' . $mhdata ['title'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $mhdata ['updatetime'] ) . '" Image="' . $mhdata ['Url'] . '">';
				//$mdata ['CartoonImage'] = eval ( "return " . stripcslashes ( $mdata ['CartoonImage'] ) . ";" );
				

				$r3 = array_slice ( $r2, 0, count ( $r2 ) );
				foreach ( $r3 as $key => $v ) {
					if ($key == 0)
						$l1 .= '<Chapter NO="0" id="' . $v ['id'] . '" Point="2" Name="' . $v ['title'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" Url="" />';
					else
						$l1 .= '<Chapter NO="1" id="' . $v ['id'] . '" Point="2" Name="' . $v ['title'] . '" UpdateTime="' . date ( "Y-m-d H:i:s", $v ['updatetime'] ) . '" Url="" />';
				
				}
				echo $this->xmlstr . $l . $l1 . '</Book>';
			
			} else
				echo $this->xmlstr . '<Chapter Count="0"></Chapter>';
		
		}
	
	}
	public function recommand(){
		if($_GET['id']){
			$id = intval ( $_GET ['id'] );
			$this->db->set_model ( 12 );
			$this->db->table_name = $this->db->table_name . '_data';
			$this->db->update(array('recnum'=>'+=1'),array('id'=>$id));
			$r=$this->db->get_one('id='.$id,'id,recnum');
			if($r)echo $r['recnum']; 
		}else 'error';
	}
	
	/*
	  * 邀请下载链接
	  */
	public function ic() {
		if ($_GET ['u'])
			$userid = $_GET ['u'];
		if ($userid) {
			$ip = ip ();
			$this->ic_db = pc_base::load_model ( 'invitclick_model' );
			$url = $_SERVER ['HTTP_REFERER'];
			$time = time ();
			$this->m_db = pc_base::load_model ( 'member_model' );
			$r = $this->ic_db->get_one ( 'userid=' . $userid, 'ip,inputime', 'inputime DESC' );
			if ($ip == $r ['ip'] && ($time - $r ['inputime'] > 3600)) {
				if ($userid)
					$this->m_db->update ( array ('amount' => '+=1', 'point' => '+=1' ), array ('userid' => $userid ) );
				$this->ic_db->insert ( array ('userid' => $userid, 'ip' => $ip, 'inputime' => $time, 'url' => $url ) );
				header ( "Location:".DOWNLOADURL );
			} elseif ($ip != $r ['ip']) {
				//并给userid加金币
				if ($userid)
					$this->m_db->update ( array ('amount' => '+=1' ), array ('userid' => $userid ) );
				$this->ic_db->insert ( array ('userid' => $userid, 'ip' => $ip, 'inputime' => $time, 'url' => $url ) );
				//			
				header ( "Location:".DOWNLOADURL );
			} else {
				header ( "Location:".DOWNLOADURL );
			}
		} else {
		
			header ( "Location:".DOWNLOADURL );
		}
	}
	public function addpoint() {
		//如有uname加积分
		$this->member_db = pc_base::load_model ( 'member_model' );
		if (isset ( $_GET ['u'] ))
			$userid = $_GET ['u'];
		$p = intval ( $_GET ['p'] );
		if ($p > 0) {
			if (isset ( $userid )) {
				//会员积分+5点
				//加一步要在这个登录日志中查询到用户
				$this->member_db->update ( array ('point' => '+=' . $p, 'amount' => '+=' . $p ), array ('userid' => $userid ) );
			}
		}
	
	}
	public function subpoint($uid, $point) {
		//如有uname加积分
		$this->member_db = pc_base::load_model ( 'member_model' );
		$this->member_db->update ( array ('amount' => '-=' . $point ), array ('userid' => $uid ) );
	}
	
	
	//	public function 

	public function rank() {
		$table_name='tt_Cartoon';
		$this->db->table_name =$table_name;
		$wherearr = array();
		$wherearr[] = 'status = 99';
		$typeid = $_GET['type'];
		if(!empty($typeid)){
			$wherearr[] = 'type_id = '.$typeid;
		}else{
			$wherearr[] = 'type_id NOT IN('.dimplode(dexplode(NOT_ALLOW)).')';	
		}
	//	var_dump($wherearr);
		$top_catid = '74';
		$sql = implode(" AND ",$wherearr);
		$updatelist = $this->db->select ( $sql, '*', '20', 'views desc', '', 'id' );
	//	var_dump($updatelist);
   //     echo $sql;
		$pages=pages($num,$this->page);
		   
		include template ( 'content', 'page_rank' );
	}
}
?>
