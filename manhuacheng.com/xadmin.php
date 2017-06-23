<?php
if(!function_exists('xcache_count')){
	echo "服务器没有安装Xcache";exit();
}
$pcnt = xcache_count(XC_TYPE_PHP);
for ($cacheid = 0; $cacheid < $pcnt; $cacheid ++) {
	  xcache_clear_cache(0, $cacheid);
}
echo "Xcache清理成功-".date('H:i:s',time());exit();
?>
