<?php 
    /*
     *   该功能是用来接收用户访问记录信息的 处理步骤  *  \
     *   1 接收验证 2 写到文件队列 更新对应的cache信息 * \
     */
	if(!class_exists('Memcache') || !function_exists('fsockopen'))
	{
		echo("Unable to load the requested base class: Memcache and function fsockopen");
		exit(-1);
	}
	//修改博客点击统计信息
	define('ROOT',dirname(__FILE__));
	require_once ROOT.'/inc/config.inc.php';
	require_once ROOT.'/cls/CMemcache.php';
	require_once ROOT.'/cls/CSocket.php';
	require_once ROOT.'/func/socket.func.php';
	require_once ROOT.'/func/commen.func.php';
	
	//扩大倍数，全局变量
	$_big = rand(1,9);
	
	//调试测试
	$_debug    = (isset($_GET['debug']))?  1:0;

	//请求地址不是博客过来的过滤掉 暂时屏蔽
	/*
	if(!preg_match("/^http:\/\/blog\.cnfol\.com(.*)$/i", $_SERVER['HTTP_REFERER']) && ($_debug == 0))
	{
		echo '<!--you are a cheater!!! -->';
		exit(-1);
	}
	*/
	//获取信息
	$_GET = filterInput($_GET);
	$duserid   = (isset($_GET['duid']) && is_numeric($_GET['duid']))? $_GET['duid']:'0';   //博客 文章属主ID
	$memberid  = (isset($_GET['mid']) && is_numeric($_GET['mid']))? $_GET['mid']:'0';    
	$articleid = (isset($_GET['aid']) && is_numeric($_GET['aid']))? $_GET['aid']:'0';
	$domain    = (isset($_GET['dom']))? urldecode($_GET['dom']):'0';
	$blogname  = (isset($_GET['bn']))? urldecode($_GET['bn']):'0';
	$vuserid   = (isset($_GET['vuid']) && is_numeric($_GET['vuid']))? $_GET['vuid']:'0';   //访客
	$username  = (isset($_GET['un']))? urldecode($_GET['un']):'0';
	$nickname  = (isset($_GET['nn']))? urldecode($_GET['nn']):'0';
	
	$articletime=(isset($_GET['articletime']))? $_GET['articletime']:'0';
	$title=(isset($_GET['title']))? $_GET['title']:'0';
	
	$keystr    = $_GET['co'];
	$md5Str    = $duserid.$memberid.$domain.$blogname.$articleid.$vuserid.$username.$nickname;
	$verify    = getVerifyStr($md5Str);
	
	//验证提交信息的合法性
	if($keystr !== $verify)
	{
		//echo 'verify failed!!!';
		//exit(-1);
	}
	
	//记录队列
	$queestr   = $vuserid.','.$duserid.','.$memberid.','.$articleid.','.str_replace(',','',$domain).',';
	$queestr  .= str_replace(',','',$username).','.str_replace(',','',$nickname).',';
    $queestr  .= str_replace(',','',$blogname);
    $queestr  .= ','.$_big.','.$articletime.PHP_EOL;
	if(!queeinsert($queestr))//把访问数等写进文件里
	{
		error_log(date('Y-m-d H:i:s').'\t'.$queestr, 3, '/home/www/html/logs/insertfailed.log');
	}

	//访客信息
	$vuser['userid']	= $vuserid;
	$vuser['username']	= $username;
	$vuser['nickname']	= $nickname;
	
	//访问过的博客信息
	$vblog['memberid']  = $memberid;
	$vblog['blogname']  = $blogname;
	$vblog['domain']    = $domain;
	$vblog['userid']    = $duserid;
	
	//初始化Memcache
	$memcache  = new CMemcache;

	
	if($memberid > 0)
	{
		//更新博客访问统计
		update_blogstat($memberid, $_debug);
		if($vuserid > 0)
		{
			//更新博客最近访客
			update_blogvisit($vuser, $memberid, $_debug);	
		}
	}
	
	if($articleid > 0)
	{
		//更新文章访问统计
		update_articlestat($articleid, $_debug);
		if($vuserid > 0)
		{
			//更新文章最近访客
			update_articlevisit($vuser, $articleid, $_debug);
			
			//更新当前文章页浏览过的用户前一篇浏览的文章标题
			
			if($duserid!=$vuserid)
			{
				update_guesteverbrowse($vuserid,$domain,$articletime,$articleid,$title);
			}
		}
	}
?>