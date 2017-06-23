<?php
defined('IN_MHESITE') or exit('No permission resources.');
//将字符转义
function daddslashes($string, $force = 0) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}

	return $string;
}
//防止注入攻击
function inject_check($string) {
	$ck = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $string);    // 进行过滤
	if($ck){
		$reurl="http://".$_SERVER["HTTP_HOST"];
		echo("<script type='text/javascript'> alert('error!'); location.href='$reurl'; </script>");
		die("error!");
	}
	return $string;
}


