<?php

class MyFocus extends MY_Controller {

    function MyFocus() {
        parent::MY_Controller();
        $this->load->model("friendmodel");
        $this->load->model("memberblog_socket");
        $this->load->model("blogarticle_socket");
//		
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

        $IN['link'] = $this->config->item('base_url') . "/{$domainname}/myfocus/friend";
        //	cnfolAlert($IN['link']);

        $IN['FType'] = ($IN['TitleCate'] == 1) ? '3' : '0';

        $data = $this->pageCommon($IN);
        //error_log(print_r($data,true), 3, '/home/www/html/logs/a1.log');
        $blocks = &$this->config->item('block');

        $extract['keywords2'] = $extract['bloginfoOwner']['NickName'] . ':' . $this->lang->language['keywords_focus'];
        $extract['description'] = $extract['bloginfoOwner']['NickName'] . ':' . $this->lang->language['keywords_focus'];

        $extract = array_merge($extract, $data);
        $extract['case'] = 'follow_friends';
        $extract['title'] = $extract['bloginfoOwner']['BlogName'] . '-关注列表页-' . $extract['bloginfoOwner']['NickName'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['bloginfo']['DomainName'] = $extract['bloginfoOwner']['DomainName'];

        $this->load->view("manage/myfocus.shtml", $extract);
    }

    /*
     *  關注我的好友,即粉丝
     */

    function followMeFriends($domainname) {
        $IN = parse_incoming();
        $this->_checkUserlogin();
        //通过博客名获取博客信息

        $extract['bloginfoOwner'] = $this->_getBlogInfoByDomain($domainname);

        //	var_dump($extract['bloginfo']);
        //标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfoOwner']['UserID']);

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfoOwner'], $extract['isowner']);

        $IN['UserID'] = $extract['bloginfoOwner']['UserID'];

        $IN['link'] = $this->config->item('base_url') . "/{$domainname}/myfocused/friend";
        $IN['FType'] = ($IN['TitleCate'] == 1) ? '3' : '2';

        $data = $this->pageCommon($IN);

        $extract['in'] = $IN;
        $blocks = &$this->config->item('block');
        //	exit;		
        $extract['keywords2'] = $extract['bloginfoOwner']['NickName'] . ':' . $this->lang->language['keywords_fans'];
        $extract['description'] = $extract['bloginfoOwner']['NickName'] . ':' . $this->lang->language['keywords_fans'];

        $extract = array_merge($extract, $data);
        if ($extract['bloginfoOwner']['UserID'] == 9253493) {
            $extract['total'] += 4707;
        }
        $extract['case'] = 'follow_friends';
        $extract['title'] = $this->title['friendmanage'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['title'] = $extract['bloginfoOwner']['BlogName'] . '-粉丝列表页-' . $extract['bloginfoOwner']['NickName'];
        $extract['bloginfo']['DomainName'] = $extract['bloginfoOwner']['DomainName'];
        $this->load->view('manage/fanslist.shtml', $extract);
    }

    /*
     *   公共调用分页
     */

    function pageCommon($IN) {

        $page = is_numeric($IN['p']) ? $IN['p'] : 1;
        $pagesize = is_numeric($IN['ps']) ? $IN['ps'] : FOCUSE_FRIEND_PAGESIZE;
        $param['UserID'] = $IN['UserID'];
        if ($IN['type'] == 'focusSearch') {
            $param['FType'] = 0;
            $param['FNickName'] = $IN["nickname"];
            $param['StartNo'] = -1;
            $rs = $this->friendmodel->searchFriends($param);
        } else if ($IN['type'] == 'fanSearch') {
            $param['FType'] = 2;
            $param['FNickName'] = $IN["nickname"];
            $param['StartNo'] = -1;

            $rs = $this->friendmodel->searchFriends($param);
        } else {
            $param['FType'] = $IN['FType'];
            $param['StartNo'] = -1;
            $rs = $this->friendmodel->getFriendList($param);
        }

        $tmpCnt = $rs['TtlRecords'];

        if ($page > ceil($tmpCnt / FOCUSE_FRIEND_PAGESIZE)) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            $param['StartNo'] = ($page - 1) * FOCUSE_FRIEND_PAGESIZE;
            $param['QryCount'] = $pagesize;
            $param['FlagCode'] = $rs['FlagCode'];
            $param['SortType'] = '1';

            if (isset($IN['type'])) {

                $orderlist = $this->friendmodel->searchFriends($param);
            } else {
                $orderlist = $this->friendmodel->getFriendList($param);
            }



            $orderlist['Record'] = $tmpCnt == 1 ? array($orderlist['Record']) : $orderlist['Record'];
            if ($orderlist['Record']) {
                foreach ($orderlist['Record'] as $k => $v) {
                    $num = $this->_getFriend($v['UserID']);  //粉丝，我关注的，相互关注数    
                    $infoList[$v['UserID']] = $num;
                    $param['UserID'] = $this->user['userid'];
                    $param['FUserIDs'] = $v['UserID'];
                    $friendStatus[$v['UserID']] = $this->friendmodel->verify($param);
                    //$domainname=$this->blogarticle_socket->getBlogDomainName(array('QryData'=>$v['UserID']));
                    $domainname = $this->_getBlogListByUidFirst($v['UserID']);
                    if ($domainname) {
                        $orderlist['Record'][$k]['domainname'] = $domainname['DomainName'];
                    } else {
                        $orderlist['Record'][$k]['domainname'] = '';
                    }
                }

                /*
                  foreach ($orderlist['Record'] as $k => $v) {
                  $res[$v['UserID']] = $this->memberblog_socket->getMemberBlogListByUserID(array('QryData' => $v['UserID'], 'StartNo' => 0, 'QryCount' => 1));

                  $count[$v['UserID']] = $res[$v['UserID']][RetRecords];

                  if ($res[$v['UserID']][RetRecords] == 1) {
                  $list = array($res[$v['UserID']]['Record']);
                  foreach ($list as $key => $val) {
                  $bloginfo[$v['UserID']] = $this->getBlogInfoByDomain($val['DomainName']);
                  $data['MemberIDs'] = $bloginfo[$v['UserID']]['MemberID'];
                  $stat1[$v['UserID']][$data['MemberIDs']] = $this->memberblog_socket->getMemberBlogStat($data);
                  $data['MemberID'] = $bloginfo[$v['UserID']]['MemberID'];
                  $data['StartNo'] = 0; //代表到第几页
                  $data['QryCount'] = 1;
                  $data['Dynamic'] = 1;


                  $articleResult = $this->blogarticle_socket->getMemberArticleList($data);

                  $article[$data['MemberID']] = $articleResult['Record'];
                  }
                  }
                  }
                 */
            }
            $this->load->library('pagebarsnew');
            if (isset($IN['TitleCate'])) {
                $str .="TitleCate=" . $IN['TitleCate'] . '&';
            }
            if (isset($IN['nickname'])) {
                $str .="nickname=" . $IN['nickname'] . '&';
            }
            if (isset($IN['type'])) {
                $str .="type=" . $IN['type'] . '&';
            }
            $baseLink = $IN['link'] . "?" . $str;
            //	echo $baseLink;
            //	$baseLink = $IN['link'].'?'.$str.'&p=';
            $this->pagebarsnew->Page($tmpCnt, $page, FOCUSE_FRIEND_PAGESIZE, $baseLink, '&p=');
            $data['pagebar'] = $this->pagebarsnew->upDownList();
        }
        $data['friendStatus'] = $friendStatus;
        $data['domainname'] = $domainname;
        //$data['article'] = $article;
        //$data['blogcount'] = $count;
        $data['bloginfo'] = $bloginfo;
        $data['friendNum'] = $infoList;
        $data['total'] = $tmpCnt;
        //$data['stat1'] = $stat1;
        $data['list'] = $orderlist['Record'];

        $data['userid'] = $this->user['userid'];

        return $data;
    }

    function dynamicGet() {
        $userids = $this->input->get('userids');
        $userids = trim($userids, ',');
        if (empty($userids)) {
            return;
        }
        $userids = explode(',', $userids);

        $script = "<script>";
        foreach ($userids as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $res[$v] = $this->memberblog_socket->getMemberBlogListByUserID(array('QryData' => $v, 'StartNo' => 0, 'QryCount' => 1));

            $count[$v] = $res[$v][RetRecords];

            if ($res[$v][RetRecords] == 1) {
                $list = array($res[$v]['Record']);
                foreach ($list as $key => $val) {

                    $bloginfo[$v] = $this->getBlogInfoByDomain($val['DomainName']);
                    $data['MemberIDs'] = $bloginfo[$v]['MemberID'];
                    $stat1[$v][$data['MemberIDs']] = $this->memberblog_socket->getMemberBlogStat($data);
                    $data['MemberID'] = $bloginfo[$v]['MemberID'];
                    $data['StartNo'] = 0; //代表到第几页
                    $data['QryCount'] = 1;
                    $data['Dynamic'] = 1;


                    $articleResult = $this->blogarticle_socket->getMemberArticleList($data);

                    $article[$data['MemberID']] = $articleResult['Record'];
                    if (empty($stat1[$v][$data['MemberIDs']]['TotalArticle']) || trim($stat1[$v][$data['MemberIDs']]['TotalArticle']) == '') {
                        $stat1[$v][$data['MemberIDs']]['TotalArticle'] = 0;
                    }
                    $script.="$('#articleNum_" . $v . "').html(" . $stat1[$v][$data['MemberIDs']]['TotalArticle'] . ");";

                    if (!empty($article[$data['MemberID']]['Title']) && trim($article[$data['MemberID']]['Title']) != '') {
                        $script.="$('#dynamic_" . $v . "').html('最新动态：发表一篇文章 <a href=" . $this->config->item('base_url') . "/" . $val['DomainName'] . "/article/" . strtotime($article[$data['MemberID']]['AppearTime']) . "-" . $article[$data['MemberID']]['ArticleID'] . ".html  target=_blank >" . $article[$data['MemberID']]['Title'] . "</a>');";
                    }
                }
            }
        }
        $script.='</script>';
        echo($script);
    }

    //根据域名获取博客信息
    function getBlogInfoByDomain($domain, $force = 1) {
        $data['QryData'] = $domain;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        if (!$bloginfo || $bloginfo['Status'] == 1 || $bloginfo['Status'] == 2) {
            return(array());
        }
        return $bloginfo;
    }

    /* 好友关系处理 */

    function action() {
        //$apiurl = 'http://passport.cnfol.com/friendsapi/action?';
        $this->_checkUserlogin();
        $friendid = $this->input->get('friendIDs');
        $act = $this->input->get('act');
        $userid = $this->user['userid'];
        //	echo $userid ;

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
        error_log(date("Y-m-d H:i:s") . print_r($param, true), 3, DEFAULT_PATH . '/logs/addblack.log');
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

        $attention = $this->friendmodel->getFriendList(Array('UserID' => $bloginfo['UserID'], 'FType' => 0, 'StartNo' => -1));
        $fans = $this->friendmodel->getFriendList(Array('UserID' => $bloginfo['UserID'], 'FType' => 2, 'StartNo' => -1));
        if ($bloginfo['UserID'] == 9253493) {
            $fans['TtlRecords'] += 4707;
        }
        $data['MemberIDs'] = $bloginfo['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);

        $head = getUserHead($bloginfo['UserID'], 96);
        $followUrl = $this->config->item("base_url") . "/{$domainname}/myfocus/friend";
        $followedUrl = $this->config->item("base_url") . "/{$domainname}/myfocused/friend";
        $articleUrl = $this->config->item("base_url") . "/{$domainname}/articlelist/alist";

        $str = '
		<div class="PicBox"><img src="' . $head . '" /></div>
	      <ul class="BloggerAtten">
	        <li>
	        	<a href="' . $followUrl . '" target="_blank" style="text-decoration:none;cursor:pointer;">
		          <p><b class="focusNum">' . substr($attention['TtlRecords'], 0, 8) . '</b></p>
		          <p>关注</p>
		        </a>  
	        </li>
	        <li class="Fans" >  
	        	<a href="' . $followedUrl . '" target="_blank" style="text-decoration:none;cursor:pointer;">
		          <p><b class="fansNum">' . substr($fans['TtlRecords'], 0, 8) . '</b></p>
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

}

?>