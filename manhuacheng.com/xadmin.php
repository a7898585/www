<?php
if(!function_exists('xcache_count')){
	echo "������û�а�װXcache";exit();
}
$pcnt = xcache_count(XC_TYPE_PHP);
for ($cacheid = 0; $cacheid < $pcnt; $cacheid ++) {
	  xcache_clear_cache(0, $cacheid);
}
echo "Xcache����ɹ�-".date('H:i:s',time());exit();
?>
