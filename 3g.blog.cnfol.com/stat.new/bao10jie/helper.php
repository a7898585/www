<?php
header('content-type;text/html;charset=utf-8');
 	//获取表明信息
    function getTableName($PreFix, $MemberID, $AppearTime)
    {
        $subFix = intval($MemberID % 40);
		$AppearTime = substr($AppearTime,0,4);
        
		if($AppearTime < 2012) {
			return sprintf('%s%04d', $PreFix, $subFix); 
		} else {
			return sprintf('%s%04d%04d', $PreFix, $AppearTime, $subFix); 
		}
    }
    
    //获取博客文章的信息
	function modifyGetInfo($ArtIds)
	{
		global $db;
		$ArticleTable = getTableName('tbBlogArticle', $ArtIds[3], date('Y-m-d H:i:s',$ArtIds[1]));
		$StatTable    =getTableName('tbBlogArticleStat', $ArtIds[3], date('Y-m-d H:i:s',$ArtIds[1]));
		
		$row = $db->get_values("select a.Title, a.Summary, a.Recommend, a.SysTagID, a.Prime, a.IsTop, a.IsUsed, a.Status, a.IsDel, a.IP, a.SortID, a.MemberID, a.AppearTime, b.Content
				from {$StatTable} a,{$ArticleTable} b where a.ArticleID=b.ArticleID and a.ArticleID={$ArtIds[0]}");
		$rows = $db->get_values("select Name from tbBlogTag t inner join  tbBlogTagArticle a on t.TagID=a.TagID where ArticleID={$ArtIds[0]}");
	//	$str = implode("", $rows);
		$str = "";
		foreach($rows as $k=>$v)
		{
			$str .=$v['Name'];
		}
		$row[0]['Content'] .= $str;

		
		return $row;
	}
	
	
	function getparam($array,$task)
	{
		
		if ($task == 'Purify') {
	
			$param['isBatch'] = 0;
			$param['appType'] = $array['appType'];
			$param['textId'] = $array['ArticleID'];
			$param['threadId'] = $array['MemberID'];
			$param['class'] = $array['ArticleID'];
			$param['ip'] =$array['IP'];
			$param['userId'] =$array['UserID'];
			$param['author'] =$array['Blogname'];
			$param['title']=$array['Title'];
			$param['text'] =$array['Content'];
			$param['url'] =$array['url'];
			$param['pubDate'] = $array['AppearTime'];
			//$param['authorEx'] = "";
			//$param['contentEx'] = "";
			//$param['structureEx'] = "";
			//$param['rules'] = "";
	
		}else if ($task == 'pinglun') {
	
			$param['isBatch'] = 0;
			$param['appType'] = $array['appType'];
			$param['textId'] = $array['ArticleID'];
			$param['threadId'] = $array['CommentID'];
			$param['class'] = $array['ArticleID'];
			$param['ip'] =$array['IP'];
			$param['userId'] =$array['UserID'];
			$param['author'] =$array['NickName'];
			$param['title']='1';
			$param['text'] =$array['Content'];
			$param['url'] =$array['url'];
			$param['pubDate'] = $array['AppearTime'];
			//$param['authorEx'] = "";
			//$param['contentEx'] = "";
			//$param['structureEx'] = "";
			//$param['rules'] = "";
	
		}else if ($task == 'Feedback'){
			//反馈接口
			$param['appType'] = "bbs";
			$param['textId'] = "12323".rand();
			$param['threadId'] = "123456";
			$param['class'] = "11wee0";
			$param['ip'] ="23.23.3.3";
			$param['userId'] ="abcd";
			$param['author'] ="wwww";
			$param['title']="12 ab";
			$param['text'] ="12";
			$param['url'] ="http://test.html";
			$param['pubDate'] = "2010-12-01 00:00:00";
			$param['authorEx'] = "作者扩展";
			$param['contentEx'] = "内容扩展";
			$param['structureEx'] = "structureEx";
			$param['rules'] = "规则";

			$param['status'] = "1"; 
			$param['reason'] = "reason";
		}else {
			//通知接口、批量标引通知接口
			$param['appType'] = "bbs";
		}
		return $param;
	}
	
	//是否属于名家看市组、高手看盘组、采用组及已审组
	function verify($GroupID,$Group)
	{
		//$GroupID=explode(',',$GroupID[0]['GroupID']);
		
		foreach($GroupID as $value)
		{
			if($Group[$value['GroupID']])
			{
				return true;
			}
		}
		return false;
	}
	
//清除评论缓存
function clearCommentCache($ArticleID,$key)
{
	global $memcache;
	$keyStr = str_replace('{ArticleID}',$ArticleID,$key);
    $flag = $memcache->delete($keyStr);
	return $flag;
}



function getSocket($data,$type)
{
	$socket = new CSocket();
	
	$rs   = $socket->senddata($type,$data); 
    $rs['type'] = $type;
	$rs['args'] = $data;
	$rs   = checkrs($rs); 
	$rs['Record']   = (isset($rs['Record']))? $rs['Record']:array(); 
	//error_log(print_r($rs,true).'|'.print_r($data,true).'|'.$type,3,'/home/httpd/logs/articlecommentbaolog_1.log');
	return $rs;
}

//与gw交互的相关操作
//验证请求信息的合法性
function checkrs($data)
{
	$rs = false;
	if(isset($data['Status']['Code']) && $data['Status']['Code'] == '00')
	{
		$rs['RetRecords'] = isset($data['Status']['RetRecords'])? $data['Status']['RetRecords']:0;
		$rs['TtlRecords'] = isset($data['Status']['TtlRecords'])? $data['Status']['TtlRecords']:0;
		$rs['Record']     = isset($data['Records']['Record'])? $data['Records']['Record']:false;
		//$error = '';
	}
	//else
	//{
	//	$error = "| args:".print_r($data['args'], true);
	//}
	//$filename = '/home/html/logs/statnew_checkrs_'.date('Ymd').'.log';
	//if (file_exists($filename) === false) {
    //    $fp = fopen($filename, 'w+'); 
	//	chmod($filename, 0777);
	//	fclose($fp);
	//}
	//error_log(date('Y-m-d H:i:s')." | ".__FILE__." | ".__FUNCTION__." | type:{$data['type']} | code:{$data['Status']['Code']} $error\r\n", 3, $filename);
	return $rs;
}

function tidyParams($value)
{
	return array('BlogType'=>'1','ArticleIDs'=>$value['ArticleID'],'CommentIDs'=>$value['CommentID'],'ArtMemberIDs'=>$value['ArticleMemberID'],'IllegalArticles'=>1);
}


	//文章内容
	function delBlogArtilceContent($data)
	{
		global $memcache,$config;
    
		if(isset($data['ArticleIDs']) && is_array($data['ArticleIDs'])){
			foreach($data['ArticleIDs'] as $ArticleID){
				$ckey  = str_replace('{ArticleID}',$ArticleID, $config['K1014']);
				$ckey  = str_replace('{MemberID}',$data['MemberID'], $ckey);
				$flag = $memcache->delete($ckey);
	
				$ckey  = str_replace('{ArticleID}',$ArticleID, $config['K1017']);
				$ckey  = str_replace('{MemberID}',$data['MemberID'], $ckey);
				$flag2 = $memcache->delete($ckey);
			}
		}else{
			$ckey  = str_replace('{ArticleID}',$data['ArticleID'], $config['K1014']);
			$ckey  = str_replace('{MemberID}',$data['MemberID'], $ckey);
			$flag = $memcache->delete($ckey);
				
			$ckey2  = str_replace('{ArticleID}',$data['ArticleID'], $config['K1017']);
			$ckey2  = str_replace('{MemberID}',$data['MemberID'], $ckey2);
			$flag2 = $memcache->delete($ckey2);
		}
		//error_log($ckey.'|'.$ckey2.'|'.$flag.'|'.$flag2.'|',3,'/home/httpd/logs/alog.log');
		return $flag.$flag2;
	}
	
	//文章列表
	function delBlogArticleList($data)
	{
		global $memcache,$config;
		
		$StartDate   =  isset($data['StartDate'])? date("Ymd",strtotime($data['StartDate'])):'';
		$EndDate   =  isset($data['EndDate'])? date("Ymd",strtotime($data['EndDate'])):'';
		$flag = false;
	
		$ckey 	= $config['K1015'];
		$ckey 	= str_replace('{MemberID}',$data['MemberID'],$ckey);
		$ckey 	= str_replace('{StartDate}',$StartDate,$ckey);
		$ckey 	= str_replace('{EndDate}',$EndDate,$ckey);
		$flag = $memcache->delete($ckey);
	
		$ckey  = $config['K1040'];
		$ckey  = str_replace('{MemberID}',$data['MemberID'],$ckey);
		$ckey  = str_replace('{SelfRecommend}','-1',$ckey);
		$flag2 = $memcache->delete($ckey);
	
		$ckey	= $config['K1005'];
		$ckey   = str_replace('{MemberID}',$data['MemberID'],$ckey);
		$flag3 = $memcache->delete($ckey);
	
		$ckey	= $config['K1023'];
		$ckey   = str_replace('{MemberID}',$data['MemberID'],$ckey);
		$flag4 = $memcache->delete($ckey);
	
		$flag5 = delBlogUserTagArticle($data);
		//error_log($flag.$flag2.$flag3.$flag4.$flag5,3,'/home/httpd/logs/alog.log');
		return $flag.$flag2.$flag3.$flag4.$flag5;
	}
	
	//删除用户自定义分类下的文章缓存
	function delBlogUserTagArticle($data)
	{
		global $memcache,$config;
		
		$data['SortID']  =  isset($data['SortID']) ? $data['SortID'] : 0;
	
		if($data['SortID'] <= 0)
		{
		}
		else
		{
			$ckey	= $config['K1042'];
			$ckey   = str_replace('{MemberID}',$data['MemberID'],$ckey);
			$ckey   = str_replace('{SortID}',$data['SortID'],$ckey);
			$flag = $memcache->delete($ckey);
		}
	
		$ckey	= $config['K1042'];
		$ckey   = str_replace('{MemberID}',$data['MemberID'],$ckey);
		$ckey   = str_replace('{SortID}','18295',$ckey);
		$flag = $memcache->delete($ckey);
		
		return $flag;
	}
?>