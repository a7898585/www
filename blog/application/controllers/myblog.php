<?php

class MyBlog extends MY_Controller {

    function __construct() {

        parent::__construct();
        $this->load->model("devfriend_socket");
        $this->pagesize = $this->config->item("showc");
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
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //echo $extract['viewurl'] 
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $extract['userinfo'] = $this->_getUserInfoByUid($extract['bloginfo']['UserID']);
        /* --------统计各文章或各博客主页被访问次数--------------- */
        //$this->_hotBlogArticle(array('domainname' => $domainname, 'appearTime' => '', 'articleID' => '', 'guestType' => $this->user['userid']), $extract['bloginfo']['UserID']);
        /* ------------------start粉丝，我关注的，相互关注数  ----------------------------------- */
        $extract['friendsnumber'] = $this->_getFriend($extract['bloginfo']['UserID']);


        $extract['isFrends'] = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        /* ------------------end粉丝，我关注的，相互关注数  ------------------------------------- */

        /* ----------start博主的文章数量----------------------------------- */
        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $extract['TotalArticle'] = $stat1['TotalArticle'];
        /* ------------end博主的文章数量------------------------------------ */
        $extract['user'] = $this->user;
        $extract['pagesize'] = $this->pagesize;
        $extract['userid'] = $extract['user']['userid'];
        $extract['blocks'] = &$this->config->item('block');
        //$extract['title'] = $extract['bloginfo']['BlogName'] . '_' . $extract['bloginfo']['NickName'];
        $extract['title'] = $extract['bloginfo']['NickName'] . '_' . $this->lang->language['keywords_article_cnfol_2'];
        $blocks = &$this->config->item('block');
        $extract['devmyblogloginheader'] = $blocks['devmyblogloginheader'];
        $extract['devmyblogcommonright'] = $blocks['devmyblogcommonright'];
        $extract['peronalfoot'] = $blocks['devmyblogcommonfooter'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['modulepath'] = &config_item('module_path');
        $extract['show_renewvisitor'] = $blocks['show_renewvisitor'];
        $extract['cuttrent_domainname'] = $domainname;



        //为我的博客页面keyword赋值
        $str = $this->getArtKeyWords($extract['bloginfo']['UserID'], $extract['bloginfo']['MemberID']);

        $extract['keywords2'] = $extract['bloginfo']['NickName'] . '_' . $this->lang->language['keywords_cnfol'] . '，' . $this->lang->language['keywords_article_cnfol_3'] . '，' . $str['tagStr'];
        $extract['description'] = $extract['bloginfo']['NickName'] . '的' . $this->lang->language['keywords_cnfol'] . '，' . $str['title'];


        /*
          $data['MemberID'] = $extract['bloginfo']['MemberID'];
          $this->load->model('blogarticle_socket');

          $data['StartNo'] = 0;
          $data['QryCount'] = 1;

          $keywords = $this->blogarticle_socket->getMemberArticleFirst($data);

          $extract['keywords2']=$extract['bloginfo']['NickName'].'_'.$keywords['Record']['Title'].'_'.$this->lang->language['keywords_cnfol'];
          $extract['description']=$extract['bloginfo']['NickName'].'_'.$keywords['Record']['Title'].'_'.$keywords['Record']['Summary'];
         */
        //为我的博客页面keyword赋值




        $extract['followUrl'] = $this->config->item("base_url") . "/{$domainname}/myfocus/friend";
        $extract['followedUrl'] = $this->config->item("base_url") . "/{$domainname}/myfocused/friend";
        $extract['articleUrl'] = $this->config->item("base_url") . "/{$domainname}/articlelist/alist";
        /*
         * 实名认真 -start
         */
        $params['UserID'] = $extract['bloginfo']['UserID'];
        $extract['auth'] = $this->user_socket->realNameAuth($params);

        /*
         * 实名认真 -end
         */

        $extract['tmp_jointly'] = $blocks['jointly'];
        $extract['navConfig'] = 'myblog';
        $extract['logintool'] = $blocks['logintool'];

        $extract['isonline'] = $this->isOnLine($extract['bloginfo']['UserID']); //判断是否在线
        //博客设置
        $extract['sysmodules'] = $this->config->item('sysmodules');
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

        $this->load->view('manage/myblog.shtml', $extract);
    }

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

    function createGroup() {
        $this->_checkUserlogin();
    }

    function ajaxAdd() {
        $this->_checkUserlogin();
        $userid = $this->user['userid'];
        $param['UserID'] = $userid;
        $rs = $this->devfriend_socket->addFriendTag($param);
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

    /*
     * 文章列表
     */

    function articleList($domainname) {
        //$this->_checkUserlogin();
        $this->user['userid'] = $this->_getUserID();
        //echo "<br>";
        //echo __LINE__;
        //echo "<br>";
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        //	$extract['viewurl']     = $this->_getviewURL($extract['bloginfo'],$extract['isowner'],'0');
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //获取参数

        $extract['ajaxlmcount'] = config_item('ajaxlmcount');
        $data['UserID'] = $this->input->get('currentid') != '' ? $this->input->get('currentid') : $this->user['userid'];
        $extract['UserID'] = $data['UserID'];
        $extract['loginUserID'] = $this->user['userid'];
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        $extract['page'] = $page;

        $this->load->model('blogarticle_socket');

        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['all'];

        if ($page > ceil($tmpCnt / MYBLOG_ARTICLE_PAGESIZE)) {
            $page = 1;
        }

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

            $data['StartNo'] = ($page - 1) * 2 * MYBLOG_MORE_PAGESIZE;

            $data['QryCount'] = MYBLOG_MORE_PAGESIZE;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);


            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            if (empty($extract['artlist']['Record'][0])) {
                $extract['artlist'] = false;
            }
        } else {
            $extract['artList'] = false;
        }
//error_log(PHP_EOL .print_r($extract['artlist'], true) . date('H:i'), 3, '/home/html/logs/alist.log');
        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];

        $baseLink = config_item('base_url') . '/' . $domainname . '?page=';

        $notLoad = '';

        //echo($page.'|'.$tmpCnt.'|'.$data['StartNo'].'|'.MYBLOG_MORE_PAGESIZE);
        if ($tmpCnt == ($data['StartNo'] + MYBLOG_MORE_PAGESIZE) || $tmpCnt < ($data['StartNo'] + MYBLOG_MORE_PAGESIZE)) {
            $this->load->library('pagebarsnew');

            $this->pagebarsnew->Page($tmpCnt, $page, '30', $baseLink, '/');
            $extract['page'] = $page;
            $extract['pagebar'] = str_replace('=/', '=', $this->pagebarsnew->upDownList());
            $extract['pagebar'] = '<div class="Page">' . $extract['pagebar'] . '</div>';
            $notLoad = '<script>$("#loadCrontal").val("0");$(".Page").show();</script>';
        }

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['notLoad'] = $notLoad;

        $this->load->view('article/devmybolg_articlelist.shtml', $extract);
    }

    function articleListConfig($domainname) {
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        //	$extract['viewurl']     = $this->_getviewURL($extract['bloginfo'],$extract['isowner'],'0');
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //获取参数

        $extract['ajaxlmcount'] = config_item('ajaxlmcount');
        $data['UserID'] = $this->input->get('currentid') != '' ? $this->input->get('currentid') : $this->user['userid'];
        $extract['UserID'] = $data['UserID'];
        $extract['loginUserID'] = $this->user['userid'];
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        $extract['page'] = $page;

        $this->load->model('blogarticle_socket');

        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['all'];

        if ($page > ceil($tmpCnt / 10)) {
            $page = 1;
        }

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

            $data['StartNo'] = 0;

            $data['QryCount'] = 10;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);

            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            if (empty($extract['artlist']['Record'][0])) {
                $extract['artlist'] = false;
            }
        } else {
            $extract['artList'] = false;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];

        $baseLink = config_item('base_url') . '/' . $domainname . '?page=';

        $notLoad = '';

        if ($tmpCnt == ($data['StartNo'] + FRIEND_GROUP_BUTTON_NUM) || $tmpCnt < ($data['StartNo'] + FRIEND_GROUP_BUTTON_NUM)) {
            $this->load->library('pagebars');

            $this->pagebars->Page($tmpCnt, $page, '30', $baseLink, '/');
            $extract['page'] = $page;
            $extract['pagebar'] = str_replace('=/', '=', $this->pagebars->upDownList());
            $notLoad = '<script>$("#loadCrontal").val("0");$(".Page").show();</script>';
        }

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['notLoad'] = $notLoad;
        $extract['dragable'] = 1;
        $this->load->view('manage/articlelist.shtml', $extract);
    }

    /*
     * ajax调用更多内容
     */

    function ajaxMoreArticle($domainname) {

        //$this->_checkUserlogin();
        $this->user['userid'] = $this->_getUserID();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);

        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //获取参数

        $extract['ajaxlmcount'] = config_item('ajaxlmcount');
        $data['UserID'] = $this->input->get('currentid') != '' ? $this->input->get('currentid') : $this->user['userid'];
        $extract['UserID'] = $data['UserID'];
        $extract['loginUserID'] = $this->user['userid'];

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        $this->load->model('blogarticle_socket');
        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);

        //$tmpCnt = $tempInfo['TtlRecords'];
        $tmpCnt = $tempInfo['all'];

        if ($page > ceil($tmpCnt / MYBLOG_ARTICLE_PAGESIZE)) {
            $page = 1;
        }

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

            $data['StartNo'] = ($page * 2 - 1) * MYBLOG_MORE_PAGESIZE;
            $data['QryCount'] = MYBLOG_MORE_PAGESIZE;
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);


            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            if (empty($extract['artlist']['Record'][0])) {
                $extract['artlist'] = false;
                echo json_encode(array('data' => ''));
                return;
            }
        } else {
            $extract['artList'] = false;

            $html.='<script>$(".LoadingBox").html("系统繁忙，稍后再试");</script>';
            echo($html);
            exit;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];

        $baseLink = config_item('base_url') . '/' . $domainname . '?page=';

        $this->load->library('pagebarsnew');

        $this->pagebarsnew->Page($tmpCnt, $page, '30', $baseLink, '/');
        $extract['page'] = $page;
        $extract['pagebar'] = str_replace('=/', '=', $this->pagebarsnew->upDownList());
        $extract['pagebar'] = '<div class="Page">' . $extract['pagebar'] . '</div>';
        if ($tmpCnt < 31) {
            unset($extract['pagebar']);
        }

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');

        $extract['hide'] = '1';

        $html = $this->load->view('manage/articlelist.shtml', $extract, true);
        $html.='<script>$("#moreartlistMain").show();$(".LoadingBox").hide();$(".Page").show();</script>';

        echo json_encode(array('data' => $html));
    }

}

?>