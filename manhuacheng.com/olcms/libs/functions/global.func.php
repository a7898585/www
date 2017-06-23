<?php

/**
 *  global.func.php 公共函数库
 *
 * @copyright			(C) 2005-2010 OLCMS
 * @license				http://www.olcms.cn/license/
 * @lastmodify			2010-6-1
 */

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string) {
    if (!is_array($string))
        return addslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_addslashes($val);
    return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if (!is_array($string))
        return stripslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 返回经addslashe处理过的字符串或数组
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_html_special_chars($string) {
    if (!is_array($string))
        return htmlspecialchars($string);
    foreach ($string as $key => $val)
        $string[$key] = new_html_special_chars($val);
    return $string;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    return $string;
}

/**
 * 过滤ASCII码从0-28的控制字符
 * @return String
 */
function trim_unsafe_control_chars($str) {
    $rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
    return str_replace(chr(0), '', preg_replace($rule, '', $str));
}

/**
 * 格式化文本域内容
 *
 * @param $string 文本域内容
 * @return string
 */
function trim_textarea($string) {
    $string = nl2br(str_replace(' ', '&nbsp;', $string));
    return $string;
}

/**
 * 将文本格式成适合js输出的字符串
 * @param string $string 需要处理的字符串
 * @param intval $isjs 是否执行字符串格式化，默认为执行
 * @return string 处理后的字符串
 */
function format_js($string, $isjs = 1) {
    $string = addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
    return $isjs ? 'document.write("' . $string . '");' : $string;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str) {
    $str = preg_replace('/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str);
    $str = preg_replace('/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str);
    $str = preg_replace('/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str);
    $str = preg_replace('/]]\>/si', ']] >', $str);
    return $str;
}

/**
 * 获取当前页面完整URL地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
    $strlen = strlen($string);
    if ($strlen <= $length)
        return $string;
    $string = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
        $replace_arr = array('&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}

/**
 * 字符截取 支持UTF8/GBK,安汉字长度截取
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cutword($string, $length, $dot = '') {
    $string = strip_tags($string);
    $strlen = strlen($string);
    if ($strlen / 2 <= $length)
        return $string;
    $string = str_replace(array(' ', '	', '　', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', ' ', ' ', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    $n = $tn = $noc = 0;
    if (strtolower(CHARSET) == 'utf-8') {
        while ($n < $strlen) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                ++$n;
                $noc += 0.5;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 1;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 1;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 1;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 1;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 1;
            } else {
                ++$n;
            }
            if ($noc >= $length) {
                if ($n < $strlen)
                    ++$noc;
                break;
            }
        }
    } else {
        while ($n < $strlen) {
            if (ord($string[$n]) > 127) {
                $tn = 2;
                $n += 2;
                $noc += 1;
            } else {
                $tn = 1;
                ++$n;
                $noc += 0.5;
            }
            if ($noc >= $length) {
                if ($n < $strlen)
                    ++$noc;
                break;
            }
        }
    }
    if ($noc > $length && !empty($dot)) {
        $n -= $tn;
        $strcut = substr($string, 0, $n);
        $strcut .= $dot;
    } else {
        $strcut = substr($string, 0, $n);
    }
    $strcut = str_replace(array('&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array('&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    return $strcut;
}

/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
}

function get_cost_time() {
    $microtime = microtime(TRUE);
    return $microtime - SYS_START_TIME;
}

/**
 * 程序执行时间
 *
 * @return	int	单位ms
 */
function execute_time() {
    $stime = explode(' ', SYS_START_TIME);
    $etime = explode(' ', microtime());
    return number_format(($etime [1] + $etime [0] - $stime [1] - $stime [0]), 6);
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度 
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
    if ($data == '')
        return array();
    $data = stripslashes($data);
    eval("\$array = $data;");
    return $array;
}

/**
 * 将数组转换为字符串
 *
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
    if ($data == '')
        return '';
    if ($isformdata)
        $data = new_stripslashes($data);
    return addslashes(var_export($data, TRUE));
}

/**
 * 转换字节数为其他单位
 *
 *
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize) {
    if ($filesize >= 1073741824) {
        $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
    } elseif ($filesize >= 1048576) {
        $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
    } elseif ($filesize >= 1024) {
        $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
    } else {
        $filesize = $filesize . ' Bytes';
    }
    return $filesize;
}

/**
 * 字符串加密、解密函数
 *
 *
 * @param	string	$txt		字符串
 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param	string	$key		密钥：数字、字母、下划线
 * @return	string
 */
function sys_auth($txt, $operation = 'ENCODE', $key = '') {
    $key = $key ? $key : pc_base::load_config('system', 'auth_key');
    $txt = $operation == 'ENCODE' ? (string) $txt : base64_decode($txt);
    $len = strlen($key);
    $code = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        $k = $i % $len;
        $code .= $txt[$i] ^ $key[$k];
    }
    $code = $operation == 'DECODE' ? $code : base64_encode($code);
    return $code;
}

/**
 * 语言文件处理
 *
 * @param	string		$language	标示符
 * @param	array		$pars	转义的数组,二维数组 ,'key1'=>'value1','key2'=>'value2',
 * @param	string		$modules 多个模块之间用半角逗号隔开，如：member,guestbook
 * @return	string		语言字符
 */
function L($language = 'no_language', $pars = array(), $modules = '') {
    static $LANG = array();
    static $LANG_MODULES = array();
    $lang = pc_base::load_config('system', 'lang');
    if (!$LANG) {
        require_once PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . 'system.lang.php';
        if (defined('IN_ADMIN'))
            require_once PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . 'system_menu.lang.php';
        if (file_exists(PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . ROUTE_M . '.lang.php'))
            require PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . ROUTE_M . '.lang.php';
    }
    if (!empty($modules)) {
        $modules = explode(',', $modules);
        foreach ($modules AS $m) {
            if (!isset($LANG_MODULES[$m]))
                require PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $m . '.lang.php';
        }
    }
    if (!array_key_exists($language, $LANG)) {
        return $LANG['no_language'] . '[' . $language . ']';
    } else {
        $language = $LANG[$language];
        if ($pars) {
            foreach ($pars AS $_k => $_v) {
                $language = str_replace('{' . $_k . '}', $_v, $language);
            }
        }
        return $language;
    }
}

/**
 * 模板调用
 * 
 * @param $module
 * @param $template
 * @param $istag
 * @return unknown_type
 */
function template($module = 'content', $template = 'index', $style = '') {
    if (!empty($style) && preg_match('/([a-z0-9\-_]+)/is', $style)) {
        
    } elseif (empty($style) && !defined('STYLE')) {
        $siteinfo = siteinfo();
        $style = $siteinfo['default_style'];
    } elseif (empty($style) && defined('STYLE')) {
        $style = STYLE;
    } else {
        $style = 'default';
    }
    if (!$style)
        $style = 'default';
    $template_cache = pc_base::load_sys_class('template_cache');
    $compiledtplfile = OLCMS_PATH . 'caches' . DIRECTORY_SEPARATOR . 'caches_template' . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.php';
    if (file_exists(PC_PATH . 'templates' . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.html')) {
        if (!file_exists($compiledtplfile) || (@filemtime(PC_PATH . 'templates' . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.html') > @filemtime($compiledtplfile))) {
            $template_cache->template_compile($module, $template, $style);
        }
    } else {
        $compiledtplfile = OLCMS_PATH . 'caches' . DIRECTORY_SEPARATOR . 'caches_template' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.php';
        if (!file_exists($compiledtplfile) || (file_exists(PC_PATH . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.html') && filemtime(PC_PATH . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.html') > filemtime($compiledtplfile))) {
            $template_cache->template_compile($module, $template, 'default');
        } elseif (!file_exists(PC_PATH . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.html')) {
            showmessage('Template does not exist.' . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template . '.html');
        }
    }
    return $compiledtplfile;
}

/**
 * 输出自定义错误
 * 
 * @param $errno 错误号
 * @param $errstr 错误描述
 * @param $errfile 报错文件地址
 * @param $errline 错误行号
 * @return string 错误提示
 */
function my_error_handler($errno, $errstr, $errfile, $errline) {
    if ($errno == 8)
        return '';
    $errfile = str_replace(OLCMS_PATH, '', $errfile);
    if (pc_base::load_config('system', 'errorlog')) {
        error_log(date('m-d H:i:s', SYS_TIME) . ' | ' . $errno . ' | ' . str_pad($errstr, 30) . ' | ' . $errfile . ' | ' . $errline . "\r\n", 3, CACHE_PATH . 'error_log.php');
    } else {
        $str = '<div style="font-size:12px;text-align:left; border-bottom:1px solid #9cc9e0; border-right:1px solid #9cc9e0;padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;"><span>errorno:' . $errno . ',str:' . $errstr . ',file:<font color="blue">' . $errfile . '</font>,line' . $errline . '<br /><a href="http://faq.phpcms.cn/?type=file&errno=' . $errno . '&errstr=' . urlencode($errstr) . '&errfile=' . urlencode($errfile) . '&errline=' . $errline . '" target="_blank" style="color:red">Need Help?</a></span></div>';
        echo $str;
    }
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.olcms.cn'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
    if (defined('IN_ADMIN')) {
        include(admin::admin_tpl('showmessage', 'admin'));
    } else {
        include(template('content', 'message'));
    }
    exit;
}

/**
 * 查询字符是否存在于某字符串
 * 
 * @param $haystack 字符串
 * @param $needle 要查找的字符
 * @return bool
 */
function str_exists($haystack, $needle) {
    return !(strpos($haystack, $needle) === FALSE);
}

/**
 * 取得文件扩展
 * 
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 加载模板标签缓存
 * @param string $name 缓存名
 * @param integer $times 缓存时间
 */
function tpl_cache($name, $times = 0) {
    $filepath = 'tpl_data';
    $info = getcacheinfo($name, $filepath);
    if (SYS_TIME - $info['filemtime'] >= $times) {
        return false;
    } else {
        return getcache($name, $filepath);
    }
}

/**
 * 写入缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $data 缓存数据
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 * @param $timeout 过期时间
 */
function setcache($name, $data, $filepath = '', $type = 'file', $config = '', $timeout = '') {
    pc_base::load_sys_class('cache_factory', '', 0);
    if ($config) {
        $cacheconfig = pc_base::load_config('cache');
        $cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
    } else {
        $cache = cache_factory::get_instance()->get_cache($type);
    }

    return $cache->set($name, $data, $timeout, '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcache($name, $filepath = '', $type = 'file', $config = '') {
    pc_base::load_sys_class('cache_factory', '', 0);
    if ($config) {
        $cacheconfig = pc_base::load_config('cache');
        $cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
    } else {
        $cache = cache_factory::get_instance()->get_cache($type);
    }
    return $cache->get($name, '', '', $filepath);
}

/**
 * 删除缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 */
function delcache($name, $filepath = '', $type = 'file', $config = '') {
    pc_base::load_sys_class('cache_factory', '', 0);
    if ($config) {
        $cacheconfig = pc_base::load_config('cache');
        $cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
    } else {
        $cache = cache_factory::get_instance()->get_cache($type);
    }
    return $cache->delete($name, '', '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcacheinfo($name, $filepath = '', $type = 'file', $config = '') {
    pc_base::load_sys_class('cache_factory');
    if ($config) {
        $cacheconfig = pc_base::load_config('cache');
        $cache = cache_factory::get_instance($cacheconfig)->get_cache($config);
    } else {
        $cache = cache_factory::get_instance()->get_cache($type);
    }
    return $cache->cacheinfo($name, '', '', $filepath);
}

/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 */
function to_sqls($data, $front = ' AND ', $in_column = false) {
    if ($in_column && is_array($data)) {
        $ids = '\'' . implode('\',\'', $data) . '\'';
        $sql = "$in_column IN ($ids)";
        return $sql;
    } else {
        if ($front == '') {
            $front = ' AND ';
        }
        if (is_array($data) && count($data) > 0) {
            $sql = '';
            foreach ($data as $key => $val) {
                $sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
            }
            return $sql;
        } else {
            return $data;
        }
    }
}

/**
 * 分页函数
 * 
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $perpage 每页显示数
 * @param $urlrule URL规则
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 分页
 */
function pages($num, $curr_page, $perpage = 20, $urlrule = '', $array = array(), $setpages = 10) {
    if (defined('URLRULE')) {
        $urlrule = URLRULE;
        $array = $GLOBALS['URL_ARRAY'];
    } elseif ($urlrule == '') {
        $urlrule = url_par('page={$page}');
    }
    $multipage = '';
    if ($num > $perpage) {
        $page = $setpages + 1;
        $offset = ceil($setpages / 2 - 1);
        $pages = ceil($num / $perpage);
        if (defined('IN_ADMIN') && !defined('PAGES'))
            define('PAGES', $pages);
        $from = $curr_page - $offset;
        $to = $curr_page + $offset;
        $more = 0;
        if ($page >= $pages) {
            $from = 2;
            $to = $pages - 1;
        } else {
            if ($from <= 1) {
                $to = $page - 1;
                $from = 2;
            } elseif ($to >= $pages) {
                $from = $pages - ($page - 2);
                $to = $pages - 1;
            }
            $more = 1;
        }
        $multipage .= '<a class="a1">' . $num . L('page_item') . '</a>';
        if ($curr_page > 0) {
            $multipage .= ' <a href="' . pageurl($urlrule, $curr_page - 1, $array) . '" class="a1">' . L('previous') . '</a>';
            if ($curr_page == 1) {
                $multipage .= ' <span>1</span>';
            } elseif ($curr_page > 6 && $more) {
                $multipage .= ' <a href="' . pageurl($urlrule, 1, $array) . '">1</a>..';
            } else {
                $multipage .= ' <a href="' . pageurl($urlrule, 1, $array) . '">1</a>';
            }
        }
        for ($i = $from; $i <= $to; $i++) {
            if ($i != $curr_page) {
                $multipage .= ' <a href="' . pageurl($urlrule, $i, $array) . '">' . $i . '</a>';
            } else {
                $multipage .= ' <span>' . $i . '</span>';
            }
        }
        if ($curr_page < $pages) {
            if ($curr_page < $pages - 5 && $more) {
                $multipage .= ' ..<a href="' . pageurl($urlrule, $pages, $array) . '">' . $pages . '</a> <a href="' . pageurl($urlrule, $curr_page + 1, $array) . '" class="a1">' . L('next') . '</a>';
            } else {
                $multipage .= ' <a href="' . pageurl($urlrule, $pages, $array) . '">' . $pages . '</a> <a href="' . pageurl($urlrule, $curr_page + 1, $array) . '" class="a1">' . L('next') . '</a>';
            }
        } elseif ($curr_page == $pages) {
            $multipage .= ' <span>' . $pages . '</span> <a href="' . pageurl($urlrule, $curr_page, $array) . '" class="a1">' . L('next') . '</a>';
        } else {
            $multipage .= ' <a href="' . pageurl($urlrule, $pages, $array) . '">' . $pages . '</a> <a href="' . pageurl($urlrule, $curr_page + 1, $array) . '" class="a1">' . L('next') . '</a>';
        }
    }
    return $multipage;
}

/**
 * 返回分页路径
 * 
 * @param $urlrule 分页规则
 * @param $page 当前页
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 完整的URL路径
 */
function pageurl($urlrule, $page, $array = array()) {
    if (strpos($urlrule, '~')) {
        $urlrules = explode('~', $urlrule);
        $urlrule = $page < 2 ? APP_PATH . $urlrules[0] : (strpos($urlrule, '?') ? $urlrules[1] : APP_PATH . $urlrules[1]);
    }
    $findme = array('{$page}');
    $replaceme = array($page);
    if (is_array($array))
        foreach ($array as $k => $v) {
            $findme[] = '{$' . $k . '}';
            $replaceme[] = $v;
        }
    $url = str_replace($findme, $replaceme, $urlrule);
    $url = str_replace(array('http://', '//', '~'), array('~', '/', 'http://'), $url);
    return $url;
}

/**
 * URL路径解析，pages 函数的辅助函数
 *
 * @param $par 传入需要解析的变量 默认为，page={$page}
 * @param $url URL地址
 * @return URL
 */
function url_par($par, $url = '') {
    if ($url == '')
        $url = get_url();
    $pos = strpos($url, '?');
    if ($pos === false) {
        $url .= '?' . $par;
    } else {
        $querystring = substr(strstr($url, '?'), 1);
        parse_str($querystring, $pars);
        $query_array = array();
        foreach ($pars as $k => $v) {
            $query_array[$k] = $v;
        }
        $querystring = http_build_query($query_array) . '&' . $par;
        $url = substr($url, 0, $pos) . '?' . $querystring;
    }
    return $url;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * iconv 编辑转换
 */
if (!function_exists('iconv')) {

    function iconv($in_charset, $out_charset, $str) {
        $in_charset = strtoupper($in_charset);
        $out_charset = strtoupper($out_charset);
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, $out_charset, $in_charset);
        } else {
            pc_base::load_sys_func('iconv');
            $in_charset = strtoupper($in_charset);
            $out_charset = strtoupper($out_charset);
            if ($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312')) {
                return utf8_to_gbk($str);
            }
            if (($in_charset == 'GBK' || $in_charset == 'GB2312') && $out_charset == 'UTF-8') {
                return gbk_to_utf8($str);
            }
            return $str;
        }
    }

}

/**
 * 代码广告展示函数
 * @param intval $id 广告ID
 * @return 返回广告代码
 */
function show_ad($id) {
    $id = intval($id);
    if (!$id)
        return false;
    $p = pc_base::load_model('poster_model');
    $r = $p->get_one(array('spaceid' => $id), 'setting', '`id` ASC');
    if ($r['setting']) {
        $c = string2array($r['setting']);
    } else {
        $r['code'] = '';
    }
    return $c['code'];
}

/**
 * 获取用户昵称
 * 不传入userid取当前用户nickname,如果nickname为空取username
 * 传入field，取用户$field字段信息
 */
function get_nickname($userid = '', $field = '') {
    $return = '';
    if (is_numeric($userid)) {
        $member_db = pc_base::load_model('member_model');
        $memberinfo = $member_db->get_one(array('userid' => $userid));
        if (!empty($field) && $field != 'nickname' && isset($memberinfo[$field]) && !empty($memberinfo[$field])) {
            $return = $memberinfo[$field];
        } else {
            $return = isset($memberinfo['nickname']) && !empty($memberinfo['nickname']) ? $memberinfo['nickname'] . '(' . $memberinfo['username'] . ')' : $memberinfo['username'];
        }
    } else {
        if ($return = param::get_cookie('_nickname')) {
            $return .= '(' . param::get_cookie('_username') . ')';
        }
    }
    return $return;
}

/**
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo($userid, $field = '') {
    if (!is_numeric($userid)) {
        return false;
    } else {
        $member_db = pc_base::load_model('member_model');
        $memberinfo = $member_db->get_one(array('userid' => $userid));
        if (!empty($field) && !empty($memberinfo[$field])) {
            return $memberinfo[$field];
        } else {
            return $memberinfo;
        }
    }
}

/**
 * 通过 username 值，获取用户所有信息
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo_buyusername($username, $field = '') {
    if (empty($username)) {
        return false;
    }
    $member_db = pc_base::load_model('member_model');
    $memberinfo = $member_db->get_one(array('username' => $username));
    if (!empty($field) && !empty($memberinfo[$field])) {
        return $memberinfo[$field];
    } else {
        return $memberinfo;
    }
}

/**
 * 获取用户头像，建议传ucuid
 * @param $uid 建议传ucuid 
 * @param $is_userid $uid是否为userid，如果为真，建议传ucuid
 * @param $size 头像大小 有四种[30x30 45x45 90x90 180x180] 默认30
 */
//function get_memberavatar($uid, $is_userid='', $size='30') {
//	if($is_userid) {
//		$db = pc_base::load_model('member_model');
//		$memberinfo = $db->get_one(array('userid'=>$uid));
//		if(isset($memberinfo['ucuid'])) {
//			$uid = $memberinfo['ucuid'];
//		} else {
//			return false;
//		}
//	}
//	
//	pc_base::load_app_class('client', '', 0);
//	define('APPID', pc_base::load_config('system', 'uc_appid'));
//	$uc_api_url = pc_base::load_config('system', 'uc_api_url');
//	$uc_auth_key = pc_base::load_config('system', 'uc_auth_key');
//	$client = new client($uc_api_url, $uc_auth_key);
//	$avatar = $client->ps_getavatar($uid);
//	if(isset($avatar[$size])) {
//		return $avatar[$size];
//	} else {
//		return false;
//	}
//}
function get_memberavatar($ucuid, $size = 'small') {
    if (pc_base::load_config('system', 'uc')) {
        $uc_api_url = pc_base::load_config('system', 'uc_api_url');
        $big = "$uc_api_url/avatar.php?uid=$ucuid&type=real&size=big";
        $middle = "$uc_api_url/avatar.php?uid=$ucuid&type=real&size=middle";
        $small = "$uc_api_url/avatar.php?uid=$ucuid&type=real&size=small";
        $avatar = array('big' => $big, 'middle' => $middle, 'small' => $small);
        return $avatar[$size];
    } else {
        $dir1 = ceil($ucuid / 10000);
        $dir2 = ceil($ucuid % 10000 / 1000);
        $avatarfile = pc_base::load_config('system', 'upload_url') . 'avatar/';
        $url = $avatarfile . $dir1 . '/' . $dir2 . '/' . $ucuid . '/';
        $avatar = array('big' => $url . '180x180.jpg', 'middle' => $url . '90x90.jpg', '45' => $url . '45x45.jpg', 'small' => $url . '30x30.jpg');
        return $avatar[$size];
    }
}

/**
 * 调用关联菜单
 * @param $linkageid 联动菜单id
 * @param $id 生成联动菜单的样式id
 * @param $defaultvalue 默认值
 */
function menu_linkage($linkageid = 0, $id = 'linkid', $defaultvalue = 0) {
    $linkageid = intval($linkageid);
    $datas = array();
    $datas = getcache($linkageid, 'linkage');
    $infos = $datas['data'];

    if ($datas['style'] == '1') {
        $title = $datas['title'];
        $container = 'content' . random(3) . date('is');
        if (!defined('DIALOG_INIT_1')) {
            define('DIALOG_INIT_1', 1);
            $string .= '<script type="text/javascript" src="' . JS_PATH . 'dialog.js"></script>';
            //TODO $string .= '<link href="'.CSS_PATH.'dialog.css" rel="stylesheet" type="text/css">';
        }
        if (!defined('LINKAGE_INIT_1')) {
            define('LINKAGE_INIT_1', 1);
            $string .= '<script type="text/javascript" src="' . JS_PATH . 'linkage/js/pop.js"></script>';
        }
        $var_div = $defaultvalue && (ROUTE_A == 'edit' || ROUTE_A == 'account_manage_info') ? menu_linkage_level($defaultvalue, $linkageid, $infos) : $datas['title'];
        $var_input = $defaultvalue && (ROUTE_A == 'edit' || ROUTE_A == 'account_manage_info') ? '<input type="hidden" name="info[' . $id . ']" value="' . $defaultvalue . '">' : '<input type="hidden" name="info[' . $id . ']" value="">';
        $string .= '<div name="' . $id . '" value="" id="' . $id . '" class="ib">' . $var_div . '</div>' . $var_input . ' <input type="button" name="btn_' . $id . '" class="button" value="选择" onclick="open_linkage(\'' . $id . '\',\'' . $title . '\',' . $container . ',\'' . $linkageid . '\')">';
        $string .= '<script type="text/javascript">';
        $string .= 'var returnid_' . $id . '= \'' . $id . '\';';
        $string .= 'var returnkeyid_' . $id . ' = \'' . $linkageid . '\';';
        $string .= 'var ' . $container . ' = new Array(';
        foreach ($infos AS $k => $v) {
            if ($v['parentid'] == 0) {
                $s[] = 'new Array(\'' . $v['linkageid'] . '\',\'' . $v['name'] . '\',\'' . $v['parentid'] . '\')';
            } else {
                continue;
            }
        }
        $s = implode(',', $s);
        $string .=$s;
        $string .= ')';
        $string .= '</script>';
    } else {
        $title = $defaultvalue ? $infos[$defaultvalue]['name'] : $datas['title'];
        $colObj = random(3) . date('is');
        $string = '';
        if (!defined('LINKAGE_INIT')) {
            define('LINKAGE_INIT', 1);
            $string .= '<script type="text/javascript" src="' . JS_PATH . 'linkage/js/mln.colselect.js"></script>';
            if (defined('IN_ADMIN')) {
                $string .= '<link href="' . JS_PATH . 'linkage/style/admin.css" rel="stylesheet" type="text/css">';
            } else {
                $string .= '<link href="' . JS_PATH . 'linkage/style/css.css" rel="stylesheet" type="text/css">';
            }
        }
        $string .= '<input type="hidden" name="info[' . $id . ']" value="1"><div id="' . $id . '"></div>';
        $string .= '<script type="text/javascript">';
        $string .= 'var colObj' . $colObj . ' = {"Items":[';

        foreach ($infos AS $k => $v) {
            $s .= '{"name":"' . $v['name'] . '","topid":"' . $v['parentid'] . '","colid":"' . $k . '","value":"' . $k . '","fun":function(){}},';
        }

        $string .= substr($s, 0, -1);
        $string .= ']};';
        $string .= '$("#' . $id . '").mlnColsel(colObj' . $colObj . ',{';
        $string .= 'title:"' . $title . '",';
        $string .= 'value:"' . $defaultvalue . '",';
        $string .= 'width:100';
        $string .= '});';
        $string .= '</script>';
    }
    return $string;
}

/**
 * 联动菜单层级
 */
function menu_linkage_level($linkageid, $keyid, $infos, $result = array()) {
    if (array_key_exists($linkageid, $infos)) {
        $result[] = $infos[$linkageid]['name'];
        return menu_linkage_level($infos[$linkageid]['parentid'], $keyid, $infos, $result);
    }
    krsort($result);
    return implode(' > ', $result);
}

/**
 * 通过id获取显示联动菜单
 * @param  $linkageid 联动菜单ID
 * @param  $keyid 菜单keyid
 * @param  $space 菜单间隔符
 * @param  $result 递归使用字段1
 * @param  $infos 递归使用字段2
 */
function get_linkage($linkageid, $keyid, $space = '>', $type = 1, $result = array(), $infos = array()) {
    if ($space == '' || !isset($space))
        $space = '>';
    if (!$infos) {
        $datas = getcache($keyid, 'linkage');
        $infos = $datas['data'];
    }
    if ($type == 1) {
        if (array_key_exists($linkageid, $infos)) {
            $result[] = $infos[$linkageid]['name'];
            return get_linkage($infos[$linkageid]['parentid'], $keyid, $space, $type, $result, $infos);
        } else {
            if (count($result) > 0) {
                krsort($result);
                $result = implode($space, $result);
                return $result;
            } else {
                return $result;
            }
        }
    } else {
        return $infos[$linkageid]['name'];
    }
}

/**
 * IE浏览器判断
 */
function is_ie() {
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false))
        return false;
    if (strpos($useragent, 'msie ') !== false)
        return true;
    return false;
}

/**
 * 文件下载
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */
function file_down($filepath, $filename = '') {
    if (!$filename)
        $filename = basename($filepath);
    if (is_ie())
        $filename = rawurlencode($filename);
    $filetype = fileext($filename);
    $filesize = sprintf("%u", filesize($filepath));
    if (ob_get_length() !== false)
        @ob_end_clean();
    header('Pragma: public');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
    header('Content-Transfer-Encoding: binary');
    header('Content-Encoding: none');
    header('Content-type: ' . $filetype);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-length: ' . $filesize);
    readfile($filepath);
    exit;
}

/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 * @param $string
 * @return bool
 */
function is_utf8($string) {
    return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}

/**
 * 组装生成ID号
 * @param $modules 模块名
 * @param $contentid 内容ID
 */
function id_encode($modules, $contentid) {
    return urlencode($modules . '-' . $contentid);
}

/**
 * 解析ID
 * @param $id 评论ID
 */
function id_decode($id) {
    return explode('-', $id);
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt = '') {
    $pwd = array();
    $pwd['encrypt'] = $encrypt ? $encrypt : create_randomstr();
    $pwd['password'] = md5(md5(trim($password)) . $pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}

/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
    return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
    $strlen = strlen($password);
    if ($strlen >= 6 && $strlen <= 20)
        return true;
    return false;
}

/**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
    $badwords = array("\\", '&', ' ', "'", '"', '/', '*', ',', '<', '>', "\r", "\t", "\n", "#");
    foreach ($badwords as $value) {
        if (strpos($string, $value) !== FALSE) {
            return TRUE;
        }
    }
    return FALSE;
}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_username($username) {
    $strlen = strlen($username);
    if (is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)) {
        return false;
    } elseif (20 <= $strlen || $strlen < 2) {
        return false;
    }
    return true;
}

/**
 * 检查id是否存在于数组中
 * 
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',') {
    if (!$ids)
        return false;
    $ids = explode($s, $ids);
    return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}

/**
 * 对数据进行编码转换
 * @param array/string $data       数组
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
    if (!is_array($data)) {
        return iconv($input, $output, $data);
    } else {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = array_iconv($val, $input, $output);
            } else {
                $data[$key] = iconv($input, $output, $val);
            }
        }
        return $data;
    }
}

/**
 * 生成缩略图函数
 * @param  $imgurl 图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @param  $autocut 是否自动裁剪 默认裁剪，当高度或宽度有一个数值为0是，自动关闭
 * @param  $smallpic 无图片是默认图片路径
 */
function thumb($imgurl, $width = 100, $height = 100, $autocut = 1, $smallpic = 'nopic.gif') {
    global $image;
    $upload_url = pc_base::load_config('system', 'upload_url');
    $upload_path = pc_base::load_config('system', 'upload_path');
    if (empty($imgurl))
        return IMG_PATH . $smallpic;
    $imgurl_replace = str_replace($upload_url, '', $imgurl);
    if (!extension_loaded('gd') || strpos($imgurl_replace, '://'))
        return $imgurl;
    if (!file_exists($upload_path . $imgurl_replace))
        return IMG_PATH . $smallpic;

    list($width_t, $height_t, $type, $attr) = getimagesize($upload_path . $imgurl_replace);
    if ($width >= $width_t || $height >= $height_t)
        return $imgurl;

    $newimgurl = dirname($imgurl_replace) . '/thumb_' . $width . '_' . $height . '_' . basename($imgurl_replace);

    if (file_exists($upload_path . $newimgurl))
        return $upload_url . $newimgurl;

    if (!is_object($image)) {
        pc_base::load_sys_class('image', '', '0');
        $image = new image(1, 0);
    }
    return $image->thumb($upload_path . $imgurl_replace, $upload_path . $newimgurl, $width, $height, '', $autocut) ? $upload_url . $newimgurl : $imgurl;
}

/**
 * 水印添加
 * @param $source 原图片路径
 * @param $target 生成水印图片途径，默认为空，覆盖原图
 */
function watermark($source, $target = '') {
    global $image_w;
    if (empty($source))
        return $source;
    if (!extension_loaded('gd') || strpos($source, '://'))
        return $source;
    if (!$target)
        $target = $source;
    if (!is_object($image_w)) {
        pc_base::load_sys_class('image', '', '0');
        $image_w = new image(0);
    }
    $image_w->watermark($source, $target);
    return $target;
}

/**
 * 当前路径 
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol = ' > ') {
    $category_arr = array();
    $category_arr = getcache('category_content', 'commons');
    if (!isset($category_arr[$catid]))
        return '';
    $pos = '';
    $siteurl = siteurl();
    $arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'] . ',' . $catid));
    foreach ($arrparentid as $catid) {
        $url = $category_arr[$catid]['url'];
        $pos .= '<a href="' . $url . '">' . $category_arr[$catid]['catname'] . '</a>' . $symbol;
    }
    return $pos;
}

/**
 * 根据catid获取子栏目数据的sql语句
 * @param string $module 缓存文件名
 * @param intval $catid 栏目ID
 */
function get_sql_catid($module = 'category_content', $catid = 0) {
    $category = getcache($module, 'commons');
    $catid = intval($catid);
    if (!isset($category[$catid]))
        return false;
    return $category[$catid]['child'] ? " `catid` IN(" . $category[$catid]['arrchildid'] . ") " : " `catid`=$catid ";
}

/**
 * 获取子栏目
 * @param $parentid 父级id 
 * @param $type 栏目类型
 * @param $self 是否包含本身 0为不包含
 */
function subcat($parentid = NULL, $type = NULL, $self = '0') {
    $category = getcache('category_content', 'commons');
    foreach ($category as $id => $cat) {
        if (($parentid === NULL || $cat['parentid'] == $parentid) && ($type === NULL || $cat['type'] == $type))
            $subcat[$id] = $cat;
        if ($self == 1 && $cat['catid'] == $parentid && !$cat['child'])
            $subcat[$id] = $cat;
    }
    return $subcat;
}

/**
 * 获取内容地址
 * @param $catid   栏目ID
 * @param $id      文章ID
 * @param $allurl  是否以绝对路径返回
 */
function go($catid, $id, $allurl = 0) {
    static $category;
    if (empty($category))
        $category = getcache('category_content', 'commons');
    $id = intval($id);
    if (!$id || !isset($category[$catid]))
        return '';
    $modelid = $category[$catid]['modelid'];
    if (!$modelid)
        return '';
    $db = pc_base::load_model('content_model');
    $db->set_model($modelid);
    $r = $db->get_one(array('id' => $id), '`url`');
    if (!empty($allurl)) {
        $site = siteinfo();
        if (strpos($r['url'], '/') == 0) {
            $r['url'] = substr($site['domain'], 0, -1) . $r['url'];
        } elseif (strpos($r['url'], '/') > 0) {
            $r['url'] = $site['domain'] . $r['url'];
        }
    }
    return $r['url'];
}

/**
 * 将附件地址转换为绝对地址
 * @param $path 附件地址
 */
function atturl($path) {
    if (strpos($path, ':/')) {
        return $path;
    } else {
        $sitelist = siteinfo();
        $siteurl = $sitelist['domain'];
        $domainlen = strlen($sitelist['domain']) - 1;
        $path = $siteurl . $path;
        $path = substr_replace($path, '/', strpos($path, '//', $domainlen), 2);
        return $path;
    }
}

/**
 * 判断模块是否安装
 * @param $m	模块名称
 */
function module_exists($m = '') {
    if ($m == 'admin')
        return true;
    $modules = getcache('modules', 'commons');
    $modules = array_keys($modules);
    return in_array($m, $modules);
}

/**
 * 生成SEO
 * @param $catid        栏目ID
 * @param $title        标题
 * @param $description  描述
 * @param $keyword      关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '') {
    if (!empty($title))
        $title = strip_tags($title);
    if (!empty($description))
        $description = strip_tags($description);
    if (!empty($keyword))
        $keyword = str_replace(' ', ',', ( $keyword));
    $site = siteinfo();
    $cat = array();
    if (!empty($catid)) {
        $categorys = getcache('category_content', 'commons');
        $cat = $categorys [$catid];
        $cat ['setting'] = string2array($cat ['setting']);
    }
    $seo['site_name'] = $site['name'];
    $seo ['site_title'] = isset($site ['site_title']) && !empty($site ['site_title']) ? $site ['site_title'] : $site ['name'];
    $seo ['keyword'] = (isset($title) && !empty($title) ? $title : (isset($cat ['setting'] ['meta_keywords']) && !empty($cat ['setting'] ['meta_keywords']) ? $cat ['setting'] ['meta_keywords'] : (isset($site ['keywords']) && !empty($site ['keywords']) ? $site ['keywords'] : ''))) . (!empty($keyword) ? ',' . $keyword : '');
    $seo ['description'] = isset($description) && !empty($description) ? $description : (isset($cat ['setting'] ['meta_description']) && !empty($cat ['setting'] ['meta_description']) ? $cat ['setting'] ['meta_description'] : (isset($site ['description']) && !empty($site ['description']) ? $site ['description'] : ''));
    $seo ['title'] = (isset($title) && !empty($title) ? $title . ' - ' : '') . (isset($cat ['setting'] ['meta_title']) && !empty($cat ['setting'] ['meta_title']) ? $cat ['setting'] ['meta_title'] . ' - ' : (isset($cat ['catname']) && !empty($cat ['catname']) ? $cat ['catname'] . ' - ' : ''));
    if (!$seo ['title'])
        $seo ['site_title'] .=' - ' . $site ['name'];
    foreach ($seo as $k => $v) {
        $seo [$k] = htmlspecialchars(str_replace(array("\n", "\r"), '', $v));
    }
    return $seo;
}

/**
 * 获取站点的信息
 */
function siteinfo() {
    static $sitelist;
    if (empty($sitelist))
        $sitelist = getcache('sitelist', 'commons');
    return isset($sitelist) ? $sitelist : '';
}

/**
 * 生成标题样式
 * @param $style   样式
 * @param $html    是否显示完整的STYLE
 */
function title_style($style, $html = 1) {
    $str = '';
    if ($html)
        $str = ' style="';
    $style_arr = explode(';', $style);
    if (!empty($style_arr[0]))
        $str .= 'color:' . $style_arr[0] . ';';
    if (!empty($style_arr[1]))
        $str .= 'font-weight:' . $style_arr[1] . ';';
    if ($html)
        $str .= '" ';
    return $str;
}

/**
 * 获取站点域名
 */
function siteurl() {
    static $sitelist;
    if (empty($sitelist))
        $sitelist = siteinfo();
    return $sitelist['domain'];
}

/**
 * 生成上传附件验证
 * @param $args   参数
 * @param $operation   操作类型(加密解密)
 */
function upload_key($args, $operation = 'ENCODE') {
    $pc_auth_key = md5(pc_base::load_config('system', 'auth_key') . $_SERVER['HTTP_USER_AGENT']);
    $authkey = sys_auth($args, $operation, $pc_auth_key);
    return $authkey;
}

/**
 * 生成SEO
 * @param $catid        栏目ID
 * @param $type    规则替换类型 
 * @param $seo_rules   替抱紧规则
 * @param $seo_result  替换紧数据
 */
function seo_extend($catid = '', $type = 'home', $seo_rules = array(), $seo_result = array()) {
    if (empty($catid))
        return '';
    $categorys = getcache('category_content', 'commons');
    if (!isset($categorys [$catid]))
        return '';
    $arrparentid = array_filter(explode(',', $categorys [$catid] ['arrparentid'] . ',' . $catid));
    $category_array = array();
    foreach ($arrparentid as $cid) {
        $category_array = $categorys [$cid];
        $category_array ['setting'] = string2array($category_array ['setting']);
        if ($category_array ['setting'] ['meta_title_' . $type]) {
            break;
        }
    }
    $seo = array();
    if ($category_array ['setting'] ['meta_title_' . $type]) {
        $site = siteinfo();
        $seo_rules = array_merge($seo_rules, array('{$catname}', '{$parent_catname}'));
        $seo_result = array_merge($seo_result, array($categorys [$catid]['catname'], $categorys [$categorys [$catid]['parentid']]['catname']));
        $seo ['title'] = str_replace($seo_rules, $seo_result, $category_array ['setting']['meta_title_' . $type]) . ' - ' . $site['name'];
        $seo ['keyword'] = str_replace($seo_rules, $seo_result, $category_array ['setting']['meta_keywords_' . $type]);
        $seo ['description'] = str_replace($seo_rules, $seo_result, $category_array ['setting']['meta_description_' . $type]);
    }
    foreach ($seo as $k => $v) {
        $seo [$k] = htmlspecialchars(str_replace(array("\n", "\r"), '', $v));
    }
    return $seo;
}

/**
 * 调用关联菜单
 * @param $linkageid
 * @param $id
 * @param $defaultvalue
 */
function select_linkages($linkageid = 0, $id = 'linkid', $defaultvalue = '', $field) {
    $linkageid = intval($linkageid);
    $datas = array();
    $datas = getcache($linkageid, 'linkage');
    $infos = $datas['data'];
    $title = $datas['title'];
    $container = 'content' . random(3) . date('is');
    if (!defined('DIALOG_INIT_1')) {
        define('DIALOG_INIT_1', 1);
        $string .= '<script type="text/javascript" src="' . JS_PATH . 'dialog.js"></script>';
        //TODO $string .= '<link href="'.CSS_PATH.'dialog.css" rel="stylesheet" type="text/css">';
    }
    if (!defined('LINKAGE_INIT_1')) {
        define('LINKAGE_INIT_1', 1);
        $string .= '<script type="text/javascript" src="' . JS_PATH . 'linkage/js/pop.js"></script>';
    }
    $var_div = '';
    if ($defaultvalue && ROUTE_A == 'edit') {
        $db = pc_base::load_model('linkage_model');
        $r = $db->select($where = ' keyid=' . $linkageid . ' and linkageid in(' . $defaultvalue . ')', $data = '`linkageid`,`name`');
        foreach ($r as $v) {
            $var_div.='<li id=\'l' . $v['linkageid'] . '\'>·<input type=\'hidden\' name=\'info[' . $field . '][]\' value=\'' . $v['linkageid'] . '\' /><span>' . $v['name'] . '</span><a href=\'javascript:;\' class=\'close\' onclick="remove_id(\'l' . $v['linkageid'] . '\')"></a></li>';
        }
    }
    $string .= "<a href='javascript:;' onclick=\"open_linkage_selected('{$id}','{$title}',{$container},'{$linkageid}','{$field}');return false;\" style='color:#B5BFBB'>[" . L('publish_to_linkage') . "]</a><ul class='list-dot-othors' id='add_linkages'>{$var_div}</ul>";
    $string .= '<script type="text/javascript">';
    $string .= 'var returnid_' . $id . '= \'' . $id . '\';';
    $string .= 'var returnkeyid_' . $id . ' = \'' . $linkageid . '\';';
    $string .= 'var ' . $container . ' = new Array(';
    foreach ($infos AS $k => $v) {
        if ($v['parentid'] == 0) {
            $s[] = 'new Array(\'' . $v['linkageid'] . '\',\'' . $v['name'] . '\',\'' . $v['parentid'] . '\')';
        } else {
            continue;
        }
    }
    $s = implode(',', $s);
    $string .=$s;
    $string .= ')';
    $string .= '</script>';
    return $string;
}

/**
 *
 *  dimplode() dexplode() Add by SouL_Z 2013-4-2 16:35
 *
 */
function dimplode($array) {
    if (!empty($array)) {
        $array = array_map('addslashes', $array);
        return "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
    } else {
        return 0;
    }
}

function dexplode($str, $is_num = 1, $separator = ',') { //分隔字符串,过滤空值的元素,$is_num为1时过滤非数字元素
    if (!empty($str)) {
        $return = explode($separator, $str);
        foreach ($return as $k => $v) {
            if ($v == '') {
                unset($return[$k]);
            } elseif ($is_num == 1) {
                if (!is_numeric($v)) {
                    unset($return[$k]);
                }
            }
        }
        return $return;
    } else {
        return 0;
    }
}

//获得客户端的IP
//调用：$ip = GetIP("HTTP_CLIENT_IP");
function GetIP() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER ['REMOTE_ADDR']) && $_SERVER ['REMOTE_ADDR'] && strcasecmp($_SERVER ['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER ['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return ($ip);
}

/**
 * 手机声明
 * @return type
 */
function mobile_agent() {
    $domain = $_SERVER['HTTP_HOST'];
    $darr = explode('.', $domain);
    $darr[0] = 'm';
    $domain = implode('.', $darr);
    $murl = 'http://' . $domain . $_SERVER['REQUEST_URI'];
    return '<meta http-equiv="mobile-agent" content="format=xhtml; url=' . $murl . '">' .
            '<script src="http://siteapp.baidu.com/static/webappservice/uaredirect.js" type="text/javascript"></script>' .
            '<script type="text/javascript">uaredirect("' . $murl . '");</script>' .
            '<meta http-equiv="Cache-Control" content="no-transform " />';
}

/**
 * 获取详情链接
 * @param type $cid  漫画id
 * @param type $id   详情id
 */
function getCartoondetailUrl($cid, $id) {
    $manhua_nosee = array(3629, 38596, 33027, 40443, 39710, 47142, 1078, 7520, 49213, 51737, 49212, 51461, 9573, 48862, 48863, 40960, 38107, 4447, 1035, 1609, 1081, 46785, 46321, 48304, 46783, 46474, 46655, 46845, 45110, 44749);
    if (in_array($cid, $manhua_nosee)) {
        return "javascript:alert('漫画版权问题，请下载客户端观看')";
    } else {
        return '/cartoondetail' . $id . '/';
    }
}



/*******
 * 广告
 */
function GetAd($i){
        $ad = array(
            1=>'<script type="text/javascript">
                /*漫画城pc对联120*300 创建于 2016-03-10*/
            var cpro_id = "u2551171";
            </script>
            <script src="http://cpro.baidustatic.com/cpro/ui/f.js" type="text/javascript"></script>',

            2=>'<script type="text/javascript">
                /*漫画城pc底部 960*90 创建于 2016-03-10*/
                var cpro_id = "u2551180";
            </script>
            <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>',

            3=>'<script type="text/javascript">
                /*漫画城资讯右侧250*250 创建于 2016-03-10*/
                var cpro_id = "u2551196";
            </script>
            <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>',

            4=>'<script type="text/javascript">
                /*漫画城pc资讯列表 728*90 创建于 2016-03-10*/
                var cpro_id = "u2551206";
            </script>
            <script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>',

            5=>'<script type="text/javascript">
                /* 漫画城pc搜索推荐 创建于 2016-03-10*/
                var cpro_psid = "u2551214";
            </script>
            <script src="http://su.bdimg.com/static/dspui/js/f.js"></script>'
        );
        return $ad[$i]?$ad[$i]:"";
}
?>