<?php

/**
 * 获取保险资讯分类列表
 */
function getNewsTypeList($level = '') {
    $data = F('NewsType' . $level);
    if (empty($data)) {
        $where['is_hide'] = '1';
        if ($level) {
            $where['parent_id'] = $level;
        } elseif ($level == 'p') {
            $where['parent_id'] = 0;
        }
        $data = M('NewsType')->where($where)->field('id,parent_id,name,pinyin')->select();
        F('NewsType' . $level, $data);
    }
    return $data;
}

/**
 * 获取资讯分类id
 * @param type $p_type 一级分类id
 */
function getNewsTypeIds($p_type) {
    $ids = array();
    $ids[] = $p_type;
    $data = getNewsTypeList($p_type);
    if (!empty($data)) {
        foreach ($data as $val) {
            $ids[] = $val['id'];
        }
    }
    return implode(',', $ids);
}

/**
 * 获取保险资讯分类名称  id
 */
function getNewsTypeName($value = null) {
    $data = F('NewsTypeName');
    if (empty($data)) {
        $list = getNewsTypeList();
        foreach ($list as $val) {
            $data[$val['id']] = $val['name'];
        }
        F('NewsTypeName', $data);
    }
    return is_null($value) ? $data : $data[$value];
}

/**
 * 获取问吧分类
 * @param type $value
 */
function getWenbaTypeName($value = null) {
    $data = F('WenbaTypeName');
    if (empty($data)) {
        $list = D('Wenda')->getType();
        foreach ($list as $val) {
            $data[$val['id']] = $val['name'];
        }
        F('WenbaTypeName', $data);
    }
    return is_null($value) ? $data : $data[$value];
}

/**
 * 根据拼音获取id
 * @param type $value
 * @return type
 */
function getWenbaTypeId($value = null) {
    $data = F('WenbaTypeId');
    if (empty($data)) {
        $list = D('Wenda')->getType();
        foreach ($list as $val) {
            $data[$val['pinyin']] = $val['id'];
        }
        F('WenbaTypeId', $data);
    }
    return is_null($value) ? $data : $data[$value];
}

/**
 * 获取保险资讯分类数据 pinyin
 */
function getNewsTypeInfo($value = null) {
    $data = F('NewsTypeInfo');
    if (empty($data)) {
        $list = getNewsTypeList();
        foreach ($list as $val) {
            $data[$val['pinyin']] = array('id' => $val['id'], 'name' => $val['name'], 'pinyin' => $val['pinyin']);
            $data[$val['id']] = array('id' => $val['id'], 'name' => $val['name'], 'pinyin' => $val['pinyin']);
        }
        F('NewsTypeInfo', $data);
    }
    return is_null($value) ? $data : $data[$value];
}

/**
 * 产品分类
 * @param type $value
 */
function getProTypeName($value = null) {
    $data = F('ProTypeInfo');
    if (empty($data)) {
        $res = D('Pro')->getProType();
        foreach ($res as $value) {
            $data[$value['id']] = $value['name'];
        }
        F('ProTypeInfo', $data);
    }
    return is_null($value) ? $data : $data[$value];
}

/**
 * 产品状态
 * @param type $value
 */
function getProStatusName($value = null) {
    $data = array('0' => '下架', '1' => '正常', '3' => '待审核', '4' => '审核失败');
    return is_null($value) ? $data : $data[$value];
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
 * 获取用户图片
 */
function getUserImg($id) {
    if ($id) {
        $url = M('UserPhoto')->where(array('uid' => $id))->getField('photo_thumb_path');
    }
    if ($url) {
        return C('YOU_PAI_YUN') . $url;
    } else {
        return C('YOU_PAI_YUN') . '/Uploads/user/no_photo_user.jpg';
    }
}

/**
 * 获取公司名称
 * @param type $id
 * @return type
 */
function getUserCompany($id) {
    return M('Company')->where(array('id' => $id))->getField('short_name');
}

/**
 * 获取公司类型
 * @param type $id
 * @return type
 */
function getCompanyTypeName($id) {
    $type = M('Company')->where(array('id' => $id))->getField('company_type_id');
    if ($type) {
        return M('CompanyType')->where(array('id' => $type))->getField('name');
    }
    return '保险公司';
}

/**
 * 获取用户名
 * @param type $id
 * @return type
 */
function getUserName($id) {
    $name = D('User')->getUserName($id);
    return $name ? $name : '投保客户';
}

function getAgentName($id) {
    $r = D('UserAgent')->getInfo($id);
    return $r['company'] ? $r['company'] : '258集团';
}

function getAgentAreaName($id) {
    $r = D('UserAgent')->getInfo($id);
    return getAreaName($r['city']);
}

/**
 * 获取代理人公司
 * @param type $id
 * @return type
 */
function getUserCompanyById($id) {
    $id = M('UserDaili')->where(array('uid' => $id))->getField('company_id');
    return getUserCompany($id);
}

/**
 * 获取地区
 * @param type $id
 * @return type
 */
function getUserAreaName($id, $type = 1) {
    $name = D('User')->getUserAreaName($id, $type);
    return $name ? $name : '厦门';
}

/**
 * 获取电话
 * @param type $id
 * @return type
 */
function getUserTel($id) {
    $name = M('User')->where(array('id' => $id))->getField('phone');
    return $name ? $name : '****';
}

/**
 * 获取公司图片
 * @param type $id
 * @return type
 */
function getCompanyImg($id) {
    $url = M('Company')->where(array('id' => $id))->getField('photo_url');
    return C('YOU_PAI_YUN') . $url;
}

function showWebPage($totalRows, $limitRow = 20) {
    $page = new \Common\Extend\Page($totalRows, $limitRow);
    $page->rollPage = 7;
    $page->lastSuffix = false;
    $page->setConfig('first', '第一页');
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('last', '最后一页');
    return $page->show();
}

function showAjaxPage($totalRows, $limitRow = 5, $showid, $scriptName) {
    $page = new \Common\Extend\Page($totalRows, $limitRow);
    $page->scriptName = $scriptName;
    $page->showid = $showid;
    $page->rollPage = 7;
    $page->lastSuffix = false;
    $page->setConfig('first', '第一页');
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('last', '最后一页');
    return $page->script_show();
}

function showMobilePage($totalRows, $limitRow = 20) {
    $page = new \Common\Extend\Page($totalRows, $limitRow);
    $page->rollPage = 7;
    $page->lastSuffix = false;
    $page->setConfig('first', '第一页');
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('last', '最后一页');
    $page->setConfig('theme', '%FIRST% %UP_PAGE% %DOWN_PAGE% %END%');
    return $page->show();
}

function showAdminPage($totalRows, $limitRow = 20) {
    $page = new \Common\Extend\Page($totalRows, $limitRow);
    $page->rollPage = 7;
    $page->lastSuffix = false;
    $page->setConfig('first', '第一页');
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('last', '最后一页');
    $page->setConfig('theme', '%FIRST% %UP_PAGE% %DOWN_PAGE% %END%');
    return $page->showAdminPage();
}

/**
 * 获取地区名称
 */
function getAreaName($id) {
    return D('Area')->getSimpleName($id);
}

function getAreaPinyin($id) {
    return D('Area')->getSimplePinyin($id);
}

/**
 * 获取省份城市
 * @param type $id
 */
function getAreaHtml($id, $name = 'area_id', $idname = '', $class = 'k_100') {
    $area = array('001001001', '001002001', '001003001', '001004001');
    $province = D('Area')->province();
    $html = '<select id="province' . $idname . '" class="' . $class . '">';
    foreach ($province as $value) {
        $str = '';
        if ($value['id'] == substr($id, 0, 6) || $value['id'] == $id) {
            $str = 'selected="selected"';
        }
        $html .=' <option value="' . $value['id'] . '" ' . $str . '>' . $value['name'] . '</option>';
    }
    $html .='</select>';
    if (in_array($id, $area)) {
        $html .='<select id = "city' . $idname . '" name = "' . $name . '" class="' . $class . '">
    <option value = "' . $id . '">' . getAreaName($id) . '</option>
    </select>';
    } else {
        $html .='<select id = "city' . $idname . '" name = "' . $name . '" class="' . $class . '">';
        $city = D('Area')->city(substr($id, 0, 6));
        foreach ($city as $value) {
            $str = '';
            if ($value['id'] == $id) {
                $str = 'selected="selected"';
            }
            $html .=' <option value="' . $value['id'] . '" ' . $str . '>' . $value['name'] . '</option>';
        }
        $html .='</select>';
    }
    return $html;
}

function uploadPhoto($file, $rootPath = 'Uploads') {
    $root_path = './' . $rootPath . '/';
    $config = array(
        'rootPath' => $root_path, //保存根路径
        'mimes' => array('image/gif', 'image/jpeg', 'image/png'), //允许上传的文件MiMe类型
        'maxSize' => 2097152, //上传的文件大小限制 (0-不做限制)
        'exts' => array(), //允许上传的文件后缀
        'saveName' => '', //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
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

function encodeFileName($str) {
    $pathinfo = pathinfo($str);
    return dirname($str) . '/' . rawurlencode(basename($str));
}

function setUpUrl($url) {
    if (empty($url))
        return '';
    if (substr_count($url, 'http://'))
        return $url;
    $url = str_replace("&amp;", "&", $url);
    return C('YOU_PAI_YUN') . encodeFileName($url);
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
    print_r($content);
    exit;
    $status = curl_getinfo($ch);
    curl_close($ch);
    if (intval($status["http_code"]) == 200) {
        return $content;
    } else {
//        echo $status["http_code"];
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

function getAreas($areaid) {
    $url = "http://sapi.258.com/area/view/getchildren?areaid=" . $areaid;
    return get($url);
}

/**
 * 系统邮件发送函数
 *
 * @param string $to
 *        	接收邮件者邮箱
 * @param string $name
 *        	接收邮件者名称
 * @param string $subject
 *        	邮件主题
 * @param string $body
 *        	邮件内容
 * @param string $attachment
 *        	附件列表
 * @return boolean
 */
function think_send_mail($to, $subject, $body, $attachment = null) {
    $config = C('THINK_EMAIL');
    vendor('PHPMailer.class#phpmailer'); // 从PHPMailer目录导class.phpmailer.php类文件
    $mail = new PHPMailer (); // PHPMailer对象
    $mail->CharSet = 'UTF-8'; // 设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    $mail->SMTPDebug = 1; // 关闭SMTP调试功能
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能
    if ($config ['smtp_port'] != 25) {
        $mail->SMTPSecure = 'ssl'; // 使用安全协议
    }

    $mail->Host = $config ['smtp_host']; // SMTP 服务器
    $mail->Port = $config ['smtp_port']; // SMTP服务器的端口号
    $mail->Username = $config ['smtp_user']; // SMTP服务器用户名
    $mail->Password = $config ['smtp_pass']; // SMTP服务器密码
    $mail->SetFrom($config ['from_email'], $config ['from_name']);
    $replyEmail = $config ['reply_email'] ? $config ['reply_email'] : $config ['from_email'];
    $replyName = $config ['reply_name'] ? $config ['reply_name'] : $config ['from_name'];
    $mail->AddReplyTo($replyEmail, $replyName);
// 邮件主题
    $mail->Subject = $subject;
// 邮件内容
    $mail->MsgHTML($body);
    if (is_array($to)) {
        foreach ($to as $email) {
            $mail->AddAddress($email);
        }
    } else {
        $mail->AddAddress($to);
    }
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 获取域名前缀
 * @param type $host
 * @return type
 */
function subDomain($host) {
    $r = explode('.', $host);
    return $r[0];
}

function getDoMain($pinyin = '') {
    if ($pinyin == '') {
        return "http://" . I("server.HTTP_HOST") . '/';
    } elseif ($pinyin == 'www' || $pinyin == 'daili' || $pinyin == 'member' || $pinyin == 'agent') {
        return 'http://' . $pinyin . '.' . C('DOMAIN') . '/';
    }
//    $subdomain = subDomain(I("server.HTTP_HOST"));
//    return "http://" . str_replace($subdomain, $pinyin, I("server.HTTP_HOST")) . '/';
    return "http://" . I("server.HTTP_HOST") . '/' . $pinyin . '/';
}

/**
 * 获取目录二级域名
 * @param type $host
 * @return type
 */
function subPathInfo($host) {
    $r = explode('/', $host);
    return $r[0];
}

function subRequestUri($host) {
    $r = explode('/', $host);
    array_shift($r);
    array_shift($r);
    return implode('/', $r);
}

/**
 * 获取IP所在地址
 * @param $ip
 */
function get_ip_address($ip = "") {
    $ip = $ip ? $ip : get_client_ip();
    $ip = "120.42.85.193";
    $get_sina_api = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=" . $ip;

    $get_taobao_api = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;

    $header = array();
    $opts = array(
        CURLOPT_TIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_URL => $get_sina_api
    );
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, true);
    return $data;
}

/**
 * 擅长领域
 * @param type $value
 */
function getGoodAtField($value = null, $style = '', $limit = '') {
    $data = C('good_at_field');
    if (is_null($value)) {
        return $data;
    } else {
        $str = '';
        $ga = explode(',', $value);
        foreach ($ga as $k => $v) {
            if (($k + 1) > $limit && $limit != '')
                break;
            if ($style) {
                $str .= '<em>' . $data[$v] . '</em>';
            } else {
                $str .= $data[$v];
            }
        }
        return $str;
    }
}

/**
 * 获取收入
 * @param type $value
 */
function getShouru($value = null) {
    $data = C('shouru');
    return is_null($value) ? $data : $data[$value];
}

/**
 * 获取支出
 * @param type $value
 */
function getZhichu($value = null) {
    $data = C('zhichu');
    return is_null($value) ? $data : $data[$value];
}

/**
 * 获取保障需求
 */
function getConcept($value = null, $point = '') {
    $data = C('concept');
    if ($point && $value) {
        $concept = explode(',', $value);
        $res = array();
        foreach ($concept as $value) {
            $res[] = $data[$value];
        }
        return implode(',', $res);
    } else {
        return is_null($value) ? $data : $data[$value];
    }
}

/**
 * 投保对象
 */
function getInsureObject($value = null) {
    $data = C('insure_object');
    return is_null($value) ? $data : $data[$value];
}

/**
 * 定制保险来源
 * @param type $value
 * @return type
 */
function getOrigin($value = null) {
    $data = C('insure_origin');
    return is_null($value) ? $data : $data[$value];
}

/**
 * 家庭情况
 * @param type $value
 * @return type
 */
function getFamilyStatus($value = null) {
    $data = C('family_status');
    if (is_null($value)) {
        return $data;
    } else {
        $arr = explode(',', $value);
        $r = array();
        foreach ($arr as $value) {
            $r[] = $data[$value];
        }
        return implode(',', $r);
    }
}

/**
 * 未来五年计划
 * @param type $value
 * @return type
 */
function getFiveYearPlans($value = null) {
    $data = C('five_year_plans');
    if (is_null($value)) {
        return $data;
    } else {
        $arr = explode(',', $value);
        $r = array();
        foreach ($arr as $value) {
            $r[] = $data[$value];
        }
        return implode(',', $r);
    }
}

/**
 * 方案侧重的保障
 * @param type $value
 * @return type
 */
function getMainConcept($value = null) {
    $data = C('main_concept');
    return is_null($value) ? $data : $data[$value];
}

/*
 * 创建目录
 * @dir     目录路径
 * @mode    权限设置 如0775
 * return bool
 */

function mkdirs($dir) {
    if (!is_dir($dir)) {
        if (!mkdirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}

/**
 * 获取等级
 */
function getLevel($exp) {
    $exp_arr = C('exp_level');
    foreach ($exp_arr as $key => $value) {
        if ($exp < $exp_arr[2]) {
            $data['level'] = 1;
            $data['differ_exp'] = $exp_arr[2] - $exp;
            $data['per'] = floor($exp / $exp_arr[2] * 100);
        } elseif ($exp < $value && $exp >= $exp_arr[$key - 1]) {
            $data['level'] = $key - 1;
            $data['differ_exp'] = $value - $exp;
            $data['per'] = floor(($value - $exp_arr[$key - 1] - $data['differ_exp']) / ($value - $exp_arr[$key - 1]) * 100);
        } else {
            $data['level'] = $key;
            $data['differ_exp'] = 0;
            $data['per'] = 100;
        }
    }
    return $data;
}

/**
 * 根据产品id获取方案数
 * @param type $proid
 */
function countProgramByProid($proid) {
    if ($proid) {
        return D('ProProgram')->countProgramByProid($proid);
    }
    return 0;
}

/**
 * 根据产品id获取问答数
 * @param type $proid
 */
function countWendaByProid($proid) {
    if ($proid) {
        return D('Wenda')->countWendaByProid($proid);
    }
    return 0;
}

/**
 * 用户虚拟数据
 * @param type $t
 * @return type
 */
function getUserCount($t = 'daili') {
    $s_time = strtotime(date('2015-2-11'));
    $e_time = time();
    $day = floor(($e_time - $s_time) / (3600 * 24));
    if ($t == 'daili') {
        $count = S('s_daili_count_' . $day);
//        var_dump($count);
        if ($count) {
            return $count;
        } else {
            $l_count = S('s_daili_count_' . ($day - 1));
            if ($l_count) {
                $c = $l_count + rand(1, 10);
            } else {
                $agent_count = D('UserDaili')->dataCount();
                $c = $agent_count + $day * rand(1, 10);
            }
            S('s_daili_count_' . $day, $c, 3600 * 25);
        }
    } elseif ($t == 'member') {
        $count = S('s_member_count_' . $day);
//        var_dump($count);
        if ($count) {
            return $count;
        } else {
            $l_count = S('s_member_count_' . ($day - 1));
            if ($l_count) {
                $c = $l_count + rand(30, 50);
            } else {
                $c = 100000 + $day * rand(30, 50);
            }
            S('s_member_count_' . $day, $c, 3600 * 25);
        }
    }
    return $c;
}

/**
 * 数组排序
 * @param type $data 
 * @param type $str  排序字段
 * @return type
 */
function get_array_multisort($data, $str) {
    foreach ($data as $key => $row) {
        $volume[$key] = $row[$str];
    }
    array_multisort($volume, SORT_DESC, $data);
    return $data;
}

/**
 * 3,加密程序   
 * 加密：ENCODE
 * 解密：DECODE
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 8;
    // 密匙
    $key = md5($key ? $key : "changan123baoxian456");
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();

    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
