<?php
/*
 * 搜索漫画名
 * @pram string title 搜索名
 * @pram int page  第几分页
 * @pram smallint type  检索类型
 * @result xml
 */

require_once 'MHinclude/common.inc.php';

$msg = '<?xml version="1.0" encoding="utf-8"?>';
//title为空的情况
if ($title && ! empty ( $title )) {
	$title=iconv("gbk","utf-8",$title);
//	分页情况
	$page = intval ( $page ) > - 1 ? intval ( $page ) : 0;
	$s=$page*20;
	//搜索作者
	if ($type == 1) {
		$db = new db ( $db_config );
		$numsql = "select count(id) as n from mh_cartoon where Author like '%$title%'";
		$nums = $db->GetOne ( $numsql );
		//加一句 结果为0直接结束。
		//		拼凑sql语句
		$strsql = "select id,CartoonName,Author,UpdateTime,CartoonState,CartoonImage from mh_cartoon where Author like '%$title%' limit $s,20";
		$db->Execute ( 'rs', $strsql );
		while ( $row = $db->GetArray ( 'rs' ) ) {
			$result [] = $row;
		}
		if ($result) {
			//将第一次搜索出来的结果再次去查询
			foreach ( $result as $key => $v ) {
				if ($v ['id']) {
					$urlsql = "select MHid,url from mh_cartoondetail where MHid='" . $v ['id'] . "'";
					
					$db->Execute ( 'urlrs', $urlsql );
					while ( $row = $db->GetArray ( 'urlrs' ) ) {
						$urlres [] = $row;
					}
				}
			
			}
		} else {
			$xml = $msg . '<BookList Count="0"></BookList>';
			echo $xml;
			exit;
		}
		//将两次的结果对应起来，并xml格式化
		foreach ( $result as $v ) {
			$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['CartoonName'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . $v ['UpdateTime'] . '" State="' . $v ['CartoonState'] . '" Image="' . $v ['CartoonImage'] . '">';
			if ($urlres) {
				foreach ( $urlres as $v1 ) {
					if (($v ['id'] == $v1 ['MHid']) && ($v1 ['url'])) {
						$list .= '<url>' . $v1 ['url'] . '</url>';
					}
				}
				$list .= '</Book>';
			}
		}
		$xml = $msg . '<BookList Count="' . $nums ['n'] . '" Page="' . $page . '">' . $list . '</BookList>';
		echo $xml;
		
		$db->Close ();
		unset ( $db, $result, $urlres, $list, $xml );
	} //搜索漫画名跟别名
	elseif ($type == 0) {
		$db = new db ( $db_config );
		$numsql = "select count(id) as n from mh_cartoon  where concat(CartoonName,OtherName) like '%$title%'";
		$nums = $db->GetOne ( $numsql );
		$strsql = "select id,CartoonName,Author,UpdateTime,CartoonState,CartoonImage from mh_cartoon  where concat(CartoonName,OtherName) like '%$title%' limit $s,20";
		$db->Execute ( 'rs', $strsql );
		while ( $row = $db->GetArray ( 'rs' ) ) {
			$result [] = $row;
		}
		
		if($result){
		foreach ( $result as $key => $v ) {
			if ($v ['id']) {
				$urlsql = "select MHid,url from mh_cartoondetail where MHid='" . $v ['id'] . "'";
				
				$db->Execute ( 'urlrs', $urlsql );
				while ( $row = $db->GetArray ( 'urlrs' ) ) {
					$urlres [] = $row;
				}
			}
		
		}
		}else {
			$xml=$msg.'<BookList Count="0"></BookList>';
			echo $xml;
			$db->Close ();
			exit;
			
		}
		//xml格式化
		foreach ( $result as $v ) {
			$list .= '<Book id="' . $v ['id'] . '" Name="' . $v ['CartoonName'] . '" Author="' . $v ['Author'] . '" UpdateTime="' . $v ['UpdateTime'] . '" State="' . $v ['CartoonState'] . '" Image="' . $v ['CartoonImage'] . '">';
			foreach ( $urlres as $v1 ) {
				if (($v ['id'] == $v1 ['MHid']) && ($v1 ['url'])) {
					$list .= '<url>' . $v1 ['url'] . '</url>';
				}
			}
			$list .= '</Book>';
		}
	
		$xml = $msg . '<BookList Count="' .$nums ['n']. '" Page="' . $page . '">' . $list . '</BookList>';
		echo $xml;
		
		$db->Close ();
		unset ( $db, $result, $urlres, $list, $xml );
	}

} else{
	$xml='<BookList></BookList>';
	echo $xml;
}
?>