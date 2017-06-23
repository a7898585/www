<?php

/* * **********************
 * 功能：   博客个人首页获取文章列表
 * *********************** */

class News extends MY_Controller {

    function News() {
        parent::MY_Controller();
    }

    /* 更多文章动态 */

    function morelist() {
        $this->_checkUserlogin();
        $this->load->model('blogarticle_socket');
        $begin = $this->input->get('begin');
        $limit = 12;
        $extract['isvalidate'] = true;
        $data['UserID'] = $this->user['userid'];
        $data['StartNo'] = $begin;
        $data['QryCount'] = $limit;
        $extract['artList'] = $this->blogarticle_socket->getArticleDynamic($data);
        $extract['blogurl'] = config_item('blog_url');
        $extract['domainname'] = config_item('domainname');
        $extract['blogname'] = config_item('blogname');
        $extract['loginuserid'] = $this->user['userid'];
        unset($data);

        if (count($extract['artList']) == 1 && $extract['artList']['0'] == '') {
            $extract['artList'] = '';
        }

        if ($extract['artList']) {
            $extract = array_merge($extract, $this->extract);
            $content = $this->load->view('articleList.html', $extract, true);
        } else {
            $content = '';
        }
        $baseurl = config_item('base_url');

        if ($extract['artList'] == 0) {
            $page = '';
            echo json_encode(array('data' => '1', 'paginator' => $page));
            exit;
        } else if (!empty($extract['artList'])) {
            $page = '<a href="' . $baseurl . '?c=channel&m=morelist&begin=' . ($begin + $limit) . '" class="notInd">查看更多动态 <span class="arrowDowIco Ico"></span></a>';
        } else {
            $page = '<a href="' . $baseurl . '?c=channel&m=morelist&begin=' . ($begin) . '" class="notInd">查看更多动态 <span class="arrowDowIco Ico"></span></a>';
        }

        echo json_encode(array('data' => $content, 'paginator' => $page));
    }

    /* 刷新动态列表、首次加载列表 */

    function newlist() {
        if ($this->input->get('rtime') == '1') {

            $this->_openMsg('dy');
            $extract['display'] = 'none';
        } else if ($this->input->get('rtime') == '2') {
            $this->_openMsg('dy');
        }
        $this->_checkUserlogin();
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $extract['isvalidate'] = true;
        $limit = $this->input->get('limit');
        //$begin=($page*2-2)*$limit;
        $begin = ($page - 1) * $limit;

        $type = $this->input->get('type');

        $data['UserID'] = $this->input->get('currentid') != '' ? $this->input->get('currentid') : $this->user['userid'];
        $data['StartNo'] = $begin;
        $data['QryCount'] = $limit;

        $this->load->model('blogarticle_socket');
        if ($this->input->get('rtime') == '1') {
            $extract['artList'] = $this->blogarticle_socket->getArticleMovement($data);
        } else {

            $extract['artList'] = $this->blogarticle_socket->getArticleDynamic($data);
        }

        //if($this->user['userid']=='6536425')
        //{

        if (empty($extract['artList']) && $this->input->get('my') == '1') {
            $bloginfos = $this->_getBlogInfoByDomain($this->input->get('domainname'));
            $dataNew['UserID'] = $this->user['userid'];
            $dataNew['MemberID'] = $bloginfos['MemberID'];
            $dataNew['StartNo'] = 0;
            $dataNew['QryCount'] = 1;
            $dataNew['IsSummary'] = 1;
            $dataNew['Dynamic'] = 1;
            $articleNewest = $this->blogarticle_socket->getArticleNewest($dataNew);
            $extract['artList'][$articleNewest['Record']['ArticleID']] = $articleNewest['Record'];
            $extract['artList'][$articleNewest['Record']['ArticleID']]['MemberID'] = $dataNew['MemberID'];
            $extract['artList'][$articleNewest['Record']['ArticleID']]['UserID'] = $dataNew['UserID'];
            $extract['artList'][$articleNewest['Record']['ArticleID']]['NickName'] = $bloginfos['NickName'];
            $extract['artList'][$articleNewest['Record']['ArticleID']]['DomainName'] = $this->input->get('domainname');

            //error_log(print_r($extract['artList'],true).'|', 3, '/home/www/html/logs/a11111.log');
        }
        //}

        $artId = '';
        if ($this->input->get('rtime') == '1') {
            if (!empty($extract['artList'])) {
                $artArray = array();
                foreach ($extract['artList'] as $art) {
                    if (getArtid($art['ArticleID'])) {
                        unset($extract['artList'][$art['ArticleID']]);
                        $limit--;
                    } else {
                        setArtId($art['ArticleID']);
                    }
                }


                foreach ($extract['artList'] as $key => $value) {
                    $artId.=$key . ',';
                }

                $artId = trim($artId, ',');
            }
        }

        $extract['blogurl'] = config_item('blog_url');
        $extract['ajaxlmcount'] = config_item('ajaxlmcount');
        $extract['UserID'] = $data['UserID'];

        $extract['loginuserid'] = $this->user['userid'];

        $extract['bloginfo'] = $this->_getBlogInfoByDomain($this->input->get('domainname'));
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);

        if ($extract['artList'] && count($extract['artList']) > 0) {
            $content = $this->load->view('article/devmyindex_articlelist.html', $extract, true);
        } else {
            if ($this->input->get('rtime') != '1') {
                $content = '<div class="borBot">暂无信息</div><script>$(".LoadingBox").hide();</script>';
            } else if ($this->input->get('rtime') == '1') {
                $content = '';
            }
        }

        if ($type == 'index') {
            echo $content;
        } else {
            echo json_encode(array('data' => $content, 'num' => $limit, 'artid' => $artId));
        }
    }

    /* 滚动条拉到浏览器底部时加载更多列表 */

    function morenewlist() {
        $this->_checkUserlogin();

        $page = $this->input->get('page') ? $this->input->get('page') : 2;
        $extract['isvalidate'] = true;
        $limit = $this->input->get('limit');
        //$begin=($page*2-1)*$limit;
        $begin = ($page - 1) * $limit;
        $type = $this->input->get('type');

        $data['UserID'] = $this->input->get('currentid');
        $data['StartNo'] = $begin;
        $data['QryCount'] = $limit;

        $this->load->model('blogarticle_socket');
        $extract['artList'] = $this->blogarticle_socket->getArticleDynamic($data);
        $extract['blogurl'] = config_item('blog_url');
        $extract['ajaxlmcount'] = config_item('ajaxlmcount');
        $extract['UserID'] = $data['UserID'];

        $extract['bloginfo'] = $this->_getBlogInfoByDomain($this->input->get('domainname'));
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        $extract['loginuserid'] = $this->user['userid'];

        if ($extract['artList']) {

            /*
              $baseLink = 'http://blog.cnfol.com/my/'.$this->input->get('domainname').'?pg=';
              $this->load->library('pagebars');
              $extract['tmpCnt']	= $this->blogarticle_socket->getArticleDynamic(array('UserID'=>$data['UserID'],'StartNo'=>-1));

              //$tmpCnt=$extract['tmpCnt'];//接口待定
              //$tmpCnt=300;
              $page=$page-1;
              $this->pagebars->Page($tmpCnt, $page, '30', $baseLink, '');
              $extract['page']        = $page;
              $extract['pagebar']		= str_replace('=/', '=', $this->pagebars->upDownList());
              $extract['pagebar']='<div class="Page">'.$extract['pagebar'].'</div>';
              if($this->input->get('showpage')=='1')
              {
              $extract['showpage']='1';
              }

              if($tmpCnt<31)
              {
              unset($extract['pagebar']);
              }
             */

            $content = $this->load->view('module/articleList.html', $extract, true);
            $content.='<script>$(".LoadingBox").hide();$("#scrolltopflag").val("2");$("#currentpage").val(Number($("#currentpage").val())+1);</script>';
        } else {
            $content = '<div class="ArticleBox">无更多信息可加载...</div>';
            $content.='<script>$(".LoadingBox").hide();</script>';
        }

        if ($type == 'index') {
            echo $content;
        } else {
            echo json_encode(array('data' => $content));
        }
    }

    function _openMsg($type) {
        $this->_checkUserlogin();
        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $rs = $mcache->get('getmyblogmsg_' . $this->user['userid']);

        $rs[$type . 'Time'] = time();
        $mcache->set('getmyblogmsg_' . $this->user['userid'], $rs);
    }

}

//end class
?>