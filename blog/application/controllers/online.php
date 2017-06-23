<?php

/* * **********************
 * 功能：   博客直播
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Online extends MY_Controller {

    function Online() {
        parent::MY_Controller();
    }

    // @展示博客文章详细信息
    function DisPlay($domainname) {
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //博客组权限
        if ($extract['isowner']) {
            $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
            $blogaccess = $this->memberblog_socket->getAccessList($param);
            $this->_checkAccess($blogaccess, 'OnlineDisplay');
        }
        $subjectID = intval($this->input->get('subjectid'));
        //获取直播主题信息
        $data['OnlineID'] = $subjectID;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        $this->load->model('blogonline_socket');
        $Online = $this->blogonline_socket->getOnlineSubjectList($data);
        if (!isset($Online['Record']) || empty($Online['Record'])) {
            cnfolAlert('该直播主题信息已经删除');
            cnfolLocation(config_item('base_url') . '/' . $domainname . '/onlineList');
            exit(-1);
        }
        $extract['Online'] = $Online['Record'][0];
        $extract['OrderType'] = 1;
        //获取直播主题下的内容信息
        unset($data);
        $data['OnlineID'] = $extract['Online']['OnlineID'];
        $data['OrderType'] = $extract['OrderType'];
        $data['StartNo'] = 0;
        $data['QryCount'] = maxonlineblocklimit;

        $extract['subContent'] = $this->blogonline_socket->getOnlineSubjectBlock($data);

        //本次请求时间
        $extract['lastreqtime'] = date('Y-m-d H:i:s');

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $extract['modulepath'] = config_item('module_path');

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['Online']['Subject'] . '-' . $extract['bloginfo']['BlogName'] . '_' . $extract['bloginfo']['NickName'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 4;

        $this->load->view('online/online_display.shtml', $extract);
    }

    /**
     * @ 个人博客直播列表
     * */
    function onlinelist($domainname) {
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //博客组权限
        if ($extract['isowner']) {
            $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
            $blogaccess = $this->memberblog_socket->getAccessList($param);
            $this->_checkAccess($blogaccess, 'OnlineDisplay');
        }
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $this->load->model('blogonline_socket');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        unset($data);

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $tmpCnt = $this->blogonline_socket->getOnlineSubjectList($data);

        //取文章信息
        $data['StartNo'] = ($page - 1) * onlinesubjectpagesize;
        $data['QryCount'] = onlinesubjectpagesize;
        $extract['Subject'] = $this->blogonline_socket->getOnlineSubjectList($data);

        //翻页信息
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/onlineList';
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['modulepath'] = config_item('module_path');
        $extract['block'] = $blocks['onlinesublist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['onlinesublisttitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 4;

        $this->load->view('online/subject_list.shtml', $extract);
    }

    /**
     * @ 个人博客直播列表
     * */
    function onlinesubjectlist($domainname) {
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        $extract['isowner'] = 1;
        //博客组权限
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'OnlineDisplay');
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $this->load->model('blogonline_socket');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        unset($data);

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $tmpCnt = $this->blogonline_socket->getOnlineSubjectList($data);

        //取文章信息
        $data['StartNo'] = ($page - 1) * onlinesubjectpagesize;
        $data['QryCount'] = onlinesubjectpagesize;
        $extract['Subject'] = $this->blogonline_socket->getOnlineSubjectList($data);

        //翻页信息
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/onlineList';
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['onlinesublist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['onlinesublisttitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 4;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    //新增博客直播主题
    function createonline($domainname) {
        $this->_checkLogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        //博客组权限
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'OnlineDisplay');
        //可能是修改
        $subjectid = intval($this->input->get('subjectid'));
        if ($subjectid > 0) {
            $data['OnlineID'] = $subjectid;
            $data['MemberID'] = $extract['bloginfo']['MemberID'];
            $data['StartNo'] = 0;
            $data['QryCount'] = 1;
            $this->load->model('blogonline_socket');
            $extract['subject'] = $this->blogonline_socket->getOnlineSubjectList($data);
        }

        $extract['baseurl'] = &config_item('base_url');
        $this->load->view('online/addsubject.shtml', $extract);
    }

    //直播主题相关操作
    function action($domainname) {
        $this->_checkLogin();
        //获取博客信息
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($bloginfo['UserID']);
        //博客组权限
        $param['GroupIDs'] = trim($bloginfo['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'OnlineDisplay');

        $act = $this->input->post('act');
        switch ($act) {
            case 'addsubject':
                $data['Subject'] = trim($this->input->post('subject', TRUE));
                $errstr = '';
                if (strlen($data['Subject']) < 3 || strlen($data['Subject']) > 200) {
                    $errstr = "直播主题长度应该在3-200个字节之间";
                } else {
                    $data['OnlineID'] = intval($this->input->post('subjectid'));
                    $data['OnlineID'] = ($data['OnlineID'] > 0) ? $data['OnlineID'] : 0;
                    $data['MemberID'] = $bloginfo['MemberID'];
                    $data['Domainname'] = $domainname;
                    $data['UserID'] = $bloginfo['UserID'];
                    $data['IsCheck'] = 1;  //默认是审核通过的
                    $this->load->model('blogonline_socket');
                    $flag = $this->blogonline_socket->addOnlineSubject($data);
                    if ($flag == true) {
                        $errstr = ($data['OnlineID'] < 1) ? "直播主题添加成功" : '直播主题更新成功';
                    } else {
                        $errstr = ($data['OnlineID'] < 1) ? "直播主题添加失败" : '直播主题更新失败';
                    }
                }
                echo $errstr;
                $str = '<script>';
                if ($flag == true) {
                    $str .= 'setTimeout(function(){
                        window.parent.location.reload();
                    },2000);';
                } else {
                    $str .= 'setTimeout(function(){
                        window.parent.g_pop.close();
                    },2000);';
                }
                $str .= '</script>';
                echo $str;
                break;
            case 'delsub':
                $data['OnlineIDs'] = intval($this->input->post('subjectid'));
                if ($data['OnlineIDs'] < 1) {
                    echo json_encode(array('error' => '请选择要删除的主题信息', 'errno' => 'id'));
                }
                $data['MemberID'] = $bloginfo['MemberID'];
                $this->load->model('blogonline_socket');
                $flag = $this->blogonline_socket->delOnlineSubject($data);
                if ($flag == true) {
                    $error['error'] = "直播主题删除成功";
                    $error['errno'] = 'success';
                } else {
                    $error['error'] = "直播主题删除失败";
                    $error['errno'] = 'failed';
                }
                echo json_encode($error);
                break;
            default:
                break;
        }
    }

    //发表直播信息内容
    function blockaction($domainname) {
        $this->_checkLogin();
        //获取博客信息
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($bloginfo['UserID']);
        //博客组权限
        $param['GroupIDs'] = trim($bloginfo['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'OnlineDisplay');

        $act = $this->input->post('act');
        switch ($act) {
            case 'addblock':
                //增修
                $flashCode = $this->input->post('flashCode');
                $subjectID = $this->input->post('subjectid');
                $userID = $bloginfo['UserID'];
                $MemberID = $bloginfo['MemberID'];
                $msgContent = trim($this->input->post('message'));
                if ($flashCode !== getVerifyStr($MemberID . $subjectID . $userID)) {
                    echo json_encode(array('error' => '参数传递错误', 'errno' => 'param'));
                    exit;
                }

                if (strlen($msgContent) > 500 || strlen($msgContent) < 6) {
                    echo json_encode(array('error' => '直播内容长度在6-500字节之间', 'errno' => 'cnt'));
                    exit;
                }
                unset($param);
                $param['BlockID'] = intval($this->input->post('BlockID'));
                $param['BlockID'] = ($param['BlockID'] > 0) ? $param['BlockID'] : 0;
                $param['OnlineID'] = $subjectID;
                $param['Content'] = $msgContent;
                $param['IsCheck'] = 1; //默认审核通过
                $this->load->model('blogonline_socket');
                $flag = $this->blogonline_socket->addOnlineSubjectBlock($param);
                if ($flag) {
                    echo json_encode(array('error' => '发表成功', 'errno' => 'succ'));
                    exit;
                } else {
                    echo json_encode(array('error' => '提交失败', 'errno' => 'failed'));
                    exit;
                }
                break;
            default:
                break;
        }
    }

    //获取板块内容
    function ajaxgetonlineblock($domainname) {
        $OnlineID = intval($this->input->post('onlineid'));
        $MemberID = intval($this->input->post('memberid'));
        $OrderBy = intval($this->input->post('orderby'));
        $startTime = trim($this->input->post('reqtime'));

        if (!preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $startTime)) {
            $startTime = date('Y-m-d H:i:s');
        }
        $data['TolCnt'] = 0;
        $data['Record'] = array();

        $param['OnlineID'] = $OnlineID;
        $param['OrderType'] = $OrderBy;
        $param['StartTime'] = $startTime;
        $param['StartNo'] = 0;
        $param['QryCount'] = maxonlineblocklimit;
        $this->load->model('blogonline_socket');
        $subContent = $this->blogonline_socket->getOnlineSubjectBlock($param);
        $data['Record'] = $subContent;
        if (is_array($subContent) && !empty($subContent)) {
            $data['TolCnt'] = count($subContent);
        }
        echo json_encode($data);
    }

}

//end class
?>