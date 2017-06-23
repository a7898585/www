<?php

/* * **********************
 * 功能：   博客汇总管理页
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Register extends MY_Controller {

    function Register() {
        parent::MY_Controller();
    }

    /**
     * @ 博客注册页
     * @ 开通博客注册页
     * */
    function Index() {
        $this->_checkLogin();
        //获取个人博客列表	
        $extract['bloglist'] = $this->_getBlogListByUid(0);

        //选定头部信息
        $extract['isindex'] = 1;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['registerindex'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['user'] = $this->user;
        $extract['common_login'] = $blocks['common_login'];
        $extract['imagesite'] = $this->config->item('imgsite');

        //$BlogCntGW = $this->_getBlogListByUid($this->user['userid']);
        $extract['blogCount'] = intval($extract['bloglist']['RetRecords']);

        if ($extract['blogCount'] == memberbloglimit) {
            if ($extract['bloglist']['Record']['Status'] == 0) {
                cnfolAlert("您的博客已经开通！");
                rollbackto();
            } else {
                cnfolAlert("抱歉！一个ID只能创建一个博客！");
                rollbackto();
            }
        } else if ($extract['blogCount'] > memberbloglimit) {
            foreach ($extract['bloglist']['Record'] as $key => $value) {
                if ($value['Status'] == 0) {
                    cnfolAlert("您的博客已经开通！");
                    rollbackto();
                }
            }
            cnfolAlert("抱歉！一个ID只能创建一个博客！");
            rollbackto();
        }


        $this->load->view('register/RegIndex.shtml', $extract);
    }

    /**
     * 开通博客的动作页
     * Ajax判断
     * @开通博客提交表单的处理页
     * */
    function Action() {
        //验证登入

        if (!$this->_checkUserlogin()) {
            $data['errno'] = 'errorloading';
            $data['error'] = '您的登入信息错误，顾本次操作失败';
            echo json_encode($data);
            exit;
        }
        //验证验证码
        $verifycode = $this->input->post('VerifyCode');

        /* $this->load->library('authcode');
          if(!$this->authcode->validate($verifycode))
          {
          $data['errno']	=	'verifycodespan';
          $data['error']	=	'<br />您输入的验证码信息错误';
          echo json_encode($data);
          exit;
          } */

        $this->load->library('SimpleCaptcha');
        if (!$this->simplecaptcha->validate($verifycode, $this->user['userid'])) {
            $data['errno'] = 'verifycodespan';
            $data['error'] = '您输入的验证码信息错误';
            echo json_encode($data);
            exit;
        }

        //验证提交信息合法性
        $BlogCnt = intval($this->input->post('TolCnt'));
        $BlogCntGW = $this->_getBlogListByUid($this->user['userid']);

        if ($BlogCntGW == false) {
            $data['errno'] = 'gwisnone';
            $data['error'] = '请求中断，请重新登录后再次申请创建';
            echo json_encode($data);
            exit;
        }



        if ($BlogCnt != intval($BlogCntGW['RetRecords'])) {
            $data['errno'] = 'errorloading';
            $data['error'] = '加密信息被篡改，顾本次请求失败.';
            echo json_encode($data);
            exit;
        }

        $falshCode = $this->input->post('flashCode');
        if ($falshCode != getVerifyStr($BlogCnt . $this->user['userid'])) {
            $data['errno'] = 'errorloading';
            $data['error'] = '加密信息被篡改，顾本次请求失败';
            echo json_encode($data);
            exit;
        }


        $this->load->model('memberblog_socket');
        $DomainName = htmlEncode($this->input->post('DomainName'));
        $BlogName = htmlEncode($this->input->post('BlogName'));


        if (FALSE === $BlogName || mb_strlen($BlogName, 'UTF-8') > 30) {
            $data['errno'] = 'spanblogName';
            $data['error'] = '博客名不符合已定的规则';
        } else if (!preg_match("/^[a-zA-Z][a-zA-Z0-9\_]{5,}$/", $DomainName)) {
            $data['errno'] = 'spnInfo';
            $data['error'] = '博客网址请使用字母开头，长度大于6位';
        } else if (strlen($DomainName) > 35) {
            $data['errno'] = 'spnInfo';
            $data['error'] = '博客网址最长超过35个字符限制';
        } else if (preg_match("/^ajax|cnfol.*$/i", $DomainName)) {
            $data['errno'] = 'spnInfo';
            $data['error'] = '<博客地址不能以ajax、cnfol开头';
        } else if ($BlogCntGW['RetRecords'] >= memberbloglimit) {
            $data['errno'] = 'errorloading';
            $data['error'] = '您拥有的博客数已经达到上限，故开通失败';
            echo json_encode($data);
            exit;
        } else {
            $params['BlogName'] = $BlogName;
            if ($this->memberblog_socket->checkRegInfo($params)) {
                $data['errno'] = 'spanblogName';
                $data['error'] = '博客名已被使用,请更换';
                echo json_encode($data);
                exit;
            }

            unset($params);
            $params['DomainName'] = $DomainName;
            if ($this->memberblog_socket->checkRegInfo($params)) {
                $data['errno'] = 'spnInfo';
                $data['error'] = '博客网址已被人使用,请更换';
                echo json_encode($data);
                exit;
            }

            unset($params);
            $params['UserID'] = $this->user['userid'];
            $params['NickName'] = $this->user['nickname'];
            $params['Status'] = intval($this->input->post('status'));
            $params['Sort'] = intval($this->input->post('sort'));
            $params['BlogName'] = $BlogName;
            $params['DomainName'] = $DomainName;
            $params['DefaultGroupID'] = config_item('defaultgroup');
            $MemberID = $this->memberblog_socket->addNewBlog($params);

            if (!$MemberID) {
                $data['errno'] = 'errorloading';
                $data['error'] = '创建博客失败，请稍候再试';
            } else {
                $data['errno'] = 'success';
                $data['error'] = '恭喜您，博客申请开通成功';

                //默认开通微博
                //$loginipaddr = $this->input->ip_address();
                //$url = 'http://t.cnfol.com/ajaxaction/signup?username=' . $this->user['username'] . '&password=000123&loginipaddr=' . $loginipaddr;
                //$rs = curl_get($url);
                //error_log(date('Y-m-d H:i:s') . ' | ' . __FILE__ . ' | ' . __METHOD__ . ' | ' . $url . ' | ' . $rs . PHP_EOL, 3, '/home/www/html/logs/register_' . date('Ymd') . '.log');
            }
        }
        echo json_encode($data);
        exit;
    }

    /**
     * 验证动作
     * Ajax判断
     * @开通博客提交表单的处理页
     * */
    function Check() {
        if (!$this->_checkUserlogin()) {
            $data['errno'] = 'login';
            $data['error'] = '您的登入信息错误，顾本次操作失败';
            echo json_encode($data);
            exit;
        }
        $this->load->model('memberblog_socket');
        $act = $this->input->post('act');
        switch ($act) {
            case 'checkblogname':
                $BlogName = htmlEncode($this->input->post('BlogName'));
                if (FALSE !== $BlogName && mb_strlen($BlogName, 'UTF-8') <= 30) {
                    $params['BlogName'] = $BlogName;
                    if ($this->memberblog_socket->checkRegInfo($params)) {
                        $data['errno'] = 'hadbeuse';
                        $data['error'] = '<font style="color:red">博客名已被使用,请更换</font>';
                    } else {
                        $data['errno'] = 'success';
                        $data['error'] = '<font style="color:green">博客名可以使用</font>';
                    }
                } else {
                    $data['errno'] = 'paramerr';
                    $data['error'] = '<font style="color:red">参数错误，请查看博客名规则</font>';
                }
                break;
            case 'checkdomainname':
                $DomainName = htmlEncode($this->input->post('DomainName'));

                if (preg_match("/^ajax|cnfol.*$/i", $DomainName)) {
                    $data['errno'] = 'baoliu';
                    $data['error'] = '<font style="color:red">博客地址不能以ajax、cnfol开头</font>';
                } else if (!preg_match("/^[a-zA-Z][a-zA-Z0-9\_]{5,}$/", $DomainName)) {
                    $data['errno'] = 'spnInfo';
                    $data['error'] = '<font style="color:red">博客网址请使用字母开头，长度大于6位</font>';
                } else if (FALSE !== $DomainName) {
                    $params['DomainName'] = $DomainName;
                    if ($this->memberblog_socket->checkRegInfo($params)) {
                        $data['errno'] = 'hadbeuse';
                        $data['error'] = '<font style="color:red">博客网址已被人使用,请更换</font>';
                    } else {
                        $data['errno'] = 'success';
                        $data['error'] = '<font style="color:green">博客网址可以使用</font>';
                    }
                } else {
                    $data['errno'] = 'paramerr';
                    $data['error'] = '<font style="color:red">参数错误，请查看博客地址规则</font>';
                }
                break;
            case 'linksort':
                $params['MemberID'] = intval($this->input->post('memberid'));
                $params['LinkSortName'] = htmlEncode($this->input->post('name'));
                if (strlen($params['LinkSortName']) < 30 && strlen($params['LinkSortName']) > 0) {
                    if ($this->memberblog_socket->checkRegInfo($params)) {
                        $data['errno'] = 'linksortname';
                        $data['error'] = '该分类已经存在';
                    } else {
                        $data['errno'] = 'success';
                        $data['error'] = '';
                    }
                } else {
                    $data['errno'] = 'paramerr';
                    $data['error'] = '链接分类名称长度应该在1-30个字节之内';
                }
                break;
            case 'linkinfo':
                $params['MemberID'] = intval($this->input->post('memberid'));
                $params['LinkName'] = htmlEncode($this->input->post('name'));
                if (strlen($params['LinkName']) < 30 && strlen($params['LinkName']) > 0) {
                    if ($this->memberblog_socket->checkRegInfo($params)) {
                        $data['errno'] = 'linkname';
                        $data['error'] = '该链接名称已经存在';
                    } else {
                        $data['errno'] = 'success';
                        $data['error'] = '';
                    }
                } else {
                    $data['errno'] = 'paramerr';
                    $data['error'] = '链接名称长度应该在1-30个字节之内';
                }
                break;
            case 'asort':  //文章分类
                $params['MemberID'] = intval($this->input->post('memberid'));
                $params['MemberSortName'] = htmlEncode($this->input->post('name'));
                if (strlen($params['MemberSortName']) < 30 && strlen($params['MemberSortName']) > 0) {
                    if ($this->memberblog_socket->checkRegInfo($params)) {
                        $data['errno'] = 'sortname';
                        $data['error'] = '该分类名称已经存在';
                    } else {
                        $data['errno'] = 'success';
                        $data['error'] = '';
                    }
                } else {
                    $data['errno'] = 'paramerr';
                    $data['error'] = '分类名长度为0-30个字符之间';
                }
                break;
            default:
                $data['errno'] = 'action';
                $data['error'] = '参数信息错误';
                break;
        }
        echo json_encode($data);
        exit;
    }

    /**
     * 管理用户的博客信息
     * @用户登入博客管理页
     * */
    function Home() {
        $this->_checkLogin();

        //获取个人博客列表	
        $extract['bloglist'] = $this->_getBlogListByUid(0);


        if (isset($extract['bloglist']['Record']) && $extract['bloglist']['RetRecords'] > 0) {
            if ($extract['bloglist']['RetRecords'] == 1) {
                $Tmpbloglist = array(0 => $extract['bloglist']['Record']);
            } else {
                $Tmpbloglist = $extract['bloglist']['Record'];
            }
            $memberIDs = array();
            foreach ($Tmpbloglist as $v) {

                if (empty($v['MemberID'])) {
                    continue;
                }
                $data['MemberIDs'] = $v['MemberID'];
                $extract['blogstat'][$v['MemberID']] = $this->memberblog_socket->getMemberBlogStat($data);
            }
        }

        $blocks = &$this->config->item('block');
        $extract['common_login'] = $blocks['common_login'];
        $extract['block'] = $blocks['register_home'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['user'] = $this->user;

        $this->load->view('register/RegIndex.shtml', $extract);
    }

    function verifycode() {
        $t = $this->input->get('t');
        $this->load->library('SimpleCaptcha');
        $this->simplecaptcha->resourcesPath = APPPATH . 'resources';
        $this->simplecaptcha->imageFormat = 'png';
        if ($t == 1) {
            $this->simplecaptcha->minSize = 14;
            $this->simplecaptcha->maxSize = 18;
            $this->simplecaptcha->width = 120;
            $this->simplecaptcha->height = 25;
            $this->simplecaptcha->minWordLength = 4;
            $this->simplecaptcha->maxWordLength = 6;
            $this->simplecaptcha->Yperiod = 0.1;
            $this->simplecaptcha->Yamplitude = 255;
            $this->simplecaptcha->Xperiod = 0.1;
            $this->simplecaptcha->Xamplitude = 400;
            $this->simplecaptcha->scale = 3;
            $this->simplecaptcha->spacing = -1;
            $this->simplecaptcha->numeric = true;
        } else {
            $this->simplecaptcha->minSize = 14;
            $this->simplecaptcha->maxSize = 18;
            $this->simplecaptcha->width = 90;
            $this->simplecaptcha->height = 25;
            $this->simplecaptcha->spacing = 1;
            $this->simplecaptcha->Yperiod = 1;
            $this->simplecaptcha->Yamplitude = 0.1;
            $this->simplecaptcha->Xperiod = 11;
            $this->simplecaptcha->Xamplitude = 5;
            $this->simplecaptcha->minWordLength = 4;
            $this->simplecaptcha->maxWordLength = 4;
            $this->simplecaptcha->scale = 3;
        }
        $this->simplecaptcha->CreateImage($this->user['userid']);
    }

}

//end class
?>