<?php

define('ROOT', dirname(__FILE__));
require_once ROOT . '/ketfilter_com.php';

/**
 * 过滤公告内容
 * @param type $mem
 * @param string $year   ketfilter_affiche
 * @return boolean
 */
//global $gjc;
//$gjc = file(DEFAULT_PATH . 'gjc.txt');

function filterAffiche() {
    global $mysqli, $gjc;
    $result = array();
    foreach ($gjc as $value) {
        $value = trim($value);
        if (!empty($value)) {
//            $value = iconv('GBK', 'UTF-8', $value);
            $where = "WHERE  Content LIKE '%" . $value . "%' ";
            $sql = "select MemberID,AfficheID FROM tbBlogMemberAffiche  $where ";
//            echo $value;
            $rs = $mysqli->GetAll($sql);
            if (!empty($rs)) {
//                print_r($rs);
                error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |filterArticle|过滤总数：" . count($rs) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_affiche.log');
                array_merge($result, $rs);
                delfilterInfo($rs);
//                error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |filterArticle|过滤总数：" . count($rs) . "|参数：|" . serialize($rs) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_affiche_array.log');
            }
        }
    }
    if (!empty($result)) {
        file_put_contents(BLOG_MANAGE_LOG . 'affiche.php', $result);
    }
    return true;
}

function delfilterInfo($arr) {
    global $mysqli;
    if (!empty($arr)) {
        $type = 'B021';

        foreach ($arr as $v) {
            if (!isset($v['MemberID']))
                continue;
            $socket = new CSocket();
            $data['MemberID'] = $v['MemberID'];
            $data['AfficheID'] = $v['AfficheID'];
            $data['Content'] = '';
            $data['Status'] = '0';
//            print_r($data);
            $rs = $socket->senddata($type, $data);
            if (isset($rs['Status']['Code']) && $rs['Status']['Code'] == '00') {
                error_log(date("Y-m-d H:i:s", time()) . '|sql:' . $table . " ||参数：" . print_r($rs, true) . " |进来的参数：" . print_r($data, true) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_affiche_delsucc.log');
            } else {
                error_log(date("Y-m-d H:i:s", time()) . '|sql:' . $table . " ||参数：" . print_r($rs, true) . " |进来的参数：" . print_r($data, true) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_affiche_delerror.log');
            }
        }
    }
}

error_log(date("Y-m-d H:i:s", time()) . " || ------start filterAffiche--1---|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

filterAffiche(); //过滤文章内容

error_log(date("Y-m-d H:i:s", time()) . " || ------end filterAffiche--1---|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

exit;