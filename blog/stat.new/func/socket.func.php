<?php

if (!defined('ROOT'))
    exit('access deny!');

//与gw交互的相关操作
//验证请求信息的合法性
function checkrs($data) {
    $rs = false;
    if (isset($data['Status']['Code']) && $data['Status']['Code'] == '00') {
        $rs['RetRecords'] = isset($data['Status']['RetRecords']) ? $data['Status']['RetRecords'] : 0;
        $rs['TtlRecords'] = isset($data['Status']['TtlRecords']) ? $data['Status']['TtlRecords'] : 0;
        $rs['Record'] = isset($data['Records']['Record']) ? $data['Records']['Record'] : false;
        $error = '';
    } else {
        $error = "| args:" . print_r($data['args'], true);
    }
    $filename = '/home/www/html/logs/statnew_checkrs_' . date('Ymd') . '.log';
    if (file_exists($filename) === false) {
        $fp = fopen($filename, 'w+');
        chmod($filename, 0777);
        fclose($fp);
    }
    error_log(date('Y-m-d H:i:s') . " | " . __FILE__ . " | " . __FUNCTION__ . " | type:{$data['type']} | code:{$data['Status']['Code']} $error\r\n", 3, $filename);
    return $rs;
}

//获取博客点击统计
function getBlogStat($memberids) {
    $memberids = is_array($memberids) ? $memberids : array(0 => $memberids);
    $socket = new CSocket();
    $type = 'B379';
    $data['MemberIDs'] = join(',', $memberids);
    $data['StartNo'] = 0;
    $data['QryCount'] = count($memberids);

    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
    $rs = (isset($rs['Record'])) ? $rs['Record'] : array();
    return $rs;
}

//增修博客点击统计
function setBlogStat($data, $isadd = 1) {
    $socket = new CSocket();
    $type = 'B380';
    $data['Type'] = $isadd;
    $data['Dates'] = date('Ymd');
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    checkrs($rs);
    return true;
}

//更新博客访问统计
function setBlogAccess($data) {
    $socket = new CSocket();
    $type = 'B341';
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    checkrs($rs);
    return true;
}

//获取博客访客记录
function getBlogVisitor($memberid) {
    $socket = new CSocket();
    $type = 'B072';
    $data['MemberID'] = $memberid;
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
    $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();
    return $rs;
}

//获取博客访客记录
function setBlogVisitor($memberid, $data) {
    $socket = new CSocket();
    $type = 'B073';
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
}

//设置用户访问过的博客记录
function setUserVisitorTo($data) {
    $socket = new CSocket();
    $type = 'B075';
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
}

//获取用户访问过的博客记录
function getUserVisitorBlogTo($userid) {
    $socket = new CSocket();
    $type = 'B074';
    $data['UserID'] = $userid;
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
    $rs = (isset($rs['Record'])) ? $rs['Record'] : array();
    return $rs;
}

//获取文章点击
function getArticleStat($articles) {
    $articles = is_array($articles) ? $articles : array(0 => $articles);
    $socket = new CSocket();
    $type = 'B382';
    $data['ArticleIDs'] = join(',', $articles);
    $data['StartNo'] = 0;
    $data['QryCount'] = count($articles);
    $time1 = microtime(true);
    $rs = $socket->senddata($type, $data);
    $time2 = microtime(true);
    $time = $time2 - $time1;
    //error_log(date('Y-m-d H:i:s').":{$time}".print_r($rs, true).PHP_EOL, 3, 'gwlog.log');
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);

    $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();

    return $rs;
}

//增修博客点击统计
function setArticleStat($data, $isadd = 1) {
    $socket = new CSocket();
    $type = 'B383';
    $data['Type'] = $isadd;
    $data['Dates'] = date('Ymd');
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
    return true;
}

//获取文章访客记录
function getArticleVisitor($articleid) {
    if (!is_numeric($articleid)) {
        return false;
    }
    $socket = new CSocket();
    $type = 'B375';
    $data['ArticleIDs'] = $articleid;
    $data['StartNo'] = 0;
    $data['QryCount'] = 1;
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);

    $rs = (isset($rs['Record']) && $rs['Record'] != false) ? $rs['Record'] : array();
    return $rs;
}

//新增博客文章访客记录
function setArticleVisitor($data) {
    $socket = new CSocket();
    $type = 'B376';
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    checkrs($rs);
    return true;
}

//更新用户最近访问过的文章
function setNearViewArticle($data) {
    $socket = new CSocket();
    $type = 'B378';
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    checkrs($rs);
    return $rs;
}

//获取用户访问过的文章记录
function getUserVisitorArticleTo($userid) {
    $socket = new CSocket();
    $type = 'B377';
    $data['StartNo'] = 0;
    $data['QryCount'] = 1;
    $data['UserIDs'] = $userid;
    $rs = $socket->senddata($type, $data);
    $rs['type'] = $type;
    $rs['args'] = $data;
    $rs = checkrs($rs);
    $rs = (isset($rs['Record'])) ? $rs['Record'] : array();
    return $rs;
}

//设置博客投票记录
function setArticleVote($data) {
    $socket = new CSocket();
    $type = 'B361';

    $data['VoteID'] = 0;
    $rs = $socket->senddata($type, $data);

    $rs['type'] = $type;
    $rs['args'] = $data;
    if (checkrs($rs) != false) {
        return (isset($rs['Status']['TtlRecords']) && $rs['Status']['TtlRecords'] == 1) ? true : false;
    }
    return false;
}


//获取文章排行
function getStatList($data) {
    global $config, $memcache;
    $type = 'B392';
    $data['nTimes'] = isset($data['nTimes']) ? $data['nTimes'] : 1;
    $data['nStart'] = isset($data['nStart']) ? $data['nStart'] : 0;
    $data['nCount'] = isset($data['nCount']) ? $data['nCount'] : 30;
    $socket = new CSocket();
    if ($data['nStart'] == -1) {
        $ckey = $config['keys']['K2100'];
        $rs = $socket->senddata($type, $data);
        $rs['type'] = $type;
        $rs['args'] = $data;
        $rs = checkrs($rs);
        if ($rs) {
            $getData = array();
            $getData['FlagCode'] = md5(time());
            $getData['UTopCnt'] = $rs['TtlRecords'];
            $memcache->set($ckey, $getData, 60 * 6);
            return $getData;
        }
        return false;
    } else {
        $ckey = $config['keys']['K2101'];
        $ckey = str_replace('{nTimes}', $data['nTimes'], $ckey);
        $ckey = str_replace('{PageNo}', $data['nStart'], $ckey);
        $ckey = str_replace('{PageSize}', $data['nCount'], $ckey);

        $rs = $socket->senddata($type, $data);
        $rs['type'] = $type;
        $rs['args'] = $data;
        $rs = checkrs($rs);
        if ($rs) {
            $getData['RetRecords'] = $rs['RetRecords'];
            $getData['Record'] = $rs['Record'];
            $getData['FlagCode'] = $data['FlagCode'];
            $memcache->set($ckey, $getData, 60 * 6);
            unset($rs);
            return $getData;
        }
        return false;
    }
}


?>