<?php

/* * **********************
 * 功能：   博客个人主页
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class DevBlog extends MY_Controller {

    function DevBlog() {
        parent::MY_Controller();
        $this->load->model("devfriend_socket");
    }

    /**
     * @ 个人博客主页 // $styleid 是做预览用
     * */
    function index($domainname, $styleid = 0) {
        $this->_checkUserlogin();

        //通过博客名获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //	var_dump($extract['bloginfo']);
        //标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        $extract['userinfo'] = $this->_getUserInfoByUid($extract['bloginfo']['UserID']);


        /* --------统计各文章或各博客主页被访问次数--------------- */
        //$this->_hotBlogArticle(array('domainname'=>$domainname,'appearTime'=>'','articleID'=>'','guestType'=>$this->user['userid']),$extract['bloginfo']['UserID']);
        /* ----------------------------------------------------- */


        $num = $this->_getFriend($extract['bloginfo']['UserID']);  //粉丝，我关注的，相互关注数
        //	var_dump($num);
        $extract['friendsnumber'] = $num;

        $TransmitFlag = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        $extract['isFrends'] = $TransmitFlag;
        //	echo '######';
        //	var_dump($extract['isFrends']);
        //	echo '@@@@@@@';
        if ($styleid > 0) {
            $extract['blogconfig']['StyleID'] = $styleid;
        }

        //添加样式页面的预览
        $prevCnt = $this->input->post('PreviewContent');
        if ($prevCnt !== FALSE) {
            $extract['PreviewContent'] = $prevCnt;
        }
        $extract['layoutlist'] = $this->config->item('layout');
        $extract['sysmodules'] = $this->config->item('sysmodules');
        $extract['bglist'] = $this->config->item('bgurl');
        $extract['defaultcss'] = $this->config->item('defaultcss');
        $extract['default'] = $this->config->item('default');
        $Modules = array();
        $RModules = (trim($extract['blogconfig']['RModules']) != '') ? explode(',', $extract['blogconfig']['RModules']) : array();
        $MModules = (trim($extract['blogconfig']['MModules']) != '') ? explode(',', $extract['blogconfig']['MModules']) : array();
        $LModules = (trim($extract['blogconfig']['LModules']) != '') ? explode(',', $extract['blogconfig']['LModules']) : array();

        foreach ($RModules as $v) {
            if (isset($extract['sysmodules'][$v])) {
                $Modules['rmods'][] = $extract['sysmodules'][$v][3];
            }
        }
        foreach ($MModules as $v) {
            if (isset($extract['sysmodules'][$v])) {
                $Modules['mmods'][] = $extract['sysmodules'][$v][3];
            }
        }
        foreach ($LModules as $v) {
            if (isset($extract['sysmodules'][$v])) {
                $Modules['lmods'][] = $extract['sysmodules'][$v][3];
            }
        }



        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        // 	var_dump($stat1);




        $extract['module'] = $Modules;
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '_' . $extract['bloginfo']['NickName'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['modulepath'] = &config_item('module_path');
        $extract['isconfig'] = 0;


        /*
         * 实名认真 -start
         */
        $params = "";
        $extract['auth'] = $this->devfriend_socket->realNameAuth($params);




        /*
         * 实名认真 -end
         */
        //	echo '###';
        //	$params = array('UserID' => $this->user['userid']);
        //	var_dump($params);
        //	$rs = $this -> user_socket -> getUserDetailInfo($params);
        //	var_dump($rs);
        //	$params = array(
        //			'UserID' => $this->user['userid']
        //	);
        //	$rs = $this -> user_socket -> getUserDetailInfo($params);
        //	var_dump($rs);
        $param = array(
            'UserID' => $this->user['userid'],
            'Type' => 0,
            'FType' => 0,
            'FriendData' => '1541429',
            'TagName' => 'TAGTILE23444444444233111'
        );
        //  	$this -> devfriend_socket -> addFriendTag($param);

        $params = array(
            'UserID' => $this->user['userid'],
            'FType' => 0
        );

        //  	$this -> devfriend_socket -> searchFriendTag($params);    	
        echo '@@##33#@';
        $this->load->view('manage/devmyblog.shtml', $extract);
    }

    function test() {
        $this->load->view('manage/devfocus.shtml');
    }

    function addTag($blogname) {
        $this->_checkUserlogin();
        $userid = $this->user['userid'];
        $nickname = $this->input->get("blogname");
        $this->load->model('devfriend_socket');
        $param = array('UserID' => $userid,
            'FType' => 0
        );
        $rs = $this->devfriend_socket->searchFriendTag($param);
        $data['userid'] = $userid;
        $data['blogname'] = $blogname;
        $data['friend_tag'] = $rs;
        $this->load->view('manage/devfocus.shtml', $data);
    }

    function createGroup() {
        $this->_checkUserlogin();
    }

    function ajaxAdd() {
        $this->_checkUserlogin();
        $userid = $this->user['userid'];
        $param['UserID'] = $userid;
        $rs = $this->devfriend_socket->addFriendTag($param);
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => '创建成功'));
            exit;
        } else {
            echo json_encode(array('erron' => '02', 'error' => '创建失败'));
            exit;
        }
    }

    function ajaxTagList() {
        $this->_checkUserlogin();
        $userid = $this->user['userid'];

        $this->load->model('devfriend_socket');
        $param = array('UserID' => $userid,
            'FType' => 0
        );
        $rs = $this->devfriend_socket->searchFriendTag($param);
        $html = "";
        foreach ($rs as $val) {
            $html.= "<input type='radio' value='{$val[tagid]}'>{$val['tagname']}";
        }
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => $html));
            exit;
        } else {
            echo json_encode(array('erron' => '02', 'error' => '失败'));
            exit;
        }
    }

    /*
     *  测试列表
     */

    function article($domainname, $styleid = 0) {
        $this->_checkUserlogin();


        //通过博客名获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //	var_dump($extract['bloginfo']);
        //标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        $extract['userinfo'] = $this->_getUserInfoByUid($extract['bloginfo']['UserID']);


        /* --------统计各文章或各博客主页被访问次数--------------- */
        //$this->_hotBlogArticle(array('domainname'=>$domainname,'appearTime'=>'','articleID'=>'','guestType'=>$this->user['userid']),$extract['bloginfo']['UserID']);
        /* ----------------------------------------------------- */


        $num = $this->_getFriend($extract['bloginfo']['UserID']);  //粉丝，我关注的，相互关注数
        $extract['friendsnumber'] = $num;

        $TransmitFlag = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        $extract['isFrends'] = $TransmitFlag;

        if ($styleid > 0) {
            $extract['blogconfig']['StyleID'] = $styleid;
        }

        //添加样式页面的预览
        $prevCnt = $this->input->post('PreviewContent');
        if ($prevCnt !== FALSE) {
            $extract['PreviewContent'] = $prevCnt;
        }
        $extract['layoutlist'] = $this->config->item('layout');
        $extract['sysmodules'] = $this->config->item('sysmodules');
        $extract['bglist'] = $this->config->item('bgurl');
        $extract['defaultcss'] = $this->config->item('defaultcss');
        $extract['default'] = $this->config->item('default');
        $Modules = array();
        $RModules = (trim($extract['blogconfig']['RModules']) != '') ? explode(',', $extract['blogconfig']['RModules']) : array();
        $MModules = (trim($extract['blogconfig']['MModules']) != '') ? explode(',', $extract['blogconfig']['MModules']) : array();
        $LModules = (trim($extract['blogconfig']['LModules']) != '') ? explode(',', $extract['blogconfig']['LModules']) : array();

        foreach ($RModules as $v) {
            if (isset($extract['sysmodules'][$v])) {
                $Modules['rmods'][] = $extract['sysmodules'][$v][3];
            }
        }
        foreach ($MModules as $v) {
            if (isset($extract['sysmodules'][$v])) {
                $Modules['mmods'][] = $extract['sysmodules'][$v][3];
            }
        }
        foreach ($LModules as $v) {
            if (isset($extract['sysmodules'][$v])) {
                $Modules['lmods'][] = $extract['sysmodules'][$v][3];
            }
        }



        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        // 	var_dump($stat1);




        $extract['module'] = $Modules;
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '_' . $extract['bloginfo']['NickName'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['modulepath'] = &config_item('module_path');
        $extract['isconfig'] = 0;
        $this->load->view('manage/devarticle.shtml', $extract);
    }

    function articleList($domain) {
        //	echo date("Y".'年'."n".'月'.j.'年');
        //	$subject = '<p style="padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 200%;"><img border="0" src="upfiles/2009/07/1246430143_4.jpg" alt=""/></p><p style="padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 200%;"><img border="0" src="upfiles/2009/07/1246430143_3.jpg" alt=""/></p><p style="padding: 0px; margin-top: 0px; margin-bottom: 0px; line-height: 200%;"><img border="0" src="upfiles/2009/07/1246430143_1.jpg" alt=""/></p>';
        //	$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]/";
        //	preg_match_all($pattern, $subject, $matches);	
        //	var_dump($matches);
        //	$m = preg_match($pattern, $subject);
        //	var_dump($m);

        $memberid = $this->input->get_post('mid');
        $num = intval($this->input->get_post('num'));
        $showmode = $this->input->get_post('mod');

        //获取标志是否有权限修改
        $isowner = false;
        if (false !== $this->_checkUserlogin()) {
            $bloginfo = $this->_getBlogInfoByDomain($domain);
            $this->_checkOwnBlog($bloginfo['MemberID'], $memberid);
            $isowner = $this->_checkOwnUser($bloginfo['UserID']);
        }

        $data['MemberID'] = $memberid;
        $data['StartNo'] = 0;
        $data['QryCount'] = empty($num) ? commonpagesize : $num;
        $this->load->model('blogarticle_socket');

        // 文章列表
        $data['MemberID'] = $memberid;
        $data['StartNo'] = -1;
        $this->load->model('blogarticle_socket');
        //var_dump($data);
        $tempInfo = $this->blogarticle_socket->getMemberArticleListIndex($data);
        //	var_dump($tempInfo);
        $totalcount = $tempInfo;
        //	var_dump($totalcount);
        // 文章列表
        //page pagesize
        $page = intval($this->input->get_post('page'));
        $page = ($page < 1) ? 1 : $page;
        $pagesize = 8;
        //page pagesize

        echo '$totalcount' . $totalcount . '$totalcount';
        if ($totalcount > 0) {
            page(&$page, &$pagesize, &$pagecount, &$totalcount, &$start);
            echo '$pagesize' . $pagesize . '<br/>';
            echo 'page' . $page . '<br/>';
            echo '$pagecount' . $pagecount . '<br/>';
            echo '$totalcount' . $totalcount . '<br/>';
            echo '$$start' . $start . '<br/>';
            $link = config_item('base_url') . '/index.php/devblog/article/' . $bloginfo['DomainName'] . '?&page=';
            $listcount = 5;
            $pagestr = paging($link, $page, $pagecount, $listcount);
            echo '@@@@@@@@@' . $pagestr . '@@@@@@@@@@@';

            $data['StartNo'] = $start;
            $artlist = $this->blogarticle_socket->getMemberArticleListIndex($data);
        }

        //	$artlist = $this->blogarticle_socket->getMemberArticleListIndex($data);

        $str = '';
        $default = &config_item('default');
        if (isset($artlist['RetRecords']) && ($artlist['RetRecords'] > 0)) {
            $artidlist = '';
            $urlprefix = config_item('base_url') . '/' . $domain;

            if ($artlist['RetRecords'] == 1) {
                $artlist['Record'] = array('0' => $artlist['Record']);
            }

            //	foreach($artlist['Record'] as $art)
            $str = "";
            $str .= "<div style='background:green'>";
            for ($i = 0; $i < 4; $i++) {



                $param['ArticleID'] = $artlist['Record'][$i]['ArticleID'];
                $param['AppearTime'] = $artlist['Record'][$i]['AppearTime'];
                $param['MemberID'] = $memberid;
                //		$bloginfo['AppearTime'] = 
                var_dump($bloginfo);
                $extract['viewurl'] = $this->_getviewURL($bloginfo, $extract['isowner'], $param['ArticleID']);


                //	var_dump($param);
                $content = $this->blogarticle_socket->getBlogArticleByID($param, $t = 'view');

                $articlestat = $this->blogarticle_socket->getBlogArticleStatByID($param);  //好评次数
                $TotleVote = isset($articlestat['TotleVote']) ? $articlestat['TotleVote'] : '0';

                //	$artlist['Record'][$i]
                $artlist['Record'][$i]['Title'] = filter_word($artlist['Record'][$i]['Title']);
                //$art['Title'] = $art['IsTop']==1 ? '<span style="color:red;">[顶]</span>'.utf8_str($art['Title'],32) : utf8_str($art['Title'],32);

                $artlist['Record'][$i]['Title'] = $artlist['Record'][$i]['IsTop'] == 1 ? '<span style="color:red;">[顶]</span>' . $artlist['Record'][$i]['Title'] : $artlist['Record'][$i]['Title'];

                if ($artlist['Record'][$i]['Recommend'] == 2 || $artlist['Record'][$i]['Recommend'] == 3 || $artlist['Record'][$i]['IsUsed'] == 1) {
                    $artlist['Record'][$i]['Title'] = '<img align="absmiddle" title="该文章已被采用到博客首页" border="0" alt="该文章已被采用到博客首页" src="http://img.cnfol.com/newblog/Version2/images/tui.png" /> ' . $artlist['Record'][$i]['Title'];
                }

                $artidlist .= ',' . $artlist['Record'][$i]['ArticleID'];
                $str .= $extract['viewurl'] . '<div class="article_main" style="border:1px solid yellow"><div id="Mod_IndexArticle" class="wzlbsz01"><a href="' . $urlprefix . '/article/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '.html">' . $artlist['Record'][$i]['Title'] . '</a></div><div class="wzlbsz02">[  ' . timeop($artlist['Record'][$i]['AppearTime']) . ' ]</div><div class="wzlbsz03">';

                if ($showmode == 1) {
                    $str .= $artlist['Record'][$i]['Summary'];
                }
                $str .= '是否推荐' . $artlist['Record'][$i]['SelfRecommend'] . '是否推荐';

                //	$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]/";
                //	preg_match_all($pattern, $content['Content'], $matches);
                //var_dump($matches);
                $str .= "pic" . $matches[1][1] . "pic";
                $str .= '好评：' . $TotleVote . '内容是22：<div class="strContent">' . utf8_str($content['Content'], 1000) . '</div><br/>';

                $str .='<span class="strContentHidden hidden" style="display:none">' . $content['Content'] . '</span>44<div class="all">33展开全部</div>';
                $flashCode = ($isowner == true) ? getVerifyStr($bloginfo['UserID'] . $artlist['Record'][$i]['ArticleID']) : '0';

                $str .= '</div><div class="wzlbsz04"><a href="' . $urlprefix . '/article/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '.html">阅读全文</a></div><div class="wzlbsz05"><a onclick="javascript:Show(\'Link' . $artlist['Record'][$i]['ArticleID'] . '\',\'trackback' . $artlist['Record'][$i]['ArticleID'] . '\',\'Manage' . $artlist['Record'][$i]['ArticleID'] . '\')" href="javascript:void(0)">分享</a> | <span id="atonclick_' . $artlist['Record'][$i]['ArticleID'] . '">0</span>次浏览 | <a onclick="javascript:Show(\'trackback' . $artlist['Record'][$i]['ArticleID'] . '\',\'Link' . $artlist['Record'][$i]['ArticleID'] . '\',\'Manage' . $artlist['Record'][$i]['ArticleID'] . '\');UpdateTrackbackPage(\'' . $artlist['Record'][$i]['ArticleID'] . '\',\'1\')" href="javascript:void(0)" co=\'' . $flashCode . '\' id=\'co' . $artlist['Record'][$i]['ArticleID'] . '\'>引用通告</a> | 类别：<a href="' . $urlprefix . '/sort/' . $artlist['Record'][$i]['SortID'] . '">' . (($artlist['Record'][$i]['SortName'] == '') ? $default['articlesort'][1] : $artlist['Record'][$i]['SortName']) . '</a>';

                if ($isowner == true) {
                    $str .= ' | <span onclick="javascript:submit_form(\'action_form\',\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\');" style="cursor: pointer;">编辑</span> | <span onclick="javascript:del(\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\');" style="cursor: pointer;">删除</span> | <a target="_blank" href="' . $urlprefix . '/manage/comment/CommentList/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '">评论(' . (isset($artlist['Record'][$i]['CommentNumber']) ? $artlist['Record'][$i]['CommentNumber'] : '0') . ')</a>';
                    if ($artlist['Record'][$i]['IsTop'] == 0)
                        $str .= ' | <a href="javascript:;" onclick="blogtoparticle(\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\',1);">置顶</a>';
                    else
                        $str .= ' | <a href="javascript:;" onclick="blogtoparticle(\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\',0);">取消置顶</a>';
                }
                $str .= '</div>' . "<input type='text' class='time_" . $i . "' value='" . substr(($artlist['Record'][$i]['AppearTime']), 0, -15) . "'>" . '<div style="display: none;padding-left:20px;" id="Link' . $artlist['Record'][$i]['ArticleID'] . '"><span id="copyLink' . $artlist['Record'][$i]['ArticleID'] . '">' . $urlprefix . '/article/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '.html</span>&nbsp;&nbsp;<br /><a style="cursor: pointer;" onclick="javascript:copy(\'copyLink' . $artlist['Record'][$i]['ArticleID'] . '\')" href="javascript:void(0)">复制链接</a><span style="color:#999">(请复制文章连接，您可以粘贴至QQ、MSN、EMAIL等发给您的好友！)</span></div>';

                $str .= '<div style="display: none;padding-left:20px;" id="trackback' . $artlist['Record'][$i]['ArticleID'] . '">本文引用地址:<br /><span id="copytrack' . $artlist['Record'][$i]['ArticleID'] . '">' . TrackbackUrl($artlist['Record'][$i]['ArticleID']) . '</span>&nbsp;&nbsp;<br /><a style="cursor: pointer;" onclick="javascript:copy(\'copytrack' . $artlist['Record'][$i]['ArticleID'] . '\')" href="javascript:void(0)">复制链接</a><br><div id="tbl' . $artlist['Record'][$i]['ArticleID'] . '">正在请求中...</div></div><div class="wzlbsz06"></div></div>';
            }
            $str .="</div><div id='contentTwo' style='background:#aaa;display:none'>";

            for ($i = 4; $i < 8; $i++) {

                //	$artlist['Record'][$i]

                $param['ArticleID'] = $artlist['Record'][$i]['ArticleID'];
                $param['AppearTime'] = $artlist['Record'][$i]['AppearTime'];
                $param['MemberID'] = $memberid;
                //	var_dump($param);
                $content = $this->blogarticle_socket->getBlogArticleByID($param, $t = 'view');
                $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";

                preg_match_all($pattern, $content, $matches);


                $artlist['Record'][$i]['Title'] = filter_word($artlist['Record'][$i]['Title']);
                //$art['Title'] = $art['IsTop']==1 ? '<span style="color:red;">[顶]</span>'.utf8_str($art['Title'],32) : utf8_str($art['Title'],32);

                $artlist['Record'][$i]['Title'] = $artlist['Record'][$i]['IsTop'] == 1 ? '<span style="color:red;">[顶]</span>' . $artlist['Record'][$i]['Title'] : $artlist['Record'][$i]['Title'];

                if ($artlist['Record'][$i]['Recommend'] == 2 || $artlist['Record'][$i]['Recommend'] == 3 || $artlist['Record'][$i]['IsUsed'] == 1) {
                    $artlist['Record'][$i]['Title'] = '<img align="absmiddle" title="该文章已被采用到博客首页" border="0" alt="该文章已被采用到博客首页" src="http://img.cnfol.com/newblog/Version2/images/tui.png" /> ' . $artlist['Record'][$i]['Title'];
                }

                $artidlist .= ',' . $artlist['Record'][$i]['ArticleID'];
                $str .= '<div id="Mod_IndexArticle" class="wzlbsz01"><a href="' . $urlprefix . '/article/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '.html">' . $artlist['Record'][$i]['Title'] . '</a></div><div class="wzlbsz02">[  ' . $artlist['Record'][$i]['AppearTime'] . ' ]</div><div class="wzlbsz03">';

                if ($showmode == 1) {
                    $str .= $artlist['Record'][$i]['Summary'];
                }

                $str .= '是否推荐' . $artlist['Record'][$i]['SelfRecommend'] . '是否推荐';

                $str .= '内容是：' . utf8_str($content['Content'], 30) . print_r($matches) . '<br/>';



                $flashCode = ($isowner == true) ? getVerifyStr($bloginfo['UserID'] . $artlist['Record'][$i]['ArticleID']) : '0';

                $str .= '</div><div class="wzlbsz04"><a href="' . $urlprefix . '/article/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '.html">阅读全文</a></div><div class="wzlbsz05"><a onclick="javascript:Show(\'Link' . $artlist['Record'][$i]['ArticleID'] . '\',\'trackback' . $artlist['Record'][$i]['ArticleID'] . '\',\'Manage' . $artlist['Record'][$i]['ArticleID'] . '\')" href="javascript:void(0)">分享</a> | <span id="atonclick_' . $artlist['Record'][$i]['ArticleID'] . '">0</span>次浏览 | <a onclick="javascript:Show(\'trackback' . $artlist['Record'][$i]['ArticleID'] . '\',\'Link' . $artlist['Record'][$i]['ArticleID'] . '\',\'Manage' . $artlist['Record'][$i]['ArticleID'] . '\');UpdateTrackbackPage(\'' . $artlist['Record'][$i]['ArticleID'] . '\',\'1\')" href="javascript:void(0)" co=\'' . $flashCode . '\' id=\'co' . $artlist['Record'][$i]['ArticleID'] . '\'>引用通告</a> | 类别：<a href="' . $urlprefix . '/sort/' . $artlist['Record'][$i]['SortID'] . '">' . (($artlist['Record'][$i]['SortName'] == '') ? $default['articlesort'][1] : $artlist['Record'][$i]['SortName']) . '</a>';

                if ($isowner == true) {
                    $str .= ' | <span onclick="javascript:submit_form(\'action_form\',\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\');" style="cursor: pointer;">编辑</span> | <span onclick="javascript:del(\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\');" style="cursor: pointer;">删除</span> | <a target="_blank" href="' . $urlprefix . '/manage/comment/CommentList/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '">评论(' . (isset($artlist['Record'][$i]['CommentNumber']) ? $artlist['Record'][$i]['CommentNumber'] : '0') . ')</a>';
                    if ($artlist['Record'][$i]['IsTop'] == 0)
                        $str .= ' | <a href="javascript:;" onclick="blogtoparticle(\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\',1);">置顶</a>';
                    else
                        $str .= ' | <a href="javascript:;" onclick="blogtoparticle(\'' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '\',0);">取消置顶</a>';
                }
                $str .= '</div><div style="display: none;padding-left:20px;" id="Link' . $artlist['Record'][$i]['ArticleID'] . '"><span id="copyLink' . $artlist['Record'][$i]['ArticleID'] . '">' . $urlprefix . '/article/' . strtotime($artlist['Record'][$i]['AppearTime']) . '-' . $artlist['Record'][$i]['ArticleID'] . '.html</span>&nbsp;&nbsp;<br /><a style="cursor: pointer;" onclick="javascript:copy(\'copyLink' . $artlist['Record'][$i]['ArticleID'] . '\')" href="javascript:void(0)">复制链接</a><span style="color:#999">(请复制文章连接，您可以粘贴至QQ、MSN、EMAIL等发给您的好友！)</span></div>';

                $str .= '<div style="display: none;padding-left:20px;" id="trackback' . $artlist['Record'][$i]['ArticleID'] . '">本文引用地址:<br /><span id="copytrack' . $artlist['Record'][$i]['ArticleID'] . '">' . TrackbackUrl($artlist['Record'][$i]['ArticleID']) . '</span>&nbsp;&nbsp;<br /><a style="cursor: pointer;" onclick="javascript:copy(\'copytrack' . $artlist['Record'][$i]['ArticleID'] . '\')" href="javascript:void(0)">复制链接</a><br><div id="tbl' . $artlist['Record'][$i]['ArticleID'] . '">正在请求中...</div></div><div class="wzlbsz06"></div>';
            }


            $str .='</div>';




            if ($artlist['RetRecords'] >= $data['QryCount']) {
                $str .= '<div id="refresh">更多文章</div><div id="more">加载更多</div>
	<div id="page" style="display:none">' . $pagestr . '</div>	';
            }
            $str .= '<div id="ajaxdiv"></div>
            <script language="javascript">
    		$(function(){	
				var winH = $(window).height();
    			$(window).scroll(function(){
    				var pageH = $(document.body).height();
    				var scrollT = $(window).scrollTop();
    				var aa = (pageH - winH - scrollT)/winH;
    				
	    			if(aa < 0.02) {
						$("#more").html("正在加载更多，请稍后");
			
						setTimeout(function(){
							$("#contentTwo").css("display", "block");
							$("#more").css("display", "none");
							$("#page").css("display", "block");
						},1000)		
    				}    				
    	
    			})

    		})	
  				
    				
                $(function(){
	            $("#ajaxdiv").load("' . config_item('base_url') . '/ajaxomcount/art/' . $artidlist . '?"+new Date().getTime());
                })

				/*
	             *	分割线
	             */			            		
				$(function(){
	            	for(var $i=0; $i<8; $i++)
	            	{	
	            		$firstTime = parseInt($(".time_"+$i).val());
	            		$j = $i+1;
	            		$secondTime = parseInt($(".time_"+$j).val());
		           		$cut = $firstTime - $secondTime;	            		
	    				if($firstTime - $secondTime ==1)
		            	{
	    					$(".time_"+$i).parent().css("border-bottom", "1px red solid");		            		
	    				}
    				}
    			})
	            		
	            $(".all").click(function(){
    				$(this).parent().find(".strContent").toggle();
     				$(this).parent().find(".strContentHidden").toggle();		
    			})		
            </script>';
        } else {
            exit('<div class="wzlbsz11"></div>');
        }
        echo $str;
    }

}

?>