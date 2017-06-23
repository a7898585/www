<?php

//header('content-type;text/html;charset=utf-8');
require_once("BCPurifyClient.php");
require_once("BCPurifyItem.php");
require_once("BCPurifyResult.php");

//mysql类
define('ROOT', dirname(__FILE__));
define('url', 'http://blog.cnfol.com/');
$blogAppType = 'bolg';
include ROOT . '/config/config_bao10jie.inc.php';
include('Mysql.class.utf8.php');
include('lib/CMemcache.php');
include('lib/CSocket.php');
include('helper.php');

$ArticleID = $argv[1];

error_log($ArticleID . '|', 3, '/home/www/html/logs/articleidbaolog_' . date('Ymd') . '.log');
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

        $result = $client->{$task}($item);  //	Purify/Feedback/getNotify/getIndexResult

        if (!$result) {
            //客户端内部错误,返回字段校验错误(例:appType cannot be empty)或网络请求错误(例:http code:404)
            //print_r($client->getError());
            //return array('error'=>0,'result'=>$result->getError());
            return array('error' => 0, 'result' => '客户端内部错误');
        } elseif ($result->isBusinessSuccess()) {
            //成功时取结果集,返回数组对象
            if ($result->getMarkResult() != null) {
                //print_r($result->getMarkResult());
                return array('error' => 1, 'result' => $result->getMarkResult());
            } else {
                return array('error' => 2, 'result' => '通知接口返回为空');
            }
        } else {
            //失败时取结果集
            //return($result->getMarkResult());
            return array('error' => 3, 'result' => '失败');
        }
    }

}

//初始化Memcache
$memcache = new CMemcache;


$param = array();
$db = new MySql;


$db->Connect($dbhost, $dbuser, $dbpw, $dbname);
$tres = $db->get_values('SELECT a.ArticleID, a.Recommend, a.Prime, a.IsTop, a.IsUsed, a.MemberID, a.ReadStatus, a.Blogname, a.Domainname, a.UserID,a.Title, a.Summary, a.AppearTime as AppearTime, a.IsDel, a.IP, a.Status, a.SysTagID, a.GiftPrice FROM tbBlogArticleChart a  WHERE  a.ArticleID="' . $ArticleID . '"', 'idstr');
//$tres = $db->get_values('SELECT a.ArticleID, a.Recommend, a.Prime, a.IsTop, a.IsUsed, a.MemberID, a.ReadStatus, a.Blogname, a.Domainname, a.UserID,a.Title, a.Summary, a.AppearTime as AppearTime, a.IsDel, a.IP, a.Status, a.SysTagID, a.GiftPrice FROM tbBlogArticleChart a join tbBlogMember b on a.MemberID = b.MemberID WHERE a.IsDel in(0,2,5) and b.`Status` = 0   ORDER BY a.AppearTime DESC limit 0,1','idstr');
//if($tres[0]['UserID']=='6536425')
//{
//	error_log(print_r($tres,true).'|'.print_r($argv,true),3,'/home/www/html/logs/aget.log');
//}

error_log($ArticleID . '|' . print_r($tres, true) . '|', 3, '/home/www/html/logs/articleidfalsebaolog_' . date('Ymd') . '.log');
if (!$tres) {
    error_log($ArticleID . ',', 3, '/home/www/html/newblog/stat.new/bao10jie/articleidmiss.log');
    exit;
}
$demo = new Demo();
$task = 'Purify'; //用户自定义，Purify/Feedback/getNotify/getIndexResult

foreach ($tres as $key => $value) {

    error_log($value['ArticleID'] . '|', 3, '/home/www/html/logs/articlebaolog1_' . date('Ymd') . '.log');

    $GroupID = $db->get_values('select GroupID from tbBlogMemberGroup where MemberID=' . $value['MemberID']);

    if (verify($GroupID, $_config['recommendgroup'])) {
        continue;
    }

    $ArtIds = explode('_', $value['idstr']);
    $content = modifyGetInfo($ArtIds);

    $content[0]['ArticleID'] = $value['ArticleID'];
    $content[0]['UserID'] = $value['UserID'];
    $content[0]['Blogname'] = $value['Blogname'];
    $content[0]['url'] = url . $value['Domainname'] . '/article/' . $ArtIds[1] . '-' . $ArtIds[0] . '.html';
    $content[0]['appType'] = $blogAppType;
    $content[0]['AppearTime'] = $value['AppearTime'];

    $params = getparam($content[0], $task);

    $result = $demo->run($params, $task);



    if ($result['error'] == 1) {
        $result = $result['result'];
        if ($result->flag == 1) {

            //if($result->clueClass!='BlackListService')
            //{
            $param['MemberID'] = $value['MemberID'];
            $param['IsDel'] = 4; //系统删除
            $param['ArticleIDs'] = $value['ArticleID'];
            $param['IllegalArticles'] = 1;
            $param['AppearTimes'] = $value['AppearTime'];

            $rs = getSocket($param, 'B244');

            error_log($value['ArticleID'] . '|', 3, '/home/www/html/logs/articlebaolog2_' . date('Ymd') . '.log');

            if (!empty($rs)) {
                //if($tres[0]['UserID']=='6536425')
                //{
                //	error_log(print_r($memcache,true),3,'/home/www/html/logs/aget.log');
                //}
                $ckey = delBlogArtilceContent(array('ArticleID' => $value['ArticleID'], 'MemberID' => $value['MemberID']));
                $ckey2 = delBlogArticleList(array('ArticleID' => $value['ArticleID'], 'MemberID' => $value['MemberID']));
                //error_log($ckey.'|'.$ckey2,3,'/home/www/html/logs/alog.log');
            } else {
                error_log($ArticleID . ',', 3, '/home/www/html/newblog/stat.new/bao10jie/articleidmiss.log');
            }
        } else if ($result->flag == 2) {

            $param['MemberID'] = $value['MemberID'];
            $param['ArticleID'] = $value['ArticleID'];
            $param['IllegalArticles'] = 1;

            $rs = getSocket($param, 'B273');

            error_log($value['ArticleID'] . '|', 3, '/home/www/html/logs/articlebaolog4_' . date('Ymd') . '.log');
        }

        //echo($value['ArticleID'].'|'.$result->flag.'|'.$results.'<br>');
    } else if ($result['error'] == 0) {
        $param['MemberID'] = $value['MemberID'];
        $param['ArticleID'] = $value['ArticleID'];
        $param['IllegalArticles'] = 1;

        $rs = getSocket($param, 'B273');

        if (empty($rs)) {
            error_log($ArticleID . ',', 3, '/home/www/html/newblog/stat.new/bao10jie/articleidmiss.log');
        }

        //error_log($result['error'].'|',3,'/home/www/html/logs/articlebaolog.log');
        error_log($value['ArticleID'] . '|', 3, '/home/www/html/logs/articlebaolog5_' . date('Ymd') . '.log');
    } else if ($result['error'] == 3) {
        $param['MemberID'] = $value['MemberID'];
        $param['ArticleID'] = $value['ArticleID'];
        $param['IllegalArticles'] = 1;

        $rs = getSocket($param, 'B273');

        if (empty($rs)) {
            error_log($ArticleID . ',', 3, '/home/www/html/newblog/stat.new/bao10jie/articleidmiss.log');
        }

        //error_log($result['error'].'|',3,'/home/www/html/logs/articlebaolog.log');
        error_log($value['ArticleID'] . '|', 3, '/home/www/html/logs/articlebaolog6_' . date('Ymd') . '.log');
    } else {
        $param['MemberID'] = $value['MemberID'];
        $param['ArticleID'] = $value['ArticleID'];
        $param['IllegalArticles'] = 1;

        $rs = getSocket($param, 'B273');

        if (empty($rs)) {
            error_log($ArticleID . ',', 3, '/home/www/html/newblog/stat.new/bao10jie/articleidmiss.log');
        }

        //error_log($result['error'].'|',3,'/home/www/html/logs/articlebaolog.log');
        error_log($value['ArticleID'] . '|', 3, '/home/www/html/logs/articlebaolog7_' . date('Ymd') . '.log');
    }

    unset($results);
}



unset($demo);
unset($db);
