<?php

/* * **********************
 * 功能：   博客文章分类
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Articlesort extends MY_Controller {

    function Articlesort() {
        parent::MY_Controller();
    }

    /**
     * @ 博客文章分类列表
     * */
    function AsList($domainname) {
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

        //获取栏目信息
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $this->load->model('articlesort_socket');
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['Status'] = 0;
        $data['StartNo'] = -1;
        $tempInfo = $this->articlesort_socket->getArticleSortList($data);
        $extract['tempCnt'] = $tempInfo['TtlRecords'];

        $this->checkSequence($tempInfo, $data['MemberID']);

        $data['StartNo'] = ($page - 1) * articlesortpagesize;
        if ($data['StartNo'] > $extract['tempCnt']) {
            $page = 1;
        }
        $extract['CurPage'] = $page;
        $data['StartNo'] = ($page - 1) * articlesortpagesize;
        $data['QryCount'] = articlesortpagesize; //默认每个博客最多15个栏目信息
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $extract['sortlist'] = $this->articlesort_socket->getArticleSortList($data);



        $extract['user'] = $this->user;
        $extract['userid'] = $this->user['userid'];
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']); //判断是否自己的
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlesortlist'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articlesortlisttitle'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['default'] = config_item('default');
        $extract['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * @ 初始化文章分类序号
     * */
    function checkSequence($tempInfo = array(), $memberid = 0) {
        $datas = array();
        $this->load->model('articlesort_socket');
        $datas['StartNo'] = 0;
        $datas['QryCount'] = $tempInfo['TtlRecords'];
        $datas['FlagCode'] = $tempInfo['FlagCode'];
        $datas['MemberID'] = $memberid;
        $rs = $this->articlesort_socket->getArticleSortList($datas);

        $num = $rs['RetRecords'];
        $sign = 0;

        if (!isset($_COOKIE['initialize_' . $memberid])) {
            foreach ($rs['Record'] as $value) {
                if ($value['MemberID'] != 0) {
                    $param['MemberID'] = $memberid;
                    $param['SortID'] = $value['SortID'];
                    $param['Name'] = htmlEncode($value['Name']);
                    $param['Rank'] = $num;

                    $rs = $this->articlesort_socket->addArticleSort($param);
                    if (!$rs) {
                        $sign = 1;
                    }
                    $num--;
                    unset($param);
                }
            }
            //error_log(print_r($memberid,true),3,'/home/httpd/logs/a125.log');
        } else {
            $sign = 1;
        }

        if ($sign == 0) {
            setInitialize($memberid);
        }
    }

    /**
     * @ 编辑分类
     * */
    function Edit($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //权限认证
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'EditArticleSort');

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $SortID = intval($this->input->post('sortid'));
        //获取分类栏目信息
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['SortID'] = $SortID;
        $this->load->model('articlesort_socket');
        $extract['sortinfo'] = $this->articlesort_socket->getSortInfoByID($data);
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlesortadd'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-编辑文章分类';
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @ 添加文章分类
     * */
    function Add($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //权限认证
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'AddArticleSort');

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlesortadd'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articlesortaddtitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * Ajax调用返回Json
     * @增删修改动作
     * */
    function Action($domainname = '') {
        $this->_checkLogin();
        $MemberID = $this->input->post('memid');
        $check = $this->input->post('check');
        if (!$check) {
            $flashCode = $this->input->post('flashCode');
            if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
                $data['errno'] = "verify";
                $data['error'] = "验证信息非法，禁止操作";
                echo json_encode($data);
                exit(-1);
            }
        }
        $act = $this->input->post('act');

        switch ($act) {
            case 'addsort':
                $Name = $this->input->post('name');
                $SubTitle = $this->input->post('subtitle');
                $Intro = $this->input->post('intro');
                $Rank = intval($this->input->post('rank'));

                if (strlen($Name) > 30) {
                    $data['errno'] = "name";
                    $data['error'] = "栏目名称长度应该在1-30个字节之内";
                } else if (strlen($SubTitle) > 50) {
                    $data['errno'] = "subtitle";
                    $data['error'] = "栏目副标题长度应该在0-50个字节之内";
                } else if (strlen($Intro) > 100) {
                    $data['errno'] = "subtitle";
                    $data['error'] = "栏目简介长度应该在0-100个字节之内";
                } else {
                    $this->load->model('articlesort_socket');

                    unset($data);
                    $data['MemberID'] = $MemberID;
                    $data['Status'] = 0;
                    $data['StartNo'] = -1;
                    $tempInfo = $this->articlesort_socket->getArticleSortList($data);
                    $nTalCnt = $tempInfo['TtlRecords'];

                    //获取第一条分类
                    $datas = array();
                    $datas['MemberID'] = $MemberID;
                    $data['Status'] = 0;
                    $datas['StartNo'] = 0;
                    $datas['QryCount'] = 1;

                    $datas['first'] = 1;

                    $datas['FlagCode'] = $tempInfo['FlagCode'];
                    $tempInfos = $this->articlesort_socket->getArticleSortList($datas);
                    //获取第一条分类

                    if ($nTalCnt > articlesortcntlimit) {
                        $data['errno'] = "limitsort";
                        $data['error'] = "每个博客最多拥有" . articlesortcntlimit . "个文章分类信息";
                        break;
                    }
                    $param['MemberID'] = $MemberID;
                    $param['Name'] = htmlEncode($Name);
                    $param['SubTitle'] = htmlEncode($SubTitle);
                    $param['Intro'] = htmlEncode($Intro);
                    $param['return'] = $this->input->post('return');

                    $nTalCnt = $tempInfos['Record']['Rank'];
                    $nTalCnt++;
                    $param['Rank'] = ($nTalCnt < 0) ? 0 : $nTalCnt;

                    $rs = $this->articlesort_socket->addArticleSort($param);
                    if ($param['return'] == 1) {
                        $rs2 = $rs;
                        if ($rs['Code'] == '00') {
                            $rs = true;
                        } else {
                            $rs = false;
                        }
                    }

                    if ($rs) {
                        $data['errno'] = "success";
                        $data['error'] = "文章栏目添加成功";
                        if ($param['return'] == 1) {
                            $data['SortID'] = $rs2['Record']['SortID'];
                            $data['Rank'] = $param['Rank'];
                        }
                    } else {
                        $data['errno'] = "failed";
                        $data['error'] = "文章栏目添加失败";
                    }
                }
                break;
            case 'updsort':
                $Name = $this->input->post('name');
                $SubTitle = $this->input->post('subtitle');
                $Intro = $this->input->post('intro');
                $Rank = intval($this->input->post('rank'));
                $SortID = intval($this->input->post('sortid'));
                if (strlen($Name) > 30 || strlen($Name) < 1) {
                    $data['errno'] = "name";
                    $data['error'] = "栏目名称长度应该在1-30个字节之内";
                } else if (strlen($SubTitle) > 50) {
                    $data['errno'] = "subtitle";
                    $data['error'] = "栏目副标题长度应该在0-50个字节之内";
                } else if (strlen($Intro) > 100) {
                    $data['errno'] = "subtitle";
                    $data['error'] = "栏目简介长度应该在0-100个字节之内";
                } else if ($Rank > 99 || $Rank < 0) {
                    $data['errno'] = "rank";
                    $data['error'] = "文章排序应该在0-99之内";
                } else if ($SortID < 0) {
                    $data['errno'] = "sortid";
                    $data['error'] = "文章分类序号信息错误";
                } else {
                    $param['MemberID'] = $MemberID;
                    $param['SortID'] = $SortID;
                    $param['Name'] = htmlEncode($Name);
                    $param['SubTitle'] = htmlEncode($SubTitle);
                    $param['Intro'] = htmlEncode($Intro);
                    $param['Rank'] = ($Rank < 0) ? 0 : $Rank;

                    $this->load->model('articlesort_socket');
                    if ($this->articlesort_socket->addArticleSort($param)) {
                        $data['errno'] = "success";
                        $data['error'] = "文章栏目更新成功";
                    } else {
                        $data['errno'] = "failed";
                        $data['error'] = "文章栏目更新失败";
                    }
                }
                break;
            case 'delsort':
                $sortid = intval($this->input->post('SortID'));
                $param['MemberID'] = $MemberID;
                $param['SortIDs'] = $sortid;
                $this->load->model('articlesort_socket');
                if ($this->articlesort_socket->delArticleSort($param)) {
                    $data['errno'] = "success";
                    $data['error'] = "文章分类栏目删除成功";
                } else {
                    $data['errno'] = "failed";
                    $data['error'] = "文章分类栏目删除失败";
                }
                break;
            default:
                $data['errno'] = "act";
                $data['error'] = "您还没选择操作类型";
        }
        echo json_encode($data);
        exit;
    }

    /*
      文章发表调用分类列表
     */

    function AjaxSortList($MemberID) {
        $param['MemberID'] = intval($MemberID);
        $param['Status'] = 0;
        $param['StartNo'] = 0;
        $param['QryCount'] = articlesortcntlimit;
        $param['AjaxList'] = 1;
        $sortid = $this->input->get_post('sortid');
        $script = "";
        if (!empty($sortid)) {
            $script = "<script>$('#sortid').val('" . $sortid . "')</script>";
        }
        $this->load->model('articlesort_socket');
        $data = $this->articlesort_socket->getArticleSortList($param);
        $default = config_item('default');
        $str = '';
        if ($data['RetRecords'] > 1) {
            foreach ($data['Record'] as $sortlist) {
                if ($sortlist['SortID'] == '18296') {
                    continue;
                }
                if ($default['articlesort'][0] == $sortlist['SortID'])
                    $str = '<option value="' . $sortlist['SortID'] . '">' . filter_word($sortlist['Name']) . '</option>' . $str;
                else
                    $str .= '<option value="' . $sortlist['SortID'] . '">' . filter_word($sortlist['Name']) . '</option>';
            }
        }
        else if ($data['RetRecords'] == 1) {
            $str .= '<option value="' . $data['Record']['SortID'] . '">' . filter_word($data['Record']['Name']) . '</option>';
        } else {
            $str .= '<option value="' . $default['articlesort'][0] . '">' . filter_word($default['articlesort'][1]) . '</option>';
        }
        echo $str . $script;
    }

    function editSort() {

        $SortIDValue = $this->input->post('SortIDValue');
        /*
          if (!is_array($SortIDValue) || empty($SortIDValue)) {
          $data['errno'] = "1";
          $data['error'] = "排序未变更";
          echo json_encode($data);
          exit;
          }
         */

        $this->load->model('articlesort_socket');
        $param['MemberID'] = $this->input->post('memberid');

        foreach ($SortIDValue as $value) {
            if ($value == '' || empty($value)) {
                continue;
            }

            $param['SortID'] = $value;
            $param['Name'] = htmlEncode($this->input->post('NameValue_' . $value));
            $param['Rank'] = $this->input->post('RankValue_' . $value);

            $rs = $this->articlesort_socket->addArticleSort($param);
            if (!$rs) {
                $data['errno'] = "1";
                $data['error'] = "系统繁忙，稍后再试";
                echo json_encode($data);
                exit;
            }
        }

        $data['errno'] = "0";
        $data['error'] = "保存成功";
        echo json_encode($data);
        exit;
    }

}

//end class
?>