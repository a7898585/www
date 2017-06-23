<?php

/* * **********************
 * 功能：   博客文章评论
 * author： jianglw
 * add：    2013-09-18
 * *********************** */

class Comment extends MY_Controller {

    function Comment() {
        parent::MY_Controller();
    }

    /*     * 文章评论列表
     * 可以与文章页面的评论公用 
     * @ 获取谋篇文章的评论列表
     * */

    function commentList($domainname) {
        $this->_checkUserlogin();

        $artstr = $this->input->get_post('artid');
        if (strpos($artstr, '-') === false) {
            $appearTime = '2011-01-01 00:00:00'; //兼容2011年以前文章 
            $articleID = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID = $temp[1];
        }
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //获取个人博客信息列表
        $extract['bloglist'] = $this->_getBlogListByUid();

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['ArticleID'] = $articleID;
        $data['AppearTime'] = $appearTime;
        $this->load->model('blogarticle_socket');
        $extract['article'] = $this->blogarticle_socket->getBlogArticleByID($data, 'view');
        unset($data);

        $data['BlogType'] = 1;
        $data['ArticleID'] = $articleID;
        $data['StartNo'] = -1;
        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtCommentList($data);
        $tmpCnt = $tempInfo['TtlRecords'];
        if ($tmpCnt > 0) {
            $page = 1;
            $pagesize = BLOG_COMMENT_PAGESIZE;
            $data['StartNo'] = ($page - 1) * $pagesize;
            $data['QryCount'] = $pagesize;
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $data = $this->articlecomment_socket->getArtCommentList($data);

            if (isset($data['RetRecords']) && $data['RetRecords'] > 0) {
                $data['Record'] = ($data['RetRecords'] == 1) ? array('0' => $data['Record']) : $data['Record'];
            }
        }
        $blocks = &$this->config->item('block');
        $extract['commonList'] = $data['Record'];

        $extract['flashCode'] = getVerifyStr($articleID . $this->user['userid']);
        $extract['articleid'] = $articleID;
        $extract['total'] = $tmpCnt;
        $extract['user'] = $this->user;
        $extract['loginuserid'] = $this->user['userid'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['pagesize'] = MYBLOG_MORE_PAGESIZE;
        $extract['pagecount'] = BLOG_COMMENT_PAGESIZE;
        $extract['title'] = $extract['bloginfo']['NickName'] . '-' . $blocks['articlecommentlist'] . '-' . $extract['bloginfo']['BlogName'];

        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];

        $this->load->view('article/article_commonlist.shtml', $extract);
    }

    /**
     * 加载更多的评论列表
     * 可以与文章页面的评论公用 
     * */
    function moreCommentList() {
        $articleid = intval($this->input->get_post('artid'));
        $page = intval($this->input->get_post('page'));
        if ($articleid <= 0) {
            $type = 2;
            $currentpage = $page;
            $str = '<div class="List_o_pl">&nbsp;&nbsp;暂无网友对此文章进行相关评论.！</div>';
        }
        $flashCode = $this->input->get_post('flashCode');
        $pagesize = $this->input->get_post('num');
        $pagesize = ($pagesize > 50 || $pagesize < 0) ? commonpagesize : $pagesize;

        $isowner = false;

        if ($this->_checkUserlogin()) {
            if ($flashCode == getVerifyStr($articleid . $this->user['userid'])) {
                $isowner = true;
            }
        }
        $data['BlogType'] = 1;
        $data['ArticleID'] = $articleid;
        $data['StartNo'] = -1;
        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtCommentList($data);
        $tmpCnt = $tempInfo['TtlRecords'];

        if ($tmpCnt < 1) {
            $type = 2;
            $currentpage = $page;
            $str = '<div class="List_o_pl">&nbsp;&nbsp;暂无网友对此文章进行相关评论！</div>';
        }

        $page = (is_int($page) && $page > 0) ? $page : 1;
        $pageCount = $this->input->get_post('count');
        $start = $pageCount ? ($page > 1 ? (($page - 2) * $pagesize + $pageCount) : ($page - 1) * $pagesize) : ($page - 1) * $pagesize;
//        error_log(PHP_EOL . $pageCount . '/----/' . print_r($start, true) . '----/' . $pagesize . date('H:i'), 3, '/var/tmp/wsh.log');
        $data['StartNo'] = $start;
        $data['QryCount'] = $pagesize;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $data = $this->articlecomment_socket->getArtCommentList($data);


        if (isset($data['RetRecords']) && $data['RetRecords'] > 0) {
            $str = '';
            $data['Record'] = ($data['RetRecords'] == 1) ? array('0' => $data['Record']) : $data['Record'];

            foreach ($data['Record'] as $val) {
                $val['UserID'] = ($val['NickName'] == '中金在线网友') ? 0 : $val['UserID'];
                $str .= '<div class="List_o_pl" id="Content' . $val['CommentID'] . '">';
                $str .= '<div class="Pl_o_title">';
                if ($isowner === true) {
                    $str .= '<span class="Fl F14 Pl10 MyPLtit" id="NickName' . $val['CommentID'] . '">' . $val['NickName'] . '</span>';
                    $str .= '<a onclick="if(confirm(\'确定将此评论删除?\'))DelSingleComment(' . $val['CommentID'] . ',' . $articleid . ',\'' . $flashCode . '\',' . $page . ');" class="Fr DelPL"  href="javascript:;"></a>';
                    $str .= '<time class="Fr Tr Hui_color">' . timeop($val['CommentAppearTime']) . '</time></div>';
                } else {
                    $str .= '<span class="Pl_o_name F14" id="NickName' . $val['CommentID'] . '">' . $val['NickName'] . '</span><time class="Pl_o_time Tr Hui_color">' . timeop($val['CommentAppearTime']) . '</time></div>';
                }
                //回复内容
                $str .= '<p class="Pl_o_content Pl10 Pr10 F14" id="ReContent' . $val['CommentID'] . '">' . filterEmoticon(filterURL(filter($val['Content']))) . '</p>';
                $str .= '</div>';
            }
            $type = 1;
            $currentpage = $page + 1;
        } else {
            $currentpage = $page;
            $type = 2;
            $str = '<div class="List_o_pl">&nbsp;&nbsp;暂无网友对此文章进行相关评论！</div>';
        }
        echo json_encode(array('data' => $str, 'error' => $type, 'page' => $currentpage));
    }

    /**
     * 通过Ajax获取信息
     * return json
     * @ 文章评论关联动作 增删改
     * */
    function Action() {
        $this->_checkIP('comment');
        $this->_checkUserlogin();
        $act = $this->input->get_post('act');
        switch ($act) {
            //主人回复 new 20110504  by lifeng
            case 'ownercomment':
                //判断时间限制
                if ((time() - checkPublic()) <= bublictimelimit) {
                    echo json_encode(array('error' => '两次回复时间间隔小于' . bublictimelimit . '秒，请不要灌水', 'errno' => 'limit'));
                    exit;
                }

                $Content = $this->input->post('Content', TRUE);

                if (strlen($Content) > 210 || strlen($Content) < 3) {
                    $data['errno'] = 'content';
                    $data['error'] = '回复内容长度应该在3-140个字节之内';
                    echo json_encode($data);
                    exit;
                }
                //非匿名的情况下获取正在发表评论的博客域名信息
                $domain = '';
                if (!empty($this->user)) {
                    $domain = $this->input->post('userdomain');
                    $userid = $this->user['userid'];
                    $nickname = $this->user['nickname'];
                } else {
                    $userid = $this->user['userid'];
                    $nickname = '主人回复';
                }

                unset($data);
                $this->load->model('articlecomment_socket');
                $data['BlogType'] = 1;
                $data['CommentID'] = 0;
                $data['ParentCommentID'] = $this->input->post('CommentID');
                $data['ArticleID'] = $this->input->post('ArticleID');
                $data['ArtMemberID'] = $this->input->post('MemberID');
                $data['ArticleAppearTime'] = $this->input->post('appeartime');
                $data['Subject'] = strip_tags($this->input->post('articleSubject'));
                $data['UserID'] = $userid;
                $data['NickName'] = $nickname;
                $data['Address'] = config_item('base_url') . '/' . $domain;
                $data['Content'] = strip_tags($Content);
                $data['IP'] = $this->input->ip_address();
                $data['Status'] = 0;

                $flag = $this->articlecomment_socket->aupdComment($data);

                if ($flag['Code'] == '00') {
                    $error['errno'] = 'succ';
                    $error['error'] = '回复发表成功';
                    setPublic(); //设置时间限制
                    //this
                    $articleBao = '1';
                    //this
                } else if ($flag['Code'] == '200036' || $flag['Code'] == '200037') {
                    $error['errno'] = $flag['Code'];
                    $error['error'] = '回复发表成功，等待审核中';
                    setPublic(); //设置时间限制
                    //this
                    $articleBao = '1';
                    //this
                } else {
                    $error['errno'] = 'buser';
                    $error['error'] = '禁止回复.';
                    setPublic(); //设置时间限制
                }


                //this
                if ($articleBao == 1) {
                    $this->delMyIndex();
                    systemBao($flag['Record']['CommentID'], 'articleCommentActual.php'); //保10洁过滤
                }
                //this

                echo json_encode($error);
                break;
            case 'addcomment':
                $this->load->model('user_socket');

                //用户中心广告组，不允许评论
                if ($this->user['userid'] > 0) {
                    $uc_groups = $this->user_socket->getUserGroup(array('UserID' => $this->user['userid']));
                    $uc_groups = explode(',', $uc_groups['GroupID']);
                    $uc_groups = (is_string($uc_groups)) ? array(0 => $uc_groups) : $uc_groups;
                    $offid = config_item('offcommentid');
                    if (in_array($offid, $uc_groups)) {
                        echo json_encode(array('error' => '禁止评论的用户组', 'errno' => 'buser'));
                        exit;
                    }
                    //黑名单用户禁止评论
                    $data['UserID'] = $this->input->get_post('userid');
                    $data['FType'] = 1;
                    $data['StartNo'] = 0;
                    $data['QryCount'] = -1;

                    $this->load->model('user_socket');
                    $data = $this->user_socket->getBlackList($data);

                    if (blackCheck($this->user['userid'], $data)) {
                        echo json_encode(array('error' => '博主已禁止你的评论', 'errno' => 'buser'));
                        exit;
                    }
                    //黑名单用户禁止评论
                }

                //判断时间限制
                if ((time() - checkPublic()) <= bublictimelimit) {
                    echo json_encode(array('error' => '两次评论时间间隔小于' . bublictimelimit . '秒，请不要灌水', 'errno' => 'limit'));
                    exit;
                }
                $Content = $this->input->post('content');
                $ContentWord = strip_tags($Content, '<img><p><br/><br>');

                //判断内容重复
                if (checkCommentSign($Content)) {
                    echo json_encode(array('error' => '不允许发表内容相同的评论，请重新编辑！', 'errno' => 'limit'));
                    exit;
                }

                if (strlen($ContentWord) > 3000 || strlen($ContentWord) < 3) {
                    $data['errno'] = 'content';
                    $data['error'] = '评论内容长度应该在1-3000个字节之内';
                    echo json_encode($data);
                    exit;
                }
                $flashCode = $this->input->get_post('flashCode');
                $allComent = $this->input->get_post('allowcomment');
                $MemberID = $this->input->get_post('memberid');
                $articleID = $this->input->get_post('articleId');
                $title = $this->input->get_post('articleSubject');
                if ($flashCode != getVerifyStr($allComent . $MemberID . $articleID . $title)) {
                    $data['errno'] = 'varifyError';
                    $data['error'] = '评论验证信息传递错误';
                    echo json_encode($data);
                    exit;
                }
                $anonymous = $this->input->get_post('anonymous');
                if ($allComent == 0) {
                    $data['errno'] = 'disallow';
                    $data['error'] = '该博客已经关评论功能';
                    echo json_encode($data);
                    exit;
                }
                if (($allComent == 2 && $anonymous == 1) || ($allComent == 2 && empty($this->user))) {
                    $data['errno'] = 'disallow';
                    $data['error'] = '不允许非登录用户与匿名用户评论，请先登录';
                    echo json_encode($data);
                    exit;
                }
                //非匿名的情况下获取正在发表评论的博客域名信息
                $domain = '';
                if (!empty($this->user) && ($anonymous != 1)) {
                    $domain = $this->input->post('userdomain');
                    $userid = $this->user['userid'];
                    $nickname = $this->user['nickname'];
                } else {
                    $userid = 0;
                    $nickname = '中金在线网友';
                }
                $this->load->model('articlecomment_socket');
                unset($data);
                $data['BlogType'] = 1;
                $data['CommentID'] = 0;
                $data['ArticleID'] = $articleID;
                $data['ArtMemberID'] = $MemberID;
                $data['Subject'] = htmlEncode($title);
                $data['UserID'] = $userid;
                $data['NickName'] = $nickname;
                $data['Content'] = htmlEncode($Content);

                $data['articleUserid'] = $this->input->post('userid');

                $data['IP'] = $this->input->ip_address();
                $data['Status'] = 0;
                $data['Address'] = config_item('base_url') . '/' . $domain;
                $data['ArticleAppearTime'] = $this->input->post('appeartime');

                //先审后发这里调整
                $isby = file_get_contents(DEFAULT_PATH.'newblog/expansion/CheckStatus/' . 'banperiods.txt');
                $isby = isset($isby) ? $isby : 0;
                if ($isby == 1) {
                    $data['Status'] = 1;
                }

                $flag = $this->articlecomment_socket->aupdComment($data);

                if ($flag['Code'] == '00') {
                    $error['errno'] = 'succ';
                    $error['error'] = '评论发表成功';
                    setPublic(); //设置时间限制
                    setCommentSign($Content);

                    if ($data['UserID'] > 0) {
                        //积分添加，匿名情况下不加积分
                        $args['UserID'] = $data['UserID'];
                        $args['RewardEName'] = 'addarticlecomment';
                        $args['Type'] = 1;
                        $this->user_socket->addUserPoint($args);
                        unset($args);
                    }

                    if ($data['Status'] == 1) {
                        $error['errno'] = 'check';
                        $error['error'] = '评论发表成功，您的评论需要审核后才会展示！';
                    }

                    //this
                    $articleBao = '1';
                    //this
                } else if ($flag['Code'] == '200036' || $flag['Code'] == '200037') {
                    $error['errno'] = $flag['Code'];
                    $error['error'] = '评论发表成功，等待审核中';
                    setPublic(); //设置时间限制
                    setCommentSign($Content);

                    if ($data['UserID'] > 0) {
                        //积分添加，匿名情况下不加积分
                        $args['UserID'] = $data['UserID'];
                        $args['RewardEName'] = 'addarticlecomment';
                        $args['Type'] = 1;
                        $this->user_socket->addUserPoint($args);
                        unset($args);
                    }

                    //this
                    $articleBao = '1';
                    //this
                } else {
                    $error['errno'] = 'buser';
                    $error['error'] = '您已被该博主设为禁止评论';
                    setPublic(); //设置时间限制
                }

                //this
                if ($articleBao == 1) {
                    $this->delMyIndex();
                    systemBao($flag['Record']['CommentID'], 'articleCommentActual.php'); //保10洁过滤
                }
                //this

                echo json_encode($error);
                break;
            case 'delcomment':
                $this->_checkLogin();
                $flashCode = $this->input->post('flashcode');
                $articleid = $this->input->post('artid');
                $commentid = $this->input->post('cid');
                $memberid = $this->input->post('memid');


                if ($flashCode != getVerifyStr($articleid . $this->user['userid'])) {
                    $error['error'] = "您没有删除评论的权限";
                    $error['errno'] = 'deny';
                    echo json_encode($error);
                    exit;
                }
                if (!preg_match('/([0-9]+[,]?){1,}/', $commentid)) {
                    $error['error'] = "参数信息传递错误";
                    $error['errno'] = 'param';
                    echo json_encode($error);
                    exit;
                }

                $data['BlogType'] = 1;
                $data['CommentIDs'] = trim($commentid, ',;');
                $i = count(explode(',', $data['CommentIDs']));
                for (; $i > 0; $i--) {
                    $a .= ',' . $articleid;
                    $m .= ',' . $memberid;
                }
                $data['ArticleIDs'] = trim($a, ',;');
                $data['ArtMemberIDs'] = trim($m, ',;');

                $data['articleUserid'] = $this->user['userid'];

                $this->load->model('articlecomment_socket');
                $flag = $this->articlecomment_socket->delComment($data);

                if ($flag == true) {
                    $this->delMyIndex();
                    $error['error'] = "评论删除操作成功";
                    $error['errno'] = 'succ';
                } else {
                    $error['error'] = "评论删除操作失败";
                    $error['errno'] = 'failed';
                }
                echo json_encode($error);
                exit;
                break;
            case 'delparentcomment':
                $this->_checkLogin();
                $flashCode = $this->input->post('flashcode');
                $articleid = $this->input->post('artid');
                $commentid = $this->input->post('cid');
                $memberid = $this->input->post('memid');
                $pcid = $this->input->post('pcid');

                if (!preg_match('/([0-9]+[,]?){1,}/', $commentid)) {
                    $error['error'] = "参数信息传递错误";
                    $error['errno'] = 'param';
                    echo json_encode($error);
                    exit;
                }
                $data['BlogType'] = 1;
                $data['ArticleIDs'] = $articleid;
                $data['CommentIDs'] = trim($commentid, ',;');
                $data['ArtMemberIDs'] = $memberid;
                $data['ParentCommentID'] = $pcid;
                $this->load->model('articlecomment_socket');
                $flag = $this->articlecomment_socket->delComment($data);
                if ($flag == true) {
                    $error['error'] = "评论删除操作成功";
                    $error['errno'] = 'succ';
                } else {
                    $error['error'] = "评论删除操作失败";
                    $error['errno'] = 'failed';
                }
                echo json_encode($error);
                exit;
                break;
            default:
                $error['error'] = "请选择操作类型";
                $error['errno'] = 'act';
                echo json_encode($error);
                exit;
        }
    }

}