<?php

/*
 * ***********************
 * 功能：   博客实名信息设置
 * author： jianglw
 * ***********************
 */

class Userset extends MY_Controller {

    function Userset() {
        parent::MY_Controller();
        $this->load->model('usermodel');
    }

    /**
     * 实名信息页面
     */
    function editRealuser($domainname) {
        $this->_checkIP();
        $this->_checkLogin();

        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname); //通过博客名获取博客信息	
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true); //创建点击统计url
        $extract['bloglist'] = $this->_getBlogListByUid(); //获取个人博客列表

        if ($extract['bloglist']['RetRecords'] == 1) {
            if ($extract['bloglist']['Record']['DomainName'] != $domainname) {
                cnfolAlert("您没有操作他人博客的权限");
                cnfolLocation();
                exit(-1);
            }
        } else if ($extract['bloglist']['RetRecords'] > 1) {
            $own = false;
            foreach ($extract['bloglist']['Record'] as $value) {
                if ($value['DomainName'] == $domainname) {
                    $own = true;
                }
            }

            if (!$own) {
                cnfolAlert("您没有操作他人博客的权限");
                cnfolLocation();
                exit(-1);
            }
        }

        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']); //获取博客配置信息


        $extract['user'] = $this->user;
        $extract['userid'] = $this->user['userid'];
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']); //判断是否自己的
        $authinfo = $this->usermodel->getUserAuthInfo(array('UserID' => $this->user['userid'])); //实名认证信息
        switch ($authinfo['Code']) {
            case '00':
                $authinfo = $authinfo['Record'];
                if (is_array($authinfo['0'])) {
                    $authinfo = array($authinfo);
                }
                break;
            default:
                $authinfo = array();
                break;
        }

        if ($authinfo) {
            //查看是否过了申请周期 
            if ($this->usermodel->isApplyAdd($this->user['userid'], $authinfo['Status'])) {
                $extract['isApplyAdd'] = 1; //不能申请认证
            }
        }
        $extract['authinfo'] = $authinfo;

        $user = $this->usermodel->getUserBaseInfo(array('UserID' => $this->user['userid'])); //基本信息
        if (!empty($user['Email'])) {
            $extract['authinfo']['Email'] = $user['Email'];
        }
        if (!empty($user['Mobile'])) {
            $extract['authinfo']['Mobile'] = $user['Mobile'];
        }

        $blocks = &$this->config->item('block');
        $extract['swfupload'] = $this->config->item('module_path') . 'swfupload.shtml';
        $extract['baseurl'] = &config_item('base_url');
        $extract['imgsite'] = &config_item('imgsite');
        $extract['systaglist'] = &config_item('sysTagList');
        $extract['block'] = $blocks['usersetpage'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . $blocks['usersettitle'] . '-' . $extract['bloginfo']['NickName'];

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * 实名认证保存
     */
    function ajaxSave() {
        $this->_checkLogin();
        $IN = parse_incoming();
        $param['TrueName'] = addslashes(strip_tags(trim($IN['TrueName'])));
        $param['Remark'] = addslashes(strip_tags(trim($IN['Identity'])));
        $param['IdentityNumber'] = trim($IN['IdentityNumber']);
        $param['IdentityPicFront'] = $IN['IdentityPicFront'];
        $param['OtherIdentityPic'] = $IN['OtherIdentityPic'];

        if (strlen($param['IdentityNumber']) == 18) {
            $param['Sex'] = substr($param['IdentityNumber'], -2, 1) & 1 ? 0 : 1;
        } elseif (strlen($param['IdentityNumber']) == 15) {
            $param['Sex'] = substr($param['IdentityNumber'], -1) & 1 ? 0 : 1;
        }
        $paramAdd['Province'] = addslashes(strip_tags(trim($IN['Province'])));
        $paramAdd['City'] = addslashes(strip_tags(trim($IN['City'])));
        $paramAdd['Address'] = addslashes(strip_tags(trim($IN['Region'])));
        $paramAdd['Telephone'] = trim($IN['Telephone']);

        $param['UserID'] = $this->user['userid'];
        $result = $this->usermodel->addUserAuthInfo($param);
        if ($result['flag'] <= 0) {
            $error = $result['msg'];
        }
        if ($error) {
            echo json_encode(array('flag' => -1, 'error' => $error));
        } else {
            $this->usermodel->setUserDate($param['UserID'], $param['Status']);
            echo json_encode(array('flag' => 1, 'error' => '保存成功'));
        }
    }

    function uploadSave() {
//        $this->_checkLogin();

        $picpath = UPLOAD_FILE_LOCALPATH;
        $config = array(
            'name' => 'Filedata',
            'maxbytes' => 547483647,
            'allowextension' => 'jpg,jpeg,png,bmp,phjpeg,gif'
        );
//        error_log($picpath, 3, '/home/httpd/logs/pic.log');
        $this->load->library('flashupload', $config);
        $uploaded_url = $this->flashupload->saveto($picpath, $this->_getUserID());
        sleep(1);
        echo 'http://img.cnfol.com/upload/ident/' . $uploaded_url;
    }

}

