<?php
/*
 * 返回最近更新的100条漫画列表
 * @result xml
 */

require_once 'MHinclude/common.inc.php';

$msg = '<?xml version="1.0" encoding="utf-8"?>';

$db = new db ( $db_config );
$page = intval ( $page ) > - 1 ? intval ( $page ) : 0;
$s=$page*20;
//		拼凑sql语句
$strsql = "select id,CartoonName,Author,UpdateTime,CartoonState,CartoonImage from mh_cartoon order by UpdateTime DESC limit $s,20";
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
	$db->Close ();
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
	}
	$list .= '</Book>';
}
$xml = $msg . '<BookList Count=100 Page='.$page.'>' . $list . '</BookList>';
echo $xml;

$db->Close ();
unset ( $db, $result, $urlres, $list, $xml );

?>