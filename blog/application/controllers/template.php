<?php

/* * **********************
 * 功能：   博客样式管理
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Template extends MY_Controller {

    function Template() {
        parent::MY_Controller();
    }

    /**
     * @ 高级设置
     * @ 博客模板高级设置
     * */
    function Advanced($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $data['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //验证权限
        $this->_checkUser($data['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($data['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'TemplateAdvanced');

        //验证权限
        $this->_checkUser($data['bloginfo']['UserID']);

        //创建点击统计url
        $data['viewurl'] = $this->_getviewURL($data['bloginfo'], true);

        //获取个人博客列表
        $data['bloglist'] = $this->_getBlogListByUid();

        //获取博客配置信息
        $data['blogconfig'] = $this->_getBlogConfig($data['bloginfo']['MemberID']);

        $data['user'] = $this->user;

        $blocks = &$this->config->item('block');
        $data['block'] = $blocks['advanced'];
        $data['title'] = $data['bloginfo']['BlogName'] . '-' . $blocks['advancedtitle'];
        $data['peronalhead'] = $blocks['personalhead'];
        $data['peronalfoot'] = $blocks['personalfoot'];
        $data['baseurl'] = &config_item('base_url');
        $data['isconfig'] = 1;
        $this->load->view('manage/manage_index.shtml', $data);
    }

    /**
     * @进入高级设置页面
     * */
    function Index($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //验证权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'TemplateAdvanced');

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        /* ------------------start粉丝，我关注的，相互关注数  ----------------------------------- */
        $extract['friendsnumber'] = $this->_getFriend($extract['bloginfo']['UserID']);
        /* ----------start博主的文章数量----------------------------------- */
        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $extract['TotalArticle'] = $stat1['TotalArticle'];
        /* ------------end博主的文章数量------------------------------------ */
        $extract['user'] = $this->user;
        $extract['pagesize'] = $this->pagesize;
        $extract['userid'] = $extract['user']['userid'];
        $extract['blocks'] = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['NickName'] . '_' . $this->lang->language['keywords_article_cnfol_2'];
        $blocks = &$this->config->item('block');
        $extract['devmyblogloginheader'] = $blocks['devmyblogloginheader'];
        $extract['peronalfoot'] = $blocks['devmyblogcommonfooter'];
        $extract['devmyblogcommonright'] = $blocks['devmyblogcommonright'];
        $extract['show_articlelist'] = $blocks['show_articlelist'];
        $extract['logintool'] = $blocks['logintool'];
        $extract['tmp_jointly'] = $blocks['jointly'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['modulepath'] = &config_item('module_path');
        $extract['show_renewvisitor'] = $blocks['show_renewvisitor'];
        $extract['cuttrent_domainname'] = $domainname;
        /*
         * 实名认真 -start
         */
        $params['UserID'] = $extract['bloginfo']['UserID'];
        $extract['auth'] = $this->user_socket->realNameAuth($params);

        /*
         * 实名认真 -end
         */
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        $extract['layoutlist'] = $this->config->item('layout');
        $extract['sysmodules'] = $this->config->item('sysmodules');
        $extract['bglist'] = $this->config->item('bgurl');
        $extract['defaultcss'] = $this->config->item('defaultcss');
        $extract['default'] = $this->config->item('default');

        $Modules = array();
        $RModules = (trim($extract['blogconfig']['RModules']) != '') ? explode(',', $extract['blogconfig']['RModules']) : array();
        $MModules = (trim($extract['blogconfig']['MModules']) != '') ? explode(',', $extract['blogconfig']['MModules']) : array();
        $LModules = (trim($extract['blogconfig']['LModules']) != '') ? explode(',', $extract['blogconfig']['LModules']) : array();
        $Modules['lmods'][0] = $extract['sysmodules'][10][3];
        unset($extract['sysmodules'][10]);
        if ($extract['blogconfig']['GlobalCssID'] == 1 || $extract['blogconfig']['GlobalCssID'] == 2) {
            foreach ($RModules as $v) {
                if (isset($extract['sysmodules'][$v])) {
                    $Modules['rmods'][] = $extract['sysmodules'][$v][3];
                }
            }
            foreach ($MModules as $v) {
                if (isset($extract['sysmodules'][$v])) {
                    $Modules['mmods'][] = $extract['sysmodules'][$v][3];
                }
            }
            foreach ($LModules as $v) {
                if (isset($extract['sysmodules'][$v])) {
                    $Modules['lmods_extra'][] = $extract['sysmodules'][$v][3];
                }
            }
            if (!empty($Modules['rmods']) && !empty($Modules['mmods'])) {
                $Modules['rmods'] = array_merge($Modules['mmods'], $Modules['rmods']);
            } else {
                $Modules['rmods'] = $Modules['mmods'] ? $Modules['mmods'] : $Modules['rmods'];
            }
            if (!empty($Modules['lmods_extra'])) {
                $Modules['rmods'] = array_merge($Modules['rmods'], $Modules['lmods_extra']);
            }
        } else {
            for ($i = 1; $i < 10; $i++) {
                $Modules['rmods'][] = $extract['sysmodules'][$i][3];
            }

            $extract['blogconfig']['GlobalCssID'] = 1;
        }

        $extract['module'] = $Modules;
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['tindexcsstitle'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;
        $extract['dragable'] = 1;
        $extract['modulepath'] = &config_item('module_path');
        $extract['sysmodules'][10] = array('Mod_ArtList', '10', '文章列表', 'devshow_articlelist.shtml');
        $this->load->view('manage/manage_advanced_index.shtml', $extract);
    }

    /**
     * 通过Ajax获取信息
     * 保存布局等信息
     * @ 高级设置的动作页
     * */
    function LayoutAction($domainname) {
        $this->_checkLogin();
        $MemberID = $this->input->post('MemberID');
        $flashCode = $this->input->post('flashCode');
        if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
            $data['errno'] = 'verify';
            $data['error'] = '本次请求参数不合法';
            echo json_encode($data);
            exit;
        }
        $act = $this->input->post('act');
        switch ($act) {
            case 'updCss':
                $StyleID = intval($this->input->post('templateID'));
                $StyleID = ($StyleID > 0) ? $StyleID : 1;
                $OriStyleID = intval($this->input->post('OriTemlID'));
                if ($OriStyleID == $StyleID) {
                    $data['errno'] = 'unchange';
                    $data['error'] = '模板样式修改成功';
                    echo json_encode($data);
                    exit;
                }
                $this->load->model('memberblog_socket');
                $param['MemberID'] = $MemberID;
                $param['StyleID'] = $StyleID;
                $param['DomainName'] = $domainname;
                if ($this->memberblog_socket->updateBlogConfig($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '模板样式修改成功';
                    $this->load->model('template_socket');
                    unset($param);
                    $param['StyleID'] = $StyleID;
                    $param['Clicked'] = 1;   //人气值是累加的更新
                    $this->template_socket->aupdCssStyle($param);
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '模板样式修改失败';
                }
                break;
            case 'updLayout':
                $param['MemberID'] = $MemberID;
                $param['Background'] = trim($this->input->post('bgimg'));
                $layoutid = intval($this->input->post('layoutid'));
                $param['LModules'] = $this->input->post('lfmod');
                $param['MModules'] = '';
                $param['RModules'] = $this->input->post('rtmod');
                $param['StyleID'] = $this->input->post('styleid');
                $default = &config_item('defaultcss');
                if (!preg_match('/https?:\/\/.*(cnfol).+([jpg,gif,JPG,png,PNG,GIF])/', $param['Background'])) {
                    $param['Background'] = "";
                }
                $param['GlobalCssID'] = ($layoutid > 5 || $layoutid < 0) ? $default['layoutid'] : $layoutid;
                $param['DomainName'] = $domainname;
                $this->load->model('memberblog_socket');
                if ($this->memberblog_socket->updateBlogConfig($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '编辑常规配置成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '现在服务忙，请稍候再试';
                }

                break;
            default:
                $data['errno'] = 'action';
                $data['error'] = '您选择的操作不合法';
        }
        echo json_encode($data);
        exit;
    }

    /**
     * @ 自定义模板
     * */
    function UserDefine($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'TemplateDefine');

        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取用户使用的样式信息
        $this->load->model('template_socket');
        $data['StyleIDs'] = $extract['blogconfig']['StyleID'];
        $extract['usestyle'] = $this->template_socket->getCssStyleInfoById($data);
        $extract['viewurl'] = 'javascript:void(0)';
        //获取用户自己的样式信息列表
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $tmpCnt = $this->template_socket->getShareCssList($data);

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;

        $data['StartNo'] = userdefinestylepage * ($page - 1);
        if ($tmpCnt < $data['StartNo']) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            $data['QryCount'] = userdefinestylepage;
            $usercsslist = $this->template_socket->getShareCssList($data);
            if ($usercsslist['RetRecords'] == 1) {
                $usercsslist['Record'] = array(0 => $usercsslist['Record']);
            }
            $extract['usercsslist'] = $usercsslist;
            //翻页信息
            $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/template/Define';
            $this->load->library('pagebarsnew');
            $this->pagebarsnew->Page($tmpCnt, $page, userdefinestylepage, $baseLink, '/');
            $extract['pagebar'] = $this->pagebarsnew->upDownList();
        }
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['managedefine'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['definetitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @ 添加自定义模板设置
     * */
    function AddStyle($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);

        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $styleID = intval($this->input->post('editstyleid'));
        $data['StyleIDs'] = ($styleID > 0) ? $styleID : $extract['blogconfig']['StyleID'];
        if ($data['StyleIDs'] > 0) {
            $this->_checkAccess($blogaccess, 'EditDefineStyle');
        } else {
            $this->_checkAccess($blogaccess, 'AddDefineStyle');
        }
        //获取用户使用的样式信息
        $this->load->model('template_socket');
        $extract['usestyle'] = $this->template_socket->getCssStyleInfoById($data);

        $extract['editstyleid'] = ($styleID > 0) ? $styleID : 0;
        $extract['viewurl'] = 'javascript:void(0)';
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['manageaddcss'];
        $title = ($styleID > 0) ? $blocks['managedelcsstitle'] : $blocks['manageaddcsstitle'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $title;
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * Ajax调用
     * return Json
     * @ 关于博客Css的创建，删除，更变等动作
     * */
    function CssAction($domainname) {
        $this->_checkLogin();
        $MemberID = $this->input->post('MemberID');
        $flashCode = $this->input->post('flashCode');
        if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
            $data['errno'] = 'verify';
            $data['error'] = '本次请求参数不合法';
            echo json_encode($data);
            exit;
        }
        $act = $this->input->post('act');
        switch ($act) {
            case 'addcss':  //增修都一样
                $param['Name'] = htmlEncode($this->input->post('name'));
                $tmplen = strlen($param['Name']);
                if ($tmplen < 3 || $tmplen > 60) {
                    $data['errno'] = 'name';
                    $data['error'] = '样式名称长度应该在3-60个字节之内';
                    break;
                }
                $param['Content'] = htmlEncode($this->input->post('Content'));
                $tmplen = strlen($param['Content']);
                if ($tmplen < 100 || $tmplen > 1000000) {
                    $data['errno'] = 'content';
                    $data['error'] = '样式内容长度应该在100-1000000个字节之内';
                    break;
                }

                $this->load->model('template_socket');
                $param['StyleID'] = intval($this->input->post('styleid'));
                $param['StyleID'] = ($param['StyleID'] > 0) ? $param['StyleID'] : 0;
                if ($param['StyleID'] <= 0) {
                    unset($params);
                    $params['MemberID'] = $MemberID;
                    $params['StartNo'] = -1;
                    $tmpCnt = $this->template_socket->getShareCssList($params);
                    if ($tmpCnt >= blogcssmaxhave) {
                        $data['errno'] = 'limit';
                        $data['error'] = '每个博客最多允许拥有个' . blogcssmaxhave . '样式';
                        break;
                    }
                }
                //新增
                if ($param['StyleID'] <= 0) {
                    $param['MemberID'] = $MemberID;
                    $param['FirstSortID'] = 0;
                    $param['SecondSortID'] = 0;
                    $param['Preview'] = '0';
                    $param['IsChecked'] = 1;
                    $param['IsShared'] = 0;
                    $param['Clicked'] = 0;
                    $param['IsSharing'] = 0;
                    $param['Sort'] = 0;
                }
                $isuse = $this->input->post('isuse');
                if ($isuse == 1) {
                    $param['Clicked'] = 1;
                }

                $StyleID = $this->template_socket->aupdCssStyle($param);
                if ($StyleID > 0) {
                    $BuildData = array(
                        'apiKey' => config_item('buildcsskey'),
                        'StyleID' => $StyleID,
                        'content' => $param['Content']
                    );
                    BuildCss($BuildData);
                    $previewurl = config_item('base_url') . '/' . $domainname . '/preview/' . $StyleID;
                    BuildCssImg($previewurl, $StyleID);
                    setcookie('blogcss_' . $StyleID, 1, time() + config_item('cssimgcookiet'), "/", config_item('cookie_domain'));
                    //是否保存
                    if ($isuse == 1) {
                        unset($params);
                        $params['MemberID'] = $MemberID;
                        $params['StyleID'] = $StyleID;
                        $param['DomainName'] = $domainname;
                        $this->load->model('memberblog_socket');
                        $this->memberblog_socket->updateBlogConfig($params);
                    }
                    $data['errno'] = 'success';
                    $data['error'] = ($param['StyleID'] > 0) ? '自定义样式修改成功' : '自定义样式添加成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = ($param['StyleID'] > 0) ? '自定义样式修改失败' : '自定义样式添加失败';
                }
                break;
            case 'delcss':
                $param['StyleIDs'] = intval($this->input->post('id'));
                if ($param['StyleIDs'] < 1) {
                    $data['errno'] = 'id';
                    $data['error'] = '数据传递丢失,请刷新重新操作';
                    break;
                }
                $this->load->model('template_socket');
                if ($this->template_socket->delCssStyle($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '博客个人样式管理删除成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '博客个人样式管理删除失败';
                }
                break;
            case 'share':
                $param['StyleID'] = intval($this->input->post('StyleID'));
                $param['Name'] = htmlEncode($this->input->post('StyleName'));
                $param['FirstSortID'] = intval($this->input->post('FSort'));
                $param['SecondSortID'] = intval($this->input->post('SSort'));
                if ($param['StyleID'] < 1 || $param['FirstSortID'] < 1 || $param['SecondSortID'] < 1 || empty($param['Name'])) {
                    $data['errno'] = 'param';
                    $data['error'] = '数据传递丢失,请刷新重新操作';
                    break;
                }
                $param['IsShared'] = 1;
                $param['IsSharing'] = 1;
                $this->load->model('template_socket');
                $StyleID = $this->template_socket->aupdCssStyle($param);
                if ($StyleID > 0) {
                    $data['errno'] = 'success';
                    $data['error'] = '共享样式设置成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '共享样式设置失败';
                }
                break;
            default:
                $data['errno'] = 'action';
                $data['error'] = '您选择的操作不合法';
        }
        echo json_encode($data);
        exit;
    }

    function ShareList($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取用户使用的样式信息
        $this->load->model('template_socket');

        //获取用户自己的样式信息列表
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['IsShared'] = 1;
        $data['IsSharing'] = 1;
        $data['StartNo'] = 0;
        $data['QryCount'] = blogcssmaxhave;
        $usercsslist = $this->template_socket->getShareCssList($data);
        if ($usercsslist['RetRecords'] == 1) {
            $usercsslist['Record'] = array(0 => $usercsslist['Record']);
        }
        $extract['usercsslist'] = $usercsslist;

        //获取样式分类
        $sortidlist = array();
        foreach ($usercsslist['Record'] as $csslist) {
            $sortidlist[] = $csslist['FirstSortID'];
        }
        if (!empty($sortidlist)) {
            unset($data);
            $data['ShareIDs'] = join(',', $sortidlist);
            $data['StartNo'] = 0;
            $data['QryCount'] = count($sortidlist);
            $sortlist = $this->template_socket->getCssStyleSortList($data);
            if ($sortlist['RetRecords'] <= 1) {
                $sortlist['Record'] = isset($sortlist['Record']) ? array(0 => $sortlist['Record']) : false;
            }
            foreach ($sortlist['Record'] as $v) {
                $extract['sortlist'][$v['ShareID']] = $v['SortName'];
            }
            unset($sortlist);
        }

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['mysharelist'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['mysharelisttitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    //样式共享类型的设置
    function SetCssShareSort($domainname, $styleid) {
        $this->_checkLogin();
        $styleid = intval($styleid);
        $name = trim($this->input->get_post('name', TRUE));
        if ($styleid < 1 || empty($name)) {
            echo "参数信息传递丢失";
            exit;
        }
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'ShareDefineStyle');

        $extract['DomainName'] = $domainname;
        $extract['StyleID'] = $styleid;
        $extract['StyleName'] = $name;
        $extract['baseurl'] = &config_item('base_url');
        $this->load->view('module/setsharesort.shtml', $extract);
    }

    /**
     * Ajax调用
     * @ 设置自定义样式共享类型
     * */
    function CssSortList() {
        $this->_checkLogin();
        //获取系统样式一级分类
        $FSortID = intval($this->input->post('FSortID'));
        $FSelID = intval($this->input->post('FSelID'));
        $data['RetRecords'] = 0;
        if ($FSortID >= 0) {
            $param['FsortShareID'] = $FSortID;
            $param['StartNo'] = 0;
            $param['QryCount'] = 10;
            $this->load->model('template_socket');
            $data = $this->template_socket->getCssStyleSortList($param);
        }
        $str = '<option value="-1"">请选择分类</option>';

        if ($data['RetRecords'] > 1) {
            foreach ($data['Record'] as $val) {
                $str .= '<option value="' . $val['ShareID'] . '"';
                if ($FSelID == $val['ShareID'])
                    $str .= 'selected="selected"';
                $str .= '>' . $val['SortName'] . '</option>';
            }
        }
        else if ($data['RetRecords'] == 1) {
            $str .= '<option value="' . $data['Record']['ShareID'] . '"';
            if ($FSelID == $val['ShareID'])
                $str .= 'selected="selected"';
            $str .= '>' . $data['Record']['SortName'] . '</option>';
        }
        echo $str;
    }

    /**
     * 背景图片上传
     */
    function uploadSave() {

        $uploaddir = UPLOAD_FILE_LOCALPATH;
        if (!file_exists($uploaddir)) {
            x_mkdir($uploaddir, 0775);
        }
        if (!preg_match('/.*([jpg,gif,JPG,png,PNG,GIF])/', $_FILES['Filedata']["name"])) {
            $filedata = array(
                'result' => 'false',
                'name' => $_FILES['Filedata']["name"],
                'error' => '只限制jpg、gif、png图片上传',
                'size' => filesize($_FILES['Filedata']['tmp_name'])
            );
            echo json_encode($filedata);
            exit;
        }

        if (filesize($_FILES['Filedata']['tmp_name']) / 1024 > 500) {
            $filedata = array(
                'result' => 'false',
                'name' => $_FILES['Filedata']["name"],
                'error' => '只限制大小不超过500k的图片上传',
                'size' => filesize($_FILES['Filedata']['tmp_name'])
            );
            echo json_encode($filedata);
            exit;
        }

        $filename = date("Ymdhis") . rand(10000, 99999) . substr($_FILES['Filedata']["name"], strrpos($_FILES['Filedata']["name"], "."));
        $uploadfile = $uploaddir . '/' . $filename;
        $temploadfile = $_FILES['Filedata']['tmp_name'];
        $getsize = getimagesize($temploadfile);
        if ($getsize['0'] > 600 || $getsize['1'] > 330) {
            $imagetype = strtolower(substr($_FILES['Filedata']["name"], strrpos($_FILES['Filedata']["name"], ".") + 1));
            switch ($imagetype) {
                case 'jpg':
                    $source = imagecreatefromjpeg($temploadfile);
                    break;
                case 'jpeg':
                    $source = imagecreatefromjpeg($temploadfile);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($temploadfile);
                    break;
                case 'png':
                    $source = imagecreatefrompng($temploadfile);
                    break;
                default:
                    $filedata = array(
                        'result' => 'false',
                        'name' => $_FILES['Filedata']["name"],
                        'error' => '只限制jpg、gif、png图片上传',
                        'size' => filesize($_FILES['Filedata']['tmp_name'])
                    );
                    echo json_encode($filedata);
                    exit;
            }
            $width = $getsize['0'] > 600 ? 960 : $getsize['0'];
            $height = $getsize['1'] > 330 ? 330 : $getsize['1'];
            $image_p = imagecreatetruecolor($width, $height);
            imagecopyresampled($image_p, $source, 0, 0, 0, 0, $width, $height, $getsize['0'], $getsize['1']);
            imagejpeg($image_p, $uploadfile);
        } else {
            move_uploaded_file($temploadfile, $uploadfile);
        }
        $imgfile = 'blogbgpic/' . date('Ym') . '/' . $filename;
        ftpAttachment($imgfile, $uploadfile); //ftp同步图片
        $imgurl = 'http://img.cnfol.com/upload/' . $imgfile;
        $fileurl = config_item('base_url') . '/attached/' . $filename;
        if (!@fopen($fileurl, 'r')) {
            $fileurl = $imgurl;
        }
//返回数据  在页面上js做处理
        $filedata = array(
            'result' => 'true',
            'name' => $_FILES['Filedata']["name"],
            'filepath' => $fileurl,
            'imgurl' => $imgurl,
            'size' => filesize($_FILES['Filedata']['tmp_name'])
        );
        echo json_encode($filedata);
        exit;
    }

}

//end class
?>