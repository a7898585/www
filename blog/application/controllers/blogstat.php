<?php

/* * **********************
 * 功能：   博客统计
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Blogstat extends MY_Controller {

    function Blogstat() {
        parent::MY_Controller();
    }

    /**
     * @ 博客统计
     * */
    function view($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $data['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //验证权限
        $this->_checkUser($data['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($data['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param, $data['bloginfo']['MemberID']);
        $this->_checkAccess($blogaccess, 'BlogStat');

        //获取个人博客列表
        $data['bloglist'] = $this->_getBlogListByUid($this->user['userid']);
        //获取博客配置信息
        $data['blogconfig'] = $this->_getBlogConfig($data['bloginfo']['MemberID']);

        //获取60天日点击明细
        $params['MemberIDs'] = $data['bloginfo']['MemberID'];
        //$params['AccessBegin']      = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')-60,date('Y')));
        $params['AccessBegin'] = date('Ymd', mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')));

        $detailstat = $this->memberblog_socket->getMemberBlogStatDetail($params);

        $data['nowdate'] = date('Y-n-j');
        $data['result'] = ''; //没有任何统计记录
        if (!empty($detailstat)) {
            $daystat = array();
            while ($v = array_pop($detailstat)) {
                $daystat[$v['AccessDate']] = $v['AccessNums'];
            }
            unset($detailstat);

            $nowtime = time();
            for ($i = 0; $i < 60; $i++) {
                $key = date('Ymd', ($nowtime - 24 * 3600 * $i));
                $tmpstat = 0;
                if (isset($daystat[$key])) {
                    $tmpstat = $daystat[$key];
                    unset($daystat[$key]);
                }
                $daystat[$key] = $tmpstat;
            }

            $data['result'] = join(',', $daystat);
        }

        $data['viewurl'] = 'javascript:void(0)';
        //获取博客统计信息
        unset($params);
        $params['MemberIDs'] = $data['bloginfo']['MemberID'];
        $data['blogstat'] = $this->memberblog_socket->getMemberBlogStat($params);
        $data['user'] = $this->user;
        $data['userid'] = $this->user['userid'];
        $data['isowner'] = $this->_checkOwnUser($data['bloginfo']['UserID']); //判断是否自己的
        //获取统计信息
        $blocks = &$this->config->item('block');
        $data['block'] = $blocks['blogstat'];
        $data['title'] = $data['bloginfo']['BlogName'] . '-' . $blocks['blogstattitle'];
        $data['peronalhead'] = $blocks['devpersonalhead'];
        $data['peronalfoot'] = $blocks['personalfoot'];
        $data['baseurl'] = &config_item('base_url');
        $data['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $data);
    }

}

//end class
?>