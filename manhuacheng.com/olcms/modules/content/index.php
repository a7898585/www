<?php

defined('IN_OLCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH', CACHE_PATH . 'caches_model' . DIRECTORY_SEPARATOR . 'caches_data' . DIRECTORY_SEPARATOR);
pc_base::load_app_func('util', 'content');

class index {

    private $db, $invitclick_db, $member_db, $sw_db;

    function __construct() {
        $this->db = pc_base::load_model('content_model');
        $this->sw_db = pc_base::load_model('search_words_model');
        $this->link = pc_base::load_model('link_model');
        $this->manhua = pc_base::load_model('Cartoon_model');
        $this->dongman = pc_base::load_model('dongman_model');
        $this->daohang = pc_base::load_model('link_model');
        $this->hezuo = pc_base::load_model('link_model');

    }

    //首页
    public function init() {
        //SEO
        $SEO = seo();
        $sitelist = getcache('sitelist', 'commons');
        $default_style = $sitelist['default_style'];
        $CATEGORYS = getcache('category_content', 'commons');
        $swdata = $this->sw_db->select('', '*', 100);
        //友情链接
        $link = $this->link->select('typeid=13', '*', 50);
        //网址导航
        $daohang = $this->daohang->select('typeid=27', '*', 50);
        //合作伙伴
        $hezuo = $this->hezuo->select('typeid=28', '*', 50);
        $manhua = $this->manhua->select('status=99', '*', 10, 'views DESC');
        //var_dump($manhua);
        $dongman = $this->dongman->select('', '*', 10, 'views DESC');
        //今日热门
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $jinri = $this->manhua->select('1431187200<inputtime<1431273599', '*', 10, 'views DESC');
        //var_dump($jinri);exit;
        $shouye = 1;
        include template('content', 'index', $default_style);
    }

    public function search() {
        //SEO
        $SEO = seo();
        $key = trim($_GET ['key']);
        $sitelist = getcache('sitelist', 'commons');
        $default_style = $sitelist ['default_style'];
        $CATEGORYS = getcache('category_content', 'commons');
        $swdata = $this->sw_db->select('', '*', 100);
        include template('content', 'search_dongman', $default_style);
    }

    //动漫播放内容页
    public function view() {
        $swdata = $this->sw_db->select('', '*', 100);
        $catid = intval($_GET ['catid']);
        $top_catid = '32';
        $id = intval($_GET ['id']);
        $vid = intval($_GET ['vid']);

        if (!$catid || !$id)
            header('Location: /404.html');
        $page = intval($_GET ['page']);
        $page = max($page, 1);
        $CATEGORYS = getcache('category_content', 'commons');

        if (!isset($CATEGORYS [$catid]) || $CATEGORYS [$catid] ['type'] != 0)
            header('Location: /404.html');
        $this->category = $CAT = $CATEGORYS [$catid];
        $this->category_setting = $CAT ['setting'] = string2array($this->category ['setting']);
        $parameters = $CAT ['setting'] ['content_ishtml'];
        $MODEL = getcache('model', 'commons');
        $modelid = $CAT ['modelid'];

        $tablename = $this->db->table_name = $this->db->db_tablepre . $MODEL [$modelid] ['tablename'];
        $r = $this->db->get_one(array('id' => $id));
        if (!$r)
            header('Location: /404.html');

        $this->db->table_name = $tablename . '_data';
        $r2 = $this->db->get_one(array('id' => $id));
        $rs = $r2 ? array_merge($r, $r2) : $r;

        //再次重新赋值，以数据库为准
        $catid = $CATEGORYS [$r ['catid']] ['catid'];
        $modelid = $CATEGORYS [$catid] ['modelid'];

        require_once CACHE_MODEL_PATH . 'content_output.class.php';
        $content_output = new content_output($modelid, $catid, $CATEGORYS);
        $data = $content_output->get($rs);
        extract($data);

        $this->db->table_name = 'tt_dongmanDetail';

        $views = $this->db->get_one("`id` = '$vid'", 'title,url,from,views', 'id DESC');
//                print_r($views);
        if ($views['from'] == '土豆') {
            preg_match('/([^d]+)([0-9]+)i([0-9]+)\.html/s', $views['url'], $pp);
            $uuu = "http://www.tudou.com/v/" . $pp[3] . "/v.swf";
        } elseif ($views['from'] == '乐视') {
            $uuu = $views['url'];
        }
        include template('content', 'view_dongman');
    }

    //内容页
    public function show() {
        
        $swdata = $this->sw_db->select('', '*', 100);
        $catid = intval($_GET['catid']);
        $id = intval($_GET['id']);
        if($id == 32617){$id = 52001;}
        if($id == 41121){$id = 40959;}
        $modelid = intval($_GET['modelid']);
        if (!($catid || $modelid) || !$id)
            showmessage(L('information_does_not_exist'), 'blank');
        $page = max(intval($_GET['page']), 1);
        $CATEGORYS = getcache('category_content', 'commons');
        if (is_array($CAT)) {
            extract($CAT);
        }
        if ($catid) {
            if (!isset($CATEGORYS[$catid]) || $CATEGORYS[$catid]['type'] != 0)
                showmessage(L('information_does_not_exist'), 'blank');
        }
        $this->category = $CAT = $CATEGORYS[$catid];
        $this->category_setting = $CAT['setting'] = string2array($this->category['setting']);
        $parameters = $CAT['setting']['content_ishtml'];
        $MODEL = getcache('model', 'commons');
        $modelid = $modelid ? $modelid : $CAT['modelid'];

        $tablename = $this->db->table_name = $this->db->db_tablepre . $MODEL[$modelid]['tablename'];
        $r = $this->db->get_one(array('id' => $id));
        if (!$r || $r['status'] != 99)
            showmessage(L('info_does_not_exists'), 'blank');
        if ($MODEL[$modelid]['type'] != 3) {
            $this->db->table_name = $tablename . '_data';
            $r2 = $this->db->get_one(array('id' => $id));
            $r = $r2 ? array_merge($r, $r2) : $r;
        }
        //再次重新赋值，以数据库为准
        $catid = $CATEGORYS[$r['catid']]['catid'];
        $modelid = $CATEGORYS[$catid]['modelid'];
        require_once CACHE_MODEL_PATH . 'content_output.class.php';
        $content_output = new content_output($modelid, $catid, $CATEGORYS);
        $data = $content_output->get($r);
        extract($data);

        $no_type = dexplode(NOT_ALLOW);

//		if($modelid == 12){  //判断不予显示的分类
//			if(in_array($type_id,$no_type)){
//				showmessage(L('info_does_not_exists'),'/manhua');
//				exit;
//			}
//		}
        //检查文章会员组权限
        if ($groupids_view && is_array($groupids_view)) {
            $_groupid = param::get_cookie('_groupid');
            $_groupid = intval($_groupid);
            if (!$_groupid) {
                $forward = urlencode(get_url());
                showmessage(L('login_website'), APP_PATH . 'index.php?m=member&c=index&a=login&forward=' . $forward);
            }
            if (!in_array($_groupid, $groupids_view))
                showmessage(L('no_priv'));
        } else {
            //根据栏目访问权限判断权限
            $_priv_data = view_priv($catid);
            if ($_priv_data == '-1') {
                $forward = urlencode(get_url());
                showmessage(L('login_website'), APP_PATH . 'index.php?m=member&c=index&a=login&forward=' . $forward);
            } elseif ($_priv_data == '-2') {
                showmessage(L('no_priv'));
            }
        }
        if (module_exists('comment')) {
            $allow_comment = isset($allow_comment) ? $allow_comment : 1;
        } else {
            $allow_comment = 0;
        }
        //阅读收费 类型
        $paytype = $rs['paytype'];
        $readpoint = $rs['readpoint'];
        $allow_visitor = 1;
        if ($readpoint || $this->category_setting['defaultchargepoint']) {
            if (!$readpoint) {
                $readpoint = $this->category_setting['defaultchargepoint'];
                $paytype = $this->category_setting['paytype'];
            }

            //检查是否支付过
            $allow_visitor = self::_check_payment($catid . '_' . $id, $paytype);
            if (!$allow_visitor) {
                $http_referer = urlencode(get_url());
                $allow_visitor = sys_auth($catid . '_' . $id . '|' . $readpoint . '|' . $paytype) . '&http_referer=' . $http_referer;
            } else {
                $allow_visitor = 1;
            }
        }
        //最顶级栏目ID
        $arrparentid = explode(',', $CAT['arrparentid']);
        $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;

        $template = $template ? $template : $CAT['setting']['show_template'];
        if (!$template)
            $template = 'show';
        //新改
        $sitemodel_field_db = pc_base::load_model('sitemodel_field_model');
        $f_datas = $sitemodel_field_db->select(array('modelid' => $modelid, 'isseo' => 1), 'field,name', 100, 'listorder ASC');
        $SEO = '';
        if ($f_datas[0]) {
            foreach ($f_datas as $v) {
                $seo_rule [] = '{$' . $v ['field'] . '}';
                $seo_result [] = $$v ['field'];
            }
            $SEO = seo_extend($catid, 'show', $seo_rule, $seo_result);
        } else {
            $SEO = seo($catid, $title, $description, $keywords);
        }
        unset($f_datas, $sitemodel_field_db);

        define('STYLE', $CAT['setting']['template_list']);
        if (isset($rs['paginationtype'])) {
            $paginationtype = $rs['paginationtype'];
            $maxcharperpage = $rs['maxcharperpage'];
        }
        $pages = $titles = '';
        if ($rs['paginationtype'] == 1) {
            //自动分页
            if ($maxcharperpage < 10)
                $maxcharperpage = 500;
            $contentpage = pc_base::load_app_class('contentpage');
            $content = $contentpage->get_data($content, $maxcharperpage);
        }
        if ($rs['paginationtype'] != 0) {
            //手动分页
            $CONTENT_POS = strpos($content, '[page]');
            if ($CONTENT_POS !== false) {
                $this->url = pc_base::load_app_class('url', 'content');
                $contents = array_filter(explode('[page]', $content));
                $pagenumber = count($contents);
                if (strpos($content, '[/page]') !== false && ($CONTENT_POS < 7)) {
                    $pagenumber--;
                }
                for ($i = 1; $i <= $pagenumber; $i++) {
                    $pageurls[$i] = $this->url->show($id, $i, $catid, $rs['inputtime']);
                }
                $END_POS = strpos($content, '[/page]');
                if ($END_POS !== false) {
                    if ($CONTENT_POS > 7) {
                        $content = '[page]' . $title . '[/page]' . $content;
                    }
                    if (preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
                        foreach ($m[1] as $k => $v) {
                            $p = $k + 1;
                            $titles[$p]['title'] = strip_tags($v);
                            $titles[$p]['url'] = $pageurls[$p][0];
                        }
                    }
                }
                //当不存在 [/page]时，则使用下面分页
                $pages = content_pages($pagenumber, $page, $pageurls);
                //判断[page]出现的位置是否在第一位 
                if ($CONTENT_POS < 7) {
                    $content = $contents[$page];
                } else {
                    if ($page == 1 && !empty($titles)) {
                        $content = $title . '[/page]' . $contents[$page - 1];
                    } else {
                        $content = $contents[$page - 1];
                    }
                }
                if ($titles) {
                    list($title, $content) = explode('[/page]', $content);
                    $content = trim($content);
                    if (strpos($content, '</p>') === 0) {
                        $content = '<p>' . $content;
                    }
                    if (stripos($content, '<p>') === 0) {
                        $content = $content . '</p>';
                    }
                }
            }
        }
        if (preg_match('/MSIE|Trident/i', $_SERVER['HTTP_USER_AGENT'])) {
            $liulanqi = 'ie';
        }

        if ($modelid == 13) {
            $top_catid = '13';

            $Cartoon_db = pc_base::load_model('Cartoon_model');
            $rs = $Cartoon_db->get_one(array('id' => $manhuaid), 'type_id');
            $manhua_typeid = $rs['type_id'];
            if (in_array($manhua_typeid, $no_type)) { //判断不予显示的分类
                include template('content', 'noshow_manhua');
                exit;
            }

            $this->db->table_name = $tablename;
            $thisinfo = $previous_page = $this->db->get_one("`id`='$id' AND `status`=99 AND `manhuaid` = $manhuaid", 'id,title,url,listorder', 'id DESC');
            //上一话
            $previous_page = $this->db->get_one("`catid` = '$catid' AND `listorder`<'{$thisinfo[listorder]}' AND `status`=99 AND `manhuaid` = $manhuaid", 'id,title,url', 'id DESC');
            //下一话
            $next_page = $this->db->get_one("`catid`= '$catid' AND `listorder`>'{$thisinfo[listorder]}' AND `status`=99 AND `manhuaid` = $manhuaid", 'id,title,url', 'id');
            
            } elseif ($modelid == 17) {
            $top_catid = '32';
        }


        if (isset($_GET['iframe'])) {
            if (strpos($url, APP_PATH) === 0) {
                $domain = APP_PATH;
            } else {
                $urls = parse_url($url);
                $domain = $urls['scheme'] . '://' . $urls['host'] . (isset($urls['port']) && !empty($urls['port']) ? ":" . $urls['port'] : '') . '/';
            }

            switch ($modelid) {
                case 12:
                    include template('content', 'show_manhualist');
                    break;
                case 17:
                    include template('content', 'show_dongmanlist');
                    break;
            }
        } else {
            include template('content', $template);
        }
    }

    //列表页
    public function lists() {
        $swdata = $this->sw_db->select('', '*', 100);
        $catid = intval($_GET['catid']);
        $catdir = trim($_GET['catdir']);
        $linkageid = intval($_GET['linkageid']);
        $CATEGORYS = getcache('category_content', 'commons');
        if (!$catid && $catdir) {
            foreach ($CATEGORYS as $v) {
                $catinfo[$v['catdir']] = $v['catid'];
            }
            $catid = $catinfo[$catdir];
        }
        $_priv_data = view_priv($catid);
        if ($_priv_data == '-1') {
            $forward = urlencode(get_url());
            showmessage(L('login_website'), APP_PATH . 'index.php?m=member&c=index&a=login&forward=' . $forward);
        } elseif ($_priv_data == '-2') {
            showmessage(L('no_priv'));
        }
        if (!$catid && !$catdir)
            showmessage(L('category_not_exists'), 'blank');
        if (!isset($CATEGORYS[$catid]))
            showmessage(L('category_not_exists'), 'blank');
        $CAT = $CATEGORYS[$catid];
        extract($CAT);
        $setting = string2array($setting);
        $seotype = $child ? 'home' : 'list';
        $SEO = seo_extend($catid, $seotype);

        define('STYLE', $setting['template_list']);
        $page = $_GET['page'];

        $template = $setting['category_template'] ? $setting['category_template'] : 'category';
        $template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
        if ($type == 0) {
            $template = $setting['islist'] ? $template : $template_list;
            $arrparentid = explode(',', $arrparentid);
            $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
            $array_child = array();
            $self_array = explode(',', $arrchildid);
            //获取一级栏目ids
            foreach ($self_array as $arr) {
                if ($arr != $catid && $CATEGORYS[$arr][parentid] == $catid) {
                    $array_child[] = $arr;
                }
            }
            $arrchildid = implode(',', $array_child);
            //URL规则
            $urlrules = getcache('urlrules', 'commons');
            $urlrules = str_replace('|', '~', $urlrules[$category_ruleid]);
            $tmp_urls = explode('~', $urlrules);
            $tmp_urls = isset($tmp_urls[1]) ? $tmp_urls[1] : $tmp_urls[0];
            preg_match_all('/{\$([a-z0-9_]+)}/i', $tmp_urls, $_urls);
            if (!empty($_urls[1])) {
                foreach ($_urls[1] as $_v) {
                    $GLOBALS['URL_ARRAY'][$_v] = $_GET[$_v];
                }
            }
            define('URLRULE', $urlrules);
            $GLOBALS['URL_ARRAY']['categorydir'] = $categorydir;
            $GLOBALS['URL_ARRAY']['catdir'] = $catdir;
            $GLOBALS['URL_ARRAY']['catid'] = $catid;
            include template('content', $template);
        } else {
            //单网页
            $this->page_db = pc_base::load_model('page_model');
            $r = $this->page_db->get_one(array('catid' => $catid));
            if ($r)
                extract($r);
            $template = $setting['page_template'] ? $setting['page_template'] : 'page';
            $arrchild_arr = $CATEGORYS[$parentid]['arrchildid'];
            if ($arrchild_arr == '')
                $arrchild_arr = $CATEGORYS[$catid]['arrchildid'];
            $arrchild_arr = explode(',', $arrchild_arr);
            array_shift($arrchild_arr);
            $keywords = $keywords ? $keywords : $setting['meta_keywords'];
            $SEO = seo(0, $title, $setting['meta_description'], $keywords);
            include template('content', $template);
        }

    }

    //JSON 输出
    public function json_list() {
        if ($_GET['type'] == 'keyword' && $_GET['modelid'] && $_GET['keywords']) {
            //根据关键字搜索
            $modelid = intval($_GET['modelid']);
            $id = intval($_GET['id']);

            $MODEL = getcache('model', 'commons');
            if (isset($MODEL[$modelid])) {
                $keywords = safe_replace(htmlspecialchars($_GET['keywords']));
                $keywords = CHARSET == 'gbk' ? iconv('gbk', 'utf-8', $keywords) : $keywords;
                $keywords = addslashes($keywords);
                $this->db->set_model($modelid);
                $result = $this->db->select("keywords LIKE '%$keywords%'", 'id,title,url', 10);
                if (!empty($result)) {
                    $data = array();
                    foreach ($result as $rs) {
                        if ($rs['id'] == $id)
                            continue;
                        if (CHARSET == 'gbk') {
                            foreach ($rs as $key => $r) {
                                $rs[$key] = iconv('gbk', 'utf-8', $r);
                            }
                        }
                        $data[] = $rs;
                    }
                    if (count($data) == 0)
                        exit('0');
                    echo json_encode($data);
                } else {
                    //没有数据
                    exit('0');
                }
            }
        }
    }

    /**
     * 检查支付状态
     */
    private function _check_payment($flag, $paytype) {
        $_userid = param::get_cookie('_userid');
        $_username = param::get_cookie('_username');
        if (!$_userid)
            return false;
        pc_base::load_app_class('spend', 'pay', 0);
        $setting = $this->category_setting;
        $repeatchargedays = intval($setting['repeatchargedays']);
        if ($repeatchargedays) {
            $fromtime = SYS_TIME - 86400 * $repeatchargedays;
            $r = spend::spend_time($_userid, $fromtime, $flag);
            if ($r['id'])
                return true;
        }
        return false;
    }

}

?>
