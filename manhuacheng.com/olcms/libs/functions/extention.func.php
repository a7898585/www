<?php
/**
 * extention.func.php 用户自定义函数库
 *
 * @copyright			(C) 2005-2010 OLCMS
 * @license				http://www.olcms.cn/license/
 * @lastmodify			2010-10-27
 */
/*去除空格，包括中文字符*/
function _trim($str) {
	if (is_array ( $str )) {
		foreach ( $str as $key => $val ) {
			$str [$key] = _trim ( $val );
		}
	} else {
		$str = str_replace ( '&amp;', '&', $str );
		$str = preg_replace ( '/([\n\r\t ]+)/is', ' ', $str );
		$str = str_replace ( array ('　', ' ', '&nbsp;' ), ' ', $str );
		$str = trim ( $str );
	}
	return $str;
}
//转换选项字段为数组
function getboxname($modelid, $field) {
	$fulltext_array = getcache ( 'model_field_' . $modelid, 'model' );
	$m = $fulltext_array [$field] ['setting'];
	$m = string2array ( $m );
	$options = explode ( "\n", $m ['options'] );
	foreach ( $options as $_k ) {
		$v = explode ( "|", $_k );
		$k = trim ( $v [1] );
		$option [$k] = $v [0];
	}
	return $option;
}
//字数统计,汉字1个，字母0.5个
function get_num($string) {
	$string=strip_tags($string);
	$strlen = strlen($string);
	$string = str_replace(array(' ','	','　','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ',' ',' ',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	$n = $tn = $noc = 0;
		while($n < $strlen) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; ++$n; $noc += 0.5;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 1;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 1;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 1;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 1;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 1;
			} else {
				++$n;
			}
		}
		return $noc;
}
//根据modelid得出catid
function modelid_to_catid($modelid) {
	$categorys = getcache('category_content','commons');
	foreach($categorys as $c=>$v){
		if($v['modelid']==$modelid)
		$m[] = $c;
	}
	return $m;
}
//内容页实时浏览记录
function view_memory($title) {
		$pageurl = $_SERVER ["REQUEST_URI"];
		$fromurl = $_SERVER ['HTTP_REFERER'];
		$nowtime = time ();
		$minid = array();
		pc_base::load_sys_class("get_model", "model", 0);
		$db = new get_model();
		$db->sql_query ( "select id from olcms_views_memory order by pbtime asc limit 1" );
		while(($rs = $db->fetch_next()) != false) {
		$minid[] = $rs;
		}
		if (! $minid) {
			for($i = 1; $i < 9; $i ++) {
				$db->sql_query ( "insert into olcms_views_memory (id,pbtime) values ($i,$i)" );
			}
		}
		if (strpos ( $fromurl, 'olcms' )) {
			$db->sql_query ( "update olcms_views_memory set title='$title',url='$pageurl',pbtime='$nowtime' where id={$minid[0][id]}");
	}
}

/**
 * 检查阅读,发布权限
 *
 */
function view_priv($catid, $action = 'visit', $is_admin = 0) {
	$catid = intval ( $catid );
	if (! $catid)
		return '-2';
	$_groupid = param::get_cookie ( '_groupid' );
	$_groupid = intval ( $_groupid );
	if ($_groupid == 0)
		$_groupid = 8;
	if (! $_groupid)
		return '-1';
	$result = getcache ( 'priv_' . $catid, 'member' );
	if ($result == FALSE) {
		$category_priv_db = pc_base::load_model ( 'category_priv_model' );
		$result = $category_priv_db->select ( array ('catid' => $catid, 'is_admin' => $is_admin, 'action' => $action ) );
		if ($result) {
			foreach ( $result as $r ) {
				if ($r ['roleid'] == $_groupid)
					return '1';
			}
			return '-1';
		} else {
			return '1';
		}
	} elseif ($result ['action'] [$action] && ! empty ( $result )) {
		if (in_array ( $action, $result [$_groupid] ))
			return '1';
		return '-1';
	} elseif (! in_array ( $action, $result ['action'] )) {
		return '1';
	} elseif (empty ( $result [$_groupid] )) {
		return '-1';
	}
}

//获取一个字段调用的type信息
function get_content_type($typeid, $catid) {
	$CATEGORYS = getcache ( 'category_content', 'commons' );
	$usable_type = $CATEGORYS [$catid] ['usable_type'];
	$usable_array = array ();
	if ($usable_type)
		$usable_array = explode ( ',', $usable_type );
	$type_data = getcache ( 'type_content', 'commons' );
	foreach ( $type_data as $_key => $_value ) {
			if(in_array($_key,$usable_array) && $_value['parentid']==$typeid) $data[$_key] = $_value['name'];
		}
		return $data;
}

function cache_hit($name,$modelid,$id,$fields,$delay='55'){
	$logfile = './caches/caches_content/caches_data/'.$name.'.log';
	if (substr ( SYS_TIME, - 2 ) == $delay) {
		$viewlog = $viewarray = array ();
		$contentdb = pc_base::load_model('content_model');
		$CATEGORYS = getcache('category_content','commons');
		$contentdb->set_model($modelid);
		$newlog = OLCMS_PATH . $logfile . random ( 6 );
		if (@rename ( OLCMS_PATH . $logfile, $newlog )) {
			$viewlog = file ( $newlog );
			@unlink ( $newlog );
			if (is_array ( $viewlog ) && ! empty ( $viewlog )) {
				$viewlog = array_count_values ( $viewlog );
				foreach ( $viewlog as $contentid => $views ) {
					$viewarray [$views] .= ($contentid > 0) ? ',' . intval ( $contentid ) : '';
				}
				foreach ( $viewarray as $views => $ids ) {
					$contentdb->update ( array ($fields => '+=' . $views ), 'id IN (0' . $ids . ')' );
				}
			}
		}
	}
	if (@$fp = fopen ( OLCMS_PATH . $logfile, 'a' )) {
		fwrite ( $fp, "$id\n" );
		fclose ( $fp );
	}
}

//判断是爬虫还是访客
function getrobot() {
	$kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
	$kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
	if(stripos($_SERVER ['HTTP_USER_AGENT'], 'spider')!==false){
		return TRUE;
	}elseif (preg_match ( "/($kw_browsers)/i", $_SERVER ['HTTP_USER_AGENT'] )) {
		return FALSE;
	} elseif (preg_match ( "/($kw_spiders)/i", $_SERVER ['HTTP_USER_AGENT'] )) {
		return TRUE;
	} else {
		return FALSE;
	}
}

//判断数组（多维）数值是否为空的函数
function array_is_null($arr = null){  
        if(is_array($arr)){  
           foreach($arr as $k=>$v){  
            if($v&&!is_array($v)){  
                return false;  
            }  
               $t = self::array_is_null($v);  
            if(!$t){  
                return false;  
            }  
            }  
            return true;  
        }elseif(!$arr){  
            return true;  
                }else{  
            return false;  
        }  
    }

function set_search_record($modelid,$word='',$url=''){
	$writefile = './caches/caches_content/caches_log/w_' . $modelid . '.log';
	$readfile = './caches/caches_content/caches_log/r_' . $modelid . '.log';
	if(!empty($word)){
	if (substr ( SYS_TIME, - 2 ) == '00') {
		$records = array ();
		$newlog = OLCMS_PATH . $writefile . random ( 6 );
		if (@rename ( OLCMS_PATH . $writefile, $newlog )) {
			$records = file ( $newlog );			
			if (is_array ( $records ) && ! empty ( $records[0] )) {
				@unlink ( $readfile );
				@rename ( $newlog, OLCMS_PATH . $readfile );
			}
		}
	}
	if (@$fp = fopen ( OLCMS_PATH . $writefile, 'a' )) {
		fwrite ( $fp, "{$word}|{$url}\n" );
		fclose ( $fp );
	}
	}
}
 function get_search_record($modelid,$num=10){
	$readfile = './caches/caches_content/caches_log/r_' . $modelid . '.log';
	if(!file_exists(OLCMS_PATH . $readfile)) return false;
		$rs = file ( $readfile );
		$i=1;
		$data = array();
		foreach($rs as $v){
			$record=explode('|',$v);
			if(isset($data[$record[0]])) continue;
			++$i;
			$data[$record[0]]=$record[1];
			if($i>=$num) break;
		}
		return $data;
}
?>