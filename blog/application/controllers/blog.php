<?php

/* * **********************
 * 功能：   博客个人主页
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Blog extends MY_Controller {

    function Blog() {
        parent::MY_Controller();
    }

    /**
     * @ 个人博客主页 // $styleid 是做预览用
     * */
    function index($domainname, $styleid = 0) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //标志是否有管理权限
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);

        $this->_jumpIndex($extract['bloginfo']['UserID']);

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();

        //获取个人博客列表过滤掉被关闭的
        //$extract['bloglistfilter']=$this->_getBlogListFilter($extract['bloglist']);
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);


        /* --------统计各文章或各博客主页被访问次数--------------- */
        //$this->_hotBlogArticle(array('domainname'=>$domainname,'appearTime'=>'','articleID'=>'','guestType'=>$this->user['userid']),$extract['bloginfo']['UserID']);
        /* ----------------------------------------------------- */


        $num = $this->_getFriend($extract['bloginfo']['UserID']);
        $extract['friendsnumber'] = $num;

        $TransmitFlag = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        $extract['isFrends'] = $TransmitFlag;



        if ($styleid > 0) {
            $extract['blogconfig']['StyleID'] = $styleid;
        }

        //添加样式页面的预览
        $prevCnt = $this->input->post('PreviewContent');
        if ($prevCnt !== FALSE) {
            $extract['PreviewContent'] = $prevCnt;
        }
        $extract['layoutlist'] = $this->config->item('layout');
        $extract['sysmodules'] = $this->config->item('sysmodules');
        $extract['bglist'] = $this->config->item('bgurl');
        $extract['defaultcss'] = $this->config->item('defaultcss');
        $extract['default'] = $this->config->item('default');
        $Modules = array();
        $RModules = (trim($extract['blogconfig']['RModules']) != '') ? explode(',', $extract['blogconfig']['RModules']) : array();
        $MModules = (trim($extract['blogconfig']['MModules']) != '') ? explode(',', $extract['blogconfig']['MModules']) : array();
        $LModules = (trim($extract['blogconfig']['LModules']) != '') ? explode(',', $extract['blogconfig']['LModules']) : array();

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
                $Modules['lmods'][] = $extract['sysmodules'][$v][3];
            }
        }
        $extract['module'] = $Modules;
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '_' . $extract['bloginfo']['NickName'];

        //添加文章模块
        $extract['blogmess'] = array($extract['bloginfo']);
        $extract['isvalidate'] = $this->_checkValidate(1, $extract['bloginfo']['GroupID']); //验证是否要输入验证码

        $extract['systaglist'] = $this->config->item('sysTagList');
        $extract['imgbase'] = $this->config->item('estbase');
        $extract['imagesite'] = $this->config->item('imagesite');
        $extract['hssite'] = $this->config->item('hssite');
        $extract['tmp_editor'] = $blocks['addarticle'];
        $extract['tmp_taglist'] = $blocks['taglist'];

        $extract['show_renewvisitor'] = $blocks['show_renewvisitor'];
        $extract['show_newestcomment'] = $blocks['show_newestcomment'];
        $extract['showAffiche'] = $blocks['showAffiche'];
        $extract['recommend'] = $blocks['recommend'];
        $extract['showStat'] = $blocks['showStat'];
        $extract['pagesize'] = array('recommendlist' => '10');
        $extract['cuttrent_domainname'] = $domainname;
        //添加文章模块

        $extract['reperonalhead'] = $blocks['repersonalhead'];
        $extract['devpersonallogin'] = $blocks['devpersonallogin'];

        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['modulepath'] = &config_item('module_path');
        $extract['isconfig'] = 0;

        $extract['page'] = $this->input->get_post('pg') ? $this->input->get_post('pg') : 1;
        $extract['limit'] = 15;
        $extract['navConfig'] = 'myindex';
        $this->load->view('manage/MyIndex.html', $extract);
    }

    /**
     * @ 用户选择模板
     * */
    function sharecsslist() {
        $this->_checkLogin();
        $domainname = $this->input->get_post('domain');
        if (!preg_match('/[0-9a-zA-Z\_]+/', $domainname)) {
            cnfolAlert("您注册的博客地址非法");
            cnfolLocation();
            exit(-1);
        }

        //通过博客名获取博客信息	
        $data['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($data['bloginfo']['UserID']);

        //获取博客配置信息
        $data['blogconfig'] = $this->_getBlogConfig($data['bloginfo']['MemberID']);

        //获取系统定义的样式信息	
        $this->load->model('template_socket');
        $param['MemberID'] = 0; //0代表系统样式
        $param['IsShared'] = 1;
        $param['IsSharing'] = 1;
        if (intval($this->input->get_post('FSort')) > 0) {
            $param['FirstSortID'] = $this->input->get_post('FSort');
            $data['search']['fsortid'] = $param['FirstSortID'];
        }
        if (intval($this->input->get_post('SSort')) > 0) {
            $param['SecondSortID'] = $this->input->get_post('SSort');
            $data['search']['ssortid'] = $param['SecondSortID'];
        }
        $param['OrderBy'] = intval($this->input->get_post('rank'));
        $data['search']['orderby'] = $param['OrderBy'];

        $param['StartNo'] = -1;

        $tempCnt = $this->template_socket->getShareCssList($param);
        $page = intval($this->input->get_post('page'));
        $page = ($page < 1) ? 1 : $page;
        if ($page > ceil($tempCnt / cssstylepagesize)) {
            $page = 1;
        }
        $data['CurPage'] = $page;
        if ($tempCnt > 0) {
            //获取样式列表
            $param['StartNo'] = (cssstylepagesize * ($page - 1));
            $param['QryCount'] = cssstylepagesize;
            $data['tempList'] = $this->template_socket->getShareCssList($param);
            if (isset($data['tempList']['Record'])) {
                if ($data['tempList']['RetRecords'] == 1) {
                    $data['tempList']['Record'] = array('0' => $data['tempList']['Record']);
                }
            } else {
                $data['tempList'] = false;
            }
        } else {
            $data['tempList'] = false;
        }
        $data['channelTitle'] = '中金博客_系统模板样式列表 _ 中金在线';
        //翻页函数
        $this->load->library('pagebarsnew');
        $baseLink = config_item('base_url') . '/ShareCssList,domain=' . $domainname;
        $baseLink .= isset($param['FirstSortID']) ? '&FSort=' . $param['FirstSortID'] : '';
        $baseLink .= isset($param['SecondSortID']) ? '&SSort=' . $param['SecondSortID'] : '';
        $baseLink .= '&rank=' . $param['OrderBy'];

        $this->pagebarsnew->Page($tempCnt, $page, cssstylepagesize, $baseLink, '&page=');
        $data['pagebar'] = $this->pagebarsnew->upDownList();

        $data['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('channal/share_css_list.shtml', $data);
    }

    /* 好友关系处理 */

    function action() {
        $this->_checkUserlogin();

        $friendid = $this->input->get('friendIDs');
        $act = $this->input->get('act');
        $userid = $this->user['userid'];

        switch ($act) {
            case 'add':
                $act = 'mvfriend';
                $FType = '0';
                $apiurl = 'http://passport.cnfol.com/api/friendinfo/follow?';
                break;
            case 'del':
                $act = 'delfriend';
                $FType = '0';
                $apiurl = 'http://passport.cnfol.com/api/friendinfo/unfollow?';
                break;
            default:
                echo json_encode(array('errno' => '07', 'error' => '您还没选择所要的操作'));
                exit;
        }
        $curlpath = $apiurl . 'uid=' . $userid . '&fuid=' . $friendid;
        $result = curl_get($curlpath);

        if ($result == '添加关注成功！') {
            echo json_encode(array('errno' => '00', 'error' => '操作成功'));
            exit;
        } else if ($result == '取消关注成功！') {
            echo json_encode(array('errno' => '00', 'error' => '操作成功'));
            exit;
        } else if ($result == '不能关注自己！') {
            echo json_encode(array('errno' => '01', 'error' => '不能关注自己'));
            exit;
        } else if ($result == '非法访问！') {
            echo json_encode(array('errno' => '03', 'error' => '非法访问'));
            exit;
        } else if ($result == '参数错误！') {
            echo json_encode(array('errno' => '04', 'error' => '参数错误'));
            exit;
        } else if ($result == '您已经关注他了！') {
            echo json_encode(array('errno' => '05', 'error' => '您已经关注他了'));
            exit;
        } else {
            echo json_encode(array('errno' => '06', 'error' => '操作失败(' . $result . ')'));
            exit;
        }


        /*
          if($result == '1' || $result == '100'){
          echo json_encode(array('errno'=>'00','error'=>'操作成功'));exit;
          }elseif($result == '200'){
          echo json_encode(array('errno'=>'01','error'=>'不能关注自己'));exit;
          }elseif($result == '2'){
          echo json_encode(array('errno'=>'03','error'=>'非法访问'));exit;
          }elseif($result == '199'){
          echo json_encode(array('errno'=>'04','error'=>'参数错误'));exit;
          }elseif($result == '0'){
          echo json_encode(array('errno'=>'05','error'=>'您已经关注他了'));exit;
          }else{
          echo json_encode(array('errno'=>'06','error'=>'操作失败('.$result.')'));exit;
          }
         */
    }

}

//end class
?>