<?php

define('ROOT', dirname(__FILE__));
require_once ROOT . '/ketfilter_com.php';

/**
 * 过滤文章内容
 * @param type $mem
 * @param string $year
 * @return boolean
 */
global $gjc;
$gjc = file(DEFAULT_PATH . 'gjc.txt');

function filterArticle($mem = 33, $year = 2012) {
    global $mysqli, $gjc;

    $table = 'tbBlogArticle' . $year . sprintf("%04d", $mem);

    foreach ($gjc as $value) {
        $value = trim($value);
        if (!empty($value)) {
            $value = iconv('GBK', 'UTF-8', $value);
            $where = "WHERE  Content LIKE '%" . $value . "%' ";
            if ($year == 2014)
                $where .= ' and AppearTime < \'2014-04-20 23:59:59\'';
            $sql = "SELECT ArticleID,AppearTime,MemberID FROM  $table " . $where . " ";
//            print_r($sql);
            $rs = $mysqli->GetAll($sql);
            if (!empty($rs)) {
//                print_r($rs);
                foreach ($rs as $v) {
                    delfilterArticleInfo($v);
                }
                error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |filterArticle|过滤总数：" . count($rs) . "|归档年份：" . $year . "|分表：" . $mem . "|表：" . $table . "|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_datacount_'.$year.'.log');
//            error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |\r\n" . print_r($rs, true) . "\r\n", 3, BLOG_MANAGE_LOG.'keyfilter/socket_keyfilter_data_art' . md5($value) . '.log');
//                $result = array_merge($result, $rs);
//            echo '/-filterArticle-select--/';
            }
        }
    }
    if ($mem == 39 && $year == '') {
        $mem = -1;
        $year = '2012';
    } elseif ($mem == 39 && $year != '') {
        $mem = -1;
        ++$year;
    }
    if ($year > 2014) {
        return true;
    }
    filterArticle(++$mem, $year);
    return true;
}

function delfilterArticleInfo($info) {
    global $mysqli;
    if (!empty($info)) {
            $year = date('Y', strtotime($info['AppearTime']));
            if ($year < 2012)
                $year = '';
            $table = 'tbBlogArticleStat' . $year . sprintf("%04d", ($info['MemberID'] % 40));
            $where = "WHERE IsDel = 0 and ArticleID =" . intval($info['ArticleID']);
            $sql = "SELECT ArticleID,MemberID,AppearTime FROM  $table " . $where . " ";
//                print_r($sql);
            $rs = $mysqli->GetAll($sql);
//            print_r($rs);
            if (!empty($rs)) {
                error_log(date("Y-m-d H:i:s", time()) .'|sql:'.$table. " ||参数：" . print_r($rs, true) .  " |进来的参数：" . print_r($info, true) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_data_delInfo.log');
                delblogarticle($rs[0]);
            }
    }
}


error_log(date("Y-m-d H:i:s", time()) . " || ------start filterArticle--2012-30--|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

filterArticle(); //过滤文章内容

error_log(date("Y-m-d H:i:s", time()) . " || ------end filterArticle---2012-30-|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

exit;