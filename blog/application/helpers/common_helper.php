<?php

/*
 * FCK 获取编辑框
 */

function geteditor($Width = 0, $Height = 0, $Value = '', $ToolBar = 0) {
    require_once ROOTPATH . '/FCKeditor/fckeditor.php';
    $CI = &get_instance();
    $oFCKeditor = new FCKeditor('content');
    $oFCKeditor->BasePath = $CI->config->item('base_url') . '/FCKeditor/';
    $oFCKeditor->Width = ($Width != 0) ? $Width : '100%';
    $oFCKeditor->Height = ($Height != 0) ? $Height : '100%';
    if ($ToolBar != 0)
        $oFCKeditor->ToolbarSet = htmlspecialchars($ToolBar);
    $oFCKeditor->Value = $Value;
    $oFCKeditor->Create();
}

/**
 * 功能：获取文件名和扩展名
 * @param
 * @return array
 */
function getFilename($filename) {
    $tmp = explode(".", $filename);
    if (count($tmp) < 2) {
        $result['filename'] = $filename;
    } else {
        for ($i = 0; $i < count($tmp) - 1; $i++) {
            $prename .= $tmp[$i];
        }
        $result['filename'] = $prename;
        $result['extdname'] = $tmp[count($tmp) - 1];
    }
    return $result;
}

function getSmilie() {
    
}

/*
  数组转换成字符串
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

function tran_num($num) {
    if ($num >= 1000000) {
        $tmp = floor($num / 1000000);
        $tmp = empty($tmp) ? "" : $tmp1 . "百万";
    } else if ($num >= 100000000) {
        $tmp = floor($num / 100000000);
        $tmp = empty($tmp) ? "" : $tmp1 . "亿";
    } else {
        $tmp = $num;
    }
    return $tmp;
}

//验证信息函数
function getVerifyStr($str) {
    $md5Str = md5($str);
    $md5Str = substr($md5Str, 1, 4) . substr($md5Str, 16, 7) . substr($md5Str, 25, 5);
    return $md5Str;
}

//接口提交信息编码
function htmlEncode($str) {
    //$str = addslashes($str);  //gw那边默认会做这一步

    $str = htmlspecialchars($str);
    return $str;
}

function remove_invisible_code($str) {

    // every control character except newline (10), carriage return (13), and horizontal tab (09),
    // both as a URL encoded character (::shakes fist at IE and WebKit::), and the actual character
    $non_displayables = array(
        '/%0[0-8]/', '/[\x00-\x08]/', // 00-08
        '/%11/', '/\x0b/', '/%12/', '/\x0c/', // 11, 12
        '/%1[4-9]/', '/%2[0-9]/', '/%3[0-1]/', // url encoded 14-31
        '/[\x0e-\x1f]/');      // 14-31

    do {
        $cleaned = $str;
        $str = preg_replace($non_displayables, '', $str);
    } while ($cleaned != $str);

    return $str;
}

/*
 * 创建统计脚本的URL
 */

function createviewurl($duid, $mid, $dom, $bn, $aid = '0', $vuid = '0', $un = '0', $nn = '0', $articletime = '0', $title = '0') {
    $url = config_item('base_url') . '/stat.new/i.count.php?';
    $vuid = empty($vuid) ? '0' : $vuid;
    $un = empty($un) ? '0' : $un;
    $nn = empty($nn) ? '0' : $nn;
    $url .= empty($duid) ? 'duid=0' : 'duid=' . $duid;
    $url .= empty($mid) ? '&mid=0' : '&mid=' . $mid;
    $url .= empty($dom) ? '&dom=0' : '&dom=' . urlencode($dom);
    $url .= empty($bn) ? '&bn=0' : '&bn=' . urlencode($bn);
    $url .= empty($aid) ? '&aid=0' : '&aid=' . $aid;
    $url .= empty($vuid) ? '&vuid=0' : '&vuid=' . $vuid;
    $url .= empty($un) ? '&un=0' : '&un=' . urlencode($un);
    $url .= empty($nn) ? '&nn=0' : '&nn=' . urlencode($nn);
    $url .= empty($articletime) ? '&articletime=0' : '&articletime=' . $articletime;
    $url .= empty($title) ? '&title=0' : '&title=' . $title;
    $url .= '&co=' . getVerifyStr($duid . $mid . $dom . $bn . $aid . $vuid . $un . $nn);
    return $url;
}

function curPageURL() {
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/*
 * 获取用户头像
 */

function getUserHead($uid, $size = 48) {
    $imgsite = &config_item('headurl');
    $head = $imgsite . getIdFolder($uid) . 'head.' . $uid . '.' . $size;
    return $head;
}

function getUserHeadbig($uid, $size = 192) {
    $imgsite = &config_item('headurl');
    $head = $imgsite . getIdFolder($uid) . 'head.' . $uid . '.' . $size . '?r=' . time();
    $getsize = @getimagesize($head);
    if ($getsize['0'] != 192 || $getsize['1'] != 192) {
        $head = $imgsite . getIdFolder($uid) . 'head.' . $uid . '.96?r=' . time();
    }
    return $head;
}

/*
 * 字符截取
 */

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
    return mb_strimwidth($string, $start, $width, $trimmarker, $encoding);
}

//获取博客文章摘要
function getsummary($str, $size, $point = 0) {
    $str = strip_tags($str);

    if ($point == 0) {
        $temp = utf8_str($str, $size);
    } else {
        $temp = utf8_str($str, $size, 'false');
    }
    $temp = str_replace("\r\n", ' ', $temp);
    $temp = str_replace("\n", ' ', $temp);
    $temp = str_replace("\r", ' ', $temp);
    $temp = str_replace("\t", ' ', $temp);
    $temp = str_replace("&nbsp;", ' ', $temp);

    return trim($temp);
}

//特殊符号转换
function replace($str) {
    $str = str_replace('“', '"', $str);
    $str = str_replace('”', '"', $str);
    $str = str_replace('：', ':', $str);
    $str = str_replace('！', '!', $str);
    $str = str_replace('—', '-', $str);
    return $str;
}

/* * js跳转 */

function cnfolAlert($message) {
    echo '<script>';
    echo "alert('" . $message . "');";
    echo '</script>';
}

//回转
function rollbackto() {
    echo '<script language="javascript">window.history.back();</script>';
    exit;
}

/**
 *  cnfolLocation
 *  博客地址转向
 */
function cnfolLocation($url = '', $target = 'top') {
    $BLOG_URL = &config_item('base_url');

    if ($url == '') {
        $url = $BLOG_URL;
    }
    echo '<script language="javascript">';
    if ($target == 'top') {
        echo "window.top.location.href='" . $url . "';";
    } else if ($target == 'self') {
        echo "window.location.href='" . $url . "';";
    }
    echo '</script>';
    exit;
}

/* 将xml转化为数组 */

function xmltoarray($string) {
    $xml = &load_class('xmlClass');
    $xml = new CI_xmlClass($string);
    $rs = $xml->loadStr($string);
    $result = $rs['Status'];

    if (isset($rs['Records']['Record'])) {
        $result['Record'] = $rs['Records']['Record'];
    }

    unset($rs);
    return $result;
}

/**
 * 功能：获取用户上传文件的目录
 * @id 用户ID
 * @return array
 */
function getIdFolder($id = 0) {
    $id_encode = md5($id);
    $folder_1 = substr($id_encode, 0, 2);
    $folder_2 = substr($id_encode, 2, 2);
    return '/' . $folder_1 . '/' . $folder_2 . '/' . $id . '/';
}

/**
 * 功能：获取当前地址
 */
function getCurUrl($flag = '') {
    $result = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if ($flag == 'base64') {
        return base64_encode($result);
    }
    return $result;
}

/*
 * trackback加密
 */

function Md5trackback($articleId) {
    $trackId = substr(md5($articleId), 10, 5) . substr(md5($articleId), 20, 5);
    return $trackId;
}

//生成TrackBackURL
function TrackbackUrl($articleId) {
    $baseurl = config_item('base_url');
    return $baseurl . "/" . $articleId . "/" . Md5trackback($articleId) . ".trackback";
}

//发起引用通告
function sendping($tractback, $data) {
    if (!empty($tractback)) {
        $CI = & get_instance();
        $CI->load->library('trackback');
        $tractback = explode(';', $tractback);
        $tractback = (is_string($tractback)) ? array(0 => $tractback) : $tractback;
        foreach ($tractback as $pingurl) {
            $data['ping_url'] = $pingurl;
            $return = $CI->trackback->send($data);
        }
    }
}

/*
  根据模板id获取css地址
 */

function getCssLink($templateid, $hz = 'css') {
    $CI = &get_instance();
    $path = $CI->config->item('cssfileurl') . '/';
    $id_encode = md5($templateid);
    $folder_1 = substr($id_encode, 0, 2);
    $folder_2 = substr($id_encode, 2, 2);
    $style = $path . $folder_1 . '/' . $folder_2 . '/' . $id_encode . '.' . $hz;
    return $style;
}

//根据样式id获取Img 预览图片地址
function getCssImg($styleid) {
    if (!isset($_COOKIE['blogcss_' . $styleid])) {
        $path = config_item('cssimgurl') . '/';
        $id_encode = md5($styleid);
        $folder_1 = substr($id_encode, 0, 2);
        $folder_2 = substr($id_encode, 2, 2);
        $style = $path . $folder_1 . '/' . $folder_2 . '/' . $id_encode . '.jpg';
    } else {
        $style = config_item('cssimgloading');
    }
    return $style;
}

/*
  根据根据用户ID获取相册图片的URL
 */

function getPhotoImg($userid) {
    $path = config_item('photourl');
    return $path . getIdFolder($userid);
}

/*
  根据根据用户ID获取相册图片上传目录
 */

function getUploadPath($userid, $basepath = '') {
    if ($basepath) {
        $path = $basepath;
    } else {
        $path = config_item('uploadpath');
    }
    return $path . getIdFolder($userid);
}

/*
 * 创建目录
 * @path 目录路径
 */

function create_folder($path) {
    $status = true;
    if (!dir_exists($path)) {
        $status = mkdirs($path, 0777);
    }
    return $status;
}

/*
 * 创建目录
 * @dir     目录路径
 * @mode    权限设置 如0775
 * return bool
 */

function mkdirs($dir, $mode = FS_RIGHTS_D) {
    $stack = array(basename($dir));
    $path = null;
    while ($d = dirname($dir)) {
        if (!is_dir($d)) {
            $stack[] = basename($d);
            $dir = $d;
        } else {
            $path = $d;
            break;
        }
    }

    if (($path = realpath($path)) === false) {
        return false;
    }

    $created = array();
    for ($n = count($stack) - 1; $n >= 0; $n--) {
        $s = $path . '/' . $stack[$n];
        if (!mkdir($s, $mode)) {
            for ($m = count($created) - 1; $m >= 0; $m--) {
                rmdir($created[$m]);
            }
            return false;
        }
        if (!chmod($s, 0777)) {
            return false;
        }
        $created[] = $s;
        $path = $s;
    }
    return true;
}

/*
 * 判断目录是否存在
 * @dir_name    目录路径
 * return bool
 */

function dir_exists($dir_name = false) {
    if (!$dir_name) {
        return false;
    }

    if (is_dir($dir_name)) {
        return true;
    }
    return false;
}

//获取图片的名字
function getImgName($imgname) {
    $begin = strlen($imgname) - strrpos($imgname, '.');
    $ext = substr($imgname, -$begin);
    srand(time());
    return date('Ymdhis') . rand(1000, 9000) . $ext;
}

/**
 * 获取唯一的文件名
 * @ext 扩展名
 */
function getUploadFileName($ext = '') {
    $filename = time() . rand(10000, 99999);
    if (!empty($ext)) {
        $filename = $filename . "." . $ext;
    }
    return $filename;
}

/**
 * @ 同步头像、附件到服务器
 * @ params
 *           $localurl
 *           $op
 * @ return  bool
 * */
function ftpAttachment($dest, $localurl = '', $op = 1) {
    if ($op == 1) {
        $exec = '/opt/aiya/nginx/fastcgi/bin/php ';
        $exec .= URL_CRONTAB . 'upload/upload.php ' . $dest . ' ' . $localurl . ' 1>> /home/www/html/logs/upfile.log &';
        system($exec);
    } else {
        system('/opt/aiya/nginx/fastcgi/bin/php ' . URL_CRONTAB . 'upload/delfile.php ' . $dest . ' 1>> /home/www/html/logs/delfile.log &');
    }
}

/**
 * @ ftp上传博客文章内的图片
 * */
function ftpArticleImg($dest, $localurl = '') {
    $exec = '/opt/aiya/nginx/fastcgi/bin/php ';

    $exec .= URL_CRONTAB . 'upload/upload_article_img.php ' . $dest . ' ' . $localurl . ' 1>> /home/www/html/logs/upfile.log &';
    //echo $exec;exit;
    system($exec);
}

//创建样式图片
function BuildCssImg($url, $id) {
    $buildcssimg = config_item('buildcssimg');
    file_get_contents($buildcssimg . $url . '|' . $id);
}

//图片服务生成样式
function BuildCss($data) {
    $url = config_item('buildcssurl');
    $url = parse_url($url);
    if (!$url) {
        return "couldn't parse url";
    }
    if (!isset($url['port'])) {
        $url['port'] = "";
    }
    if (!isset($url['query'])) {
        $url['query'] = "";
    }
    $encoded = "";
    foreach ($data as $k => $v) {
        $encoded .= ($encoded ? "&" : "");
        $encoded .= rawurlencode($k) . "=" . rawurlencode($v);
    }
    $fp = @fsockopen($url['host'], $url['port'] ? $url['port'] : 80);
    if (!$fp) {
        return "Failed to open socket to $url[host]";
    }
    fputs($fp, sprintf("POST %s%s%s HTTP/1.0\n", $url['path'], $url['query'] ? "?" : "", $url['query']));
    fputs($fp, "Host: $url[host]\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
    fputs($fp, "Content-length: " . strlen($encoded) . "\n");
    fputs($fp, "Connection: close\n\n");
    fputs($fp, "$encoded\n");
    $line = fgets($fp, 1024);
    if (!eregi("^HTTP/1\.. 200", $line)) {
        return;
    }
    $results = "";
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp, 1024);
        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        } elseif (!$inheader) {
            $results .= $line;
        }
    }
    @fclose($fp);
    return $results;
}

function checkhtml($html) {

    preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

    $searchs[] = '<';
    $replaces[] = '&lt;';
    $searchs[] = '>';
    $replaces[] = '&gt;';

    if ($ms[1]) {
        $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote|object|param|embed';
        $ms[1] = array_unique($ms[1]);
        foreach ($ms[1] as $value) {
            $searchs[] = "&lt;" . $value . "&gt;";

            $value = str_replace('&', '_uch_tmp_str_', $value);
            $value = dhtmlspecialchars($value);
            $value = str_replace('_uch_tmp_str_', '&', $value);

            $value = str_replace(array('\\', '/*'), array('.', '/.'), $value);
            $skipkeys = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
                'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
                'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
                'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
                'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
                'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
                'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
                'onsubmit', 'onunload', 'javascript', 'script', 'eval', 'behaviour', 'expression', 'class');
            $skipstr = implode('|', $skipkeys);
            $value = preg_replace(array("/($skipstr)/i"), '.', $value);
            if (!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                $value = '';
            }
            $replaces[] = empty($value) ? '' : "<" . str_replace('&quot;', '"', $value) . ">";
        }
    }
    $html = str_replace($searchs, $replaces, $html);

    //$html = addslashes($html);

    return $html;
}

function dhtmlspecialchars($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
        if (strpos($string, '&amp;#') !== false) {
            $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
        }
    }
    return $string;
}

/* 过滤危险html */

function FilterJs($str) {

    /* 过滤style标签 */
    return preg_replace_callback(
                    /* 过滤style标签内容 */
                    '/(\<\s*style[^\>]*\>)((?:(?!\<\s*\/\s*style\s*\>).)*)(\<\s*\/\s*style\s*\>)?/i', @create_function('$str', 'return $str[1] . fillter_css($str[2]) . $str[3];'), preg_replace(
                            array(
                        /* 删除html注释 */
                        '/\<\!\-\-.*?\-\-\>/i',
                        /* 删除标签：script、link、object、embed、iframe、frame、frameset */
                        //'/\<\s*(script|object|embed|link|i?frame(set)?)[^\>]*\>(.*?\<\s*\/\s*\\1\s*\>)?/i',
                        '/\<\s*(meta|script|link|i?frame(set)?)[^\>]*\>(.*?\<\s*\/\s*\\1\s*\>)?/i',
                        /* 删除事件、javascript协议、css表达式 */
                        //'/\<[^\>]+((on[a-z]+\s*\=|javascript\:[^\;\"\\\']|expression\s*\()[^\>]*)+\>?/i',
                        '/\<[^\>]+((onload|onmouseout|onmouseover\s*\=|javascript\:[^\;\"\\\']|expression\s*\()[^\>]*)+\>?/i',
                            ), '', $str
                    )
    );
}

/* 防止挂马直接全部过滤掉 */

function FilterAllEmbed($str) {
    return preg_replace('/\<\s*(object|embed)[^\>]*\>(.*?\<\s*\/\s*\\1\s*\>)?/i', '', $str);
}

/* 过滤Embed跳转flash，未能有效的防止挂马，但为了一些历史的影音代码，写了个兼容，效果一般 */

function FilterEmbed($str) {
    //return preg_replace('/\<\s*(object|embed)[^\>]*\>(.*?\<\s*\/\s*\\1\s*\>)?/i','',$str);

    if (preg_match_all('/\<\s*(embed)[^\>]*\>(.*?\<\s*\/\s*\\1\s*\>)?/i', $str, $out)) {
        if (!empty($out[0])) {
            foreach ($out[0] as $k => $v) {
                if (!preg_match("/(allowNetworking\s*=\s*(\'|\")internal(\'|\"))/i", $v)) {
                    //$str = str_replace($v, '', $str);
                    $v2 = substr($v, 0, -2);
                    $v2 .= ' allowNetworking="internal" />';
                    $str = str_replace($v, $v2, $str);
                }
            }
        }
    }
    return $str;
}

//过滤表情以外的图片
function filterEmoticon($str) {
    //emoticons
    if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $str, $out)) {
        if (!empty($out[0])) {
            foreach ($out[0] as $k => $v) {
                if (!preg_match("/(emoticons)/i", $v)) {
                    $str = str_replace($v, '', $str);
                }
            }
        }
    }
    return $str;
}

/* 过滤样式正文 */

function fillter_css($str) {
    /* 删除注释、javascript协议、表达式 */
    return preg_replace(array('/(\/\*((?!\*\/).)*\*\/|\/\*|\*\/)/i', '/expression\s*\((.*?\))?|javascript\s*\:/i',), '', $str);
}

/* 过滤关键字屏蔽 */

function filter_word($str) {
    $fileword = config_item('filter_path') . 'FilterWord/FilterWord.txt';
    if (file_exists($fileword)) {
        $filterword = unserialize(file_get_contents($fileword));
        if (count($filterword) > 0) {
            foreach ($filterword as $word) {
                $wordArr = explode(',', $word);
                if (count($wordArr) > 1) {
                    foreach ($wordArr as $w) {
                        if (strstr($str, $w) && !is_numeric($w)) {
                            $str = str_replace($w, '***', $str);
                        }
                    }
                } else {
                    if (strstr($str, $word) && !is_numeric($word)) {
                        $str = str_replace($wordArr[0], '***', $str);
                    }
                }
            }
        }
    }
    return $str;
}

/* Ip屏蔽 */

function filter_ip() {
    $fileword = config_item('filter_path') . 'Ip/filterIp.txt';
    if (file_exists($fileword)) {
        $filterword = unserialize(file_get_contents($fileword));
        if (count($filterword) > 0) {
            $CI = &get_instance();
            $thsiIp = $CI->input->ip_address();
            foreach ($filterword as $ip) {
                if (preg_match('/' . $ip . '/', $thsiIp)) {
                    return true;
                }
            }
        }
    }
    return false;
}

//内容过滤
function filter($str, $f = false) {
    $str = filter_word($str);
    $str = FilterJs($str);
    if ($f === true) {
        $str = checkhtml($str);
    } else {
        $str = FilterEmbed($str);
    }
    return $str;
}

//外连接过滤
function filterURL($str) {
    if (preg_match_all("/((http:\/\/|https:\/\/|ftp:\/\/|www\.)+[A-Za-z0-9_]+\.[A-Za-z0-9_]+([\w-.\/?%&=]*)?)+/is", $str, $out)) {
        if (!empty($out[0])) {
            foreach ($out[0] as $k => $v) {
                if (!preg_match("/(http:\/\/|www).*?cnfol\.com/i", $v) && !preg_match("/(http:\/\/|www).*?cnfolimg\.com/i", $v)) {
                    $str = str_replace($v, '', $str);
                }
            }
        }
    }
    return $str;
}

//获取主博客的域名
function getPrimariBlogDomain($BlogList) {

    $domain = '';
    if ($BlogList['RetRecords'] == 1) {
        $domain = isset($BlogList['Record'][0]['DomainName']) ? $BlogList['Record'][0]['DomainName'] : $BlogList['Record']['DomainName'];
        return $domain;
    } else if ($BlogList['RetRecords'] > 1) {
        foreach ($BlogList['Record'] as $vblog) {

            if ($vblog['IsPrimary'] == 1) {

                if ($vblog['Status'] == '1') {
                    foreach ($BlogList['Record'] as $otherblog) {
                        if ($otherblog['Status'] != '1') {
                            return $otherblog['DomainName'];
                        }
                    }
                }

                return $vblog['DomainName'];
            }
        }

        if ($BlogList['Record'][0]['Status'] == '1') {
            foreach ($BlogList['Record'] as $otherblog) {
                if ($otherblog['Status'] != '1') {
                    return $otherblog['DomainName'];
                }
            }
        }


        return $BlogList['Record'][0]['DomainName'];
    }
    return $domain;
}

//获取主博客的域名的memberid,如果主域名被关闭就获取其他域名的memberid
function getPrimariBlogMemberId($BlogList) {
    $MemberID = '';
    if ($BlogList['RetRecords'] == 1) {
        $MemberID = isset($BlogList['Record'][0]['MemberID']) ? $BlogList['Record'][0]['MemberID'] : $BlogList['Record']['MemberID'];
        return $MemberID;
    } else if ($BlogList['RetRecords'] > 1) {
        foreach ($BlogList['Record'] as $vblog) {
            $i = 0;
            if ($vblog['IsPrimary'] == 1 && $vblog['Status'] == 0) {
                return $vblog['MemberID'];
            }

            if ($vblog['Status'] == 0 && $i == 0) {
                $MemberID = $vblog['MemberID'];
                $i++;
            }
        }
        return $MemberID;
    }
    return $MemberID;
}

//设置Cookie定义已经发表
function setPublic($name = '_pubtimelimit', $expiretime = bublictimelimit) {
    $path = config_item('cookie_path');
    $domain = config_item('cookie_domain');
    $flag = setCookie($name, time(), time() + 60, $path, $domain);
    return $flag;
}

//验证是否有发表的Cookie
function checkPublic($name = '_pubtimelimit') {
    $CI = &get_instance();
    $data = intval($CI->input->cookie('_pubtimelimit'));
    return $data == "" ? 0 : $data;
}

//设置Cookie定义 发表内容 key  30分钟
function setCommentSign($content) {
    $path = config_item('cookie_path');
    $domain = config_item('cookie_domain');
    $flag = setCookie('_pubcommentsign', md5(trim($content)), time() + commenttimelimit, $path, $domain);
    return $flag;
}

//设置Cookie定义 初始化文章分类序号用
function setInitialize($memberid) {
    $path = config_item('cookie_path');
    $domain = config_item('cookie_domain');
    $flag = setCookie('initialize_' . $memberid, '1', time() + initializesort, $path, $domain);
    return $flag;
}

//设置我的博客页面已经展示过的文章id Cookie
function setArtId($id = '', $expiretime = '1800') {

    $path = config_item('cookie_path');
    $domain = config_item('cookie_domain');
    $flag = setCookie('already_' . $id, $id, time() + $expiretime, $path, $domain);
    return $flag;
}

function getArtid($id = '') {
    if (!isset($_COOKIE['already_' . $id])) {
        return false;
    }
    return true;
}

//验证 发表内容 key
function checkCommentSign($content) {
    $sign = md5(trim($content));
    $CI = &get_instance();
    $data = $CI->input->cookie('_pubcommentsign');
    error_log(date("Y-m-d H:i:s") . " | $content | $sign | $data\r\n", 3, '/home/www/html/logs/comment.log');
    return $data == $sign;
}

/*
  | 模拟POST
  |===========================
 */

function curl_post($url, $curlPost) {
    $ch = curl_init();
    if (is_array($curlPost)) {
        $curlPost = http_build_query($curlPost);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/*
  | 模拟GET
  |===========================
 */

function curl_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/*
  | 输出数据
  |===========================
 */

function d($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

/*
  | 输出数据
  |===========================
 */

function v($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

/*
  | 日志
  |===========================
 */

function loger($data, $filename = '', $class, $method) {
    $time = date('Y-m-d H:i:s', time());
    $msg = "$time | $class | $method | $data" . PHP_EOL;
    $logpath = &config_item('logbasepath');
    error_log($msg, 3, $logpath . $filename . '_' . date('Ymd') . '.log');
}

/*
  | 全角转半角
  |===========================
 */

function make_semiangl($str) {
    //$str = "０１２３ＡＢＣＤＦＷＳ＼＂，．？＜＞｛｝［］＊＆＾％＃＠！～（）＋－｜：；"; 
    $str = preg_replace('/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)', $str);
    return $str;
}

/* 二维数组排序 */

function array_sort($arr, $keys, $type = 'asc') {
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/*
  | 文章页评论用户是否在黑名单查询用
  |===========================
 */

function blackCheck($fuid, $data) {
    if (empty($data)) {
        return false;
    }

    if (empty($data[0])) {
        $data = array('0' => $data);
    }

    foreach ($data as $value) {
        if ($value['UserID'] == $fuid) {
            return true;
        }
    }

    return false;
}

/**
 * page 分页
 *
 * @param int $page 当前页码
 * @param int $pagesize 每页条数
 * @param int $pagecount 总页数
 * @param int $totalcount 总条数
 * @param int $start 起始数
 * @return void
 */
function page(&$page, &$pagesize, &$pagecount, &$totalcount, &$start) {
    $pagecount = ceil($totalcount / $pagesize);
    if ($page == '') {
        $page = 1;
    }

    if ($page > $pagecount) {
        $page = $pagecount;
    }

    if ($page < 1) {
        $page = 1;
    }

    $start = ($page - 1) * $pagesize;
}

/**
 * paging 分页串
 *
 * @param string $link 链接
 * @param int $page 当前页码
 * @param int $pagecount 总页数
 * @param int $listcount 显示页数
 * @return string
 */
function paging($link, $page, $pagecount, $listcount) {
    $leftpage = $page;
    $rightpage = $page;
    $prevpage = $page - 1;
    $nextpage = $page + 1;

    $i = 1;

    while ($listcount > $i) {
        $mod = $i % 2;

        if ($mod == '0') {
            $rightpage++;
            if ($pagecount >= $rightpage) {
                $rightrtn .= '<a href=' . $link . $rightpage . '>' . $rightpage . '</a>&nbsp;';
            } else {
                $leftpage--;
                if ($leftpage > 0) {
                    $leftrtn = '<a href=' . $link . $leftpage . '>' . $leftpage . '</a>&nbsp;' . $leftrtn;
                }
            }
            $i++;
        } else {

            $leftpage--;
            if ($leftpage > 0) {
                $leftrtn = '<a href=' . $link . $leftpage . '>' . $leftpage . '</a>&nbsp;' . $leftrtn;
            } else {
                $rightpage++;
                if ($pagecount >= $rightpage) {
                    $rightrtn .= '<a href=' . $link . $rightpage . '>' . $rightpage . '</a>&nbsp;';
                }
            }
            $i++;
        }
    }

    $currtn = '<a class="Cur">' . $page . '</a>&nbsp;';
    if ($page > 1) {
        $prevpagelist = '<a class="first" href=' . $link . '1>首页</a>&nbsp;<a class="review" href=' . $link . $prevpage . '>上一页</a>&nbsp;';
    }

    if ($pagecount > $page) {
        $nextpagelist = '<a class="next" href=' . $link . $nextpage . '>下一页</a>&nbsp;<a class="last" href=' . $link . $pagecount . '>尾页</a>';
    }

    $rtn = $prevpagelist . $leftrtn . $currtn . $rightrtn . $nextpagelist;

    if ($pagecount == '0') {
        return '';
    }
    return $rtn;
}

//返回时间
function timeop($inTime) {
    $time = strtotime($inTime);
    $ntime = time() - $time;
    if ($ntime < 60) {
        return("刚才");
    } elseif ($ntime < 3600) {
        return(intval($ntime / 60) . "分钟前");
    } elseif ($ntime < 3600 * 24) {
        return(intval($ntime / 3600) . "小时前");
    } else {
        if (substr($inTime, 0, -15) < date("Y")) {
            return (date("Y年n月j日", $time));
        } else {
            return (date("n月j日", $time));
        }
    }
}

/**
 * parse_incoming 格式化输入
 *
 * @return void
 */
function parse_incoming() {
    $return = array();

    if (is_array($_GET)) {
        while (list($k, $v) = each($_GET)) {
            if (is_array($_GET[$k])) {
                while (list($k2, $v2) = each($_GET[$k])) {
                    $return[$k][$k2] = trim($v2);
                }
            } else {
                $return[$k] = trim($v);
            }
        }
    }

    if (is_array($_POST)) {
        while (list($k, $v) = each($_POST)) {
            if (is_array($_POST[$k])) {
                while (list($k2, $v2) = each($_POST[$k])) {
                    $return[$k][$k2] = trim($v2);
                }
            } else {
                $return[$k] = trim($v);
            }
        }
    }
    $return['ip'] = $_SERVER['REMOTE_ADDR'];
    $pattern = "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/";
    $replacement = "\\1.\\2.\\3.\\4";
    $return['ip'] = preg_replace($pattern, $replacement, $return['ip']);
    $return['method'] = $_SERVER['REQUEST_METHOD'] ? strtolower($_SERVER['REQUEST_METHOD']) : strtolower($REQUEST_METHOD);

    return $return;
}

/**
 * utf8_strlen 获取uft8字符串长度
 *
 * @param string $str 字符串数据
 * @return string
 */
function utf8_strlen($str) {
    return ccHtmlStrLen($str);
//    return strlen(iconv('utf-8', 'gb2312', $str));
}

/**
 * 去html统计字数
 * @param type $str 字符串数据
 * @return type
 */
function ccHtmlStrLen($str) { #计算中英文混合字符串的长度 
    $search = array("'<script[^>]*?>.*?</script>'si", // 去掉 javascript 
        "'<[\/\!]*?[^<>]*?>'si", // 去掉 HTML 标记 
        "'([\r\n])[\s]+'", // 去掉空白字符 
        "'&(quot|#34);'i", // 替换 HTML 实体 
        "'&(amp|#38);'i",
        "'&(lt|#60);'i",
        "'&(gt|#62);'i",
        "'&(nbsp|#160);'i",
        "'&(iexcl|#161);'i",
        "'&(cent|#162);'i",
        "'&(pound|#163);'i",
        "'&(copy|#169);'i",
        "'&#(\d+);'e");                    // 作为 PHP 代码运行 

    $replace = array("",
        "",
        "\\1",
        "\"",
        "&",
        "<",
        ">",
        " ",
        chr(161),
        chr(162),
        chr(163),
        chr(169),
        "chr(\\1)");

    $b = preg_replace($search, $replace, $str);
    $b = str_replace(" ", "", $b); //去掉空格 
    $str_number = mb_strlen($b, 'UTF-8');
    return $str_number * 2;
}

/**
 * @ check_str 信息校验
 *
 * @param string $input 输入信息
 * @param $type $type 信息类型
 * @return mixed
 */
function check_str($input, $type) {
    $formats = array(
        'email' => '/^[\w\-\.]+\@[\w\-]+[\w\-\.]+$/',
        'mobile' => '/^1[0-9]{10}$/',
        'password' => '/[^\w\@\#\$]/',
        'username' => '/[^\w]|^\d+$/',
        'ousername' => '/[^\w\-]/',
        'nickname' => '',
        'ip' => '',
        'empty' => ''
    );

    switch ($type) {
        case 'empty':
            return empty($input);
        default:
            return preg_match($formats[$type], $input);
    }

    return true;
}

//获取发文章用热门标签
function getHotTag($file) {
    $hotTag = fopen($file, r);
    $file = fgets($hotTag);
    $file = explode('，', $file);
    fclose($hotTag);

    if (!$file) {
        return false;
    }

    $str = array();
    foreach ($file as $key) {
        if (trim($key) != '') {
            $str[] = $key;
        }
    }
    return $str;
}

//this
//保10洁过滤
function systemBao($id, $file) {
    system("/opt/aiya/nginx/fastcgi/bin/php  " . DEFAULT_PATH . "/newestblog/stat.new/bao10jie/$file \"" . $id . "\"" . ' 1>> ' . DEFAULT_PATH . '/logs/baofile.log &'); //注意上线后：Warning: mkdir(): Permission denied in /mysqldata/html/newblog/stat.new/bao10jie/lib/BCLogger.php on line 53
}

function writefile($content, $filename) {

    $config['baseurl'] = config_item('base_url');
    $config['destpath'] = './shtml/';
    $config['fprefix'] = 'runcront_';

    //关键字过滤路劲
    $config['filter_path'] = '/expansion/FilterWord/';
    //不参与排行的博客组
    $config['limitgroups'] = '23';        //过滤排行组用户，属于排行组的博客不能参与排行
    $config['limitsystag'] = '1459,1470,1472'; //过滤 广告区 和 休闲区上排行
    //Cache的时间限制
    $config['timelimit'] = 600;  //10分钟
    $config['pagesize'] = 60;   //排行单次请求条数的限制
    $config['pagelimit'] = 60;   //排行最多往后翻页60页
    //博客点击排行配置
    $config['click']['maxsize'] = 60; //点击获取的总数，这个需要大于下面配置的值
    $config['click']['rqphb'] = 50; //人气排行展示条数
    $config['click']['bkphb_index'] = 20; //博客排行版20
    $config['click']['bkphb_systag'] = 30; //博客排行版20
    $config['click']['topzph'] = 50; //Top总排行
    $config['click']['24xsph'] = 50; //24小时排行
    //配置目标文件名
    $config['dest']['clickrenqiphb'] = 'renqipaihangban.shtml';
    $config['dest']['clickblogrank_index'] = 'shouyeblogrank.shtml';
    $config['dest']['clickblogrank_systag'] = 'systagblogrank.shtml';
    $config['dest']['clicktoptolrank'] = 'clicktoprank.shtml';
    $config['dest']['topclick24rank'] = 'topclick24rank.shtml';

    //博客热门文章排行配置
    $config['artcom']['daysrank'] = 50; //今日热门回复
    $config['artcom']['weekrank'] = 15; //一周热门回复
    $config['artcom']['tdayrank'] = 15; //48小时热门回复
    $config['artcom']['totalrank'] = 15; //总热门回复
    //配置目标文件名
    $config['dest']['artcomdaysrank'] = 'artcomdaysrank.shtml';
    $config['dest']['artcomtdayrank'] = 'artcomtdayrank.shtml';
    $config['dest']['artcomweekrank'] = 'artcomweekrank.shtml';
    $config['dest']['artcomtotalrank'] = 'artcomtotalrank.shtml';

    //博客热门点击排行配置
    $config['artclick']['day24rank'] = 35; //今日热门文章
    $config['artclick']['daygiftrank'] = 30; //分类栏目鲜花排行
    $config['artclick']['daygiftrank_golden'] = 20; //分类栏目鲜花排行(黄金博客用)
    $config['artclick']['weekrank'] = 15; //一周热门点击
    $config['artclick']['tdayrank'] = 15; //48小时热门点击
    $config['artclick']['totalrank'] = 15; //总热门点击
    $config['artclick']['meijiurank'] = 31; //美酒博客页右侧(给鲜花排行用)
    //配置目标文件名
    $config['dest']['artclickday24rank'] = 'artclickday24rank.shtml';
    $config['dest']['artclicktdayrank'] = 'artclicktdayrank.shtml';
    $config['dest']['artclickweekrank'] = 'artclickweekrank.shtml';
    $config['dest']['artclicktotalrank'] = 'artclicktotalrank.shtml';

    $config['dest']['artclickday24rank_1461'] = 'artclickday24rank_1461.shtml'; //股市天地
    $config['dest']['artclickday24rank_1445'] = 'artclickday24rank_1445.shtml'; //基金
    $config['dest']['artclickday24rank_1463'] = 'artclickday24rank_1463.shtml'; //财经杂谈
    $config['dest']['artclickday24rank_1465'] = 'artclickday24rank_1465.shtml'; //外汇
    $config['dest']['artclickday24rank_1433'] = 'artclickday24rank_1433.shtml'; //期指期货
    $config['dest']['artclickday24rank_1464'] = 'artclickday24rank_1464.shtml'; //港股
    $config['dest']['artclickday24rank_1469'] = 'artclickday24rank_1469.shtml'; //权政
    $config['dest']['artclickday24rank_1447'] = 'artclickday24rank_1447.shtml'; //理财消费
    $config['dest']['artclickday24rank_1449'] = 'artclickday24rank_1449.shtml'; //保险
    $config['dest']['artclickday24rank_1451'] = 'artclickday24rank_1451.shtml'; //银行
    $config['dest']['artclickday24rank_1453'] = 'artclickday24rank_1453.shtml'; //黄金
    $config['dest']['artclickday24rank_1457'] = 'artclickday24rank_1457.shtml'; //债券
    $config['dest']['artclickday24rank_1455'] = 'artclickday24rank_1455.shtml'; //汽车
    $config['dest']['artclickday24rank_1459'] = 'artclickday24rank_1459.shtml'; //休闲
    $config['dest']['artclickday24rank_1471'] = 'artclickday24rank_1471.shtml'; //美酒
    $config['dest']['artclickday24rank_1462'] = 'artclickday24rank_1462.shtml'; //白银
    $config['dest']['artclickday24rank_1460'] = 'artclickday24rank_1460.shtml'; //投资收藏
    $config['dest']['artclickday24rank_1446'] = 'artclickday24rank_1446.shtml'; //信托

    $config['dest']['artclickday24rank_r2'] = 'artclickday24rank_r2.shtml'; //名家
    $config['dest']['artclickday24rank_r3'] = 'artclickday24rank_r3.shtml'; //高手
    $config['dest']['artclickday24rank_r1'] = 'artclickday24rank_r1.shtml'; //网友自荐
    $config['dest']['artclickday24rank_r4'] = 'artclickday24rank_r4.shtml'; //热门专题


    $config['tagusesize'] = 6; //首页栏目标签采用的文章列表获取数目
    //系统标签
    $config['usetag'] = array('1461' => 'newartusebytaggstt', //股市天地
        '1445' => 'newartusebytagjj', //基金
        '1463' => 'newartusebytagjjzt', //经济杂谈
        '1465' => 'newartusebytagwh', //外汇
        '1433' => 'newartusebytagqh', //期货
        '1464' => 'newartusebytaggg', //港股
        '1469' => 'newartusebytagzz', //政权
        '1447' => 'newartusebytaglc', //理财
        '1449' => 'newartusebytagbx', //保险
        '1451' => 'newartusebytagyh', //银行
        '1453' => 'newartusebytaghj', //黄金
        '1457' => 'newartusebytagzq', //债券
        '1455' => 'newartusebytagqc', //汽车
        '1459' => 'newartusebytagxxq', //休闲区
        '1471' => 'newartusebytagmj', //美酒
        '1462' => 'newartusebytagby', //白银
        '1460' => 'newartusebytagtzsc', //投资收藏
        '1446' => 'newartusebytagxt'        //信托
    );
    //被采用的标签最新文章目标配置
    $config['dest']['newartusebytagjjzt'] = 'newartusebytagjjzt.shtml';
    $config['dest']['newartusebytagjj'] = 'newartusebytagjj.shtml';
    $config['dest']['newartusebytaghj'] = 'newartusebytaghj.shtml';

    $config['dest']['newartusebytagwh'] = 'newartusebytagwh.shtml';
    $config['dest']['newartusebytagqh'] = 'newartusebytagqh.shtml';
    $config['dest']['newartusebytaggg'] = 'newartusebytaggg.shtml';

    $config['dest']['newartusebytagyh'] = 'newartusebytagyh.shtml';
    $config['dest']['newartusebytaglc'] = 'newartusebytaglc.shtml';
    $config['dest']['newartusebytagbx'] = 'newartusebytagbx.shtml';

    $config['dest']['newartusebytagzq'] = 'newartusebytagzq.shtml';
    $config['dest']['newartusebytagxxq'] = 'newartusebytagxxq.shtml';
    $config['dest']['newartusebytagqc'] = 'newartusebytagqc.shtml';

    $config['dest']['newartusebytagby'] = 'newartusebytagby.shtml';
    $config['dest']['newartusebytagtzsc'] = 'newartusebytagtzsc.shtml';

    //获取首页黄金文章列表 （用于高手点金）
    $config['dest']['recommend_index_golden_1'] = 'recommend_index_golden_1.shtml';
    $config['dest']['recommend_index_golden_2'] = 'recommend_index_golden_2.shtml';

    //获取首页黄金文章列表 （用于今日精选）
    $config['dest']['recommend_index_blog_golden_1'] = 'recommend_index_blog_golden_1.shtml';
    $config['dest']['recommend_index_blog_golden_2'] = 'recommend_index_blog_golden_2.shtml';

    //获取首页黄金文章列表（用于热门点击）
    $config['dest']['goldenartclicktdayrank'] = 'goldenartclicktdayrank.shtml';
    $config['dest']['goldenartclickweekrank'] = 'goldenartclickweekrank.shtml';
    $config['dest']['goldenartclicktotalrank'] = 'goldenartclicktotalrank.shtml';


    //获取首页黄金文章列表（用于总排行）
    $config['dest']['goldenartcomtdayrank'] = 'goldenartcomtdayrank.shtml';
    $config['dest']['goldenartcomweekrank'] = 'goldenartcomweekrank.shtml';
    $config['dest']['goldenartcomtotalrank'] = 'goldenartcomtotalrank.shtml';


    //热门博客 本周精华 热点文章的获取
    $config['recommend']['maxsize'] = 40;  //下面的最大值
    $config['recommend']['factiveblog'] = 20;  //首页推荐博客
    $config['recommend']['tactiveblog'] = 30;  //标签页推荐博客
    $config['recommend']['primeart'] = 20;  //首页本周精华
    $config['recommend']['fvotearts'] = 20;  //首页热点文章也就是最顶的文章
    $config['recommend']['tvotearts'] = 35;  //标签页网友最支持文章
    $config['recommend']['gifts0'] = 15;
    $config['recommend']['gifts1'] = 15;
    $config['recommend']['gifts2'] = 15; //标签页鲜花排行文章
    //配置目标文件名
    $config['dest']['rankfactiveblog'] = 'rankfactiveblog.shtml';
    $config['dest']['ranktactiveblog'] = 'ranktactiveblog.shtml';
    $config['dest']['rankprimeart'] = 'rankprimearts.shtml';
    $config['dest']['rankfvotearts'] = 'rankfvotearts.shtml';
    $config['dest']['ranktvotearts'] = 'ranktvotearts.shtml';

    //首页鲜花排行榜
    $config['dest']['rankgifts0'] = 'rankgifts12.shtml';
    $config['dest']['rankgifts1'] = 'rankgifts24.shtml';
    $config['dest']['rankgifts2'] = 'rankgiftsweek.shtml';

    //标签（分类）页鲜花排行榜
    $config['dest']['tagpage_rankgifts0'] = 'tagpage_rankgifts12.shtml';
    $config['dest']['tagpage_rankgifts1'] = 'tagpage_rankgifts24.shtml';
    $config['dest']['tagpage_rankgifts2'] = 'tagpage_rankgiftsweek.shtml';

    //标签（分类）页鲜花排行榜(黄金博客用)
    $config['dest']['tagpage_rankgifts_golden_0'] = 'tagpage_rankgifts_golden_12.shtml';
    $config['dest']['tagpage_rankgifts_golden_1'] = 'tagpage_rankgifts_golden_24.shtml';
    $config['dest']['tagpage_rankgifts_golden_2'] = 'tagpage_rankgifts_golden_week.shtml';

    //美酒博客右侧鲜花排行榜
    $config['dest']['mjbk_rankgifts0'] = 'mjbk_rankgifts12.shtml';
    $config['dest']['mjbk_rankgifts1'] = 'mjbk_rankgifts24.shtml';
    $config['dest']['mjbk_rankgifts2'] = 'mjbk_rankgiftsweek.shtml';

    //财经时评 高手看盘 股市精粹
    $config['finance']['financecjsp'] = 50;  //首页财经时评
    $config['finance']['financeghkp'] = 50;  //高手看盘
    $config['finance']['financegsjc'] = 60;  //股市精粹
    //配置目标文件名
    $config['dest']['recommend_cjsp'] = 'recommend_cjsp.shtml';
    $config['dest']['recommend_gskp_1'] = 'recommend_gskp_1.shtml';
    $config['dest']['recommend_gskp_2'] = 'recommend_gskp_2.shtml';
    $config['dest']['recommend_gsjc_1'] = 'recommend_gsjc_1.shtml';
    $config['dest']['recommend_gsjc_2'] = 'recommend_gsjc_2.shtml';

    //文章投票排行
    $config['artvote']['maxsize'] = 50;  //投票排行的页数
    $config['dest']['artvote_24'] = 'artvote_24.shtml';
    $config['dest']['artvote_48'] = 'artvote_48.shtml';
    $config['dest']['artvote_168'] = 'artvote_168.shtml';

    //文章鲜花排行
    $config['giftrank']['maxsize'] = 50;
    $config['dest']['giftrank_12'] = 'giftrank_12.shtml';
    $config['dest']['giftrank_24'] = 'giftrank_24.shtml';
    $config['dest']['giftrank_168'] = 'giftrank_168.shtml';

    //最新有更新的博客
    $config['newupdblog']['maxsize'] = 200;
    $config['dest']['blognewupdate'] = 'blognewupdate_' . $config['newupdblog']['maxsize'] . '.shtml';

    //用户推荐文章
    $config['userrecommend']['maxsize'] = 20;
    $config['dest']['userrecommend'] = 'userrecommend_' . $config['userrecommend']['maxsize'] . '.shtml';

    $config['recommendblog']['maxsize'] = 3000; //总数
    $config['recommendblog']['eachsize'] = 5; //列
    $config['recommendblog']['leastsize'] = 10;
    $config['recommendblog']['blocksize'] = 200; //一块多少记录
    $config['dest']['recommendbloglist'] = 'recommendbloglist.shtml';

    //首页目标静态页
    $config['dest']['index'] = dirname($config['destpath']) . '/index.html';
//	global $config;
    if (!isset($config['dest'][$filename])) {
        return false;
    }
    $destfile = $config['destpath'] . $config['fprefix'] . $config['dest'][$filename];

    if (file_exists($destfile)) {
        @unlink($destfile);
    }
    error_log($content, 3, $destfile);
    chmod($destfile, 0777);
    return true;
}

function writehtml($content, $filename) {
//	global $config;
    $config['destpath'] = './shtml/';

    $destfile = $config['destpath'] . $filename;

    if (file_exists($destfile)) {
        @unlink($destfile);
    }
    error_log($content, 3, $destfile);
    chmod($destfile, 0777);
}

function articlePicture($param) {
//preg_match('/<img\s*(height\=[\"|\']?\w*[\"|\']?\s*alt\=[\"|\']?[\"|\']?\s*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $param['Content'], $matchesPic);
    preg_match('/<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $param['Content'], $matchesPic);

    if (count($matchesPic) < 1) {
        return false;
    }
    $param['PictureUrl'] = $matchesPic['0'];

    //preg_match('/height\=\"(\w*)\"[^>]*width\=\"(\w*)\"/i', $matchesPic['0'], $matchesPicHW);
    preg_match('/height\=\"(\w*)\"/i', $matchesPic['0'], $matchesPicH);

    preg_match('/width\=\"(\w*)\"/i', $matchesPic['0'], $matchesPicW);

    //匹配img中style的width,height
    preg_match('/style\=\"[^\"]*height\:(\d*)/i', $matchesPic['0'], $styleH);
    preg_match('/style\=\"[^\"]*width\:(\d*)/i', $matchesPic['0'], $styleW);

    if ($styleH['1'] > 0 && is_numeric($styleH['1'])) {
        $matchesPicH = $styleH;
    }
    if ($styleW['1'] > 0 && is_numeric($styleW['1'])) {
        $matchesPicW = $styleW;
    }
    //匹配img中style的width,height

    if (count($matchesPicW) == 0 || count($matchesPicH) == 0 || $matchesPicW['1'] == 0 || $matchesPicH['1'] == 0) {
        preg_match('/^http\:/i', $matchesPic['2'], $http);
        if ($http['0']) {
            preg_match('/(\.bmp)|(\.jpg)|(\.tiff)|(\.gif)|(\.jpeg)/i', $matchesPic['2'], $Pic);
            //error_log(print_r($Pic,true), 3, '/home/www/html/logs/a2.log');
            if (!empty($Pic['0'])) {
                $matchesPicHW = @getimagesize($matchesPic['2']);
            }
        } else {
            preg_match('/(\.bmp)|(\.jpg)|(\.tiff)|(\.gif)|(\.jpeg)/i', $matchesPic['2'], $Pic);
            //error_log(print_r($Pic,true), 3, '/home/www/html/logs/a2.log');
            if (!empty($Pic['0'])) {
                $matchesPicHW = @getimagesize(config_item('base_url') . $matchesPic['2']);
            }
        }
        //error_log(print_r($http,true), 3, '/home/httpd/logs/a23132.log');
        if ($matchesPicHW['0'] > '237' || $matchesPicHW['1'] > '160') {
            $param['PictureUrl'] = '<img height="160" alt="" src="' . $matchesPic['2'] . '" width="237" border="0" />';
        } else if (empty($matchesPicHW) || empty($matchesPicHW)) {
            $param['PictureUrl'] = '<img height="100" alt="" src="' . $matchesPic['2'] . '" width="130" border="0" />';
        }
    } else {
        if ($matchesPicW['1'] > '237' || $matchesPicH['1'] > '160') {
            $param['PictureUrl'] = '<img height="160" alt="" src="' . $matchesPic['2'] . '" width="237" border="0" />';
        }
    }

    return $param['PictureUrl'];
}

function affichePicture($param, $width = '160', $height = '120') {

    preg_match_all('/<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $param['Content'], $matchesEmbed);


    if (count($matchesEmbed['0']) > 0) {
        $y = count($matchesEmbed['0']);
        for ($i = 0; $i < $y; $i++) {

            preg_match('/<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $param['Content'], $matchesPic);
            $param['PictureUrl'] = $matchesPic['0'];


            $qqSign = strpos($matchesPic['0'], 'qqOnline');

            if ($qqSign) {
                preg_match('/alt\=(\")?\s*(\d)+\s*(\")?/i', $matchesPic['0'], $qqmatches);
                //error_log(print_r($qqmatches,true).'|', 3, '/home/www/html/logs/a1.log');
                $PictureUrl = '<img height="22" ' . $qqmatches['0'] . ' sb+1+l+2+o+3+g+4rc="' . $matchesPic['2'] . '" name="qqOnline" width="77" border="0" />';
                $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $PictureUrl, $param['Content'], 1);
                continue;
            }

            preg_match('/height\=\"(\w*)\"/i', $matchesPic['0'], $matchesPicH);

            preg_match('/width\=\"(\w*)\"/i', $matchesPic['0'], $matchesPicW);


            //匹配img中style的width,height
            preg_match('/style\=\"[^\"]*height\:(\d*)/i', $matchesPic['0'], $styleH);
            preg_match('/style\=\"[^\"]*width\:(\d*)/i', $matchesPic['0'], $styleW);

            if ($styleH['1'] > 0 && is_numeric($styleH['1'])) {
                $matchesPicH = $styleH;
            }
            if ($styleW['1'] > 0 && is_numeric($styleW['1'])) {
                $matchesPicW = $styleW;
            }
            //匹配img中style的width,height
            //ie6 width和height获取
            preg_match('/height\:(\w*)px/i', $matchesPic['0'], $iematchesPicH);
            preg_match('/width\:(\w*)px/i', $matchesPic['0'], $iematchesPicW);
            if (!empty($iematchesPicH['1'])) {
                $matchesPicH['1'] = $iematchesPicH['1'];
            }
            if (!empty($iematchesPicW['1'])) {
                $matchesPicW['1'] = $iematchesPicW['1'];
            }
            //error_log(print_r($iematchesPicH,true).'|'.print_r($iematchesPicW,true), 3, '/home/httpd/logs/a2.log');
            //ie6 width和height获取


            if (count($matchesPicW) == 0 || count($matchesPicH) == 0 || $matchesPicW['1'] == 0 || $matchesPicH['1'] == 0) {
                //error_log($matchesPicHW['0'].'|'.$matchesPicHW['1'], 3, '/home/httpd/logs/a3.log');

                preg_match('/^http\:/i', $matchesPic['2'], $http);
                if ($http['0']) {
                    preg_match('/(\.bmp)|(\.jpg)|(\.tiff)|(\.gif)|(\.png)|(\.jpeg)/i', $matchesPic['2'], $Pic);
                    //error_log(print_r($Pic,true), 3, '/home/www/html/logs/a2.log');
                    if (!empty($Pic['0'])) {
                        $matchesPicHW = @getimagesize($matchesPic['2']);
                    }
                } else {
                    preg_match('/(\.bmp)|(\.jpg)|(\.tiff)|(\.gif)|(\.png)|(\.jpeg)/i', $matchesPic['2'], $Pic);
                    //error_log(print_r($Pic,true), 3, '/home/www/html/logs/a2.log');
                    if (!empty($Pic['0'])) {
                        $matchesPicHW = @getimagesize(config_item('base_url') . $matchesPic['2']);
                    }
                }


                if ($matchesPicHW['0'] > $width || $matchesPicHW['1'] > $height) {
                    //error_log(print_r($matchesPicHW['0'],true).'|'.print_r($matchesPicHW['1'],true), 3, '/home/httpd/logs/a2.log');
                    if ($matchesPicHW['0'] > $matchesPicHW['1']) {
                        $width = $width;
                        $rs = round($matchesPicHW['0'] / $width, 2);
                        $height = round($matchesPicHW['1'] / $rs, 2);
                    } else if ($matchesPicHW['0'] < $matchesPicHW['1']) {
                        $height = $height;
                        $rs = round($matchesPicHW['1'] / $height, 2);
                        $width = round($matchesPicHW['0'] / $rs, 2);
                    }
                    //error_log(print_r($width,true).'|'.print_r($height,true), 3, '/home/httpd/logs/a1.log');
                    $PictureUrl = '<img height="' . $height . '" alt="" sb+1+l+2+o+3+g+4rc="' . $matchesPic['2'] . '" width="' . $width . '" border="0" />';
                    $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $PictureUrl, $param['Content'], 1);
                } else if (empty($matchesPicHW) || empty($matchesPicHW)) {
                    $PictureUrl = '<img height="25" alt="" sb+1+l+2+o+3+g+4rc="' . $matchesPic['2'] . '" width="25" border="0" />';
                    $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $PictureUrl, $param['Content'], 1);
                } else {
                    $PictureUrl = '<img  alt="" sb+1+l+2+o+3+g+4rc="' . $matchesPic['2'] . '" border="0" />';
                    $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $PictureUrl, $param['Content'], 1);
                }
            } else {

                //error_log($matchesPicW['1'].'|'.$matchesPicH['1'], 3, '/home/httpd/logs/a1.log');
                if ($matchesPicW['1'] > $width || $matchesPicH['1'] > $height) {
                    //error_log(print_r($matchesPicW['1'],true).'|'.print_r($matchesPicH['1'],true), 3, '/home/httpd/logs/a2.log');
                    if ($matchesPicH['1'] < $matchesPicW['1']) {
                        $width = $width;
                        $rs = round($matchesPicW['1'] / $width, 2);
                        $height = round($matchesPicH['1'] / $rs, 2);
                    } else if ($matchesPicH['1'] > $matchesPicW['1']) {
                        $height = $height;
                        $rs = round($matchesPicH['1'] / $height, 2);
                        $width = round($matchesPicW['1'] / $rs, 2);
                    }
                    //error_log(print_r($width,true).'|'.print_r($height,true), 3, '/home/httpd/logs/a3.log');
                    $PictureUrl = '<img height="' . $height . '" alt="" sb+1+l+2+o+3+g+4rc="' . $matchesPic['2'] . '" width="' . $width . '" border="0" />';
                    $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $PictureUrl, $param['Content'], 1);
                } else {
                    $PictureUrl = '<img height="' . $matchesPicH['1'] . '" alt="" sb+1+l+2+o+3+g+4rc="' . $matchesPic['2'] . '"  width="' . $matchesPicW['1'] . '" border="0" />';
                    $param['Content'] = preg_replace('/(<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>)/i', $PictureUrl, $param['Content'], 1);
                }
            }
        }
    }


    $param['Content'] = preg_replace('/b\+1\+l\+2\+o\+3\+g\+4/i', '', $param['Content']);
    //error_log(print_r($param['Content'],true), 3, '/home/httpd/logs/a23132.log');
    return $param['Content'];
}

function RemoveXSS($val) {
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    // straight replacements, the user should never need these since they're normal characters  
    // this prevents like <IMG SRC=@avascript:alert('XSS')>  
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional 
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 
        // @ @ search for the hex values 
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ; 
        // @ @ 0{0,7} matches '0' zero to seven times  
        $val = preg_replace('/(�{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ; 
    }

    // now the only remaining whitespace attacks are \t, \n, and \r 
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something 
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag  
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags  
            if ($val_before == $val) {
                // no replacements were made, so exit the loop  
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * write_log 写日志
 *
 * @param string $msg 内容
 * @param string $file 文件
 * @param string $method 执行方法
 * @param string $logtype 内容类型
 * @return bool
 */
function write_log($msg, $file, $method, $logtype = 'INFO') {
    if (!file_exists($file)) {
        mkdirs(dirname($file), 0775); //创建文件夹
    }

    $info = $logtype . '||' . date('H:i:s') . '||' . $method . '||' . $msg . PHP_EOL;

    return error_log($info, 3, $file);
}

/**
 * x_mkdir 创建文件夹
 *
 * @param string $pathname 文件夹名
 * @param int $mode 权限
 * @return bool
 */
function x_mkdir($pathname, $mode) {
    $fullpath = '';
    $pathname = explode('/', $pathname);

    while (list(, $v) = each($pathname)) {
        $fullpath .= "$v/";

        if (is_dir($fullpath) == false) {
            $oldmask = umask(0);

            if (mkdir($fullpath, $mode) == false) {
                return false;
            }

            umask($oldmask);
        }
    }

    return true;
}

//获取贵金属参赛文章期数
function getnTimes() {
    return ((date('Y') - 2014) * 12 + (date('m') - config_item('statday')) + 1);
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

//this
?>
