<?php if(!defined('ROOT')) exit('access deny!');
//脚本函数的定义

//更新博客统计参数数组
function update_sockblogstat($memstat)
{
	global $config, $memcache;
	$data = array();
    
    $newdate = date('Ymd');
	foreach($memstat as $memid=>$stat)
	{
        //总统计
		$keyStr = str_replace('memberid', $memid, $config['keys']['blogclick']);
		$data['MemberID']		= $memid;
        $clicknum               = $stat;
		$data['TotalClick']		= $clicknum[0];
		$data['RealTotalClick']	= $stat[1];
		$data['TodayClick']	    = $clicknum[0];
		$data['RealTodayClick']	= $stat[1];
		setBlogStat($data);
		$memcache->delete($keyStr);

        //天统计明细
        $param['MemberID']		= $memid;
		$param['AccessNums']	= $clicknum[0];
		$param['AccessDate']	= $newdate;
		setBlogAccess($param);
	}
}

//更新文章统计
function update_sockartstat($artstat)
{
	global $config, $memcache;
	$data = array();

	foreach($artstat as $artid=>$stat)
	{
		$keyStr = str_replace('articleid', $artid, $config['keys']['articleclick']);
		$data['ArticleID']		= $artid;
		$data['MemberID']		= $stat['memid'];
		$data['TotalClick']		= $stat['stat'][0];
		$data['RealTotalClick']	= $stat['stat'][1];
		$data['TodayClick']	    = $stat['stat'][0];
		$data['RealTodayClick']	= $stat['stat'][1];
		setArticleStat($data);
		$memcache->delete($keyStr);
	}
}

//更新博客访客
function update_sockblogvisitor($vusers)
{
	global $config, $memcache;
	$data   = array();
	foreach($vusers as $memberid=>$vuser)
	{
		$keyStr = str_replace('memberid', $memberid, $config['keys']['blogvisitor']);

		//如果访客记录比较多，直接入库返回
        $largeNum = count($vuser) - $config['vBlogTlCnt'];
		if($largeNum < 0)
		{
			$data   = $memcache->get($keyStr);
			if(empty($data))
			{
				$data = getBlogVisitor($memberid);
			}
			//获取最近访客信息
			$visitor = isset($data['VUsers'])? unserialize($data['VUsers']):array();
            if(is_array($visitor) && !empty($visitor))
            {
		        foreach($vuser as $vu)
                {
                    if(array_key_exists($vu['userid'], $visitor))
                    {
                        unset($visitor[$vu['userid']]);
                    }
                    $visitor[$vu['userid']] = $vu;
                }
                $vuser = $visitor;
                unset($visitor);
            }
		}
        $largeNum = count($vuser) - $config['vBlogTlCnt'];
        if($largeNum > 0)
        {
            $vuser =  array_splice($vuser,0, $largeNum);
		}
        $data['MemberID'] = $memberid;
		$data['VUsers']   = serialize($vuser);
		setBlogVisitor($memberid, $data);
		$memcache->delete($keyStr);
	}
}

//更新文章访客
function update_sockartvisitor($vusers)
{
	global $config, $memcache;
	$data   = array();
	foreach($vusers as $articleid=>$vuser)
	{
		$keyStr = str_replace('articleid', $articleid, $config['keys']['articlevisitor']);

		//如果访客记录比较多，直接入库返回
        $largeNum = count($vuser) - $config['vBlogTlCnt'];
		if($largeNum < 0)
		{
			$data   = $memcache->get($keyStr);
			if(empty($data))
			{
				$data = getArticleVisitor($articleid);
			}
			//获取最近访客信息
			$visitor = isset($data['VUsers'])? unserialize($data['VUsers']):array();
            if(is_array($visitor) && !empty($visitor))
            {
		        foreach($vuser as $vu)
                {
					//去重复
                    if(array_key_exists($vu['userid'], $visitor))
                    {
                        unset($visitor[$vu['userid']]);
                    }
                    $visitor[$vu['userid']] = $vu;
                }
                $vuser = $visitor;
                unset($visitor);
            }
		}
        $largeNum = count($vuser) - $config['vBlogTlCnt'];
        if($largeNum > 0)
        {
            $vuser =  array_splice($vuser,0, $largeNum);
        }
		$data['ArticleID'] = $articleid;
		$data['VUsers']    = serialize($vuser);
		setArticleVisitor($data);
		$memcache->delete($keyStr);
	}
}

//更新用户最近访问过的博客
function update_sockuserblogvisitto($vblogs)
{
	global $config, $memcache;
	$data   = array();
	foreach($vblogs as $userid=>$vblog)
	{
		$keyStr = str_replace('userid', $userid, $config['keys']['blogvisitorto']);
        $largeNum = count($vblog) - $config['vBlogToTlCnt'];
		if($largeNum < 0)
		{
			$data = $memcache->get($keyStr);
			if(empty($data))
			{
				$data = getUserVisitorBlogTo($userid);
			}
			//获取最近访客信息
			$orivblog = isset($data['VBlogs'])? unserialize($data['VBlogs']):array();

            if(is_array($orivblog) && !empty($orivblog))
            {
                foreach($vblog as $vb)
                {
                    if(array_key_exists($vb['memberid'], $orivblog))
                    {
                        unset($orivblog[$vb['memberid']]);
                    }
                    $orivblog[$vb['memberid']] = $vb;
                }
                $vblog = $orivblog;
                unset($orivblog);
            }
		}
        $largeNum = count($vblog) - $config['vBlogToTlCnt'];
        if($largeNum > 0)
        {
            $vblog =  array_splice($vblog,0, $largeNum);
        }
		$data['UserID']    = $userid;
		$data['VBlogs']    = serialize($vblog);
		setUserVisitorTo($data);
        $flag = $memcache->delete($keyStr);
	}
}

//更新用户最近访问过的文章记录 正在更新
function update_sockuserartvisitto($arts)
{
	global $config, $memcache;
	$data   = array();
	foreach($arts as $userid=>$varts)
	{
		$keyStr = str_replace('userid', $userid, $config['keys']['artvisitorto']);
        $largeNum = count($varts) > $config['vArtToTlCnt'];
		if($largeNum < 0)
		{
			$data = $memcache->get($keyStr);
			if(empty($data))
			{
				$data = getUserVisitorArticleTo($userid);
			}
          
			//获取最近访客信息
			$orivart = isset($data['VArticles'])? unserialize($data['VArticles']):array();
            if(is_array($orivart) && !empty($orivart))
            {
                foreach($varts as $va)
                {
                    if(array_key_exists($va['articleid'], $orivart))
                    {
                        unset($orivart[$va['articleid']]);
                    }
                    $orivart[$va['articleid']] = $va;
                }
			    $varts =  $orivart;  
                unset($orivart);
		    }
        }
        $largeNum = count($varts) > $config['vArtToTlCnt'];
        if($largeNum > 0)
        {
            $varts = array_splice($varts, 0, $largeNum);
        }
		$data['UserID']    = $userid;
		$data['VArticles'] = serialize($varts);
		setNearViewArticle($data);
        $flag = $memcache->delete($keyStr);
	}
}
?>