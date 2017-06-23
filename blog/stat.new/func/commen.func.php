<?php

if (!defined('ROOT'))
    exit('access deny!');

//基本函数的定义

function replace($str) {
    $str = str_replace('“', '"', $str);
    $str = str_replace('”', '"', $str);
    $str = str_replace('：', ':', $str);
    $str = str_replace('！', '!', $str);
    $str = str_replace('—', '-', $str);
    return $str;
}

//设置投票cookie
function setVoteCookie($artid) {
    global $config, $memcache;
    $domain = '.' . $config['domain'];
    $name = 'blogartvote' . $artid;
    if (!isMobile()) {
        $pre = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
        $memcache->set($name . $pre, 1, 3600);
    }
    return setCookie($name, 1, time() + 3600, '/', $domain);
}

//获取投票记录
function checkHasVote($artid) {
    $name = 'blogartvote' . $artid;
    return (isset($_COOKIE[$name]) ? 1 : 0);
}

//记录投票信息
function set_articlevote($data) {
    global $config;
    $uid = isset($_COOKIE['cookie']['passport']['userId']) ? $_COOKIE['cookie']['passport']['userId'] : 0;
    $data['UserID'] = $uid;
    if (setArticleVote($data)) {
        $msg = $data['ArticleID'] . ',' . $data['UserID'] . "\r\n";
        error_log($msg, 3, $config['artvote']);
        setVoteCookie($data['ArticleID']);
        return true;
    }
    return false;
}

// 字符截取
function utf8_str($string, $width, $point = '') {
    $string = trim(replace($string));
    $start = 0;
    $encoding = 'UTF-8';
    if ($point == '') {
        $trimmarker = '...';
    } else {
        $trimmarker = '';
    }

    if ($width == '') {
        $width = mb_strwidth($string, "UTF-8");
    }
    return htmlspecialchars(mb_strimwidth($string, $start, $width, $trimmarker, $encoding));
}

//获取头像地址
function getuserhead($userid) {
    global $config;
    $md5id = md5($userid);
    $head = $config['userhead'];
    $head .= substr($md5id, 0, 2);
    $head .= '/' . substr($md5id, 2, 2);
    $head .= '/' . $userid . '/head.' . $userid . '.48';
    return $head;
}

//验证信息函数
function getVerifyStr($str) {
    $md5Str = md5($str);
    $md5Str = substr($md5Str, 1, 4) . substr($md5Str, 16, 7) . substr($md5Str, 25, 5);
    return $md5Str;
}

//写队列函数
function queeinsert($queestr) {
    global $config;
    if (!file_exists($config['queepath'])) {
        if (!mkdir($config['queepath'], 0777, TRUE)) {
            return false;
        }
    }
    $flag = error_log($queestr, 3, $config['blogstat']);
    return $flag;
}

//获取单个博客点击统计
function get_blogstat($memberid, $_debug = 0) {
    global $config, $memcache;
    $data = array();
    $keyStr = str_replace('memberid', $memberid, $config['keys']['blogclick']);
    $data = $memcache->get($keyStr);

    if (empty($data) || $_debug) {
        $data = getBlogStat($memberid);
        $flag = $memcache->set($keyStr, $data, $config['cache']['expire']);
    }
    return $data;
}

//更新博客统计cache
function update_blogstat($memberid, $_debug = 0) {
    global $config, $memcache, $_big;
    $keyStr = str_replace('memberid', $memberid, $config['keys']['blogclick']);
    $data = get_blogstat($memberid, $_debug);
    $data['TotalClick'] += 1;
    $data['RealTotalClick'] += 1;
    if (isset($data['Dates']) && $data['Dates'] == date('Ymd')) {
        $data['TodayClick'] += 1;
        $data['RealTodayClick'] += 1;
    } else {
        $data['TodayClick'] = 1;
        $data['RealTodayClick'] = 1;
        $data['Dates'] = date('Ymd');
    }

    $flag = $memcache->set($keyStr, $data, $config['cache']['expire']);
}

//获取博客访客信息
function get_blogvisitor($memberid, $_debug = 0) {
    global $config, $memcache;
    $data = array();
    $keyStr = str_replace('memberid', $memberid, $config['keys']['blogvisitor']);
    $data = $memcache->get($keyStr);

    if (empty($data) || $_debug) {
        $data = getBlogVisitor($memberid);
        $flag = $memcache->set($keyStr, $data, $config['cache']['expire']);
    }
    return $data;
}

//更新博客访客cache
function update_blogvisit($vuser, $memberid, $_debug = 0) {
    global $config, $memcache;
    $keyStr = str_replace('memberid', $memberid, $config['keys']['blogvisitor']);
    $data = get_blogvisitor($memberid, $_debug);
    //获取最近访客信息
    $visitor = isset($data['VUsers']) ? unserialize($data['VUsers']) : array();
    $visitor = (!empty($visitor) && is_array($visitor)) ? $visitor : array();

    //用户已经在里面了先删除
    if (($keys = array_search($vuser, $visitor))) {
        unset($visitor[$keys]);
    }
    //如果访客记录超过限定值移出最早的一个访客
    if (count($visitor) >= $config['vBlogTlCnt']) {
        array_shift($visitor);
    }
    $visitor[$vuser['userid']] = $vuser;
    $data['VUsers'] = serialize($visitor);
    $flag = $memcache->set($keyStr, $data);
}

//获取文章统计
function get_articlestat($articleid, $_debug = 0) {

    global $config, $memcache;
    $data = array();
    $keyStr = str_replace('articleid', $articleid, $config['keys']['articleclick']);

    $data = $memcache->get($keyStr);

    if (empty($data) || $_debug) {
        //if($articleid > 54150000 )error_log('art:'.$articleid.PHP_EOL, 3, './memcache-error-2-'.date('Ymd').'.log');
        $data = getArticleStat($articleid);
//        error_log(date('Y-m-d H:i:s').'\t'.$articleid.'==/=='.print_r($data,true), 3, '/home/httpd/logs/agetclick_'.date('Ymd').'.log');
        $flag = $memcache->set($keyStr, $data);
    }

    return $data;
}

//更新文章点击cache
function update_articlestat($articleid, $_debug = 0) {
    global $config, $memcache, $_big;
    $keyStr = str_replace('articleid', $articleid, $config['keys']['articleclick']);
    $data = get_articlestat($articleid, $_debug);

    $data['TotalClick'] += 1;
    $data['RealTotalClick'] += 1;
    if (isset($data['Dates']) && $data['Dates'] == date('Ymd')) {
        $data['TodayClick'] += 1;
        $data['RealTodayClick'] += 1;
    } else {
        $data['TodayClick'] = 1;
        $data['RealTodayClick'] = 1;
        $data['Dates'] = date('Ymd');
    }
    $flag = $memcache->set($keyStr, $data, $config['cache']['expire']);
}

function get_articlevisit($articleid, $_debug = 0) {
    global $config, $memcache;
    $data = array();
    $keyStr = str_replace('articleid', $articleid, $config['keys']['articlevisitor']);
    $data = $memcache->get($keyStr);
    if (empty($data) || $_debug) {
        $data = getArticleVisitor($articleid);
        $flag = $memcache->set($keyStr, $data);
    }
    return $data;
}

//更新文章最新访客cache
function update_articlevisit($vuser, $articleid, $_debug = 0) {
    global $config, $memcache;
    $keyStr = str_replace('articleid', $articleid, $config['keys']['articlevisitor']);
    $data = get_articlevisit($articleid, $_debug);

    //获取最近访客信息
    $visitor = isset($data['VUsers']) ? unserialize($data['VUsers']) : array();
    $visitor = (!empty($visitor) && is_array($visitor)) ? $visitor : array();

    //用户已经在里面了先删除
    if (isset($visitor[$vuser['userid']])) {
        unset($visitor[$vuser['userid']]);
    }
    //如果访客记录超过限定值移出最早的一个访客
    if (count($visitor) >= $config['vArtTlCnt']) {
        array_shift($visitor);
    }
    $visitor[$vuser['userid']] = $vuser;
    $data['VUsers'] = serialize($visitor);

    $flag = $memcache->set($keyStr, $data);
}

//更新当前文章页浏览过的用户前一篇浏览的文章标题cache
function update_guesteverbrowse($vuserid, $domain, $articletime, $articleid, $title) {
    global $config, $memcache;
    $artkey = str_replace('articleid', $articleid, $config['keys']['articleeverbrowse']); //文章内用户浏览过的文章
    $guekey = str_replace('userid', $vuserid, $config['keys']['guesteverbrowse']); //个人浏览过的文章
    $guekeyStr = unserialize($memcache->get($guekey));

    $data = array('vuserid' => $vuserid, 'DomainName' => $domain, 'AppearTime' => $articletime, 'ArticleID' => $articleid, 'Title' => $title);
    //error_log(print_r($guekeyStr,true).'555555555555555\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
    if ($guekeyStr) {
        if ($guekeyStr['ArticleID'] == $articleid) {
            //error_log('444444444444\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
            return;
        } else if (true) {
            $artkeyStr = unserialize($memcache->get($artkey));
            //error_log(print_r($artkeyStr,true).'111111111111\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
            if (!$artkeyStr) {
                $memcache->set($artkey, serialize(array('0' => $guekeyStr)), $config['cachetime']);
                $memcache->set($guekey, serialize($data), $config['cachetime']);
                //error_log(print_r(unserialize($memcache->get($artkey)),true).'555555555555555\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
            } else {
                foreach ($artkeyStr as $value) {
                    if (in_array($guekeyStr['ArticleID'], $value)) {
                        $memcache->set($guekey, serialize($data), $config['cachetime']);
                        return;
                    }
                }

                if (count($artkeyStr) < 10) {

                    $artkeyStr[] = $guekeyStr;
                    $memcache->set($artkey, serialize($artkeyStr), $config['cachetime']);
                    $memcache->set($guekey, serialize($data), $config['cachetime']);
                    //error_log(print_r(unserialize($memcache->get($artkey)),true).'1111111111\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
                } else {

                    array_shift($artkeyStr);
                    $artkeyStr[] = $guekeyStr;
                    $memcache->set($artkey, serialize($artkeyStr), $config['cachetime']);
                    $memcache->set($guekey, serialize($data), $config['cachetime']);
                    //error_log(print_r(unserialize($memcache->get($artkey)),true).'222222222222222\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
                }
            }
        }
    } else {

        if (true) {//checkGuestEverBrowse($vuserid)
            $memcache->set($guekey, serialize($data), $config['cachetime']);
            //error_log(print_r(unserialize($memcache->get($guekey)),true).'888888888888\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
        }
        return;
    }
}

//获取博客点击统计
function get_blogstatlist($memberids, $_debug = 0) {
    global $config, $memcache;

    $keyStrMIDS = str_replace('memberid', $memberids, $config['keys']['blogclick']);
    $data = $memcache->get($keyStrMIDS);
    if (empty($data) || $_debug) {
        $memids = explode(',', $memberids);
        foreach ($memids as $memberid) {
            $keyStr[] = str_replace('memberid', $memberid, $config['keys']['blogclick']);
        }
        $data = $memcache->get($keyStr);

        if (count($data) !== count($memids)) {
            $data = getBlogStat($memids);
        }
        $memcache->set($keyStrMIDS, $data, 30);
    }
    return $data;
}

//获取文章点击
function get_articlestatlist($clickartids, $_debug = 0) {
    global $config, $memcache;

    foreach ($clickartids as $aid) {
        $data[$aid] = get_articlestat($aid, $_debug);
    }
    return $data;
}

//获取用户最近访问过的博客文章记录
function get_uservisitto($userid, $type, $_debug = 0) {
    global $config, $memcache;
    if ($type == 'blog') {
        $keyStr = str_replace('userid', $userid, $config['keys']['blogvisitorto']);
    } else {
        $keyStr = str_replace('userid', $userid, $config['keys']['artvisitorto']);
    }
    $data = $memcache->get($keyStr);

    if (empty($data) || $_debug) {
        if ($type == 'blog') {
            $data = getUserVisitorBlogTo($userid);
        } else {
            $data = getUserVisitorArticleTo($userid);
        }
        $memcache->set($keyStr, $data, 60);
    }
    return $data;
}

//获取文章转载数（一条时）
function get_transshipment($clickartids, $_debug = 0) {
    global $config, $memcache;
    $data = array();

    $keyStr = $config['keys']['transshipment'];
    $keyStr = str_replace('{articleid}', $clickartids, $keyStr);

    $data = $memcache->get($keyStr);
    if (empty($data) || $_debug) {
        //$data = getTransshipment($articleid);
        //$flag =  $memcache->set($keyStr, $data);
    }
    return $data;
}

//获取文章转载数（多条时）
function get_transshipmentlist($clickartids, $_debug = 0) {
    global $config, $memcache;
    $data = array();

    foreach ($clickartids as $value) {
        $keyStr = $config['keys']['transshipment'];
        $keyStr = str_replace('{articleid}', $value, $keyStr);

        $data[$value] = $memcache->get($keyStr);
        if (empty($data[$value]) || $_debug) {
            //$data = getTransshipment($articleid);
            //$flag[$value] =  $memcache->set($keyStr,$data);
        }
    }

    return $data;
}

//获取文章收藏数（一条时）
function get_collect($clickartids, $_debug = 0) {
    global $config, $memcache;
    $data = array();

    $keyStr = $config['keys']['collect'];
    $keyStr = str_replace('{articleid}', $clickartids, $keyStr);

    $data = $memcache->get($keyStr);
    if (empty($data) || $_debug) {
        //$data = getTransshipment($articleid);
        //$flag =  $memcache->set($keyStr, $data);
    }
    return $data;
}

//获取文章收藏数（多条时）
function get_collectlist($clickartids, $_debug = 0) {
    global $config, $memcache;
    $data = array();

    foreach ($clickartids as $value) {
        $keyStr = $config['keys']['collect'];
        $keyStr = str_replace('{articleid}', $value, $keyStr);

        $data[$value] = $memcache->get($keyStr);
        if (empty($data[$value]) || $_debug) {
            //$data = getTransshipment($articleid);
            //$flag[$value] =  $memcache->set($keyStr,$data);
        }
    }

    return $data;
}

/**
 * 过滤输入
 *
 */
function filterInput($data) {
    if (!is_array($data)) {
        return stripcslashes($data);
    } else {
        $return = array();
        foreach ($data as $k => $v) {
            $return[filterInput($k)] = filterInput($v);
        }

        return $return;
    }
}

/*
  转换输入字符串格式
 */

function array2str($array) {
    $tmp = '';
    $strData = 'array(';
    if (is_array($array) && !empty($array)) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $tmp .= '[' . $k . ']=>' . array2str($v);
            } else {
                $tmp .= '[' . $k . ']=>' . $v;
            }
            $tmp .= ',';
        }
    }
    $tmp = substr($tmp, 0, -1);
    $strData .= $tmp . ')';
    return $strData;
}

/**
 * 判断手机端设备
 * @return boolean
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

function getnTimes() {
    global $config;
    return ((date('Y') - 2014) * 12 + (date('m') - $config['keys']['statday']) + 1);
}

?>