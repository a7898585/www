<?php

/* * **********************
 * 功能：   个人博客设置
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Config extends MY_Controller {

    function Config() {
        parent::MY_Controller();
    }

    /**
     * @ 个人博客配置的修改
     * */
    function Edit($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息
        $data['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $data['isowner'] = $this->_checkOwnUser($data['bloginfo']['UserID']);
        //验证权限
        $this->_checkUser($data['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($data['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'ConfigShow');

        //创建点击统计url
        $data['viewurl'] = $this->_getviewURL($data['bloginfo'], true);

        //获取个人博客列表
        $data['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $data['blogconfig'] = $this->_getBlogConfig($data['bloginfo']['MemberID']);

        $data['user'] = $this->user;

        $blocks = &$this->config->item('block');
        $data['block'] = $blocks['configedit'];
        $data['title'] = $data['bloginfo']['BlogName'] . '-' . $blocks['configedittitle'];
        $data['peronalhead'] = $blocks['devpersonalhead'];
        $data['peronalfoot'] = $blocks['personalfoot'];
        $data['baseurl'] = &config_item('base_url');
        $data['isconfig'] = 1;
        $this->load->view('manage/devmanage_index.shtml', $data);
    }

    /**
     * @ 公共头编辑个人博客标题、副标题
     * @ author lifeng
     * @ date 20110503
     * */
    function HeadEdit($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息
        $blog['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $BlogName = trim(strip_tags($this->input->post('BlogName')));
        $SubTitle = trim(strip_tags($this->input->post('Subtitle')));
        $SetType = $this->input->post('SetType');
        $data = array();

        if ($BlogName) {
            if (mb_strlen($BlogName, 'UTF-8') > 30 || mb_strlen($BlogName, 'UTF-8') <= 1) {
                $data['error'] = "博客名信息长度应该在1-30个字符之间！";
                $data['errno'] = "blogname";
            } else if ($BlogName == $blog['bloginfo']['BlogName']) {
                $data['error'] = "您没有修改博客名！";
                $data['errno'] = "blogname";
            }
        }
        if ($SubTitle) {
            if (mb_strlen($SubTitle, 'UTF-8') > 200) {
                $data['error'] = "博客描述信息长度应该在200个字符以内！";
                $data['errno'] = "subtitle";
            }
        }

        if (!empty($data)) {
            echo json_encode($data);
            exit(-1);
        }
        unset($data);

        $this->load->model('memberblog_socket');
        if ($blog['bloginfo']['BlogName'] != $BlogName) {
            $params['BlogName'] = htmlEncode($BlogName);
            if ($this->memberblog_socket->checkRegInfo($params)) {
                $data['errno'] = 'blogName';
                $data['error'] = '该博客名已被使用,信息保存失败';
                echo json_encode($data);
                exit;
            }
        }
        unset($data);

        $data['MemberID'] = $blog['bloginfo']['MemberID'];
        if ($BlogName) {
            $data['BlogName'] = htmlEncode($BlogName);
            $data['DomainName'] = $domainname;
            if ($this->memberblog_socket->updateBlogMember($data) !== true) {
                unset($data);
                $data['error'] = "更新博客名失败！";
                $data['errno'] = "updateBlogFailed";
                echo json_encode($data);
                exit(-1);
            } else {
                $data['errno'] = "success";
                $data['error'] = $data['BlogName'];
            }
        } else {
            $data['Subtitle'] = htmlEncode($SubTitle);
            $data['DomainName'] = $domainname;

            if (strlen($data['Subtitle']) > 126) {
                $data['error'] = "博客描述长度不能超过42个字节";
                $data['errno'] = "updateBlogConfigFailed";
                echo json_encode($data);
                exit(-1);
            }
            if ($this->memberblog_socket->updateBlogConfig($data) !== true) {
                unset($data);
                $data['error'] = "更新博客描述失败！";
                $data['errno'] = "updateBlogConfigFailed";
                echo json_encode($data);
                exit(-1);
            } else {
                $data['errno'] = "success";
                $data['error'] = $data['Subtitle'];
            }
        }
        echo json_encode($data);
    }

    /**
     * @ 个人博客的配置
     *   修改提交动作
     * */
    function Action($domainname) {
        $this->_checkLogin();
        $OldName = $this->input->post('oldname');
        $OldPrimary = $this->input->post('oldPrimary');
        $flashCode = $this->input->post('flashCode');
        $MemberID = $this->input->post('memberid');
        $BlogName = trim(strip_tags($this->input->post('name')));
        $SubTitle = trim(strip_tags($this->input->post('subtitle')));
        $isPrimary = $this->input->post('isPrimary');
        $showMode = $this->input->post('showMode');
        $displayNumber = $this->input->post('displayNumber');
        $commentNumber = $this->input->post('commentNumber');
        $recommendNumber = $this->input->post('recommendNumber');
        $allowComment = $this->input->post('allowComment');
        $photoProperty = $this->input->post('photoProperty');
        $data = array();

        if (mb_strlen($BlogName, 'UTF-8') > 30 || mb_strlen($BlogName, 'UTF-8') <= 1) {
            $data['error'] = "博客名信息长度应该在1-30个字符之间！";
            $data['errno'] = "blogname";
        } else if (mb_strlen($SubTitle, 'UTF-8') > 42) {
            $data['error'] = "博客描述信息长度应该在42个字符以内！";
            $data['errno'] = "subtitle";
        } else if ($isPrimary != 0 && $isPrimary != 1) {
            $data['error'] = "是否是主博客选项参数错误！";
            $data['errno'] = "isprimary";
        } else if ($showMode != 0 && $showMode != 1) {
            $data['error'] = "文章显示方式选项参数错误！";
            $data['errno'] = "showmode";
        } else if ($allowComment != 0 && $allowComment != 1 && $allowComment != 2) {
            $data['error'] = "文章是否允许评论选项参数错误！";
            $data['errno'] = "allcomment";
        } else if ($photoProperty != 0 && $photoProperty != 1) {
            $data['error'] = "相册是否允许评论选项参数错误！";
            $data['errno'] = "photoproperty";
        } else if (($displayNumber % 5) != 0 || ($commentNumber % 5) != 0 || ($recommendNumber % 5) != 0) {
            $data['error'] = "文章显示数目信息选项参数错误";
            $data['errno'] = "commentnumber";
        }

        if (!empty($data)) {
            echo json_encode($data);
            exit(-1);
        }
        unset($data);
        $this->load->model('memberblog_socket');
        if ($OldName != $BlogName) {
            $params['BlogName'] = htmlEncode($BlogName);
            if ($this->memberblog_socket->checkRegInfo($params)) {
                $data['errno'] = 'blogName';
                $data['error'] = '该博客名已被使用,信息保存失败';
                echo json_encode($data);
                exit;
            }
        }
        if ($OldName != $BlogName || $isPrimary != $OldPrimary) {
            $data['MemberID'] = $MemberID;
            $data['BlogName'] = addslashes(htmlEncode($BlogName));
            $data['IsPrimary'] = $isPrimary;
            $data['DomainName'] = $domainname;

            if ($this->memberblog_socket->updateBlogMember($data) !== true) {
                unset($data);
                $data['error'] = "更新博客基本信息失败！";
                $data['errno'] = "updateBlogFailed";
                echo json_encode($data);
                exit(-1);
            }
        }
        unset($data);

        $data['MemberID'] = $MemberID;
        $data['Subtitle'] = addslashes(htmlEncode($SubTitle));

        $data['ShowMode'] = $showMode;
        $data['DisplayNumber'] = $displayNumber;
        $data['CommentNumber'] = $commentNumber;
        $data['RecommendNumber'] = $recommendNumber;
        $data['AllowComment'] = $allowComment;
        $data['PhotoProperty'] = $photoProperty;
        $data['DomainName'] = $domainname;
        if ($this->memberblog_socket->updateBlogConfig($data) !== true) {
            unset($data);
            $data['error'] = "更新博客配置信息失败！";
            $data['errno'] = "updateBlogConfigFailed";
            echo json_encode($data);
            exit(-1);
        }
        unset($data);
        $data['error'] = "编辑常规配置信息成功！";
        $data['errno'] = "success";
        $data['code'] = getVerifyStr($MemberID . $this->user['userid'] . $OldPrimary . $BlogName);
        echo json_encode($data);
    }

}

//end class
?>