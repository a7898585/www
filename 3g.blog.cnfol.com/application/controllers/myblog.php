<?php

/* * **********************
 * 功能：   我的博客、他人博客页面 
 * author： jianglw
 * add：  2013-09-12
 * *********************** */

class MyBlog extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * @ 个人博客主页
     * */
    function index($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息
        if (is_numeric($domainname)) {
            //获取个人博客列表
            $extract['bloginfo'] = $this->_checkBlogByDomain($domainname);
            if (empty($extract['bloginfo'])) {
                $bloglist = $this->_getBlogListByUid($domainname);
                $domainname = getPrimariBlogDomain($bloglist);
            }
        }
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        $extract['userinfo'] = $this->_getUserInfoByUid($extract['bloginfo']['UserID']);
        /* --------统计各文章或各博客主页被访问次数--------------- */
        $this->_hotBlogArticle(array('domainname' => $domainname, 'appearTime' => '', 'articleID' => '', 'guestType' => $this->user['userid']), $extract['bloginfo']['UserID']);
        /* ------------------start粉丝，我关注的，相互关注数  ----------------------------------- */
        $extract['friendsnumber'] = $this->_getFriend($extract['bloginfo']['UserID']);
        $extract['isFrends'] = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        /* ------------------end粉丝，我关注的，相互关注数  ------------------------------------- */
        //访问量
        $this->load->model('memberblog_socket');
        $dataStat['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($dataStat);
        $stat2 = $this->memberblog_socket->getMemberBlogStatByCache($dataStat);
        $extract['totalVisit'] = empty($stat2['TotalClick']) ? $stat1['Totalvisit'] : $stat2['TotalClick'];
        /* ----------start博主的文章数量----------------------------------- */
        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $extract['TotalArticle'] = $stat1['TotalArticle'];
        /* ------------end博主的文章数量------------------------------------ */
        $extract['user'] = $this->user;

        $extract['userid'] = $extract['user']['userid'];
        $extract['blocks'] = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['NickName'] . '-我的博客-' . $extract['bloginfo']['BlogName'];
        $blocks = &$this->config->item('block');
        $extract['baseurl'] = &config_item('base_url');

        /* 文章列表 -start */
        if ($extract['isowner'] == false) {
            $data['Platform'] = 1;
            $data['UserID'] = $extract['bloginfo']['UserID'];
            $data['MemberID'] = $extract['bloginfo']['MemberID'];
            $data['StartNo'] = -1;
            $this->load->model('blogarticle_socket');
            $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
            $tmpCnt = $tempInfo['all'];
            if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
                $data['StartNo'] = 0;
                $data['QryCount'] = MYBLOG_INDEX_PAGESIZE;
                $data['FlagCode'] = $tempInfo['FlagCode'];

                $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);
                if ($extract['artlist']['RetRecords'] == 1) {
                    $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
                }
                if (empty($extract['artlist']['Record'][0])) {
                    $extract['artlist'] = false;
                }
            } else {
                $extract['artList'] = false;
            }
        }
        /* 文章列表 -end */
        $extract['followUrl'] = $this->config->item("base_url") . "/{$domainname}/myfocus";
        $extract['followedUrl'] = $this->config->item("base_url") . "/{$domainname}/myfocused";
        $extract['articleUrl'] = $this->config->item("base_url") . "/{$domainname}/article/list";
        /* 实名认真 -start */
        $params['UserID'] = $extract['bloginfo']['UserID'];
        $extract['auth'] = $this->user_socket->realNameAuth($params);
        /* 实名认真 -end */
        $extract['blogpagename'] = 'myblog';
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['mybloghead'] = $blocks['mybloghead'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $extract['personalhead'] = $blocks['personalhead'];
        $this->load->view('manage/myblog.shtml', $extract);
    }

    /**
     * @博客个人  我的文章收藏列表
     *  我的收藏  sordid  18296
     * */
    function myfavorites($domainname) {
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner'], '0');
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取参数
        $sortid = $this->input->get_post('sortid');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        //根据分类获取文章
        if (is_numeric($sortid) && $sortid > 0) {
            $data['SortID'] = $sortid;
        } else {
            $data['SortID'] = '18296';
        }
        $this->load->model('blogarticle_socket');
        if ($data['SortID'] == '18296') {
            $data['Property'] = 4;
        }
        $tempInfo = $this->blogarticle_socket->getMemberArticleListSort($data);

        $cachekey = array();
        $cachekey['SortID'] = isset($data['SortID']) ? $data['SortID'] : '';
        $cachekey['StartDate'] = isset($data['StartDate']) ? $data['StartDate'] : '';
        if ($cachekey['StartDate'] != '') {
            $cachekey['StartDate'] = date('Y-m-d', strtotime($cachekey['StartDate']));
        }

        $key = 'all';
        $key = $cachekey['StartDate'] . $cachekey['SortID'] . $key;

        $tmpCnt = $tempInfo[$key];
        $pagesize = MYBLOG_INDEX_PAGESIZE;
        if ($page > ceil($tmpCnt / $pagesize)) {
            $page = 1;
        }
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * $pagesize; //代表到第几页
            $data['QryCount'] = $pagesize;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            //print_r($data);
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleListSort($data);
            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }
            if (empty($extract['artlist']['Record'])) {
                $extract['artList'] = false;
            }
        } else {
            $extract['artList'] = false;
        }
        $extract['total'] = $tmpCnt;
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['title'] = $extract['bloginfo']['NickName'] . '-' . $blocks['myfavorites'] . '-' . $extract['bloginfo']['BlogName'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];

        $this->load->view("manage/myfavorites.shtml", $extract);
    }

    /**
     * 更多的收藏文章列表
     * @param type $domainname
     */
    function moreMyfavors() {
        $baseurl = &config_item('base_url');
        $domainname = $this->input->get_post('domainname');
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $sortid = $this->input->get_post('sortid');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        //根据分类获取文章
        if (is_numeric($sortid) && $sortid > 0) {
            $data['SortID'] = $sortid;
        } else {
            $data['SortID'] = '18296';
        }
        $this->load->model('blogarticle_socket');
        if ($data['SortID'] == '18296') {
            $data['Property'] = 4;
        }
        $tempInfo = $this->blogarticle_socket->getMemberArticleListSort($data);

        $cachekey = array();
        $cachekey['SortID'] = isset($data['SortID']) ? $data['SortID'] : '';
        $cachekey['StartDate'] = isset($data['StartDate']) ? $data['StartDate'] : '';
        if ($cachekey['StartDate'] != '') {
            $cachekey['StartDate'] = date('Y-m-d', strtotime($cachekey['StartDate']));
        }
        $key = 'all';
        $key = $cachekey['StartDate'] . $cachekey['SortID'] . $key;

        $tmpCnt = $tempInfo[$key];
        $pagesize = MYBLOG_MORE_PAGESIZE;
        $str = '';
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * $pagesize;
            $data['QryCount'] = $pagesize;
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleListSort($data);
            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            if (empty($extract['artlist']['Record'][0])) {
                $currentpage = $page;
                $type = 2;
            } else {
                foreach ($extract['artlist']['Record'] as $art) {
                    $artTitle = filter($art['Title']);
                    if (strlen($artTitle) > 28) {
                        $art['Title'] = utf8_str($artTitle, 28);
                    }
                    $str .= '<div class="CC_new" id="myfavor_' . $art['ArticleID'] . '"><h2 class="CC_tit F16 Pl10">';
                    $str .= '<a href="' . $baseurl . '/' . $extract['bloginfo']['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html">' . filter_word($art['Title']) . '</a></h2>';
                    $str .= '<div class="CC_cnt Bai_color"><time class="Fl CC_time Hui_color Pl10">' . $art['AppearTime'] . '</time>';
                    $str .= '<a href="javascript:void(0);" onclick="javascript:delArticleFavor(' . $art['ArticleID'] . ');" class="Fr DelSc Tc F12" >取消收藏</a></div>';
                    $str .= '<form id="article_action_form_' . $art['ArticleID'] . '" name="article_action_form" method="post" target="_blank">';
                    $str .= '<input type="hidden" name="act" value="del"/><input type="hidden" name="id"  value="' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '" />';
                    $str .= '<input type="hidden" name="memid" id="memberid" value="' . $extract['bloginfo']['MemberID'] . '" /><input type="hidden" name="flashCode" id="flashcode" value="' . getVerifyStr($extract['bloginfo']['MemberID'] . $extract['bloginfo']['UserID']) . '" />';
                    $str .= '<input type="hidden" name="ismut"  value="' . $art['IsMultimedia'] . '" /><input type="hidden" name="recommend"  value="' . $art['Recommend'] . '" /></form></div>';
                }
                $type = 1;
                $currentpage = $page + 1;
            }
        } else {
            $currentpage = $page;
            $type = 2;
        }
        echo json_encode(array('data' => $str, 'error' => $type, 'page' => $currentpage));
    }

}

