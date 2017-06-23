<?php
defined ( 'IN_OLCMS' ) or exit ( 'No permission resources.' );
pc_base::load_app_class('admin','admin',0);
$updateHost = 'http://doc.258.com/olcms';
$roles = pc_base::load_config ( 'version' );
$charset = pc_base::load_config ( 'system', 'charset' );
$upTime = $roles ['pc_release'];
$oktime = substr ( $upTime, 0, 4 ) . '-' . substr ( $upTime, 4, 2 ) . '-' . substr ( $upTime, 6, 2 ) . '第' . substr ( $upTime, 8, 2 ).'次更新';
$message = '';
//函数库--------------------------------------------------------------------------------------------
function TestWriteAble($d) {
	$tfile = '_dedet.txt';
	$fp = @fopen ( $d .$tfile, 'w' );
	if (! $fp) {
		return false;
	} else {
		fclose ( $fp );
		$rs = @unlink ( $d .$tfile );
		return true;
	}
}

function GetDirName($filename) {
	$dirname = preg_replace ( "/([^\/]*)$/", '', $filename );
	$dirname = substr($dirname,0,-1);
	return $dirname;
}
function TestIsFileDir($dirname)
{
	$dirs = array('name'=>'','isdir'=>false,'writeable'=>false);
	$dirs['name'] =  trim($dirname);
	if(is_dir(OLCMS_PATH.$dirname))
	{	
		$dirs['isdir'] = true;
		$dirs['writeable'] = TestWriteAble(OLCMS_PATH.$dirname);
	}
	return $dirs;
}

function MkTmpDir($tmpdir, $filename) {
	$basedir = OLCMS_PATH . 'caches/bakup/' . $tmpdir;
	$dirname = trim ( preg_replace ( "/[\\/]{1,}/", '/', $filename ) );
	$dirname = preg_replace ( "/([^\/]*)$/", "", $dirname );
	if (! is_dir ( $basedir )) {
		mkdir ( $basedir, 0777 );
	}
	if ($dirname == '') {
		return true;
	}
	$dirs = explode ( '/', $dirname );
	$curdir = $basedir;
	foreach ( $dirs as $d ) {
		$d = trim ( $d );
		if (empty ( $d ))
			continue;
		$curdir = $curdir . '/' . $d;
		if (! is_dir ( $curdir )) {
			mkdir ( $curdir, 0777 ) or die ( $curdir );
		}
	}
	return true;
}
function _sql_execute($sql,$r_tablepre = '',$s_tablepre = 'olcms_') {
    $sqls = _sql_split($sql,$r_tablepre,$s_tablepre);
	if(is_array($sqls))
    {
		foreach($sqls as $sql)
		{
			if(trim($sql) != '')
			{
				mysql_query($sql);
			}
		}
	}
	else
	{
		mysql_query($sqls);
	}
	return true;
}
function _sql_split($sql,$r_tablepre = '',$s_tablepre='olcms_') {
	global $dbcharset,$tablepre;
	$r_tablepre = $r_tablepre ? $r_tablepre : $tablepre;
	if(mysql_get_server_info() > '4.1' && $dbcharset)
	{
		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".$dbcharset,$sql);
	}
	
	if($r_tablepre != $s_tablepre) $sql = str_replace($s_tablepre, $r_tablepre, $sql);
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query)
	{
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query)
		{
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}
//END--------------------------------------------------------------------------------------------

/**
用AJAX获取最新版本信息
 */
pc_base::load_sys_class ( 'httpdown', '', 0 );
$step = trim ( $_REQUEST ['step'] ) ? trim ( $_REQUEST ['step'] ) : 1;
if ($step == '1') {
	//下载远程数据
	$dhd = new DedeHttpDown ();
	$dhd->OpenUrl ( $updateHost . '/verinfo.txt' );
	$updateHost . '/verinfo.txt';
	$verlist = trim ( $dhd->GetHtml () );
	$dhd->Close ();
	$verlist = preg_replace ( "/[\r\n]{1,}/", "\n", $verlist );
	$verlists = explode ( "\n", $verlist );
	
	//分析数据
	$updateVers = array ();
	$upitems = $lastTime = '';
	$n = 0;
	
	foreach ( $verlists as $verstr ) {
		if (empty ( $verstr ) || preg_match ( "/^\/\//", $verstr )) {
			continue;
		}
		list ( $vtime, $vlang, $issafe, $vmsg ) = explode ( ',', $verstr );
		$vtime = trim ( $vtime );
		$vlang = trim ( $vlang );
		$issafe = trim ( $issafe );
		$vmsg = trim ( $vmsg );
		if ($vtime > $upTime) {
			$updateVers [$n] ['issafe'] = $issafe;
			$updateVers [$n] ['vmsg'] = $vmsg;
			$upitems .= ($upitems == '' ? $vtime : ',' . $vtime);
			$lastTime = $vtime;
			$updateVers [$n] ['vtime'] = substr ( $vtime, 0, 4 ) . '-' . substr ( $vtime, 4, 2 ) . '-' . substr ( $vtime, 6, 2 ) . '_' . substr ( $vtime, 8, 2 );
			$n ++;
		}
	}

	//判断是否需要更新，并返回适合的结果
	if ($n == 0) {
		$message1 = "<span><b>你系统版本最后更新时间为：{$oktime}，当前没有可用的更新</b></span>\r";
		$result = array ('code' => 'none', 'message' => $message1 );
		echo json_encode ( $result );
	} else {
		$message1 = "<div style='width:98%'><form name='fup' action='/api.php?op=version' method='post'>\r\n";
		$message1 .= "<input type='hidden' name='step' value='2' />\r\n";
		$message1 .= "<input type='hidden' name='vtime' value='$lastTime' />\r\n";
		$message1 .= "<input type='hidden' name='upitems' value='$upitems' />\r\n";
		$message1 .= "<div class='upinfotitle'>你系统版本最后更新时间为：{$oktime}，当前可用的更新有：</div>\r\n";
		foreach ( $updateVers as $vers ) {
			$style = '';
			if ($vers ['issafe'] == 1) {
				$style = "color:red;";
			}
			$message1 .= "<div style='{$style}' class='verline'>【" . ($vers ['issafe'] == 1 ? "安全更新" : "普通更新") . "】";
			$message1 .= $vers ['vtime'] . "，更新说明：{$vers['vmsg']}</div>\r\n";
		}
		$message1 .= "<div style='line-height:32px'><input type='submit' value=' 点击此获取所有更新文件，然后选择安装 ' style='cursor:pointer' />\r\n";
		$message1 .= " &nbsp; <input type='button' value=' 忽略这些更新 ' onclick='location.reload();'  style='cursor:pointer' /></div>\r\n";
		$message1 .= "</form></div>";
		$result = array ('code' => 'update', 'message' => $message1 );
		echo json_encode ( $result );
	}
	exit ();
} /**
获取升级文件列表
 */
elseif ($step == '2') {
	$upitemsArr = explode ( ',', $_POST ['upitems'] );
	rsort ( $upitemsArr );
	$author_key = pc_base::load_config ( 'system', 'author_key' );
	$tmpdir = substr ( md5 ( $author_key ), 0, 16 );
	
	$dhd = new DedeHttpDown ();
	$fileArr = array ();
	$f = 0;
	foreach ( $upitemsArr as $upitem ) {
		$durl = $updateHost . '/' . $charset . '/' . $upitem . '.file.txt';
		$dhd->OpenUrl ( $durl );
		$filelist = $dhd->GetHtml ();
		$filelist = trim ( preg_replace ( "/[\r\n]{1,}/", "\n", $filelist ) );
		if (! empty ( $filelist )) {
			$filelists = explode ( "\n", $filelist );
			foreach ( $filelists as $filelist ) {
				$filelist = trim ( $filelist );
				if (empty ( $filelist ))
					continue;
				$fs = explode ( ',', $filelist );
				if (empty ( $fs [1] )) {
					$fs [1] = $upitem . " 常规功能更新文件";
				}
				if (! isset ( $fileArr [$fs [0]] )) {
					$fileArr [$fs [0]] = $upitem . " " . trim ( $fs [1] );
					$f ++;
				}
			}
		}
	}
	$dhd->Close ();
	
	if ($f == 0) {
		$message = "<font color='green'><b>没发现可用的文件列表信息，可能是官方服务器存在问题，请稍后再尝试！</b></font>";
	} else {
		$message .= "<div style='width:98%'><form action='/api.php?op=version' method='post'>\r\n";
		$message .= "<input type='hidden' name='vtime' value='$_POST[vtime]' />\r\n";
		$message .= "<input type='hidden' name='step' value='3' />\r\n";
		$message .= "<input type='hidden' name='upitems' value='$_POST[upitems]' />\r\n";
		$message .= "<div>以下是需要下载的更新文件（路径相对于olcms的根目录）：</div>\r\n";
		$filelists = explode ( "\n", $filelist );
		foreach ( $fileArr as $k => $v ) {
			$message .= "<div><input type='checkbox' name='files[]' value='{$k}'  checked='checked' /> $k({$v})</div>\r\n";
		}
		$message .= "<div>";
		$message .= "文件临时存放目录：../caches/bakup/<input type='text' name='tmpdir' style='width:200px' value='$tmpdir' /><br />\r\n";
		$message .= "<input type='checkbox' name='skipnodir' value='1'  checked='checked' /> 跳过系统中没有的文件夹(通常是可选模块的补丁)</div>\r\n";
		$message .= "<div style='line-height:36px;'>&nbsp;\r\n";
		$message .= "<input type='submit' value='下载并应用这些补丁' style='cursor:pointer' />\r\n";
		$message .= "</form></div>";
	}
include OLCMS_PATH."api/update.tpl.php";
	exit ();
} elseif ($step == 3) {
	$cacheFiles = OLCMS_PATH . 'caches/bakup/updatetmp.inc';
	$skipnodir = (isset ( $_POST ['skipnodir'] ) ? 1 : 0);
	$files = $_POST ['files'];
	if (! isset ( $files )) {
		
		$doneStr = "<p align='center' style='color:red'><br />你没有指定任何需要下载更新的文件，是否跳过这些更新？<br /><br />";
		$doneStr .= "<a href='/api.php?op=version&step=4&vtime=$_POST[vtime]' class='np coolbg'>[跳过这些更新]</a> &nbsp; ";
		$doneStr .= "<a href='#' onclick='history.go(-2)' class='np coolbg'>[以后再进行操作]</a></p>";
		echo $doneStr;
	} else {
		$fp = fopen ( $cacheFiles, 'w' );
		fwrite ( $fp, '<' . '?php' . "\r\n" );
		fwrite ( $fp, '$tmpdir = "' . $_POST [tmpdir] . '";' . "\r\n" );
		fwrite ( $fp, '$vtime = ' . $_POST [vtime] . ';' . "\r\n" );
		$dirs = array ();
		$i = - 1;
		foreach ( $files as $filename ) {
			$tfilename = $filename;
			$curdir = GetDirName ( $tfilename );
			if (! isset ( $dirs [$curdir] )) {
				$dirs [$curdir] = TestIsFileDir ( $curdir );
			}
			if ($skipnodir == 1 && $dirs [$curdir] ['isdir'] == false) {
				continue;
			} else {
				$curdirs = OLCMS_PATH.$curdir;
				if(!file_exists($curdirs)) @mkdir ( $curdirs, 0777 );
				$dirs [$curdir] = TestIsFileDir ( $curdir );
			}
			
			$i ++;
			fwrite ( $fp, '$files[' . $i . '] = "' . $filename . '";' . "\r\n" );
		}
		fwrite ( $fp, '$fileConut = ' . $i . ';' . "\r\n" );
		
		$items = explode ( ',', $_POST ['upitems'] );
		foreach ( $items as $sqlfile ) {
			fwrite ( $fp, '$sqls[] = "' . $sqlfile . '.sql";' . "\r\n" );
		}
		fwrite ( $fp, '?' . '>' );
		fclose ( $fp );
		$dirinfos = '';
		if ($i > - 1) {
			$message = '<div>';
			$message .= "本次升级需要在下面文件夹写入更新文件，请注意文件夹是否有写入权限：<br />\r\n";
			foreach ( $dirs as $curdir ) {
				$message .= $curdir ['name'] . " 状态：" . ($curdir ['writeable'] ? "[√正常]" : "<font color='red'>[×不可写]</font>") . "<br />\r\n";
			}
			$message .= "</div>\r\n";
		}
		$message .= "<iframe name='stafrm' src='/api.php?op=version&step=4' frameborder='0' id='stafrm' width='100%'></iframe>\r\n";
	}
include OLCMS_PATH."api/update.tpl.php";
	exit ();
} /**
下载文件，具体操作步骤
function _Down() {  }
 */
else if ($step == 4) {
	//update_guide.php?dopost=down&curfile=0
	$msg = "如果检测时发现你没安装模块的文件夹有错误，可不必理会<br />";
	$msg .= "<a href='/api.php?op=version&step=5&curfile=0'>确认目录状态都正常后，请点击开始下载文件&gt;&gt;</a><br />";
	echo $msg;
	exit ();
} else if ($step == 5) {
	$cacheFiles = OLCMS_PATH . 'caches/bakup/updatetmp.inc';
	require_once ($cacheFiles);
	$curfile = intval ( $_GET ['curfile'] );
	if (empty ( $_GET['startup'] )) {
		if ($fileConut == - 1 || $curfile > $fileConut) {
			echo "已下载所有文件，开始下载数据库升级文件...";
			echo "<script>location.href='/api.php?op=version&step=5&startup=1'</script>";
			exit ();
		}
		
		//检查临时文件保存目录是否可用
		MkTmpDir ( $tmpdir, $files [$curfile] );
		$downfile = $updateHost.'/'.$charset.'/source/'.$files[$curfile];
		$dhd = new DedeHttpDown ();
		$dhd->OpenUrl ( $downfile );
		$dhd->SaveToBin ( OLCMS_PATH . 'caches/bakup/' . $tmpdir . '/' . $files [$curfile] );
		$dhd->Close ();
		$nextfile = ($curfile+1);
		echo "<script>location.href='/api.php?op=version&step=5&curfile=$nextfile'</script>";
		include OLCMS_PATH."api/update.tpl.php";
		exit ();
	} else {
		MkTmpDir ( $tmpdir, 'sql.txt' );
		$dhd = new DedeHttpDown ();
		$ct = '';
		foreach ( $sqls as $sql ) {
			$downfile = $updateHost.'/'.$charset.'/source/'.$sql;
			$dhd->OpenUrl ( $downfile );
			$ct .= $dhd->GetHtml ();
		}
		
		$dhd->Close ();
		$truefile = OLCMS_PATH . '/caches/bakup/' . $tmpdir . '/sql.txt';
		$fp = fopen ( $truefile, 'w' );
		fwrite ( $fp, $ct );
		fclose ( $fp );		
		echo $message = "完成所有远程文件获取操作：<a href='/api.php?op=version&step=6'>&lt;&lt;点击此开始直接升级&gt;&gt;</a><br />你也可以直接使用[../caches/bakup/{$tmpdir}]目录的文件手动升级。";	
		
		exit ();
	}
	exit ();
} /**
应用升级
function _ApplyUpdate() {  }
 */
else if ($step == 6) {
	$cacheFiles = OLCMS_PATH . 'caches/bakup/updatetmp.inc';
	require_once ($cacheFiles);
	if (empty($_GET['s'])) {
		$truefile = OLCMS_PATH . 'caches/bakup/' . $tmpdir . '/sql.txt';
		$fp = fopen ( $truefile, 'r' );
		$sql = @fread ( $fp, filesize ( $truefile ) );
		fclose ( $fp );
		if (!empty ( $sql )) {
		$default_db = pc_base::load_config('database','default');
		$dbcharset = $default_db['charset'];
		$tablepre = $default_db['tablepre'];
		$lnk = mysql_connect($default_db['dbhost'], $default_db['username'], $default_db['password']) or die ('Not connected : ' . mysql_error());
		$version = mysql_get_server_info();			
		if($version > '4.1' && $dbcharset) {
			mysql_query("SET NAMES '$dbcharset'");
		}			
		if($version > '5.0') {
			mysql_query("SET sql_mode=''");
		}			
		mysql_select_db($default_db['database']);
		if($sql)
		{
			_sql_execute($sql,$tablepre);
		}
		}
		echo "完成数据库更新，现在开始复制文件,<a href='/api.php?op=version&step=6&s=1'>点击开始</a>";
		exit ();
	} else {
		$sDir = OLCMS_PATH . 'caches/bakup/'.$tmpdir;
		$tDir = OLCMS_PATH;
		
		$badcp = 0;	
		if (isset ( $files ) && is_array ( $files )) {
			foreach ( $files as $f ) {
				$tf = $f;
				if (file_exists ( $sDir . '/' . $f )) {
					$a1 = MkTmpDir($tmpdir.'/old',$tf);
					$a2 = @copy ( $tDir . $tf, $sDir . '/old/' . $f );
					if($a2)
					$rs = @copy ( $sDir . '/' . $f, $tDir . $tf );
					if ($rs) {
						unlink ( $sDir . '/' . $f );
					} else {
						$badcp ++;
					}
				}
			}
		}
		$ver = array(
		'pc_version' => 'OLCMS_Beta',	//olcms 版本号
		'pc_release' => $vtime,	//olcms 更新日期
		);
		set_version($ver);	
		
		$badmsg = '！';
		if ($badcp > 0) {
			$badmsg = "，其中失败 {$badcp} 个文件，<br />请从临时目录[../data/{$tmpdir}]中取出这几个文件手动升级。";
		}
		echo "<span style='color:red'>成功完成升级{$badmsg}</span>";
		exit ();
	}
}
	function set_version($config) {
		$configfile = CACHE_PATH.'configs/version.php';
		$pattern = $replacement = array();
		foreach($config as $k=>$v) {
			if(in_array($k,array('pc_version','pc_release'))) {
				$v = trim($v);
				$configs[$k] = $v;
				$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
	        	$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";					
			}		
		}
		$str = file_get_contents($configfile);
		$str = preg_replace($pattern, $replacement, $str);
		return file_put_contents($configfile, $str, LOCK_EX);		
	}
?>