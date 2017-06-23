<?php

/* * **********************
 * 功能：   手机博客频道首页
 * author： jianglw
 * add：  2013-09-11
 * *********************** */

class Channel extends MY_Controller {

    function Channel() {
        parent::MY_Controller();
        $this->cache->expire = EXPIRETIME1;
        $this->load->model("blogarticle_socket");
    }

    /**
     * 博客频道首页
     * @ return null
     * */
    function Index() {
        $extract['baseurl'] = &config_item('base_url');

        $artList = file_get_contents(config_item('shtml_path') . 'blog_recommendarticle.shtml'); //博客头条
        preg_match_all("/<p class=\"h2\">(.*)<\/p>/", $artList, $artUseList);
        $artUseList= implode(" ",$artUseList[1]);
         
        $pattern = '/<a.*?(?: |\\t|\\r|\\n)?href=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>(.+?)<\/a.*?>/sim';
        preg_match_all($pattern, $artUseList, $matResult);	
        if (!empty($matResult)) {
            $i = 0;
            $recomList = array();
            foreach ($matResult[1] as $k => $v) {
                if ($i < 3) {
                    preg_match("/http:\/\/blog\.cnfol\.com\/(.*)\.html/", $v, $val);
                    if (!empty($val)) {
                        $item = explode('/', $val[1]);
                        if ($item[2] && strstr($item[2], '-')) {
                            $itemTap = explode('-', $item[2]);
                            $recInfo['time'] = getTimeOut($itemTap[0]);
                            $recInfo['title'] = $matResult[2][$k];
                            $recInfo['artUrl'] = $extract['baseurl'] . '/' . $val[1] . '.html';
                            $i++;
                        }
                        if ($recInfo) {
                            $recomList[] = $recInfo;
                        }
                        unset($recInfo);
                    }
                }
            }
        }
        $userid = $_COOKIE['cookie']['passport']['userId'];
        if ($userid) {
            //获取个人博客列表
            $bloglist = $this->_getBlogListByUid($userid);
            $extract['bloginfo']['DomainName'] = getPrimariBlogDomain($bloglist);
        }
        $extract['userid'] = $userid;

        $extract['recomList'] = $recomList;
        $blocks = &$this->config->item('block');
        
        $taglist['mjgs'] = $this->blogarticle_socket->getMjArticle(4, 0, 5); //名家高手 2 3
        
        $taglist['hjby'] = $this->blogarticle_socket->getUseTagArticleList('1453,1462', 0, 5); //黄金白银
        
        
        $taglist['gsjs'] = $this->blogarticle_socket->getUseTagArticleList('1461', 0, 5,'Recommend'); //股市精萃
        $taglist['cjzt'] = $this->blogarticle_socket->getUseTagArticleList('1463', 0, 5,'Recommend'); //财经杂谈
		

        $extract['taglist'] = $taglist;
        $extract['title'] = '首页-中金手机博客';
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        

        $this->load->view('channal/index.shtml', $extract);
    }

    /**
     * 推荐博客
     */
    function recommendBlog($domainname) {
        $this->_checkUserlogin();

        $page = is_numeric($IN['p']) ? $IN['p'] : 1;
        $pagesize = is_numeric($IN['ps']) ? $IN['ps'] : FOCUSE_FRIEND_PAGESIZE;

        $this->load->model("blogarticle_socket");
        $this->load->model("memberblog_socket");

        $param['StartNo'] = ($page - 1) * FOCUSE_FRIEND_PAGESIZE;
        $param['QryCount'] = $pagesize;

        $orderlist = $this->blogarticle_socket->getRecomendBlogList($param);

        $orderlist['Record'] = $tmpCnt == 1 ? array($orderlist['Record']) : $orderlist['Record'];
        if ($orderlist['Record']) {
            $this->load->model("friendmodel");
            foreach ($orderlist['Record'] as $v) {
                $param['UserID'] = $this->user['userid'];
                $param['FUserIDs'] = $v['UserID'];
                $friendStatus[$v['UserID']] = $this->friendmodel->verify($param);

                $res[$v['UserID']] = $this->memberblog_socket->getMemberBlogListByUserID(array('QryData' => $v['UserID'], 'StartNo' => 0, 'QryCount' => 1));
                if ($res[$v['UserID']][RetRecords] == 1) {
                    $list = array($res[$v['UserID']]['Record']);
                    foreach ($list as $val) {
                        $bloginfo[$v['UserID']] = $this->getBlogInfoByDomain($val['DomainName']);
                    }
                }
            }
        }

        $extract['friendStatus'] = $friendStatus;
        $extract['bloginfo'] = $bloginfo;
        $extract['total'] = $tmpCnt;
        $extract['list'] = $orderlist['Record'];
        $extract['userid'] = $this->user['userid'];
        $extract['bloginfo']['DomainName'] = $extract['bloginfoOwner']['DomainName'];
        $extract['bloginfo']['UserID'] = $extract['bloginfoOwner']['UserID'];
        $extract['bloginfo']['FType'] = $param['FType'];

        $extract['bloginfo']['myDomainName'] = $domainname;
        $blocks = &$this->config->item('block');
        $extract['title'] = '推荐博客-中金手机博客';
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];

        $this->load->view('channal/recommend_blog.shtml', $extract);
    }

    function ajaxMoreRecomBlog() {
        $this->load->model("friendmodel");
        $this->load->model("memberblog_socket");
        $this->load->model("blogarticle_socket");
        $IN = parse_incoming();
        $page = is_numeric($IN['page']) ? $IN['page'] : 2;
        $pagesize = is_numeric($IN['ps']) ? $IN['ps'] : MYBLOG_MORE_PAGESIZE;
        
        $param['StartNo'] = ($page - 2) * $pagesize + FOCUSE_FRIEND_PAGESIZE;
        $param['QryCount'] = $pagesize;
        $orderlist = $this->blogarticle_socket->getRecomendBlogList($param);
        $orderlist['Record'] = $orderlist['RetRecords'] == 1 ? array($orderlist['Record']) : $orderlist['Record'];
        if ($orderlist['Record']) {
            foreach ($orderlist['Record'] as $v) {
                $param['UserID'] = $this->_getUserID();
                $param['FUserIDs'] = $v['UserID'];
                $friendStatus[$v['UserID']] = $this->friendmodel->verify($param);

                $res[$v['UserID']] = $this->memberblog_socket->getMemberBlogListByUserID(array('QryData' => $v['UserID'], 'StartNo' => 0, 'QryCount' => 1));
                if ($res[$v['UserID']][RetRecords] == 1) {
                    $list = array($res[$v['UserID']]['Record']);
                    foreach ($list as $val) {
                        $bloginfo[$v['UserID']] = $this->getBlogInfoByDomain($val['DomainName']);
                    }
                }
            }
        }
        $extract['bloginfoOwner'] = $this->_getBlogInfoByDomain($IN['domainname']);
        $isowner = $this->_checkOwnUser($extract['bloginfoOwner']['UserID']);
        $content = array();
        $baseurl = &config_item('base_url');
        if ($orderlist['Record']) {
            foreach ($orderlist['Record'] as $val) {
                $status = $friendStatus[$val['UserID']][0]['FriendStatus'];
                $con['name'] = $val['NickName'];
                $con['blogurl'] = $baseurl . '/' . $bloginfo[$val['UserID']]['DomainName'];
                ;
                $con['content'] = $bloginfo[$val['UserID']]['BlogName'];
                $con['topSrc'] = getUserHead($val['UserID']);
                $con['userId'] = $val['UserID'];
                $con['type'] = '';
                if ($isowner) {
                    if ($status == '1' || $status == '3') {
                        $con['type'] = '<a href="javascript:;" class="Fr GuanZhuBtn2 Tc unfocus' . $val['UserID'] . '" onClick="checkdelete_dialog(\'' . $val['UserID'] . '\', \'unfocus\',\'\',\'' . $val['NickName'] . '\')">取消关注</a>';
                    } else {
                        $con['type'] = '<a href="javascript:;" class="Fr GuanZhuBtn3 Tc unfocus' . $val['UserID'] . '" onClick="checkdelete_dialog(\'' . $val['UserID'] . '\', \'focus\',\'\',\'' . $val['NickName'] . '\')">关注</a>';
                    }
                } else {
                    if ($this->_getUserID() != $val['UserID']) {
                        if ($status == '1' || $status == '3') {
                            $con['type'] = '<a href="javascript:;" class="Fr GuanZhuBtn2 Tc unfocus' . $val['UserID'] . '" onClick="checkdelete_dialog(\'' . $val['UserID'] . '\', \'unfocus\',' . $status . ',\'' . $val['NickName'] . '\')">取消关注</a>';
                        } else {
                            $con['type'] = '<a href="javascript:;" class="Fr GuanZhuBtn3 Tc unfocus' . $val['UserID'] . '" onClick="checkdelete_dialog(\'' . $val['UserID'] . '\', \'focus\',' . $status . ',\'' . $val['NickName'] . '\')">关注</a>';
                        }
                    }
                }
                $content[] = $con;
            }
            $type = 1;
            $currentpage = $page + 1;
        } else {
            $currentpage = $page;
            $type = 2; //无列表
        }
//        print_r($content);
        echo json_encode(array('data' => $content, 'error' => $type, 'page' => $currentpage));
    }

    //根据域名获取博客信息
    function getBlogInfoByDomain($domain) {
        $data['QryData'] = $domain;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        if (!$bloginfo || $bloginfo['Status'] == 1 || $bloginfo['Status'] == 2) {
            return(array());
        }
        return $bloginfo;
    }

}