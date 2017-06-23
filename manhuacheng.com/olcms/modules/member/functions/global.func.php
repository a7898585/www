<?php
function auth_data($data) {
	define ( 'APPID', pc_base::load_config ( 'system', 'uc_appid' ) );
	$s = $sep = '';
	foreach ( $data as $k => $v ) {
		if (is_array ( $v )) {
			$s2 = $sep2 = '';
			foreach ( $v as $k2 => $v2 ) {
				$s2 .= "$sep2{$k}[$k2]=" . stripslashes ( $v2 );
				$sep2 = '&';
			}
			$s .= $sep . $s2;
		} else {
			$s .= "$sep$k=" . stripslashes ( $v );
		}
		$sep = '&';
	}
	$auth_s = '&appid=' . APPID . '&data=' . urlencode ( sys_auth ( $s ) );
	return $auth_s;
}
function getavatar_upload_html($uid) {
	$auth_data = auth_data ( array ('userid' => $uid ) );
	$js_path = JS_PATH;
	$img_path = IMG_PATH;
	$upurl = base64_encode ( '/index.php?m=member&c=index&a=uploadavatar&auth_data=' . $auth_data );
	$str = <<<EOF
				<div id="uc_uploadavatar_flash"></div>
				<script language="javascript" type="text/javascript" src="{$js_path}swfobject.js"></script>
				<script type="text/javascript">
					var flashvars = {
						'upurl':"{$upurl}&callback=return_avatar&"
					}; 
					var params = {
						'align':'middle',
						'play':'true',
						'loop':'false',
						'scale':'showall',
						'wmode':'window',
						'devicefont':'true',
						'id':'Main',
						'bgcolor':'#ffffff',
						'name':'Main',
						'allowscriptaccess':'always'
					}; 
					var attributes = {
						
					}; 
					swfobject.embedSWF("{$img_path}main.swf", "myContent", "490", "434", "9.0.0","{$img_path}expressInstall.swf", flashvars, params, attributes);
				
					function return_avatar(data) {
						if(data == 1) {
							window.location.reload();
						} else {
							alert('failure');
						}
					}

				</script> 
EOF;
	return $str;
}
?>