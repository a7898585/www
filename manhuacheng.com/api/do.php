<?php
function SMsg($msg, $gourl, $onlymsg = 0, $limittime = 0) {
	$cfg_ver_lang = 'utf-8';
	$htmlhead = "<html>\r\n<head>\r\n<title>网站系统提示</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\" />\r\n";
	$htmlhead .= "<base target='_self'/>\r\n</head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
	$htmlfoot = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";
	if ($limittime == 0)
		$litime = 10;
	else
		$litime = $limittime;
	
	if ($gourl == "-1") {
		$gourl = "javascript:history.go(-1);";
	}
	if ($gourl == "" || $onlymsg == 1) {
		$msg = "<script>alert(\"" . str_replace ( "\"", "“", $msg ) . "\");</script>";
	} else {
		$func = "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
		$rmsg = $func;
		$rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #87CFE7;border-top:1px solid #87CFE7;border-right:1px solid #87CFE7;background-color:#DBF0FD;'>系统提示信息：</div>\");\r\n";
		$rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #87CFE7;background-color:#f9fcf3'><br/><br/>\");\r\n";
		$rmsg .= "document.write(\"" . str_replace ( "\"", "“", $msg ) . "\");\r\n";
		$rmsg .= "document.write(\"";
		if ($onlymsg == 0) {
			if ($gourl != "javascript:;" && $gourl != "") {
				$rmsg .= "<br/><br/><a href='" . $gourl . "'>如果你的浏览器没反应，请点击这里...</a>";
			}
			$rmsg .= "<br/><br/></div>\");\r\n";
			if ($gourl != "javascript:;" && $gourl != "") {
				$rmsg .= "setTimeout('JumpUrl()',$litime);";
			}
		} else {
			$rmsg .= "<br/><br/></div>\");\r\n";
		}
		$msg = $htmlhead . $rmsg . $htmlfoot;
	}
	echo $msg;
	exit ();
}

function ImgUp($img) {
	$newfolder = date ( "Ymd" );
	folder ( OLCMS_PATH . "/uploadfile/{$newfolder}" );
	$b =file_get_contents ( $img );
	if (! $b) {
		return $content;
	}
	$pos = strrpos ($img, "." ); //取得文件名中后缀名的开始位置
	$ext = substr ($img, $pos ); //取得后缀名，包括点号
	$name = md5 ($img);
	$m =OLCMS_PATH . "/uploadfile/{$newfolder}/" . $name . $ext;
	$n = "/uploadfile/{$newfolder}/" . $name . $ext;
	file_put_contents ( $m, $b );
	return $n;
}
function folder($path) {
	
	$newfolder = date ( "Ymd" );
	
	if (! file_exists ( $path ) || ! is_dir ( $path )) {
		if (@mkdir ( $path, 0777 )) {
			@chmod ( $path, 0777 );
		}
	}
}
$db = pc_base::load_model ( 'content_model' );
$db->table_name='tt_Cartoon';

$id=intval($_GET['id']);

$a =$db->get_one("sab=1",'id,thumb','id asc');
$data['sab']=2;
if(strpos('_'.$a['thumb'],'http')!==false){
      $img= ImgUp($a['thumb']);
      $data['thumb']=$img;
      $data['sab']=2;
     // var_dump($data);
    //  exit();
}
    $db->update($data,"id={$a[id]}");

$id=$a['id'];

SMsg('', '/api.php?op=do');