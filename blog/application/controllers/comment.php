<?php

/* * **********************
 * 功能：   博客文章评论
 * author： leicc
 * add：    2010-11-16
 * *********************** */

class Comment extends MY_Controller {

    function Comment() {
        parent::MY_Controller();
    }

    /**
     * @ 个人博客管理
     * @ 文章评论回复管理
     * */
    function managecommentlist($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner']  =  $this->_checkOwnUser($extract['bloginfo']['UserID']);

        //验证权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'MangeComment');

        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取文章信息
        $this->load->model('blogarticle_socket');
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $artstr = $this->input->get_post('artid');

        if (strpos($artstr, '-') === false) {
            $data['AppearTime'] = '2011-01-01 00:00:00';
            $data['ArticleID'] = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $data['AppearTime'] = date("Y-m-d H:i:s", $temp[0]);
            $data['ArticleID'] = $temp[1];
        }
        $this->load->model('blogarticle_socket');
        $extract['article'] = $this->blogarticle_socket->getBlogArticleByID($data);
        
//        if (empty($extract['article'])) {
//            cnfolAlert('该文章信息已经删除，请查看其他的文章');
//            cnfolLocation(config_item('base_url') . '/' . $domainname);
//            exit(-1);
//        }

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;

        $data['BlogType'] = 1;
        $data['StartNo'] = -1;
        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtCommentList($data);
        $tmpCnt = $tempInfo['TtlRecords'];

        if ($page > ceil($tmpCnt / $extract['blogconfig']['CommentNumber'])) {
            $page = 1;
        }

        $data['StartNo'] = ($page - 1) * $extract['blogconfig']['CommentNumber'];
        $data['QryCount'] = $extract['blogconfig']['CommentNumber'];
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $extract['commentlist'] = $this->articlecomment_socket->getArtCommentList($data);

        //翻页信息
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        $baseLink .= '/manage/comment/CommentList/' . $temp[0].'-'.$data['ArticleID'];
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $data['QryCount'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['user'] = $this->user;
        $data['userid'] = $this->user['userid'];
        
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlecommentlist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articlecommentlisttitle'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * 可以与文章页面的评论的评论,主人回复
     * @ 获取谋篇文章的评论的主人回复列表
     * */
    function CommentCommentlist() {
        $aid = $this->input->post('ArticleID');
        $pcid = $this->input->post('CommentID');
        $flashCode = $this->input->post('flashCode');
        //$startno = $this->input->post('StartNo') > 0 ?$this->input->post('StartNo') : 0;	//开始位置
        //$showcount = 5;	//显示3条
        $isowner = false;

        if ($this->_checkUserlogin()) {
            if ($flashCode == getVerifyStr($aid . $this->user['userid'])) {
                $isowner = true;
            }
        }


        /**
          $data['StartNo']	= -1;
          $this->load->model('articlecomment_socket');
          $tempInfo 			= $this->articlecomment_socket->getArtCommentList($data);
          $tmpCnt 			= $tempInfo['TtlRecords'];

          if($page > ceil($tmpCnt / $extract['blogconfig']['CommentNumber']))
          {
          $page = 1;
          }

          $data['StartNo']   = ($page-1) *  $extract['blogconfig']['CommentNumber'];
          $data['QryCount']  = $extract['blogconfig']['CommentNumber'];
          $data['FlagCode']  = $tempInfo['FlagCode']
          $extract['commentlist'] = $this->articlecomment_socket->getArtCommentList($data);
         * */
        $data['StartNo'] = -1;
        $data['ParentCommentID'] = $pcid;
        $data['ArticleID'] = $aid;

        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtParentCommentList($data);
        $tmpCnt = $tempInfo['TtlRecords'];


        //if($page > ceil($tmpCnt / 3))
        //{
        //	$page = 1;
        //}
        //$data['StartNo']   = ($page-1) *  3;
        $data['StartNo'] = 0;
        $data['QryCount'] = $tmpCnt;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $result = $this->articlecomment_socket->getArtParentCommentList($data);

        if (isset($result['RetRecords']) && $result['RetRecords'] > 0) {
            $result['Record'] = ($result['RetRecords'] == 1) ? array('0' => $result['Record']) : $result['Record'];

            foreach ($result['Record'] as $row) {
                $content = filterURL(filter($row['Content']));
                if (strlen($content) > 100) {
                    $content = utf8_str($content, 100);
                }
                $str .= '<li id="li' . $row['CommentID'] . '">主人回复：' . $content . ' <span style="color:#999;font-style:italic">(' . $row['CommentAppearTime'] . ')</span>';
                if ($isowner === true) {
                    $str .=' <a href="javascript:;" onclick="if(confirm(\'确定将此回复删除?\'))DelSingleOwnerComment(' . $pcid . ',' . $row['CommentID'] . ',' . $aid . ',\'' . $flashCode . '\');return false;">删除</a>';
                }
                $str .='</li>';
            }
            echo json_encode(array('error' => $str, 'erron' => 'success'));
            exit;
        } else {
            echo json_encode(array('error' => '主人回复获取失败', 'errno' => 'empty'));
            exit;
        }
        /* $str = '';
          $i	 = 0;
          $j   = 0;
          if(isset($result['RetRecords']) && $result['RetRecords'] > 0){
          $result['Record'] = ($result['RetRecords'] == 1)? array('0'=>$result['Record']):$result['Record'];
          foreach($result['Record'] as $row){

          $content = filterURL(filter($row['Content']));
          if(strlen($content) > 100){
          $content = utf8_str($content,100);
          }
          $str .= '<li id="li'.$row['CommentID'].'">主人回复：'.$content.' <span style="color:#999;font-style:italic">('.$row['CommentAppearTime'].')</span>';
          if($isowner === true){
          $str .=' <a href="javascript:;" onclick="if(confirm(\'确定将此评论删除?\'))DelSingleOwnerComment('.$pcid.','.$row['CommentID'].','.$aid.',\''.$flashCode.'\');return false;">删除</a>';
          }
          $str .='</li>';

          }
          }else{
          $str .= '-999';
          } */
        echo $str;
    }

    /**
     * 可以与文章页面的评论公用
     * @ 获取谋篇文章的评论列表
     * */
    function CommentList() {
        $articleid = intval($this->input->get_post('artid'));

        if ($articleid <= 0) {
            echo '<div class="CommtBox">暂无网友对此文章进行相关评论.！</div>';
            exit;
        }

        
        $articleuserid = intval($this->input->get_post('articleuserid'));
        $page = intval($this->input->get_post('page'));
        $flashCode = $this->input->post('flashCode');
        $pagesize = $this->input->get_post('num');
        $pagesize = ($pagesize > 50 || $pagesize < 5) ? commonpagesize : $pagesize;
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
            echo '<div class="CommtBox">暂无网友对此文章进行相关评论！</div>';
            exit;
        }

        $page = (is_int($page) && $page > 0) ? $page : 1;
        $page = ($page <= ceil($tmpCnt / $pagesize)) ? $page : 1;

        $data['StartNo'] = ($page - 1) * $pagesize;
        $i = $tmpCnt - ($page - 1) * $pagesize;
        $j = ($page - 1) * $pagesize + 1;
        $data['QryCount'] = $pagesize;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $data = $this->articlecomment_socket->getArtCommentList($data);

        $str = '';
        if (isset($data['RetRecords']) && $data['RetRecords'] > 0) {
            $data['Record'] = ($data['RetRecords'] == 1) ? array('0' => $data['Record']) : $data['Record'];
            //翻页函数
            $this->load->library('pagebarsnew');
            $this->pagebarsnew->Page($tmpCnt, $page, $pagesize, '', '');
            $replaceid = $this->input->get_post('replaceid');
            $pagebars = $this->pagebarsnew->upDownListAjax($replaceid, 'updateCommentPage','1');

            foreach ($data['Record'] as $val) {
                $val['UserID'] = ($val['NickName'] == '中金在线网友') ? 0 : $val['UserID'];
                
                $commentUrl=config_item('base_url');
                if($val['UserID']!=0)
                {
                	$commentUrl=config_item('base_url').'/returnbolg/'.$val['UserID'].'.html';
                }
                
                $str .= '<div class="CommtBox" id="Content' . $val['CommentID'] . '">';
                $str .= '<div class="CommtInfo"><div class="HandleBox"><span id="Commenttime' . $val['CommentID'] . '"> ' . $val['CommentAppearTime'] . '</span>';
                
                if ($val['UserID'] != 0 && $this->user['userid'] != $val['UserID']&&$articleuserid==$this->user['userid']&&$this->user['userid']!='') {
                    $data['UserID'] = $this->user['userid'];
                    $data['FType'] = 1;
                    $data['StartNo'] = 0;
                    $data['QryCount'] = -1;

                    $this->load->model('user_socket');
                    $data = $this->user_socket->getBlackList($data);

                    $delstyle = 'display:none;';
                    $addstyle = '';
                    if (blackCheck($val['UserID'], $data)) {
                        $delstyle = '';
                        $addstyle = 'display:none;';
                    }
                    $str.=' | ';
                    $str.='<a href="javascript:;" class="delblack_' . $val['UserID'] . '" style="' . $delstyle . '" onclick="blacklist(' . $this->user['userid'] . ',0,' . $val['UserID'] . ',1,0);">解除黑名单</a>';
                    $str.='<a href="javascript:;" class="addblack_' . $val['UserID'] . '" style="' . $addstyle . '" onclick="blacklist(' . $this->user['userid'] . ',0,' . $val['UserID'] . ',1,1);" >黑名单</a>';
                }
                if ($isowner === true) {
                    $str .= '|<a onclick="if(confirm(\'确定将此评论删除?\'))DelSingleComment(' . $val['CommentID'] . ',' . $articleid . ',\'' . $flashCode . '\',' . $page . ');computeNum(\'ArticleCommentNum\',\'-\',1);" style="cursor: pointer;" href="javascript:;">删除</a>';
                }
                $str .= '</div><span style="padding:0 5px;font:bold 14px/25px \'\'; color:#999;">'.$j++.'楼</span>|<a style="padding:0 5px" href="'.$commentUrl.'" class="Name" id="NickName' . $val['CommentID'] . '" target="_blank" >' . $val['NickName'] . '</a></div>';
                //回复内容
                $str .= '<div class="Messages"><div class="Cont" id="ReContent' . $val['CommentID'] . '"  style="border:0px;" >' . filterEmoticon(filterURL(filter($val['Content']))) . '</div>';
                $str .= '<div class="ReplyBtn">';
                if ($isowner === true) {
                    $str .= ' <a href="javascript:;" onclick="Show(\'divOwnerComment' . $val['CommentID'] . '\');">回复</a> ';
                } else {
                    $str .= ' <a href="javascript:;" onclick="repQuote(' . $val['CommentID'] . ')">引用</a>';
                }
                $str .= '</div></div>';
                if ($isowner === true) {
                    $str .='<div class="ReplyBox" id="divOwnerComment' . $val['CommentID'] . '" style="display:none"><div class="Fl"><textarea id="txtOwnerComment' . $val['CommentID'] . '"  name="txtOwnerComment' . $val['CommentID'] . '" cols="" rows=""></textarea></div><a class="SubmitBtn" href="javascript:;" onclick="postOwnerComment(' . $val['CommentID'] . ')">提交</a><a class="CancelBtn" href="javascript:;" onclick="Show(\'divOwnerComment' . $val['CommentID'] . '\');">取消</a></div>';
                }

                if ($val['CommentCommentNum'] > 0) {
                    $str .='<div id="btnShowOwnerComment' . $val['CommentID'] . '"><a href="javascript:;" onclick="showOwnerComment(' . $articleid . ',' . $val['CommentID'] . ',0)">+点击查看主人回复(<span id="CommentCommentNum' . $val['CommentID'] . '">' . $val['CommentCommentNum'] . '</span>)</a></div><div id="CommentCommentList' . $val['CommentID'] . '" ></div>';
                }
                else
                {
                	$str .='<div id="btnShowOwnerComment'.$val['CommentID'].'" style="display:none"><a href="javascript:;" onclick="showOwnerComment('.$articleid.','.$val['CommentID'].',0)">+点击查看主人回复(<span id="CommentCommentNum'.$val['CommentID'].'">'.$val['CommentCommentNum'].'</span>)</a></div><div id="CommentCommentList'.$val['CommentID'].'" >&nbsp;</div>';
                }
                $str .= '</div>';
            }
            $str .= '<div class="CommtBox">'.$pagebars.'</div>';
        } else {
            $str = '<div class="CommtBox">暂无网友对此文章进行相关评论！</div>';
        }

        echo $str;
    }

    /**
     * 个人博客页面最新评论
     * */
    function NewestComment($domainname) {
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $data['ArticleUserID'] = $extract['bloginfo']['UserID'];
        $data['StartNo'] = -1;
        $data['QryCount'] = 0;
        $this->load->model('articlecomment_socket');

        $tmpCnt = $this->articlecomment_socket->getNewestCommentList($data);


        if ($tmpCnt > 0) {

            $data['StartNo'] = 0;
            $data['QryCount'] = 10;
            $commentList = $this->articlecomment_socket->getNewestCommentList($data);

            $newest = '';
            foreach ($commentList['Record'] as $value) {
                $dot = '';
                $value['Content']=strip_tags($value['Content']);
                $contents=$value['Content'];
                $value['Content']=preg_replace('/\&nbsp\;/i','',$value['Content']);
                
            	if(trim($value['Content'])==''||empty($value['Content']))
            	{
            		continue;
            	}
                if (strlen($value['Content']) > 23) {
                    $dot = ' ...';
                }
                $newest.='<li><a href="' . config_item('base_url') . '/' . $value['DomainName'] . '/article/' . strtotime($value['ArticleAppearTime']) . '-' . $value['ArticleID'] . '.html#commentList' . '" target="_blank" title="'.$contents.'">' . utf8_str(strip_tags($value['Content']), 23, 'false') . $dot . '</a></li>';
            }
            echo($newest);
        } else {
            echo('暂无评论...');
        }
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


                if (!$this->_checkValidate($this->input->get_post('loginuserid'))) {
                    $verifycode = $this->input->get_post('validate');
                    $this->load->library('SimpleCaptcha');

                    if (!$this->simplecaptcha->validate($verifycode, $this->user['userid'])) {
                        $data['errno'] = 'validate';
                        $data['error'] = '验证码信息错误';
                        echo json_encode($data);
                        exit;
                    }
                }

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
                $Content =$this->input->post('content');
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
                //$error['error'] = $data['Content'];
                //$error['errno']  = 'failed';
                //echo json_encode($error);
                //exit;


                $data['IP'] = $this->input->ip_address();
                $data['Status'] = 0;
                $data['Address'] = config_item('base_url') . '/' . $domain;
                $data['ArticleAppearTime'] = $this->input->post('appeartime');

                //先审后发这里调整
                $isby = file_get_contents(DEFAULT_PATH.'/newestblog/expansion/CheckStatus/' . 'banperiods.txt');
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

//end class
?>