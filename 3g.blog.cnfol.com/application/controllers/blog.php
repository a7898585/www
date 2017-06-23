<?php

/* * **********************
 * 功能：   博客个人主页  动态页
 * author： jianglw
 * add：  2013-09-11
 * *********************** */

class Blog extends MY_Controller {

    function Blog() {
        parent::MY_Controller();
    }

    /**
     * @ 个人博客主页 
     * */
    function index($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_jumpIndex($extract['bloginfo']['UserID']); //非博主跳转首页
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['friendsnumber'] = $this->_getFriend($extract['bloginfo']['UserID']); //关注数

        $extract['title'] = $extract['bloginfo']['NickName'] . '_' . $blocks['myblog_index'] . '_' . $extract['bloginfo']['BlogName'];

        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 0;

        $extract['page'] = 1;
        $extract['limit'] = MYBLOG_INDEX_PAGESIZE;

        $limit = $extract['limit'];
        $begin = ($extract['page'] - 1) * $limit;


        $data['UserID'] = $extract['bloginfo']['UserID'] != '' ? $extract['bloginfo']['UserID'] : $this->user['userid'];
        $data['StartNo'] = $begin;
        $data['QryCount'] = $limit;
        
        if ($extract['friendsnumber']['FollowingNum'] != 0) {
//            $data['nWorkOS'] = 1;
            $data['Platform'] = 1;
        }
        $extract['userid'] = $extract['user']['userid'];
//        print_r($data);
        $this->load->model('blogarticle_socket');
        $extract['artList'] = $this->blogarticle_socket->getArticleDynamic($data);
        $extract['blogpagename'] = 'myindex';
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['personalhead'] = $blocks['personalhead'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $this->load->view('manage/MyIndex.html', $extract);
    }

    /**
     * 滚动条拉到浏览器底部时加载更多列表
     * 
     */
    function morenewlist() {
        $page = $this->input->get('page') ? $this->input->get('page') : 2;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : MYBLOG_MORE_PAGESIZE;

        $begin = ($page - 1) * $limit;

        $data['UserID'] = $this->input->get('currentid');
        $data['StartNo'] = $begin;
        $data['QryCount'] = $limit;
        if ($this->input->get('showfans') == 0) {
            $data['nWorkOS'] = 1;
        }
        $this->load->model('blogarticle_socket');

        $artList = $this->blogarticle_socket->getArticleDynamic($data);
        $content = array();
        $baseurl = &config_item('base_url');
        if ($artList) {
            foreach ($artList as $art) {
                $name = ($art['UserID'] == $data['UserID']) ? '我' : $art['NickName'];
                $artTitle = filter($art['Title']);
                $artContent = filter(filter($art['Summary']));
                if (strlen($artTitle) > 28) {
                    $artTitle = utf8_str($artTitle, 28);
                }
                if (strlen($artContent) > 70) {
                    $artContent = utf8_str($artContent, 70);
                }
                $blogUrl = $baseurl . '/' . $art['DomainName'];
                $arturl = $baseurl . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                $time = timeop($art['AppearTime']);
                $topSrc = getUserHead($art['UserID']);
                $isJian = 0;
                if ($art['Recommend'] == 2 || $art['Recommend'] == 3 || $art['IsUsed'] == 1) {
                    $isJian = 1;
                }
                $con['name'] = $name;
                $con['title'] = $artTitle;
                $con['blogUrl'] = $blogUrl;
                $con['content'] = $artContent;
                $con['arturl'] = $arturl;
                $con['topSrc'] = $topSrc;
                $con['time'] = $time;
                $con['isContainImg'] = $art['PictureUrl'];
                $con['isZhuan'] = $art['Property'] == 2 ? '1' : '0';
                $con['isJian'] = $isJian;
                $con['isDing'] = $art['IsTop'] == 1 ? '1' : '0';
                $con['property'] = $art['Property'];
                $content[] = $con;
            }
            $type = 1;
            $currentpage = $page + 1;
        } else {
            $currentpage = $page;
            $type = 2; //无列表
        }
        echo json_encode(array('data' => $content, 'error' => $type, 'page' => $currentpage));
    }

}