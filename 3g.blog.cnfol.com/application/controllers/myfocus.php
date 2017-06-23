<?php

class MyFocus extends MY_Controller {

    function MyFocus() {
        parent::MY_Controller();
        $this->load->model("friendmodel");
        $this->load->model("memberblog_socket");
        $this->load->model("blogarticle_socket");
    }

    /*
     *  我关注的好友
     */

    function focusFriends($domainname) {
        $IN = parse_incoming();
        $this->_checkUserlogin();
        //通过博客名获取博客信息
        $extract['bloginfoOwner'] = $this->_getBlogInfoByDomain($domainname);
        //  标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfoOwner']['UserID']);
        //  创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfoOwner'], $extract['isowner']);
        $IN['UserID'] = $extract['bloginfoOwner']['UserID'];
        $IN['link'] = $this->config->item('base_url') . "/{$domainname}/myfocus";
        $IN['FType'] = ($IN['TitleCate'] == 1) ? '3' : '0';

        $data = $this->pageCommon($IN);
        $blocks = &$this->config->item('block');

        $extract = array_merge($extract, $data);
        $extract['userid'] = $this->user['userid'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['title'] = $extract['bloginfoOwner']['NickName'] . '-' . '关注列表页-' . $extract['bloginfoOwner']['BlogName'];
        $extract['bloginfo']['DomainName'] = $extract['bloginfoOwner']['DomainName'];
        $extract['bloginfo']['UserID'] = $extract['bloginfoOwner']['UserID'];
        $extract['bloginfo']['FType'] = $IN['FType'];

        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $this->load->view("manage/myfocus.shtml", $extract);
    }

    /*
     *  關注我的好友,即粉丝
     */

    function followFriends($domainname) {
        $IN = parse_incoming();
        $this->_checkUserlogin();
        //通过博客名获取博客信息
        $extract['bloginfoOwner'] = $this->_getBlogInfoByDomain($domainname);
        //标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfoOwner']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfoOwner'], $extract['isowner']);

        $IN['UserID'] = $extract['bloginfoOwner']['UserID'];
        $IN['link'] = $this->config->item('base_url') . "/{$domainname}/myfocused";
        $IN['FType'] = ($IN['TitleCate'] == 1) ? '3' : '2';

        $data = $this->pageCommon($IN);
        $blocks = &$this->config->item('block');

        $extract = array_merge($extract, $data);
        $extract['userid'] = $this->user['userid'];
        $extract['title'] = $this->title['friendmanage'];
        $extract['title'] = $extract['bloginfoOwner']['NickName'] . '-' . '粉丝列表页-' . $extract['bloginfoOwner']['BlogName'];
        $extract['bloginfo']['DomainName'] = $extract['bloginfoOwner']['DomainName'];
        $extract['bloginfo']['UserID'] = $extract['bloginfoOwner']['UserID'];
        $extract['bloginfo']['FType'] = $IN['FType'];
        
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $this->load->view('manage/myfans.shtml', $extract);
    }

    /*
     *   公共调用分页
     */

    function pageCommon($IN) {

        $page = is_numeric($IN['p']) ? $IN['p'] : 1;
        $pagesize = is_numeric($IN['ps']) ? $IN['ps'] : FOCUSE_FRIEND_PAGESIZE;
        $param['UserID'] = $IN['UserID'];
        $param['FType'] = $IN['FType'];
        $param['StartNo'] = -1;
        $rs = $this->friendmodel->getFriendList($param);

        $tmpCnt = $rs['TtlRecords'];
        if ($page > ceil($tmpCnt / FOCUSE_FRIEND_PAGESIZE)) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            $param['StartNo'] = ($page - 1) * FOCUSE_FRIEND_PAGESIZE;
            $param['QryCount'] = $pagesize;
            $param['FlagCode'] = $rs['FlagCode'];

            if (isset($IN['type'])) {

                $orderlist = $this->friendmodel->searchFriends($param);
            } else {
                $orderlist = $this->friendmodel->getFriendList($param);
            }
            $orderlist['Record'] = $tmpCnt == 1 ? array($orderlist['Record']) : $orderlist['Record'];
            if ($orderlist['Record']) {
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
        }
        $data['friendStatus'] = $friendStatus;
        $data['bloginfo'] = $bloginfo;
        $data['total'] = $tmpCnt;
        $data['list'] = $orderlist['Record'];
        $data['userid'] = $this->user['userid'];
        return $data;
    }

    /**
     * 加载更多粉丝
     */
    function moreMyfocus() {
        $IN = parse_incoming();
        $page = is_numeric($IN['page']) ? $IN['page'] : 2;
        $pagesize = is_numeric($IN['ps']) ? $IN['ps'] : MYBLOG_MORE_PAGESIZE;

        $param['UserID'] = $IN['currentid'];
        $param['FType'] = $IN['ftype'];
        $param['StartNo'] = -1;
        $rs = $this->friendmodel->getFriendList($param);
        $tmpCnt = $rs['TtlRecords'];
        if ($tmpCnt > 0) {
            $param['StartNo'] = ($page - 2) * $pagesize + FOCUSE_FRIEND_PAGESIZE;
            $param['QryCount'] = $pagesize;
            $param['FlagCode'] = $rs['FlagCode'];
            if (isset($IN['type'])) {
                $orderlist = $this->friendmodel->searchFriends($param);
            } else {
                $orderlist = $this->friendmodel->getFriendList($param);
            }
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

    /* 好友关系处理 */

    function action() {
        $this->_checkUserlogin();
        $friendid = $this->input->get('friendIDs');
        $act = $this->input->get('act');
        switch ($act) {
            case 'add':
                $param['UserID'] = $this->user['userid'];
                $param['Type'] = '0';
                $param['FriendData'] = $friendid;
                $param['FType'] = 2;
                $rs = $this->friendmodel->follow($param);
                if ($rs) {
                    echo json_encode(array('erron' => '01', 'error' => '添加关注成功'));
                    exit;
                } else {
                    echo json_encode(array('erron' => '02', 'error' => '添加关注失败'));
                    exit;
                }
                break;

            case 'del':
                $param['UserID'] = $this->user['userid'];
                $param['FType'] = 0;
                $param['FriendData'] = $friendid;
                $param['Type'] = 0;
                $rs = $this->friendmodel->unfollow($param);
                if ($rs) {
                    echo json_encode(array('erron' => '01', 'error' => '取消关注成功'));
                    exit;
                } else {
                    echo json_encode(array('erron' => '02', 'error' => '取消关注失败'));
                    exit;
                }
                break;

            default:
                echo json_encode(array('errno' => '07', 'error' => '您还没选择所要的操作'));
                exit;
        }
    }

    /*
     *  取消关注
     */

    function unFollowAjax() {
        $IN = parse_incoming();
        $this->_checkUserlogin();
        $param['UserID'] = $this->user['userid'];
        $param['FType'] = isset($IN['FType']) ? $IN['FType'] : 0;
        $param['FriendData'] = $IN['friendIDs'];
        $param['Type'] = 0;
        $rs = $this->friendmodel->unfollow($param);
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => '取消关注成功'));
        } else {
            echo json_encode(array('erron' => '02', 'error' => '取消关注失败'));
        }
    }

    /*
     * 移除粉丝
     */

    function removeFans() {
        $IN = parse_incoming();
        $this->_checkUserlogin();
        $param['UserID'] = $IN['UserID'];
        $param['FType'] = 0;
        $param['FriendData'] = $this->user['userid'];
        $param['act'] = 'remove';
        $param['Type'] = 0;
        $rs = $this->friendmodel->unfollow($param);
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => '移除粉丝成功'));
        } else {
            echo json_encode(array('erron' => '02', 'error' => '移除粉丝失败'));
        }
    }

    /*
     *  添加关注
     */

    function followAjax() {
        $IN = parse_incoming();
        $this->_checkUserlogin();
        $param['UserID'] = $this->user['userid'];
        $param['Type'] = '0';
        $param['FriendData'] = $IN['friendIDs'];
        $param['FType'] = isset($IN['FType']) ? $IN['FType'] : 0;
        error_log(date("Y-m-d H:i:s") . print_r($param, true), 3, '/home/httpd/logs/addblack.log');
        $rs = $this->friendmodel->follow($param);
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => '添加关注成功'));
        } else {
            echo json_encode(array('erron' => '02', 'error' => '添加关注失败'));
        }
    }

    /*
     *  获得单独的博客信息 
     */

    function domainInfo($domainname) {
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $num = $this->_getFriend($bloginfo['UserID']);  //粉丝，我关注的，相互关注数

        $data['MemberIDs'] = $bloginfo['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);

        $head = getUserHead($bloginfo['UserID']);
        $followUrl = $this->config->item("base_url") . "/{$domainname}/myfocus/friend";
        $followedUrl = $this->config->item("base_url") . "/{$domainname}/myfocused/friend";
        $articleUrl = $this->config->item("base_url") . "/{$domainname}/articlelist/alist";

        $str = '
		<div class="PicBox"><img src="' . $head . '" /></div>
	      <ul class="BloggerAtten">
	        <li>
	        	<a href="' . $followUrl . '" target="_blank" style="text-decoration:none;cursor:pointer;">
		          <p><b class="focusNum">' . substr($num['FollowingNum'], 0, 8) . '</b></p>
		          <p>关注</p>
		        </a>  
	        </li>
	        <li class="Fans" >  
	        	<a href="' . $followedUrl . '" target="_blank" style="text-decoration:none;cursor:pointer;">
		          <p><b class="fansNum">' . substr($num['FllowerNum'], 0, 8) . '</b></p>
		          <p>粉丝</p>
		        </a>  
	        </li>
	        <li>  
	        	<a href="' . $articleUrl . '" target="_blank" style="text-decoration:none;cursor:pointer;">
		          <p><b class="articleNum">' . substr($stat1['TotalArticle'], 0, 8) . '</b></p>
		          <p>文章</p>
		        </a>   		
	        </li>
	      </ul>';

        echo $str;
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

?>