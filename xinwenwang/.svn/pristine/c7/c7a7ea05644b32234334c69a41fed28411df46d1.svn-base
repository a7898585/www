<?php

/**
 * 分页
 * @param number $totalRows
 * @param number $limitRow
 * @return string
 */
function showPage($totalRows, $limitRow = 20, $parameter = array()) {
    $page = new \Common\Extend\Page($totalRows, $limitRow, $parameter);
    $page->rollPage = 7;
    $page->lastSuffix = false;
    $page->setConfig('first', '第一页');
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('last', '最后一页');
    return $page->show();
}

/**
 * 分页
 * @param number $totalRows
 * @param number $limitRow
 * @return string
 */
function showMobilePage($totalRows, $limitRow = 20, $parameter = array()) {
    $page = new \Common\Extend\Page($totalRows, $limitRow, $parameter);
    $page->rollPage = 7;
    $page->lastSuffix = false;
    $page->setConfig('first', '第一页');
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('last', '最后一页');
    return $page->showMobilePage();
}

/**
 * 分页
 * @param number $totalRows
 * @param number $limitRow
 * @return string
 */
function showHomePage($totalRows, $limitRow = 20, $parameter = array()) {
    $page = new \Common\Extend\Page($totalRows, $limitRow, $parameter);
    $page->rollPage = 5;
    $page->lastSuffix = false;
    $page->setConfig('first', '<<');
    $page->setConfig('prev', '<');
    $page->setConfig('next', '>');
    $page->setConfig('last', '>>');
    return $page->showHomePage();
}

/**
 * 导出数据为excel表格
 * @param $data    一个二维数组,结构如同从数据库查出来的数组
 * @param $title   excel的第一行标题,一个数组,如果为空则没有标题
 * @param $filename 下载的文件名
 * @examlpe
  $stu = M ('User');
  $arr = $stu -> select();
  exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
 */
function exportexcel($data = array(), $title = array(), $filename = 'report') {
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=" . $filename . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    //导出xls 开始
    if (!empty($title)) {
        foreach ($title as $k => $v) {
            $title[$k] = iconv("UTF-8", "GB2312", $v);
        }
        $title = implode("\t", $title);
        echo "$title\n";
    }
    if (!empty($data)) {
        foreach ($data as $key => $val) {
            foreach ($val as $ck => $cv) {
                $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
            }
            $data[$key] = implode("\t", $data[$key]);
        }
        echo implode("\n", $data);
    }
}

/*
 * HTTP GET Request
 */

function get($url, $param = null) {
    if ($param != null) {
        $query = http_build_query($param);
        $url = $url . '?' . $query;
    }
    $ch = curl_init();
    if (stripos($url, "https://") !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    $status = curl_getinfo($ch);
    curl_close($ch);
    if (intval($status["http_code"]) == 200) {
        return $content;
    } else {
        echo $status["http_code"];
        return false;
    }
}

/*
 * HTTP POST Request
 */

function post($url, $params) {
    $ch = curl_init();
    if (stripos($url, "https://") !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $content = curl_exec($ch);
    $status = curl_getinfo($ch);
    curl_close($ch);
    if (intval($status["http_code"]) == 200) {
        return $content;
    } else {
        echo $status["http_code"];
        return false;
    }
}

function http_build_query_multi($params, $boundary) {
    if (!$params)
        return '';

    uksort($params, 'strcmp');

    $MPboundary = '--' . $boundary;
    $endMPboundary = $MPboundary . '--';
    $multipartbody = '';

    foreach ($params as $parameter => $value) {

        if (in_array($parameter, array('pic', 'image'))) {
            $content = file_get_contents($value);
            $filename = 'upload.jpg';

            $multipartbody .= $MPboundary . "\r\n";
            $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"' . "\r\n";
            $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
            $multipartbody .= $content . "\r\n";
        } else {
            $multipartbody .= $MPboundary . "\r\n";
            $multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
            $multipartbody .= $value . "\r\n";
        }
    }

    $multipartbody .= $endMPboundary;
    return $multipartbody;
}

function responseString($code = '', $response = '', $desc = '', $type = '1') {
    header("Content-type: text/html; charset=utf-8");
    $res = array('code' => $code, 'response' => $response, 'desc' => $desc);
    switch ($type) {
        case '1':
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            break;
        default:
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            break;
    }
    exit;
}

function fdate($time) {
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = time() - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = time() - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = time() - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = time() - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = time() - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = time() - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = time() - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '刚刚';
                break;
            case $d < 0:
                $fdate = '刚刚';
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}

function fnewdate($time) {
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y-m-d', $time);
                break;
            case $d < $td:
                $fdate = '后&nbsp;&nbsp;天&nbsp;' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明&nbsp;&nbsp;天&nbsp;' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '&nbsp;&nbsp;秒&nbsp;钟&nbsp;前&nbsp;';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '&nbsp;&nbsp;分&nbsp;钟&nbsp;前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '&nbsp;&nbsp;小&nbsp;时&nbsp;前';
                break;
            case $d < $yd:
                $fdate = '昨&nbsp;&nbsp;天&nbsp;' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前&nbsp;&nbsp;天&nbsp;' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m-d H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m-d', $time);
                break;
            default:
                $fdate = date('Y-m-d', $time);
                break;
        }
    }
    return $fdate;
}

//友好时间显示结束

function showTypes($id = 0) {
    $data = array(
        '1' => '无图模式',
        '2' => '单图模式',
        '3' => '多图模式',
        '4' => '广告模式',
//        '5' => '专题',
    );
    return $id ? $data[$id] : $data;
}

function encodeFileName($str) {
    $pathinfo = pathinfo($str);
    return dirname($str) . '/' . rawurlencode(basename($str));
}

function setUpUrl($url) {
    if (empty($url))
        return '/Public/Home/images/xwwicon.png';
    if (substr_count($url, 'http://')) {
        return ($url);
    } elseif (substr_count($url, 'xwwicon.png')) {
        return ($url);
    }
    $url = str_replace("&amp;", "&", $url);
    return C('YOU_PAI_YUN') . encodeFileName($url);
}

function uploadPhoto($file, $rootPath = 'Uploads') {
    $root_path = './' . $rootPath . '/';
    $config = array(
        'rootPath' => $root_path, //保存根路径
        'mimes' => array('application/octet-stream', 'image/gif', 'image/jpeg', 'image/png'), //允许上传的文件MiMe类型
        'maxSize' => 2097152, //上传的文件大小限制 (0-不做限制)
        'exts' => array(), //允许上传的文件后缀
        'saveName' => array('getPicName', array('10000', '99999')), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'replace' => true
    );
    $mm = new \Think\Upload($config);
    $m = $mm->uploadOne($file);
    if ($m) {
        return array('status' => 1, 'url' => '/' . $rootPath . '/' . $m['savepath'] . $m['savename']);
    } else {
        return array('status' => 0, 'msg' => $mm->getError());
    }
}

function getPicName($min, $max) {
    return date('YmdHis') . rand($min, $max);
}

function get_city_by_ip($ip = null) {
    if ($ip === null || $ip == '127.0.0.1') {
        return '';
    }
    $api = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=$ip";
    $api_json = @file_get_contents($api);
    $arr = json_decode($api_json, true); //解析json
    if ($arr) {
        return $arr;
    }
    return '';
}

//获得上网IP
function get_online_ip() {
    $mip = file_get_contents("http://1111.ip138.com/ic.asp");
    if ($mip) {
        preg_match('#\[(.*?)]#ism', $mip, $match);
        if (!$match) {
            return get_client_ip();
        }
        $ip = str_replace(array('[', ']'), '', $match[0]);
        return $match[1] ? : $ip;
    } else {
        return get_client_ip();
    }
}

//推荐  热点   社会  娱乐  体育 汽车  财经
//国际   军事   游戏   科技    女性  旅游
//历史   健康   读书
function getNavList() {
    $data = array(
        '' => '推荐', '' => '热点', '' => '社会',
        '' => '娱乐', '' => '体育', '' => '汽车',
        '' => '财经', '' => '推荐', '' => '推荐',
        '' => '推荐', '' => '推荐', '' => '推荐',
        '' => '推荐', '' => '推荐', '' => '推荐',
    );
}

/**
 * 字符截取
 * @param type $str
 * @param type $start
 * @param type $length
 * @param type $charset
 * @param type $suffix
 * @return type
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
    $str = outspace($str);
    if (function_exists("mb_substr")) {
        if ($suffix)
            return mb_substr($str, $start, $length, $charset) . "...";
        else
            return mb_substr($str, $start, $length, $charset);
    }elseif (function_exists('iconv_substr')) {
        if ($suffix)
            return iconv_substr($str, $start, $length, $charset) . "...";
        else
            return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
        return $slice . "…";
    return $slice;
}

/**
 * 去除空格
 * @param type $con
 * @return type
 */
function outspace($con) {
    $temp = trim(strip_tags(htmlspecialchars_decode(trim($con))));
    return str_replace(array(" ", "　", "\t", "\n", "\r", "&nbsp;", ">", "\r\n", "&gt;"), "", trim($temp));
}

/**
 * 获取分类名称
 */
function getTypeName($id) {
    return M('NewsType')->where(array('id' => intval($id)))->getField('title');
}

function getUsersName($id) {
    return M('Users')->where(array('id' => intval($id)))->getField('username');
}

function checkPicStatus($url, $myurl = '') {
    if ($myurl == '') {
//        $tempu = parse_url($url);
//        $myurl = 'http://' . $tempu['host'];
    }
    $curl = curl_init($url); //设置url
    curl_setopt($curl, CURLOPT_REFERER, $myurl ? $myurl : $_SERVER['HTTP_HOST']); //伪装referer（正常在地址栏输入网址能打开，因为referer是空的，而在网页中用 src 打开时referer是有内容的所以打不开，referer内容可以到浏览器控制台查看，这一步就是假装是我们的网页内打开的，来判断图片是否设置了防盗链）
    curl_setopt($curl, CURLOPT_HEADER, 1); //获取Header
    curl_setopt($curl, CURLOPT_NOBODY, true); //因为我们只需要Header,所以Body就不要了吧
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //数据存到成字符串吧，别给我直接输出到屏幕了
    $data = curl_exec($curl); //开始执行啦～
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE); //获得HTTPSTAT码
    curl_close($curl);
    return ($status == 200) ? true : false;  //如果状态码是200返回真，那状态码是403自然就返回假了。
}

/**
 * 获取远程图片 保存本地
 * @param type $url
 * @param string $filename
 * @return boolean|string
 */
function getUrlImg($url = "", $filename = "") {
    if ($url == "")
        return false;
    if ($filename == "") {
        $ext = strrchr($url, ".");
        if ($ext != ".gif" && $ext != ".jpg" && $ext != ".png")
            return false;
        $filename = date("YmdHis") . $ext;
    }
    ob_start();
    readfile($url);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);
    $fp2 = @fopen($filename, "a");
    fwrite($fp2, $img);
    fclose($fp2);
    return $filename;
}

/**
 * 上传远程图片至upyun
 * @param type $imgurl
 * @return type
 */
function uploadToUpyun($imgurl) {
    $upyun = new Think\Upload\Driver\Upyun(C('UPLOAD_TYPE_CONFIG'));
    $upyun->checkRootPath('./Uploads/');

    $img_info = pathinfo($imgurl);
    if (strpos($imgurl, "\"")) {
        $imgurl = substr($imgurl, 0, strpos($imgurl, "\""));
        $img_info['basename'] = time() . ".jpg";
    }
    $file = array(
        'type' => '',
//        'md5' => $imgurl,
        'savepath' => date('Y-m-d') . '/',
        'savename' => date('YmdHis') . time() . substr($imgurl, strrpos($imgurl, ".")),
        'tmp_name' => $imgurl,
    );
    $temp = $upyun->save($file);
    if ($temp == true) {
        $news_img_url = '/Uploads/' . $file['savepath'] . $file['savename'];
        return $news_img_url;
    }
    return false;
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
 * 添加文字内容关键词
 * @param type $info
 */
function addNewsKey($info) {
    $param['news_id'] = $info['id'];
    $param['title'] = $info['title'];
    $param['addtime'] = $info['add_time'];

    $CP = new \Common\Extend\Cutpage();
    $CP->pagestr = msubstr($info['html'], 0, 3000);
    $CP->cut_str();

    $info['html'] = $CP->pagearr[0];
    $key = '["' . $info['html'] . '"]';
    $dpost = array('stype' => 's_keywordsextract', 'key' => $key);
    $gdata = \Common\Extend\Curl::post2('http://api.zhuaqu.com/nlp', $dpost);
//    print_r($gdata);
    if ($gdata['resultcode'] == 200) {
        $data = $gdata['result']['data'][0];
        $data = explode(',', $data);
        foreach ($data as $key => $value) {
            $keyArr = explode(':', $value);
            $param['keyword'] = $keyArr[0];
            M('NewsKey')->add($param);
            if ($key > 1) {
                break;
            }
        }
    }

    return true;
}

/* * ************************************************************
 *  生成指定长度的随机码。
 *  @param int $length 随机码的长度。
 *  @access public
 * ************************************************************ */

function createRandomCode($length) {
    $randomCode = "";
    $randomChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $randomChars { mt_rand(0, 35) };
    }
    return $randomCode;
}

/* * ************************************************************
 *  将物理路径转为虚拟路径。
 *  @param string $physicalPpath 物理路径。
 *  @access public
 * ************************************************************ */

function toVirtualPath($physicalPpath) {
    $virtualPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $physicalPpath);
    $virtualPath = str_replace("\\", "/", $virtualPath);
    return $virtualPath;
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

/**
 * 写入memeache缓存
 * $cache = S(array(
  'type' => 'memcache',
  'host' => 'localhost',
  'port' => '11211',
  'prefix' => 'xinwenwang_',
  'expire' => 60)
  );
  $cache->name = 'value'; // 设置缓存
  $value = $cache->name; // 获取缓存
  unset($cache->name); // 删除缓存
 */
function setMemcache($key, $data, $expire = '60') {
    if (C('MEMCACHE_ON') == false)
        return true;
    $memecache = C('MEMCACHE');
    $memecache['expire'] = $expire;
    $cache = S($memecache);
    $cache->$key = $data;
    return true;
}

/**
 * 获取memeache缓存
 */
function getMemcache($key) {
    if (C('MEMCACHE_ON') == false)
        return false;
    $memecache = C('MEMCACHE');
    $cache = S($memecache);
    $data = $cache->$key;
    return $data;
}

/**
 * 删除缓存
 * @param type $key
 */
function unsetMemcache($key) {
    if (C('MEMCACHE_ON') == false)
        return false;
    $memecache = C('MEMCACHE');
    $cache = S($memecache);
    unset($cache->$key);
    return true;
}