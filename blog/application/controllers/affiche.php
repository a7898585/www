<?php

/* * **********************
 * 功能：   博客个人公告
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Affiche extends MY_Controller {

    function Affiche() {
        parent::MY_Controller();
    }

    /**
     * @编辑公告
     * @博客个人公告编辑
     * */
    function Edit($domainname) {

        $data['point'] = $this->_checkLogin('getPoint');
        //通过博客名获取博客信息	
        $data['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //验证权限
        $this->_checkUser($data['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($data['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'EditAffiche');

        //创建点击统计url
        $data['viewurl'] = $this->_getviewURL($data['bloginfo'], true);
        //获取个人博客列表
        $data['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $data['blogconfig'] = $this->_getBlogConfig($data['bloginfo']['MemberID']);

        //获取博客公告信息
        unset($param);
        $param['MemberID'] = $data['bloginfo']['MemberID'];
        $param['Status'] = '';
        $data['blogaffiche'] = $this->memberblog_socket->getBlogAffiche($param);
        //暂时预留
        if (empty($data['blogaffiche'])) {
            $data['blogaffiche']['Content'] = "";
            $data['blogaffiche']['AfficheID'] = -1;
        }
        $data['user'] = $this->user;
        $data['userid'] = $this->user['userid'];
        $data['isowner'] = $this->_checkOwnUser($data['bloginfo']['UserID']); //判断是否自己的

        $blocks = &$this->config->item('block');
        $data['block'] = $blocks['affichedit'];
        $data['title'] = $data['bloginfo']['BlogName'] . '-' . $blocks['affichedittitle'];
        $data['peronalhead'] = $blocks['devpersonalhead'];
        $data['peronalfoot'] = $blocks['personalfoot'];
        $data['baseurl'] = &config_item('base_url');
        $data['isconfig'] = 1;

        $data['ismanage'] = true; //如果是管理页面，不加载统计代码
        $this->load->view('manage/devmanage_index.shtml', $data);
    }

    /**
     * 通过Ajax提交
     * @提交编辑公告信息
     * */
    function Action($domainname) {
        $this->_checkLogin();

        $MemberID = $this->input->post('MemberID');
        $AfficheID = $this->input->post('AfficheID');
        $flashCode = $this->input->post('Verify');
        if ($flashCode != getVerifyStr($MemberID . $this->user['userid'] . $AfficheID)) {
            $data['errno'] = 'verify';
            $data['error'] = '本次提交信息非法，重新刷新页面往下操作';
            echo json_encode($data);
            exit;
        }


        if ($AfficheID == '' || $MemberID == '' || $AfficheID == '-1') {
            $data['errno'] = 'verify';
            $data['error'] = '提交数据缺失，重新刷新页面往下操作';
            echo json_encode($data);
            exit;
        }

        $Content = $this->input->post('Content');
        $Content = htmlEncode($Content);

        $param['AfficheID'] = $AfficheID;
        $param['MemberID'] = $MemberID;
        $param['Content'] = $Content;


        /* guoping1980用户禁止发表 */
        if ($this->user['username'] == 'guoping1980') {
            $ip_address = $this->input->ip_address();
            $sign = preg_match("/fdh1980guoping/i", $param['Content']);
            if (!preg_match("/^(14\.146)|^(218\.85)|^(219\.137)/i", $ip_address) || !$sign) {
                $data['errno'] = '110';    //禁止修改，直接退出
                $data['error'] = $param['Content'];
                error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_gonggao_edit_346713_' . date('Ymd') . '.log');
                echo json_encode($data);
                exit;
            }
            $param['Content'] = preg_replace("/fdh1980guoping/i", '', $param['Content']);
        }

        $param['Content'] = preg_replace("/position(\s)*:(\s)*absolute/i", 'position:', $param['Content']);
        $param['Content'] = preg_replace("/(&lt;)(.*)id(\s)*=/i", '$1$2', $param['Content']);
        $param['Content'] = preg_replace("/(&lt;)(.*)class(\s)*=/i", '$1$2', $param['Content']);

        /* jifan761018用户禁止发表 */
        if ($this->user['username'] == 'jifan761018') {
            $ip_address = $this->input->ip_address();
            if (!preg_match("/^(124\.)|^(110\.)|^(27\.)|^(120\.)|^(117\.)/i", $ip_address)) {
                $data['errno'] = '110';    //禁止发表，直接退出
                $data['error'] = '你已被监控...';
                error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_gonggao_edit_jifan761018_' . date('Ymd') . '.log');
                echo json_encode($data);
                exit;
            }
        }

        $this->load->model('memberblog_socket');
        if ($this->memberblog_socket->aupdBlogAffiche($param)) {
            $data['errno'] = 'success';
            $data['error'] = '用户公告修改成功';
        } else {
            $data['errno'] = 'failed';
            $data['error'] = '系统忙，请稍候再试';
        }

        /* guoping1980用户修改公告日志 */
        if ($this->user['username'] == 'guoping1980') {
            error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . ' | ' . $this->user["username"] . ' | ' . $data["errno"] . '\r\n', 3, '/home/www/html/logs/gonggao_edit_346713_' . date('Ymd') . '.log');
        }
        echo json_encode($data);
        exit;
    }

    //根据ajax请求获取博客公告信息
    function getAffcheforajax($memberid) {
        $this->load->model('memberblog_socket');
        $data['MemberID'] = intval($memberid);
        $blogaffche = $this->memberblog_socket->getBlogAffiche($data);
        $blogaffche['Content'] = isset($blogaffche['Content']) ? $blogaffche['Content'] : '';
//        echo $blogaffche['Content'];
        //if (!empty($blogaffche['Content']) && strstr($blogaffche['Content'], 'img') && strpos($blogaffche['Content'], 'href')===false) {
        if (!empty($blogaffche['Content'])) {
            //把公告内的大图片等比例缩小
            $blogaffche['Content'] = $this->memberblog_socket->getMemberAfficheByMid(array('memberid' => $memberid, 'Content' => $blogaffche['Content']));
        }
        $content = (filter($blogaffche['Content'], true) == "") ? "暂时还没有公告" : filter($blogaffche['Content'], true);
        echo $content;
    }

    function getImageAff($content, $conArray = '') {
        $pattern = "/(.*)<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>(.*)/";
        preg_match($pattern, $content, $matchs);
        if (strstr($matchs[2], 'emoticons')) {
            $takeArray = '<img border="0" src="' . $matchs[2] . '">' . $matchs[3];
        } else {
            $takeArray = '<img border="0" src="' . $matchs[2] . '" width="190" height="150">' . $matchs[3];
        }

        if (!empty($conArray)) {
            array_push($conArray, $takeArray);
        } else {
            $conArray = (array) $takeArray;
        }
        if (preg_match($pattern, $matchs[1])) {
            return $this->getImageAff($matchs[1], $conArray);
        }
//        ksort($conArray);
//        print_r($conArray);
        $content = '';
        foreach ($conArray as $value) {
            $content = $value . $content;
        }

        return $matchs[1] . $content;
    }

}

//end class
?>