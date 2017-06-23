<?php

define('ROOT', dirname(__FILE__));
require_once ROOT . '/ketfilter_com.php';

/**
 * 过滤文章标题 摘要
 * @param type $mem
 * @param string $year
 * @return boolean
 */
global $gjc;
$gjc = file(DEFAULT_PATH . 'gjc.txt');

function filterChart($mem = 34, $year = 2012) {
    global $mysqli, $gjc;
    if ($mem == -1) {
        $table = 'tbBlogArticleChart';
    } else {
        $table = 'tbBlogArticleStat' . $year . sprintf("%04d", $mem);
    }

    foreach ($gjc as $value) {
        $value = trim($value);
        if (!empty($value)) {
            $value = iconv('GBK', 'UTF-8', $value);
            $where = "WHERE IsDel = 0 and (Title LIKE '%" . $value . "%' or Summary LIKE '%" . $value . "%') ";
            if ($year == 2014 || $mem == -1)
                $where .= ' and AppearTime < \'2014-04-20 23:59:59\'';
            $sql = "SELECT ArticleID,MemberID,AppearTime FROM  $table " . $where . " ";
            $rs = $mysqli->GetAll($sql);
            if (!empty($rs)) {
                foreach ($rs as $v) {
                    delblogarticle($v);
                }
                error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |filterChart|过滤总数：" . count($rs) . "||归档年份：" . $year . "|分表：" . $mem . "||表：" . $table . "|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_chartcount.log');
//            error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |\r\n" . print_r($rs, true) . "\r\n", 3, BLOG_MANAGE_LOG.'keyfilter/socket_keyfilter_data_chart' . md5($value) . '.log');
                unset($rs);
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
    if ($year > 2014)
        return true;
    filterChart(++$mem, $year);
    return true;
}

error_log(date("Y-m-d H:i:s", time()) . " || ------start filterChart1--2012-30---|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

filterChart();
error_log(date("Y-m-d H:i:s", time()) . " || ------end filterChart1--2012-30---|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

//error_log(date("Y-m-d H:i:s", time()) . " || ------end -----|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

exit;