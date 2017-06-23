<?php
require_once("BCPurifyClient.php");
require_once("BCPurifyItem.php");
require_once("BCPurifyResult.php");

//mysql类
define('ROOT', dirname(__FILE__));
define('url', 'http://blog.cnfol.com/');
$blogAppType='pinglun';
include ROOT.'/config/config_bao10jie.inc.php';
include('Mysql.class.utf8.php');
include('lib/CMemcache.php');
include('lib/CSocket.php');
include('helper.php');

$ArticleCommentID=$argv[1];

//配置
$dbhost = $_config['blog_db']['hostname'];
$dbuser = $_config['blog_db']['username'];
$dbpw = $_config['blog_db']['password'];
$dbname = $_config['blog_db']['database'];

class Demo {
	public function run($param, $task) {
		// TODO Auto-generated method stub
		$item = new BCPurifyItem();
		$item->m_hlParmas = $param;	
		$client = new BCPurifyClient();
		$result = $client->{$task}($item);		//	Purify/Feedback/getNotify/getIndexResult
		if (!$result) {
			//客户端内部错误,返回字段校验错误(例:appType cannot be empty)或网络请求错误(例:http code:404)
			//print_r($client->getError());
			//return array('error'=>0,'result'=>$result->getError());
			return array('error'=>0,'result'=>'客户端内部错误');
		}elseif ($result->isBusinessSuccess()){
			//成功时取结果集,返回数组对象
			if ($result->getMarkResult() != null) {
				//print_r($result->getMarkResult());
				return array('error'=>1,'result'=>$result->getMarkResult());
			}else {
				return array('error'=>2,'result'=>'通知接口返回为空');
			}
		}else {
			//失败时取结果集
			//return($result->getMarkResult());
			return array('error'=>3,'result'=>'失败');
		}
	}	
}

//初始化Memcache
$memcache  = new CMemcache;

$param = array();
$db = new MySql;
$db->Connect($dbhost,$dbuser,$dbpw,$dbname);
//error_log('select a.CommentID, a.Subject, a.NickName, a.UserID, a.ArticleID, a.IP, a.Status, a.ArticleAppearTime, a.Address, a.Content, a.ArticleUserID, a.ArticleMemberID, a.CommentAppearTime,b.DomainName, b.NickName as AuthorName from tbBlogCommentChart a ,tbBlogMember b WHERE a.Status in(0,1) AND a.ArticleMemberID=b.MemberID and  a.CommentID="'.$ArticleCommentID.'"',3,'/home/httpd/logs/aget.log');
//$tres = $db->get_values('select a.CommentID, a.Subject, a.NickName, a.UserID, a.ArticleID, a.IP, a.Status, a.ArticleAppearTime, a.Address, a.Content, a.ArticleUserID, a.ArticleMemberID, a.CommentAppearTime, b.DomainName, b.NickName as AuthorName from tbBlogCommentChart a ,tbBlogMember b WHERE a.Status in(0,1) AND a.ArticleMemberID=b.MemberID and a.CommentAppearTime>="2013-06-06 14:10:19" and a.CommentAppearTime<="2013-06-06 15:13:10" ORDER BY a.CommentAppearTime DESC');
$tres = $db->get_values('select a.CommentID, a.Subject, a.NickName, a.UserID, a.ArticleID, a.IP, a.Status, a.ArticleAppearTime, a.Address, a.Content, a.ArticleUserID, a.ArticleMemberID, a.CommentAppearTime,b.DomainName, b.NickName as AuthorName from tbBlogCommentChart a ,tbBlogMember b WHERE  a.ArticleMemberID=b.MemberID and  a.CommentID="'.$ArticleCommentID.'"');
if(!$tres)
{
	echo('no articlecomment');
	return;
}

$demo = new Demo();
$task = 'Purify';	//用户自定义，Purify/Feedback/getNotify/getIndexResult

foreach($tres as $key=>$value)
{

	if(!empty($value['UserID'])&&$value['UserID']!=0)
	{
		$MemberID=$db->get_values('select MemberID from tbBlogMember where UserID="'.$value['UserID'].'"');
		if($MemberID)
		{
			foreach($MemberID as $values)
			{
				$GroupID=$db->get_values('select GroupID from tbBlogMemberGroup where MemberID='.$values['MemberID']);
				if(verify($GroupID,$_config['recommendgroup']))
				{
					continue;
				}
			}
		}
		
	}
	
	
	$value['url']=url.$value['DomainName'].'/article/'.strtotime($value['ArticleAppearTime']).'-'.$value['ArticleID'].'.html';
	$value['appType']=$blogAppType;
	$value['AppearTime']=$value['CommentAppearTime'];
	
	$params=getparam($value,$blogAppType);
	$result=$demo->run($params,$task);
	if($result['error']==1)
	{
		$result=$result['result'];
		if($result->flag==1)
		{
			$params['CommentID']=$value['CommentID'];
			$params['Status']=2;
			$params['IllegalArticles']=1;
			
			$rs=getSocket($params,'B385');
			if(!empty($rs['TtlRecords']))
			{
				$ckey 	= clearCommentCache($value['ArticleID'],$config['K1027']);
			}
		}
		else if($result->flag==2)
		{
			$params['CommentID']=$value['CommentID'];
			$params['IllegalArticles']=1;
			
			$rs=getSocket($params,'B385');
		}
	
		//echo($value['CommentID'].'|'.$result->flag.'|'.$results.'<br>');
	}
	else if($result['error']==0)
	{
		$params['CommentID']=$value['CommentID'];
		$params['IllegalArticles']=1;
			
		$rs=getSocket($params,'B385');
		//echo($value['CommentID'].'|'.$result['result'].'|'.$results.'<br>');
		//error_log($result['error'].'|',3,'/home/httpd/logs/articlecommentbaolog.log');
	}
	else if($result['error']==3)
	{
		$params['CommentID']=$value['CommentID'];
		$params['IllegalArticles']=1;
			
		$rs=getSocket($params,'B385');
	}
	else
	{
		$params['CommentID']=$value['CommentID'];
		$params['IllegalArticles']=1;
			
		$rs=getSocket($params,'B385');
	}
	
	unset($results);
}



unset($demo);
unset($db);