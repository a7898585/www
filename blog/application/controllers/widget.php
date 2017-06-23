<?php

/* * **********************
 * 功能：   小窗口
 * author ：wuyq
 * add：    2010-01-22
 * modify   2010-04-20
 * *********************** */

class Widget extends MY_Controller {

    var $userID;
    var $accesskey;

    function Widget() {
        parent::MY_Controller();
        header("Content-Type: text/html; charset=utf-8");
        $this->widget_url = array(
            'jsurl' => 'http://images.cnfol.com/uploads/v5.0/passportweb/script/validator.js',
            'jqueryurl' => 'http://img.cnfol.com/core/js/jquery-1.4.4.min.js'
        );
    }

    /**
     *
     * 功能：发送私信
     *
     * */
    function sendMessage() {
        $this->_checkUserlogin();
        $uid = $this->input->get_post('frienduserid');
        $friendNickname = $this->input->get_post('friendnickname');

        if (!$uid) {
            die('非法操作' . $uid);
        }

        $data['widget_url'] = $this->widget_url;
        $data['friendUserID'] = $uid; //接收用户的ID，可多个，用英文的逗号隔开。例如:11234,45678,911123
        $data['fromUserID'] = $this->user['userid']; //发送用户的ID
        $data['friendNickname'] = $friendNickname; //接收私信用户的昵称

        $this->load->view('/module/sendmessage.shtml', $data);
    }

    /**
     *
     * 功能：获取私信数
     *
     * */
    function getMessage($uid) {
        $this->_checkUserlogin();
        $url = 'http://passport.cnfol.com/api/msginfo/getusermsgcount?uid=' . $uid . '&key=' . md5($uid . "hello_cnfol") . '&r=' . time() . '&callback=?';

        $result = curl_post($url);
        preg_match_all('/[0-9]+/', $result, $arr);
        echo json_encode($arr[0]);
    }

    /**
     *
     * 功能：登录
     *
     * */
    function login() {
        $this->_checkUserlogin();

        $this->load->view('/module/login.shtml', $data);
    }

    /**
     *
     * 功能：根据昵称获取博客域名
     *
     * */
    function getBlogDomainName() {
        //$this -> _checkUserlogin();
        $this->load->model('blogarticle_socket');
        $uid = $this->input->get_post('userid');
        $param['QryData'] = $uid;

        $rs = $this->blogarticle_socket->getBlogDomainName($param);

        if (!$rs) {
            $this->_jumpUrl(config_item('base_url'));
        } else if ($rs['RetRecords'] == 1) {
            $this->_jumpUrl(config_item('base_url') . '/' . $rs['Record']['DomainName']);
        } else {
            foreach ($rs['Record'] as $value) {
                if ($value['Status'] == 0) {
                    $this->_jumpUrl(config_item('base_url') . '/' . $value['DomainName']);
                }
            }

            $this->_jumpUrl(config_item('base_url') . '/' . $rs['Record'][0]['DomainName']);
        }
    }

    function _jumpUrl($url) {
        echo('<script>');
        echo('window.location.href="' . $url . '";');
        echo('</script>');
        exit;
    }

    /**
     *
     * 功能：文章转载
     *
     * */
    function articleTransshipment() {
        $this->_checkUserlogin();
        $loginmemberid = $this->input->get_post('loginmemberid'); //登陆用户博客memberid
        $memberid = $this->input->get_post('memberid'); //当前文章博主memberid
        $articletime = $this->input->get_post('articletime'); //当前文章页文章发表时间
        $articleid = $this->input->get_post('articleid'); //当前文章页文章id
        $this->load->model('blogarticle_socket');
        $datas['MemberID'] = $memberid;
        $datas['ArticleID'] = $articleid;
        $datas['AppearTime'] = date("Y-m-d H:i:s", $articletime);
        $article = $this->blogarticle_socket->getBlogArticleByID($datas, 'view');
        //error_log(print_r($article,true), 3, '/home/www/html/logs/a123213insertfailed.log');
        //exit;
        //标签的处理
        $tagStr = '转载';

        if (!empty($tagStr)) {
            $tagStr = $this->_taghandle($tagStr);
        }
        $data['TagIDs'] = $tagStr;
        //标签的处理
        $data['MemberID'] = $loginmemberid;
        $data['Title'] = htmlEncode($article['Title']);
        $data['Summary'] = htmlEncode($article['Summary']);
        $data['TrackBack'] = $article['TrackBack'];
        $data['Property'] = '2'; //转载
        $data['ReadStatus'] = '0'; //转载
        $data['Prime'] = $article['Prime'];

        $data['SelfRecommend'] = 0;
        $data['IsUTop'] = '0';
        $data['SysTagID'] = $article['SysTagID'];
        $data['SortID'] = '';
        $data['Status'] = 1; //默认博文不显示在频道页和首页  (1为不显示，0为显示)
        $data['GiftPrice'] = $article['GiftPrice'];
        $data['IP'] = $this->input->ip_address();
        
        if ($data['GiftPrice'] > 0) {
            echo(json_encode(array('error' => '0')));
        }

        $data['PictureUrl'] = articlePicture(array('Content' => $article['Content']));

        if ($data['PictureUrl']) {
            $data['PictureUrl'] = htmlEncode($data['PictureUrl']);
            $data['IsMultimedia'] = 1;
        } else {
            $data['PictureUrl'] = 0;
        }

        preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $data['Content'], $matchesEmbed);
        $data['Multimedia'] = $matchesEmbed['1'];

        if ($data['Multimedia']) {
            if ($data['IsMultimedia'] == '1') {
                $data['IsMultimedia'] = 3;
            } else {
                $data['IsMultimedia'] = 2;
            }
        }

        $article['Content'] = '原文地址：<a href="/' . $article['Domainname'] . '/article/' . $articletime . '-' . $articleid . '.html" target="_blank" style="color:#FF791C;cursor:hand;">' . $article['Title'] . '</a><br>' . $article['Content'];
        $data['Content'] = htmlEncode($article['Content']);

        $data['FocusArtMemberID'] = $memberid; // 被转发或被收藏文章博客ID
        $data['FocusArtAppearTime'] = $datas['AppearTime']; //被转发或被收藏文章发表时间
        $data['FocusArtArticleID'] = $articleid; //被转发或被收藏 文章ID

        $extract['return'] = $this->blogarticle_socket->articleTransshipment($data);
        $extract['widget_url'] = $this->widget_url;
        $extract['showid'] = $this->input->get_post('showid');

        if ($extract['return'] == '1') {
            $this->delMyIndexCache($memberid, $loginmemberid);
        }


        if ($this->input->get_post('type') == 'json') {
            echo(json_encode(array('error' => $extract['return'])));
        } else {

            $this->load->view('/module/articletransshipment.shtml', $extract);
        }
    }

    /**
     *
     * 功能：文章收藏
     *
     * */
    function articleCollect() {

        $this->_checkUserlogin();

        $loginmemberid = $this->input->get_post('loginmemberid'); //登陆用户博客memberid
        $memberid = $this->input->get_post('memberid'); //当前文章博主memberid
        $articletime = $this->input->get_post('articletime'); //当前文章页文章发表时间
        $articleid = $this->input->get_post('articleid'); //当前文章页文章id

        $this->load->model('blogarticle_socket');

        $datas['MemberID'] = $memberid;
        $datas['ArticleID'] = $articleid;
        $datas['AppearTime'] = date("Y-m-d H:i:s", $articletime);
        $article = $this->blogarticle_socket->getBlogArticleByID($datas, 'view');

        $data['MemberID'] = $loginmemberid;
        $data['Title'] = $article['Title'];
        $data['Summary'] = $article['Summary'];
        $data['TrackBack'] = $article['TrackBack'];
        $data['Property'] = '4'; //收藏
        $data['ReadStatus'] = '3'; //收藏
        $data['Prime'] = $article['Prime'];
        $data['SelfRecommend'] = $article['SelfRecommend'];
        $data['IsUTop'] = '0';
        $data['SysTagID'] = $article['SysTagID'];
        $data['SortID'] = '18296'; //收藏
        $data['Status'] = 1; //默认博文不显示在频道页和首页  (1为不显示，0为显示)
        $data['GiftPrice'] = $article['GiftPrice'];
        $data['IP'] = $this->input->ip_address();
        
        if ($data['GiftPrice'] > 0) {
            echo(json_encode(array('error' => '0')));
        }
        $data['Content'] = $article['Content'];

        preg_match('/<img\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $data['Content'], $matchesPic);
        $data['PictureUrl'] = $matchesPic['0'];


        if ($data['PictureUrl']) {
            $data['PictureUrl'] = htmlEncode($data['PictureUrl']);
            $data['IsMultimedia'] = 1;
        } else {
            $data['PictureUrl'] = 0;
        }

        preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $data['Content'], $matchesEmbed);
        $data['Multimedia'] = $matchesEmbed['1'];

        if ($data['Multimedia']) {

            if ($data['IsMultimedia'] == '1') {
                $data['IsMultimedia'] = 3;
            } else {
                $data['IsMultimedia'] = 2;
            }
        }

        $data['Content'] = '原文地址：<a href="/' . $article['Domainname'] . '/article/' . $articletime . '-' . $articleid . '.html" target="_blank" style="color:#FF791C;cursor:hand;">' . $article['Title'] . '</a><br>' . $data['Content'];
        $data['Content'] = '原文发布时间：<span style="color:#FF791C;">' . $datas['AppearTime'] . '</span><br>' . $data['Content'];

        $data['Content'] = htmlEncode(remove_invisible_code($data['Content']));

        $data['FocusArtMemberID'] = $memberid; // 被转发或被收藏文章博客ID
        $data['FocusArtAppearTime'] = $datas['AppearTime']; //被转发或被收藏文章发表时间
        $data['FocusArtArticleID'] = $articleid; //被转发或被收藏 文章ID

        $extract['return'] = $this->blogarticle_socket->articlecollect($data);

        if ($extract['return'] == '1') {
            $this->delMyIndexCache($memberid, $loginmemberid, '1');
        }
        $extract['widget_url'] = $this->widget_url;
        $extract['showid'] = $this->input->get_post('showid');

        if ($this->input->get_post('type') == 'json') {
            echo(json_encode(array('error' => $extract['return'])));
        } else {
            $this->load->view('/module/articlecollect.shtml', $extract);
        }
    }

    /**
     *
     * 功能：文章举报
     *
     * */
    function articleReport() {

        //$this -> _checkUserlogin();
        $data['reportuserid'] = $this->input->get_post('reportuserid'); //举报人id
        if (!$data['reportuserid']) {
            $extract['Code'] = '01';
            $extract['error'] = '3';
            $extract['widget_url'] = $this->widget_url;
            $this->load->view('/module/articlereport.shtml', $extract);
            return;
        }
        $data['articleid'] = $this->input->get_post('articleid'); //当前文章页文章id

        $this->load->model('blogarticle_socket');
        //$extract= $this->blogarticle_socket->articlereport($data);//获取文章是否被举报过
        //error_log(print_r($param,true).'|'.print_r($extract,true), 3, '/home/www/html/logs/a111111111.log');

        $data['memberid'] = $this->input->get_post('memberid'); //文章所属memberid
        //if ($extract['IllegalArticles'] == '1') {
        //$extract['Code'] = '01';
        //$extract['error'] = '1';
        //} else {
        $param['MemberID'] = $data['memberid'];
        $param['ArticleID'] = $data['articleid'];
        $param['IllegalArticles'] = 1;

        $rs = $this->blogarticle_socket->addArticlereport($param);

        if ($rs) {
            $extract['Code'] = '00';
            $extract['error'] = '1';
        } else {
            $extract['Code'] = '01';
            $extract['error'] = '2';
        }
        //}


        $extract['widget_url'] = $this->widget_url;


        if ($this->input->get_post('type') == 'json') {
            echo(json_encode(array('error' => $extract['error'])));
        } else {

            $this->load->view('/module/articlereport.shtml', $extract);
        }
    }

    /**
     *
     * 功能：加入黑名单
     *
     * */
    function addBlackList() {

        $this->_checkUserlogin();

        $data['UserID'] = $this->input->get_post('userid');
        $data['Type'] = $this->input->get_post('type');

        $data['FriendData'] = $this->input->get_post('friendid');
        $data['FType'] = $this->input->get_post('ftype');

        $this->load->model('blackuser_socket');
        if ($this->input->get_post('sign') == '1') {
            $extract = $this->blackuser_socket->addBlackUser($data);
        } else {
            $extract = $this->blackuser_socket->delBlackUser($data);
        }

        $extract['sign'] = $this->input->get_post('sign');
        $extract['widget_url'] = $this->widget_url;
        $extract['friendid'] = $data['FriendData'];

        if ($extract) {
            echo(json_encode(array('error' => $extract['Code'])));
        } else {
            echo(json_encode(array('error' => '1')));
        }


        //$this->load->view('/module/articlereport.shtml', $extract);
    }

    /**
     *
     * 批量获取文章顶，置顶等数据
     *
     * */
    function getArticleList() {
        $dingParam = $this->input->get('dingParam');

        if (empty($dingParam)) {
            return;
        }
        $dingArray = explode(',', $dingParam);
        $str = '<script language="javascript">';

        foreach ($dingArray as $value) {
            list($ArticleID, $MemberID, $AppearTime) = split('-', $value);

            if (empty($ArticleID) || empty($MemberID) || empty($AppearTime)) {
                continue;
            }
            $AppearTime = date("Y-m-d H:i:s", $AppearTime);

            $this->load->model('blogarticle_socket');

            $param['ArticleID'] = $ArticleID;
            $param['MemberID'] = $MemberID;
            $param['AppearTime'] = $AppearTime;
            //获取文章统计
            $extract = $this->blogarticle_socket->getBlogArticleStatByID($param);

            $str.='$("#ding_' . $param['ArticleID'] . '").html("(' . $extract["TotleVote"] . ')");';
            unset($extract);
        }

        $str.='</script>';
        //error_log(print_r($str, true) . "\r\n", 3, DEFAULT_PATH.'/logs/a125.log');
        echo($str);
    }

    //删除我的博客首页1-15条缓存
    function delMyIndexCache($memberid = 0, $loginmemberid = 0, $collect = 0) {

        $mcache = &load_class('Memcache');
        $mcache->addServer();

        //$indexckeynum = config_item('K1076');
        //$indexckeynum = str_replace('{MemberID}', $this->user['userid'], $indexckeynum);
        //$mcache->delete($indexckeynum);

        $indexckey = config_item('K1077');
        $indexckey = str_replace('{MemberID}', $this->user['userid'], $indexckey);
        $mcache->delete($indexckey);

        if ($memberid != 0 && $loginmemberid != 0) {
            $loginindexckey = str_replace('{MemberID}', $loginmemberid, config_item('K1015'));
            $mcache->delete($loginindexckey);
            $currentindexckey = str_replace('{MemberID}', $memberid, config_item('K1015'));
            $mcache->delete($currentindexckey);
        }

        if ($collect == '1') {
            $ckey = config_item('K1042');
            $ckey = str_replace('{MemberID}', $loginmemberid, $ckey);
            $mcache->delete($ckey);
            //error_log(print_r($ckey,true), 3, '/home/www/html/logs/a11.log');
        }
    }

//标签的处理
    function _taghandle($tagStr) {
        //error_log(date('YmdHis') . '||' . $tagStr . '||', 3, '/home/www/html/logs/tagstr.log');
        $tagStr = htmlspecialchars(strip_tags(trim($tagStr)));
        //error_log($tagStr . PHP_EOL, 3, '/home/www/html/logs/tagstr.log');

        $tags = explode(',', $tagStr);
        $tags = is_string($tags) ? array(0 => $tags) : $tags;
        $tag = array_unique($tags);
        $tagorder = array();
        if (count($tags) > eacharticletaglimit) {
            $data['errno'] = 'tagerr';
            $data['error'] = '每篇文章最多允许有5个标签';
            echo json_encode($data);
            exit;
        }
        //foreach($tags as $key=>&$tag)
        foreach ($tags as $key => $tag) {
            if (strlen($tag) > eachtaglengthlimit) {
                $data['errno'] = 'tagerr';
                $data['error'] = '单个标签长度应该在' . (eachtaglengthlimit / 3) . '个字以内';
                echo json_encode($data);
                exit;
            } else if (strlen(trim($tag)) == 0) {
                unset($tags[$key]);
            }
            //$tag = htmlEncode($tag);
            $tagorder[] = 0;
        }
        $this->load->model('articletags_socket');
        $param['UserIDs'] = $this->user['userid'];
        $param['StartNo'] = -1;

        //$tempInfo = $this->articletags_socket->getArticleTagList($param);
        //if(articletagcntlimit < $tempInfo['TtlRecords'])

        $TtlRecords = $this->articletags_socket->getArticleTagList($param);
        if (articletagcntlimit < $TtlRecords) {
            $data['errno'] = 'tagerr';
            $data['error'] = '您使用的标签数目已经超出了' . articletagcntlimit . '个的限制，请删除无效标签！';
            echo json_encode($data);
            exit;
        }
        unset($param);
        $param['UserID'] = $this->user['userid'];
        $param['OrderNos'] = join(',', $tagorder);
        $param['TagNames'] = join(',', $tags);

        $tagList = $this->articletags_socket->addArticleTag($param);

        //error_log(print_r($tagList,true).'|'.print_r($param,true), 3, '/home/www/html/logs/a111111111111111111111.log');

        if ($tagList == false) {
            $data['errno'] = 'savetag';
            $data['error'] = '标签保存失败';
            echo json_encode($data);
            exit;
        }

        $tagList = (isset($tagList['TagID'])) ? array(0 => $tagList) : $tagList;
        foreach ($tagList as $val) {
            $tagIDs[] = $val['TagID'];
        }
        $tagStr = join(',', $tagIDs);
        return $tagStr;
    }

    function saveSeoWords() {

        $data['MemberID'] = trim($this->input->get_post('memberid'));
        $data['ArticleID'] = trim($this->input->get_post('articleid'));
        $appearTime = trim($this->input->get_post('appeartime'));
        if ($data['MemberID'] == '' || $data['ArticleID'] == '' || $appearTime == '') {
            echo('参数缺失');
            exit;
        }



        $data['AppearTime'] = date("Y-m-d H:i:s", $appearTime);

        $this->load->model('blogarticle_socket');
        $extract = $this->blogarticle_socket->getBlogArticleByID($data, 'getSeo');

        $url = config_item('base_url');
        $url.='/f/utf8.php';
        $result = curl_post($url, array('content' => $extract['Content']));
        $result = iconv('GB2312', 'UTF-8', $result);
        $seo = $this->saveSeo($data['ArticleID'], $result);
        //error_log(print_r($extract['Content'],true).'|'.print_r($result,true).'|', 3, '/home/www/html/logs/a111111111.log');

        systemBao($data['ArticleID'], 'articleactual.php'); //保10洁过滤

        echo($seo);
    }

}

//end class