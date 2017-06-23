<?php

define('ROOT', dirname(__FILE__));
require_once ROOT . '/ketfilter_com.php';
/**
 * 删除评论
 * @global type $mysqli
 * @param type $mem
 * @return booleansh
 */
function filterComment($mem = -1) {
    global $mysqli, $gjc;

    if ($mem == -1) {
        $table = 'tbBlogCommentChart';
    } else {
        $table = 'tbBlogComment' . sprintf("%04d", $mem);
    }
//    print_r($gjc);
    foreach ($gjc as $value) {
        $value = trim($value);
        if (!empty($value)) {
//            $value = iconv('GBK', 'UTF-8', $value);
//            echo $value;
            $where = "WHERE Status = 0 and (Content LIKE '%" . $value . "%') ";
            if ($mem == -1)
                $where .= ' and AppearTime < \'2014-05-14 23:59:59\'';
            $sql = "SELECT CommentID,ArticleID,ArticleMemberID FROM  $table " . $where . " ";
//            echo $sql;
            $rs = $mysqli->GetAll($sql);
            if (!empty($rs)) {
                foreach ($rs as $v) {
                    delblogarticleCom($v);
                }
                error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |filterComment|过滤总数：" . count($rs) . "||分表：" . $mem . "||表：" . $table . "|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_commentcount.log');
//            error_log(date("Y-m-d H:i:s", time()) . " | " . $value . " |\r\n" . print_r($rs, true) . "\r\n", 3, BLOG_MANAGE_LOG.'keyfilter/socket_keyfilter_data_ccomment.log');
                unset($rs);
            }
        }
    }
    if ($mem == 39) {
        return true;
    }

    filterComment(++$mem);
    return true;
}

/**
 * 删除评论
 * @param type $DelIDs
 * @return boolean
 */
function delblogarticleCom($DelIDs) {
    $socket = new CSocket();
    $param['ArtMemberIDs'] = $DelIDs['ArticleMemberID'];
    $param['BlogType'] = 1; //個人博客
    $param['ArticleIDs'] = $DelIDs['ArticleID'];
    $param['CommentIDs'] = $DelIDs['CommentID'];
    $type = "B307";
    $rs = $socket->senddata($type, $param);

    if (isset($rs['Status']['Code']) && $rs['Status']['Code'] == '00') {
        error_log(date("Y-m-d H:i:s", time()) . " || GW：| 状态码：{$rs['Code']} | 参数：" . serialize($param) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_comsuc.log');
        echo '/-id：' . $DelIDs['CommentIDs'] . '-succes--/';
    } else {
        error_log(date("Y-m-d H:i:s", time()) . " || GW：| 状态码：{$rs['Code']} | 参数：" . serialize($param) . "\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_comerror.log');
        echo '/-id：' . $DelIDs['CommentIDs'] . '-error--/';
    }
    return true;
}

error_log(date("Y-m-d H:i:s", time()) . " || ------start filterComment-----|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

filterComment();
error_log(date("Y-m-d H:i:s", time()) . " || ------end filterComment-----|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

error_log(date("Y-m-d H:i:s", time()) . " || ------end -----|\r\n", 3, BLOG_MANAGE_LOG . 'socket_keyfilter_load.log');

exit;