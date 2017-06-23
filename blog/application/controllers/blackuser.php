<?php

/* * **********************
 * 功能：   博客黑名单管理
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Blackuser extends MY_Controller {

    function Blackuser() {
        parent::MY_Controller();
    }

    /**
     * @ 允许博客自己设定黑名单
     * 以阻止某个用户访问
     * @ 博客的黑名单用户列表
     * */
    function userlist($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //$data['MemberID']       = $extract['bloginfo']['MemberID'];
        //获取黑名单信息
        $this->load->model('blackuser_socket');
        $data['UserID'] = $this->user['userid'];
        $data['FType'] = 1;
        $data['StartNo'] = -1;
        $tempInfo = $this->blackuser_socket->getBlackUserList($data);
        $extract['tempCnt'] = $tempInfo['TtlRecords'];
        //echo($extract['tempCnt']);

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['StartNo'] = ($page - 1) * blackuserpagesize;

        if ($data['StartNo'] > $extract['tempCnt']) {
            $page = 1;
        }
        $extract['CurPage'] = $page;


        $data['QryCount'] = blackuserpagesize;
        $data['StartNo'] = ($page - 1) * blackuserpagesize;

        $data['FlagCode'] = $tempInfo['FlagCode'];
        $extract['blackuser'] = $this->blackuser_socket->getBlackUserList($data);
        //print_r($extract['blackuser']);
        //翻页函数
        $this->load->library('pagebarsnew');
        $baseLink = config_item('Base_url') . '/' . $extract['bloginfo']['DomainName'] . '/blackuser/list';

        $this->pagebarsnew->Page($extract['tempCnt'], $page, blackuserpagesize, $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['user'] = $this->user;

        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['blackuser'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['blackusertitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * 通过Ajax调用
     * return json
     * @增删黑名单用户的动作
     * */
    function Action() {
        $this->_checkLogin();
        $this->load->model('blackuser_socket');
        $act = $this->input->post('act');
        switch ($act) {
            case 'add':

                $param['UserID'] = $this->input->post('uid');
                $param['Type'] = $this->input->post('type');
                $param['FType'] = $this->input->post('ftype');
                $param['FriendData'] = $this->input->post('fuid');
                $rs = $this->blackuser_socket->addBlackUser($param);
                if ($rs['Code'] == '00') {
                    $data['errno'] = "success";
                    $data['error'] = "处理成功";
                } else {
                    $data['errno'] = "faile";
                    $data['error'] = "处理失败";
                }
                break;
            case 'del':
                $param['UserID'] = $this->input->post('uid');
                $param['Type'] = $this->input->post('Type');
                $param['FType'] = $this->input->post('fType');
                $param['FriendData'] = $this->input->post('FriendData');
                $rs = $this->blackuser_socket->delBlackUser($param);


                if ($rs['Code'] == '00') {
                    $data['errno'] = "success";
                    $data['error'] = "黑名单移除成功";
                } else {
                    $data['errno'] = "faile";
                    $data['error'] = "黑名单移除失败";
                }

                break;
            default:
                break;
        }
        echo json_encode($data);
        exit();
    }

}

//end class
?>