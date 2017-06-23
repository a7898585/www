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
    function sendMessage($uid, $domain) {
        $this->_checkUserlogin();
        $uid = $uid ? $uid : $this->input->get_post('friendUserID');
        $friendNickname = $this->input->get_post('friendNickname');

        if (!$uid) {
            die('非法操作');
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
        //error_log(print_r($article,true), 3, '/home/httpd/logs/ainsertfailed.log');
        //exit;

        $data['MemberID'] = $loginmemberid;
        $data['Title'] = htmlEncode($article['Title']);
        $data['Summary'] = htmlEncode($article['Summary']);
        $data['TrackBack'] = $article['TrackBack'];
        $data['Property'] = '2'; //转载
        $data['ReadStatus'] = '0'; //转载
        $data['Prime'] = $article['Prime'];
        $data['SelfRecommend'] = $article['SelfRecommend'];
        $data['IsUTop'] = '0';
        $data['SysTagID'] = $article['SysTagID'];
        $data['SortID'] = '';
        $data['GiftPrice'] = $article['GiftPrice'];
        if ($data['GiftPrice'] > 0) {
            echo(json_encode(array('error' => '0')));
        }
        $data['Content'] = htmlEncode($article['Content']);

        $data['FocusArtMemberID'] = $memberid; // 被转发或被收藏文章博客ID
        $data['FocusArtAppearTime'] = $datas['AppearTime']; //被转发或被收藏文章发表时间
        $data['FocusArtArticleID'] = $articleid; //被转发或被收藏 文章ID


        $extract['return'] = $this->blogarticle_socket->articleTransshipment($data);
        $extract['widget_url'] = $this->widget_url;
        $extract['showid'] = $this->input->get_post('showid');
        //error_log(print_r($data,true).'|'.print_r($extract['return'],true), 3, '/home/httpd/logs/a23132.log');
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
        $data['GiftPrice'] = $article['GiftPrice'];
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


        $data['Content'] = htmlEncode(remove_invisible_code($data['Content']));

        $data['FocusArtMemberID'] = $memberid; // 被转发或被收藏文章博客ID
        $data['FocusArtAppearTime'] = $datas['AppearTime']; //被转发或被收藏文章发表时间
        $data['FocusArtArticleID'] = $articleid; //被转发或被收藏 文章ID
        //error_log(print_r($data,true), 3, '/home/httpd/logs/ainsertfailed.log');
        $extract['return'] = $this->blogarticle_socket->articlecollect($data);

        if ($extract['return'] == '1') {
            $mcache = &load_class('Memcache');
            $ckey = config_item('SC0001');
            $ckey = str_replace('{MemberID}', $memberid, $ckey);
            $ckey = str_replace('{AticleId}', $articleid, $ckey);
            $mcache->set($ckey,1);
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

        $data['memberid'] = $this->input->get_post('memberid'); //文章所属memberid

        if ($extract['IllegalArticles'] == '1') {
            $extract['Code'] = '01';
            $extract['error'] = '1';
        } else {
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
        }


        $extract['widget_url'] = $this->widget_url;


        $this->load->view('/module/articlereport.shtml', $extract);
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
        error_log(print_r($str, true) . "\r\n", 3, '/home/httpd/logs/a125.log');
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
            $ckey = str_replace('{MemberID}', $memberid, $ckey);
            $mcache->delete($ckey);
        }
    }

}

//end class