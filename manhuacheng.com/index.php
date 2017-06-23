<?php
if(!strpos($_SERVER['REQUEST_URI'],'?')){
//header("Content-type: text/html; charset=utf-8");
//echo "<h1>网站维护中</h1>";
//exit();
}
if (is_mobile_request()) {
        Header("HTTP/1.1 301 Moved Permanently");
        Header("Location: http://m.manhuacheng.com".$_SERVER['REQUEST_URI']);
    }
ini_set('memory_limit','128M');
/**
 *  index.php OLCMS 入口
 *
 * @copyright			(C) 2005-2010 OLCMS
 * @license				http://www.olcms.cn/license/
 * @lastmodify			2010-6-1
 */
 //OLCMS根目录
define('OLCMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include OLCMS_PATH.'/olcms/base.php';

pc_base::creat_app();


/*******
 * 判断是否手机访问,yudonglin2015年12月2日 18:42:08
 */
function is_mobile_request() {
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $mobile_browser++;
    }

    if ((isset($_SERVER['HTTP_ACCEPT'])) and ( strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
        $mobile_browser++;
    }

    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        $mobile_browser++;
    }

    if (isset($_SERVER['HTTP_PROFILE'])) {
        $mobile_browser++;
    }
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents)) {
        $mobile_browser++;
    }
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
        $mobile_browser++;
    }

    // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
        $mobile_browser = 0;
    }

    // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
        $mobile_browser++;
    }

    if ($mobile_browser > 0) {
        return true;
    } else {
        return false;
    }
}
?>

