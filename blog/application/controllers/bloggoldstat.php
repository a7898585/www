<?php

/* * **********************
 * 功能：   贵金属博客积赞
 * author： jianglw
 * add：2014.06.03
 * *********************** */

class Bloggoldstat extends MY_Controller {

    public function __construct() {
        parent::MY_Controller();
    }

    function index($domain) {
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $extract['page'] = $page;
        $extract['domain'] = $domain;
        $extract['loginurl'] = $this->config->item('base_url') . '/index.php/widget/login';
        $extract['title'] = trim($this->input->get_post('title'));
        $this->load->view('channal/goldstat_index.shtml', $extract);
    }

    //排行列表
    function nowlist($page) {
        $pageSize = 30;
        $title = trim($this->input->get_post('title'));
        $this->load->model('blogstat_socket');


        if (!empty($title)) {
            $shtml = 'goldstat_hislist';
            $data['Title'] = $title;
            $list = $this->blogstat_socket->getSearchList($data);
            $data['type'] = 1;
            $total = $list['TtlRecords'];
        } else {
            $shtml = 'goldstat_list';

            $data['nTimes'] = getnTimes();
            $data['nStart'] = -1;

            $res = $this->blogstat_socket->getStatList($data);
            if ($page > ceil($res['UTopCnt'] / $pageSize)) {
                $page = 1;
            }
            $total = $res['UTopCnt'];

            if ($total > 0) {
                $firstRecord = 0;
                $pageCount = 0;

                page(&$page, &$pageSize, &$pageCount, &$total, &$firstRecord);

                $linkStr = config_item('base_url') . '/stat/nowlist/';
                $pageStr = paging($linkStr, $page, $pageCount, 10);

                $data['nStart'] = $firstRecord;
                $data['nCount'] = $pageSize;
                $data['FlagCode'] = $res['FlagCode'];
                $list = $this->blogstat_socket->getStatList($data);
                if ($list['RetRecords'] == 1) {
                    $list['Record'] = array(0 => $list['Record']);
                }
            }
        }

        $data['UserID'] = $this->_checkUserlogin();
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['pageStr'] = $pageStr;
        $data['list'] = $list['Record'];

        if ($page == 1 && !empty($data['list']) && $data['type'] != 1) {
            $data['list_1'] = array_slice($data['list'], 0, 3);
            $data['list_2'] = array_slice($data['list'], 3, 7);
            $data['list_3'] = array_slice($data['list'], 10);
        }
        $this->load->view('channal/' . $shtml . '.shtml', $data);
    }

    //往期排行列表
    function hislist($page) {
        $pageSize = 3;
        $per = getnTimes(); //当前期数
//        $per = 30;
        $total = $per - 1; //往期数
        $list = array();
        if ($total > 0) {
            $firstRecord = 0;
            $pageCount = 0;

            page(&$page, &$pageSize, &$pageCount, &$total, &$firstRecord);

            $linkStr = config_item('base_url') . '/stat/hislist/';
            $pageStr = paging($linkStr, $page, $pageCount, 10);

            $data['nStart'] = $firstRecord;
            $data['nCount'] = $total < $pageSize ? $total : (($total - $firstRecord) > $pageSize ? $pageSize : ($total - $firstRecord));
            $data['UserID'] = $this->_checkUserlogin();
            $this->load->model('blogstat_socket');
            $list = $this->blogstat_socket->getStatHisList($data);
            $data['pageStr'] = $pageStr;
        }
        $data['list'] = $list;
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $this->load->view('channal/goldstat_hislist.shtml', $data);
    }

    //搜索结果
    function searchlist() {
        $this->load->view('channal/goldstat_list.shtml', $data);
    }

    //获取个人参赛文章信息
    function ajaxgetstat() {
        $this->load->model('blogstat_socket');
        $data['UserID'] = $this->_checkUserlogin();
        $res = $this->blogstat_socket->getInfoStat($data);
        if (!empty($res)) {
            if (isMobile()) {
                $url = 'http://3g.blog.cnfol.com/';
            } else {
                $url = $this->config->item('base_url');
            }
            $res['pcurl'] = $url . "/" . $res['DomainName'] . "/article/" . strtotime($res['AppearTime']) . "-" . $res['ArticleID'] . ".html";
            if ($res['IsCheck'] != 2) {
                $res['url'] = "http://3g.blog.cnfol.com/" . $res['DomainName'] . "/article/" . strtotime($res['AppearTime']) . "-" . $res['ArticleID'] . ".html";
//                $res['pic'] = generateQRfromGoogle($res['url']);
                $res['pic'] = $url . "/getcode.php?url=" . urlencode($res['url']);
            }
        } else {
            $res['error'] = 0;
        }
        $bloglist = $this->_getBlogListByUid($userid);
        $res['domain'] = getPrimariBlogDomain($bloglist);
        echo json_encode($res);
        exit;
    }

    //参赛文章提交
    function ajaxaddstat($domainname) {
        $userid = $this->_checkUserlogin();
        if (!$userid) {
            echo json_encode(array('error' => '请先登入再进行文章提交', 'errno' => 'login'));
            exit;
        }
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        if ($bloginfo['UserID'] != $userid) {
            echo json_encode(array('error' => '你必须提交自己的文章', 'errno' => 'noower'));
            exit;
        }
        $artstr = $this->input->post('articleid');
        if (strpos($artstr, '-') === false) {
            echo json_encode(array('error' => '请正确填写文章链接', 'errno' => 'url'));
            exit;
        } else {
            $temp = explode('-', $artstr);
            if (empty($temp[0])) {
                echo json_encode(array('error' => '请正确填写文章链接', 'errno' => 'url'));
                exit;
            }
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID = $temp[1];
            if (date('Ym') != date("Ym", $temp[0])) {
                echo json_encode(array('error' => '必须本月的文章才能提交参赛', 'errno' => 'date'));
                exit;
            }
        }
        $ActUrl = trim($this->input->post('arturl'));
        $Title = $this->input->post('title');
        $param['nArticleID'] = $articleID;
        $param['sTitle'] = $Title;
        $param['nMemberID'] = $bloginfo['MemberID'];
        $param['nUserID'] = $userid;
        $param['sAppearTime'] = $appearTime;
        $this->load->model('blogstat_socket');
        $res = $this->blogstat_socket->addStatArticle($param);

        if ($res) {
            if ($res['IsSucceed'] == 0) {
                echo json_encode(array('error' => '文章提交成功！工作人员将于1个工作日之内审核您的参赛文章', 'errno' => 'success'));
                exit;
            } elseif ($res['IsSucceed'] == 1) {
                echo json_encode(array('error' => '您提交的文章标题与链接不符，请核对后重新提交', 'errno' => 'failed'));
                exit;
            } elseif ($res['IsSucceed'] == 2) {
                echo json_encode(array('error' => '您提交的文章不是贵金属文章，请核对后重新提交', 'errno' => 'failed'));
                exit;
            }
        } else {
            echo json_encode(array('error' => '文章信息提交失败', 'errno' => 'failed'));
            exit;
        }
    }

}
