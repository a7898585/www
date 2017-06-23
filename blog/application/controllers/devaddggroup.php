<?php

/* * **********************
 * 功能：   博客添加标签
 * author：
 * add：    2013-5-14
 * modify  2013-5-14
 * *********************** */

class DevAddGgroup extends MY_Controller {

    function __construct() {
        parent::MY_Controller();
        $this->load->model("devfriend_socket");
    }

    function index() {
        echo '####';
    }

    function groupList($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //获取友情链接分类
        $this->load->model('bloglink_socket');
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['Status'] = 0;
        $data['StartNo'] = -1;

        $tmpInfo1 = $this->devfriend_socket->friendGroup($data);
        $tmpInfo = $this->bloglink_socket->getLinkSortList($data);

        $page = intval($this->input->get_post('page'));
        $page = ($page < 1) ? 1 : $page;
        $pagesize = 1;
        $totalcount = $tmpInfo['TtlRecords'];

        if ($totalcount > 0) {
            page(&$page, &$pagesize, &$pagecount, &$totalcount, &$start);
            $link = config_item('base_url') . '/index.php/devaddggroup/grouplist/' . $domainname . '?&page=';

            $listcount = FRIEND_GROUP_BUTTON_NUM;
            $pagestr = paging($link, $page, $pagecount, $listcount);

            $data['QryCount'] = FRIEND_GROUP_PAGESIZE;
            $data['StartNo'] = $start;
            $data['FlagCode'] = $tmpInfo['FlagCode'];
            $extract['groupList'] = $this->devfriend_socket->friendGroup($data);
        }


        $blocks = &config_item('block');
        $extract['block'] = $blocks['devaddGroup'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['flinkaddtitle'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['baseurl'] = &config_item('base_url');






        $extract['pagestr'] = $pagestr;
        $extract['domainName'] = $domainname;
        $extract['memberID'] = $data['MemberID'];
        $extract['totalcount'] = $totalcount;

        //  	$this->load->view("manage/config/manage_addGroup.shtml", $extract);  
        $this->load->view("manage/devmanage_index.shtml", $extract);
    }

    /*
     * 
     */

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

    function ajaxAdd($domainname) {
        $IN = parse_incoming();
        $this->_checkLogin();

        //通过博客名获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);

        $userid = $this->user['userid'];
        $param['UserID'] = $userid;
        $param['Content'] = $IN['content'];
        //	$rs = $this -> devfriend_socket ->addFriendTag($param);
        $rs = true;
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => '创建成功'));

            exit;
        } else {
            echo json_encode(array('erron' => '02', 'error' => '创建失败'));
            exit;
        }
    }

    function ajaxDel($domainname) {
        $IN = parse_incoming();
        $this->_checkLogin();

        //通过博客名获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);

        $userid = $this->user['userid'];
        $param['groupID'] = $IN['groupId'];
        $param['MemberID'] = $extract['bloginfo']['MemberID'];
        $rs = $this->devfriend_socket->delFriendGroup($param);
        $rs = true;
        if ($rs) {
            echo json_encode(array('erron' => '01', 'error' => '创建成功'));

            exit;
        } else {
            echo json_encode(array('erron' => '02', 'error' => '创建失败'));
            exit;
        }
    }

    /*
     * 修改分组
     */

    function ajaxModify($domainname) {
        $IN = parse_incoming();
        $this->_checkLogin();

        //通过博客名获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);

        $userid = $this->user['userid'];
        $param['groupID'] = $IN['groupId'];
        $param['MemberID'] = $extract['bloginfo']['MemberID'];
        $rs = $this->devfriend_socket->modifyGroup($param);
        $rs = true;
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

}

?>