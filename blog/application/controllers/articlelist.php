<?php

/* * **********************
 * 功能：   博客个人文章
 * author： lifeng
 * *********************** */

class ArticleList extends MY_Controller {

    public $pagesize;

    function __construct() {
        parent::MY_Controller();
        $this->load->model('articlecomment_socket');
        $this->pagesize = $this->config->item("showc");
    }

    /*
     * @博客文章列表
     */

    function Alist($domainname) {

        //var_dump($_REQUEST);
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner'], '0');
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['pagesize'] = $this->pagesize;
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取参数    	
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        $this->load->model('blogarticle_socket');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        unset($data);
        $sortID = intval($this->input->get_post('sortid'));
        if ($sortID > 0) {
            $data['SortID'] = $sortID;
        }

        $extract['recommend'] = '';
        $recommend = '';
        if ($this->input->get_post('recommend')) {
            $data['Recommend'] = $this->input->get_post('recommend');
            $data['AllRecommend'] = '1'; //取后台和自己推荐的
            $extract['recommend'] = '1';
            $recommend = '/recommend-1';
        }


        $ismut = '';
        $extract['ismut'] = 0;
        if ($this->input->get_post('ismut') != '') {
            $data['IsMultimedia'] = $this->input->get_post('ismut');
            $ismut = '/ismut-' . $this->input->get_post('ismut');
        }

        $istop = '';
        if ($this->input->get_post('istop') != '') {
            $data['IsTop'] = $this->input->get_post('istop');
            $istop = '/istop-' . $this->input->get_post('istop');
        }


        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['IsSummary'] = $extract['blogconfig']['ShowMode'];
        $data['StartNo'] = -1;

        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);


        if ($data['IsMultimedia'] == 1) {
            $key = 'ismul_1';
        } else if ($data['IsMultimedia'] == 2) {
            $key = 'ismul_2';
        } else if ($data['Recommend'] != '') {
            $key = 'Recommend';
        } else if ($data['IsTop'] != '') {
            $key = 'istop';
        } else {
            $key = 'all';
        }

        $tmpCnt = $tempInfo[$key];
        //print_r($tempInfo);

        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);

            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            $param['BlogType'] = 1;
            $param['StartNo'] = -1;
            if (!empty($extract['artlist']['Record']) && count($extract['artlist']['Record']) > 0) {
                foreach ($extract['artlist']['Record'] as $v) {
                    $param['ArticleID'] = $v['ArticleID'];
                    $tempInfo = $this->articlecomment_socket->getArtCommentList($param);
                    $num[$v['ArticleID']] = $tempInfo['TtlRecords'];
                }
            } else {
                $extract['artList'] = false;
            }
        } else {
            $extract['artList'] = false;
        }


        //翻页信息
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/articlelist/alist' . $ismut . $recommend . $istop;

        $this->load->library('pagebarsnew');

        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $extract['userid'] = $this->user['userid'];
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['num'] = $num;
        $extract['default'] = config_item('default');
        //$extract['title'] = $extract['bloginfo']['BlogName'] . $blocks['articlelisttitle'] . '-' . $extract['bloginfo']['NickName'];
        $extract['title'] = $extract['bloginfo']['NickName'] . '_' . $this->lang->language['keywords_article_cnfol_2'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['show_draft'] = $blocks['show_draft'];

        //为我的博客页面keyword赋值
        $str = $this->getArtKeyWords($extract['bloginfo']['UserID'], $extract['bloginfo']['MemberID']);

        $extract['keywords2'] = '博文_' . $extract['bloginfo']['NickName'] . '_' . $this->lang->language['keywords_cnfol'] . '，' . $this->lang->language['keywords_article_cnfol_3'] . '，' . $str['tagStr'];
        $extract['description'] = $extract['bloginfo']['NickName'] . '的' . $this->lang->language['keywords_cnfol'] . '，' . $str['title'];

        //为我的博客页面keyword赋值

        $this->load->view("article/blog_articlelist_all.shtml", $extract);
    }

    /**
     * @博客个人文章列表
     * @分类文章列表以及时间归档获取文章列表
     * 原  article/SortArticleList/
     * */
    function SortArticleList($domainname) {
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

        $extract['recommend'] = '';
        $recommend = '';
        if ($this->input->get_post('recommend')) {
            $data['Recommend'] = $this->input->get_post('recommend');
            $data['AllRecommend'] = '1'; //取后台和自己推荐的
            $extract['recommend'] = '1';
            $recommend = '/recommend-1';
        }


        $ismut = '';
        $extract['ismut'] = 0;
        if ($this->input->get_post('ismut') != '') {
            $data['IsMultimedia'] = $this->input->get_post('ismut');
            $ismut = '/ismut-' . $this->input->get_post('ismut');
            $extract['ismut'] = $data['IsMultimedia'];
        }

        $istop = '';
        if ($this->input->get_post('istop') != '') {
            $data['IsTop'] = $this->input->get_post('istop');
            $istop = '/istop-' . $this->input->get_post('istop');
        }


        //根据分类获取文章
        if (is_numeric($sortid) && $sortid > 0) {
            $data['SortID'] = $sortid;
        }
        $extract['blogconfig']['DisplayNumber'] = $extract['blogconfig']['DisplayNumber'];
        $this->load->model('blogarticle_socket');

        if ($data['SortID'] == '18296') {
            $data['Property'] = 4;
        }
        $tempInfo = $this->blogarticle_socket->getMemberArticleListSort($data);
//        print_r($tempInfo);
        $cachekey = array();
        $cachekey['SortID'] = isset($data['SortID']) ? $data['SortID'] : '';
        $cachekey['StartDate'] = isset($data['StartDate']) ? $data['StartDate'] : '';
        if ($cachekey['StartDate'] != '') {
            $cachekey['StartDate'] = date('Y-m-d', strtotime($cachekey['StartDate']));
        }

        if ($data['IsMultimedia'] == 1) {
            $key = 'ismul_1';
        } else if ($data['IsMultimedia'] == 2) {
            $key = 'ismul_2';
        } else if ($data['Recommend'] != '') {
            $key = 'Recommend';
        } else if ($data['IsTop'] != '') {
            $key = 'istop';
        } else {
            $key = 'all';
        }

        $key = $cachekey['StartDate'] . $cachekey['SortID'] . $key;

        $tmpCnt = $tempInfo[$key];


        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tempInfo['FlagCode'];

            //print_r($data);
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleListSort($data);
            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }
            $param['BlogType'] = 1;
            $param['StartNo'] = -1;
            $this->load->model('articlecomment_socket');
            if (!empty($extract['artlist']['Record']) && count($extract['artlist']['Record']) > 0) {
                foreach ($extract['artlist']['Record'] as $v) {
                    $param['ArticleID'] = $v['ArticleID'];
                    $tempInfo = $this->articlecomment_socket->getArtCommentList($param);
                    $num[$v['ArticleID']] = $tempInfo['TtlRecords'];
                }
            } else {
                $extract['artList'] = false;
            }
        } else {
            $extract['artList'] = false;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        //翻页信息
        $baseLink = $extract['urlprefix'] . '/sort/' . $sortid . $ismut . $recommend . $istop;

        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . $blocks['articlelisttitle'] . '-' . $extract['bloginfo']['NickName'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['num'] = $num;
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['sortid'] = $sortid;
        $extract['show_draft'] = $blocks['show_draft'];
        $extract['pagesize'] = $this->pagesize;

        $this->load->view("article/blog_articlelist.shtml", $extract);
    }

    /**
     * @博客个人文章列表以及分类文章列表以及时间归档获取文章列表
     * */
    function Archive($domainname) {
        $this->_checkUserlogin();
        $IN = parse_incoming();

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
        $year = $this->input->get_post('year');
        $month = $this->input->get_post('month');

        $extract['year'] = $year;
        $extract['month'] = $month;

        $day = $this->input->get_post('days');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        //根据时间获取文章列表
        if (preg_match('/[0-9]{4}/', $year) && preg_match('/[0-9]{2}/', $month)) {
            if (!preg_match('/[0-9]{2}/', $day)) {
                $day = 28;
                if ($month == 2) {
                    $day = ((($year % 400) == 0) || (($year % 4) == 0 && ($year % 100) != 0)) ? 29 : 28;
                } else if (in_array($month, array(1, 3, 5, 7, 8, 10, 12))) {
                    $day = 31;
                } else {
                    $day = 30;
                }
                $data['StartDate'] = $year . '-' . $month . '-01 00:00:00';
            } else {
                $data['StartDate'] = $year . '-' . $month . '-' . $day . ' 00:00:00';
            }

            $data['EndDate'] = $year . '-' . $month . '-' . $day . ' 23:59:59';
        }




        $extract['recommend'] = '';
        $recommend = '';
        if ($this->input->get_post('recommend')) {
            $data['Recommend'] = $this->input->get_post('recommend');
            $data['AllRecommend'] = '1'; //取后台和自己推荐的
            $extract['recommend'] = '1';
            $recommend = '/recommend-1';
        }


        $ismut = '';
        $extract['ismut'] = 0;
        if ($this->input->get_post('ismut') != '') {
            $data['IsMultimedia'] = $this->input->get_post('ismut');
            $ismut = '/ismut-' . $this->input->get_post('ismut');
            $extract['ismut'] = $data['IsMultimedia'];
        }

        $istop = '';
        if ($this->input->get_post('istop') != '') {
            $data['IsTop'] = $this->input->get_post('istop');
            $istop = '/istop-' . $this->input->get_post('istop');
        }


        //根据分类获取文章
        if (is_numeric($sortid) && $sortid > 0) {
            $data['SortID'] = $sortid;
        }
        $extract['blogconfig']['DisplayNumber'] = $extract['blogconfig']['DisplayNumber'];
        $this->load->model('blogarticle_socket');

        $tempInfo = $this->blogarticle_socket->getMemberArticleListSort($data);
        //print_r($tempInfo);
        $cachekey = array();
        $cachekey['SortID'] = isset($data['SortID']) ? $data['SortID'] : '';
        $cachekey['StartDate'] = isset($data['StartDate']) ? $data['StartDate'] : '';
        if ($cachekey['StartDate'] != '') {
            $cachekey['StartDate'] = date('Y-m-d', strtotime($cachekey['StartDate']));
        }

        if ($data['IsMultimedia'] == 1) {
            $key = 'ismul_1';
        } else if ($data['IsMultimedia'] == 2) {
            $key = 'ismul_2';
        } else if ($data['Recommend'] != '') {
            $key = 'Recommend';
        } else if ($data['IsTop'] != '') {
            $key = 'istop';
        } else {
            $key = 'all';
        }

        $key = $cachekey['StartDate'] . $cachekey['SortID'] . $key;

        $tmpCnt = $tempInfo[$key];

        //print_r($tempInfo);
        $this->pagesize['articlelist'] = $extract['blogconfig']['DisplayNumber'];
        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleListSort($data);

            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }


            $param['BlogType'] = 1;
            $param['StartNo'] = -1;
            if (!empty($extract['artlist']['Record']) && count($extract['artlist']['Record']) > 0) {
                foreach ($extract['artlist']['Record'] as $v) {

                    $param['ArticleID'] = $v['ArticleID'];
                    $tempInfo = $this->articlecomment_socket->getArtCommentList($param);
                    $num[$v['ArticleID']] = $tempInfo['TtlRecords'];
                }
            } else {
                $extract['artList'] = false;
            }
        } else {
            $extract['artList'] = false;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        //翻页信息

        $sign = '';
        if ($ismut . $recommend . $istop != '') {
            $sign = '/';
        }

        $baseLink = $extract['urlprefix'] . '/articlelist/archive/' . $ismut . $recommend . $istop . $sign . $year . '/' . $month;
        if ($this->input->get_post('days')) {
            $baseLink .= '/' . $this->input->get_post('days');
        }

        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['num'] = $num;
        $extract['title'] = $extract['bloginfo']['BlogName'] . $blocks['articlelisttitle'] . '-' . $extract['bloginfo']['NickName'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['pagesize'] = $this->pagesize;
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['show_draft'] = $blocks['show_draft'];

        $this->load->view('article/blog_articlelist_time.shtml', $extract);
    }

}

?>