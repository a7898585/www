<?php

/* * **********************
 * 功能：   博客对外接口
 * author： lifeng
 * *********************** */

class Blogapi extends MY_Controller {

    public function __construct() {
        parent::MY_Controller();
    }

    /*
      | 昵称同步
      +---------------------------------------
      | @param   null
     */

    function SetNickName() {
        $UserID = $this->input->post('userid', true);
        $NickName = $this->input->post('nickname');
        $VerifyStr = $this->input->post('verifystr', true);
        $ModKey = 'wgk6imzyaB+6';

        if (!$UserID) {
            echo json_encode(array('flag' => '1', 'info' => '无效参数：UserID'));
            exit;
        }

        if (!$NickName) {
            echo json_encode(array('flag' => '1', 'info' => '无效参数：NickName'));
            exit;
        }

        if ($VerifyStr != md5("userid=$UserID&nickname=$NickName&modkey=$ModKey")) {
            echo json_encode(array('flag' => '1', 'info' => '非法请求'));
            exit;
        }

        $this->load->model('memberblog_socket');
        $rs = $this->memberblog_socket->updateBlogNickName(array('UserID' => $UserID, 'NickName' => urldecode($NickName)));

        if (!$rs) {
            echo json_encode(array('flag' => '2', 'info' => '用户不存在'));
            exit;
        }

        echo json_encode(array('flag' => '0', 'info' => '成功'));
        exit;
    }

    /*
      | 用户中心昵称同步
      +---------------------------------------
      | @param   null
     */

    function updateNickName() {
        $UserID = $this->input->get_post('userid', true);
        $NickName = $this->input->get_post('nickname');
        $VerifyStr = $this->input->get_post('verifystr', true);
        $ModKey = 'newblogUpdateNickName';
        if (!$UserID) {
            echo json_encode(array('flag' => '1', 'info' => '无效参数：UserID'));
            exit;
        }
        if (!$NickName) {
            echo json_encode(array('flag' => '1', 'info' => '无效参数：NickName'));
            exit;
        }
        if ($VerifyStr != md5("userid=$UserID&nickname=$NickName&modkey=$ModKey")) {
            echo json_encode(array('flag' => '1', 'info' => '非法请求'));
            exit;
        }

        $this->load->model('memberblog_socket');
        $rs = $this->memberblog_socket->updateBlogNickName(array('UserID' => $UserID, 'NickName' => urldecode($NickName)));
//        error_log(PHP_EOL . $UserID . '----/' . $NickName . '/' . print_r($rs, true) . '----/' . date('Y-m-d H:i'), 3, '/home/html/logs/updatenikename.log');

        if (!$rs) {
            echo json_encode(array('flag' => '2', 'info' => '用户不存在'));
            exit;
        }

        echo json_encode(array('flag' => '0', 'info' => '成功'));
        exit;
    }

    /*
      | 获取博客名
      +---------------------------------------
      | @param   null
     */

    function getDomainNameByUid() {
        $userid = (int) $this->input->get_post('userid', true);
        $return = $this->_getBlogListByUid($userid);

        if (is_array($return['Record']['0'])) {
            $bloglist = $return['Record'];
        } else {
            $bloglist = array($return['Record']);
        }

        $count = count($bloglist);
        if ($count >= 1) {
            foreach ($bloglist as $k => $v) {
                $temp[$v['MemberID']] = $v['DomainName'];
            }
            array_multisort($temp, SORT_ASC, SORT_NUMERIC);
            echo $temp['0'];
        } else {
            echo '';
        }
    }

    function GetBlogName() {
        $DomainName = $this->input->get_post('DomainName', true);

        if ($DomainName == '') {
            echo json_encode(array('erron' => '02', 'error' => '缺少参数：DomainName'));
            exit;
        }

        $data['QryData'] = $DomainName;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        if (!$bloginfo) {
            echo json_encode(array('erron' => '01', 'error' => '不存在的博客'));
            exit;
        } else {
            echo json_encode(array('erron' => '00', 'error' => $bloginfo['BlogName']));
            exit;
        }
    }

    /**
     * 修改文章 Property 状态，基于活动的支持
     * 0，缺省；1，原创；2，转载；3，那些年征文活动；
     * author lifeng
     * date 2012-5-21
     */
    function SetArticleStat() {
        $ArticleID = $this->input->get_post('ArticleID', true);
        $DomainName = $this->input->get_post('DomainName', true);
        $AppearTime = $this->input->get_post('AppearTime', true);
        $Property = $this->input->get_post('Property', true);
        $VerifyCode = $this->input->get_post('VerifyCode', true);

        if ($VerifyCode != getVerifyStr($DomainName . $AppearTime . $ArticleID . $Property)) {
            echo json_encode(array('erron' => '06', 'error' => '非法请求！'));
            exit;
        }

        $AppearTime = date("Y-m-d H:i:s", $AppearTime);
        if (!preg_match("/^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$/i", $AppearTime)) {
            echo json_encode(array('erron' => '01', 'error' => '缺少参数：AppearTime'));
            exit;
        }

        if ($DomainName == '') {
            echo json_encode(array('erron' => '02', 'error' => '缺少参数：DomainName'));
            exit;
        }

        if (!is_numeric($ArticleID)) {
            echo json_encode(array('erron' => '03', 'error' => '缺少参数：ArticleID'));
            exit;
        }

        if (!is_numeric($Property)) {
            echo json_encode(array('erron' => '04', 'error' => '缺少参数：Property'));
            exit;
        }

        $data['QryData'] = $DomainName;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        if (!$bloginfo || $bloginfo['Status'] == 1 || $bloginfo['Status'] == 2) {
            echo json_encode(array('erron' => '05', 'error' => '您访问的博客不存在或被管理员关闭'));
            exit;
        }

        $param['ArticleID'] = $ArticleID;
        $param['MemberID'] = $bloginfo['MemberID'];
        $param['AppearTime'] = $AppearTime;
        $param['Property'] = $Property;
        $this->load->model('blogarticle_socket');
        $Status = $this->blogarticle_socket->modBlogArticle($param);
        error_log(date('YmdHis') . var_export($param, true) . var_export($Status, true) . PHP_EOL, 3, '/home/www/html/logs/blogapi_SetArticleStat_' . date('Ymd') . '.log');
        if ($Status['Code'] == '00') {
            echo json_encode(array('erron' => '00', 'error' => 'success'));
            exit;
        } else {
            echo json_encode(array('erron' => $Status['Code'], 'error' => 'gw error!'));
            exit;
        }
    }

    function ApSearch() {
        $searchValue = $this->input->get_post('content');
        $page = intval($this->input->get_post('page'));

        $this->load->model('channel_socket');

        $param['TagName'] = $searchValue;
        $data['TolCnt'] = $this->channel_socket->getSearchTagArticle($param);
        $pagesize = 30;
        if ($data['TolCnt'] > 0) {
            $page = ($page < 1 || ($page > ceil($data['TolCnt'] / $pagesize))) ? 1 : $page;

            $param['StartNo'] = ($page - 1) * $pagesize;
            $param['QryCount'] = $pagesize;
            $data['SearchRes'] = $this->channel_socket->getSearchTagArticle($param);
        }

        $baseLink = config_item('base_url') . '/ap/stock/' . urlencode($searchValue);
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($data['TolCnt'], $page, $pagesize, $baseLink, '/');

        $extract['pagebar'] = $this->pagebarsnew->upDownList();
        $extract['Result'] = $data['SearchRes'];
        $extract['searchValue'] = $searchValue;

        $this->load->view('/blogapi/ApSearchArticle.shtml', $extract);
    }

    function ApBlogList() {
        $domainname = $this->input->get_post('domainname', true);

        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner'], '0');
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取参数

        $type = $this->input->get_post('type');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        $this->load->model('blogarticle_socket');
        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['TtlRecords'];
        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);

            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }
        } else {
            $extract['artList'] = false;
        }
        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        //翻页信息
        $baseLink = config_item('base_url') . $this->input->server('REQUEST_URI');
        $baseLink = substr($baseLink, 0, strrpos($baseLink, '/'));
        if (!preg_match('/.*list$/', $baseLink)) {
            $baseLink .= '/list';
        }
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');

        $extract['title'] = $extract['bloginfo']['BlogName'] . $blocks['articlelisttitle'] . '-' . $extract['bloginfo']['NickName'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 2;


        $this->load->view('/blogapi/ApBlogList.shtml', $extract);
    }

    //获取博客统计
    function ApBlogStat() {
        $memberid = $this->input->get_post('mid');
        $this->load->model('memberblog_socket');
        $data['MemberIDs'] = $memberid;
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $stat2 = $this->memberblog_socket->getMemberBlogStatByCache($data);

        $str = '访问总计:<span id="s_mtclick">' . (empty($stat2['TotalClick']) ? $stat1['Totalvisit'] : $stat2['TotalClick']) . '</span>
			今日访问:<span id="s_mdclick">' . (empty($stat2['TodayClick']) ? $stat1['TodayVisit'] : $stat2['TodayClick']) . '</span>
			文章总计:' . $stat1['TotalArticle'] . '
			评论总计:' . $stat1['TotalComment'];
        echo $str;
    }

    function ApCommentList() {
        $articleid = $this->input->get_post('artid');
        $page = intval($this->input->get_post('page'));
        $pagesize = $this->input->get_post('num');
        $pagesize = ($pagesize > 50 || $pagesize < 5) ? commonpagesize : $pagesize;

        $data['BlogType'] = 1;
        $data['ArticleID'] = $articleid;
        $data['StartNo'] = -1;
        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtCommentList($data);
        $tmpCnt = $tempInfo['TtlRecords'];

        $page = (is_int($page) && $page > 0) ? $page : 1;
        $page = ($page <= ceil($tmpCnt / $pagesize)) ? $page : 1;

        $data['StartNo'] = ($page - 1) * $pagesize;
        $i = $tmpCnt - ($page - 1) * $pagesize;
        $j = ($page - 1) * $pagesize + 1;
        $data['QryCount'] = $pagesize;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $CommentResult = $this->articlecomment_socket->getArtCommentList($data);
        $CommentResult['Record'] = ($CommentResult['RetRecords'] == 1) ? array('0' => $CommentResult['Record']) : $CommentResult['Record'];
        $extract['CommentResult'] = $CommentResult;

        //翻页函数
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $pagesize, '', '');
        $replaceid = $this->input->get_post('replaceid');
        $extract['pagebars'] = $this->pagebarsnew->upDownListAjax($replaceid, 'updateCommentPage');

        $this->load->view('/blogapi/AjaxApArticleCommentList.shtml', $extract);
    }

    function ApArticle() {
        $this->_checkUserlogin();
        $CommentID = intval($this->input->get_post('commentid'));
        $domainname = $this->input->get_post('domainname');
        $artstr = $this->input->get_post('articleid');

        if (strpos($artstr, '-') === false) {
            $appearTime = '2011-01-01 00:00:00';
            $articleID = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID = $temp[1];
        }

        $articleID = intval($articleID);

        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);

        //获取该博客的文章信息
        if ($articleID != 0) {
            $data['MemberID'] = $extract['bloginfo']['MemberID'];
            $data['ArticleID'] = $articleID;
            $data['AppearTime'] = $appearTime;

            if ($extract['isowner']) {
                $data['IsChecked'] = 0;  //博主0, 其他用户1
            }
            $this->load->model('blogarticle_socket');

            $extract['article'] = $this->blogarticle_socket->getBlogArticleByID($data);

            if ($extract['article']['GiftPrice'] > 0) { //非博主，礼物数大于0，进行鲜花判断
                if ($this->user['userid'] < 0 || empty($this->user['userid'])) {
                    echo('鲜花文章，须登录购买后才能查看');
                    exit;
                }
                //http://passport.cnfol.com/giftapi/checkGift?UserID=355031&GiftID=1&GiftCnt=100&SourceTypeID=1&SourceTabID=255
                $param = 'UserID=' . $this->user['userid'];
                $param .= '&GiftID=1';
                $param .= '&GiftCnt=' . $extract['article']['GiftPrice'];
                $param .= '&SourceTypeID=1';
                $param .= '&SourceTabID=' . $extract['article']['ArticleID'];
                $extract['article']['CheckGift'] = file_get_contents('http://passport.cnfol.com/giftapi/checkGift?' . $param);

                if ($extract['article']['GiftPrice'] > 0 && $extract['article']['CheckGift'] != 1) {
                    echo('鲜花文章，须购买后才能查看');
                    exit;
                }
                unset($param);
            }

            if (empty($extract['article'])) {
                echo '该文章信息不存在，请查看其他文章';
                exit(-1);
            }

            //处理标签
            $extract['tag'] = array();
            $tmptagids = explode(',', $extract['article']['TagID']);
            $tmptags = explode(',', $extract['article']['TagName']);
            foreach ($tmptagids as $key => $tagid) {
                if (isset($tmptags[$key]) && trim($tmptags[$key]) != "")
                    $extract['tag'][] = array($tagid, $tmptags[$key]);
            }
            unset($param);
            $param['ArticleID'] = $data['ArticleID'];
            $param['MemberID'] = $extract['bloginfo']['MemberID'];
            $param['AppearTime'] = $data['AppearTime'];
            //获取文章统计
            $extract['articlestat'] = $this->blogarticle_socket->getBlogArticleStatByID($param);
        }

        $extract['article']['Title'] = filter($extract['article']['Title']);
        $extract['article']['Content'] = filter($extract['article']['Content']);

        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        $extract['modulepath'] = config_item('module_path');

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['article']['Title'] . '-' . $extract['bloginfo']['BlogName'] . '_' . $extract['bloginfo']['NickName'];
        $extract['baseurl'] = &config_item('base_url');

        //获取评论最后一页页码
        $data2['BlogType'] = 1;
        $data2['ArticleID'] = $articleID;
        $data2['StartNo'] = -1;
        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtCommentList($data2);
        $tmpCnt = $tempInfo['TtlRecords'];
        $extract['lastpage'] = intval($tmpCnt / $extract['blogconfig']['CommentNumber']) + 1;

        if (isset($CommentID) && $CommentID > 0) { //从后台点击某评论跳转到文章页时，当前的评论id
            $extract['curcid'] = $this->articlecomment_socket->getPageNoByCommentID($articleID, $CommentID, $extract['blogconfig']['CommentNumber']);
        } else {
            $extract['curcid'] = 0;
        }

        if ($extract['curcid'] > 0) {
            $extract['lastpage'] = $extract['curcid'];
        }

        $this->load->view('/blogapi/ApArticle.shtml', $extract);
    }

    function ApArticleByID() {
        $data['ArticleID'] = intval($this->input->get_post('articleid'));
        $this->load->model('blogarticle_socket');
        $extract['article'] = $this->blogarticle_socket->getApBlogArticleByID($data);

        if (empty($extract['article'])) {
            echo '该文章信息不存在，请查看其他文章';
            exit(-1);
        }


        //处理标签
        $extract['tag'] = array();
        $tmptagids = explode(',', $extract['article']['TagID']);
        $tmptags = explode(',', $extract['article']['TagName']);
        foreach ($tmptagids as $key => $tagid) {
            if (isset($tmptags[$key]) && trim($tmptags[$key]) != "")
                $extract['tag'][] = array($tagid, $tmptags[$key]);
        }

        $extract['article']['Title'] = filter($extract['article']['Title']);
        $extract['article']['Content'] = filter($extract['article']['Content']);

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['article']['Title'];
        $extract['baseurl'] = &config_item('base_url');

        //获取评论最后一页页码
        $data2['BlogType'] = 1;
        $data2['ArticleID'] = $articleID;
        $data2['StartNo'] = -1;
        $this->load->model('articlecomment_socket');
        $tempInfo = $this->articlecomment_socket->getArtCommentList($data2);
        $tmpCnt = $tempInfo['TtlRecords'];
        $extract['lastpage'] = intval($tmpCnt / 10) + 1;

        if (isset($CommentID) && $CommentID > 0) { //从后台点击某评论跳转到文章页时，当前的评论id
            $extract['curcid'] = $this->articlecomment_socket->getPageNoByCommentID($articleID, $CommentID, $extract['blogconfig']['CommentNumber']);
        } else {
            $extract['curcid'] = 0;
        }

        if ($extract['curcid'] > 0) {
            $extract['lastpage'] = $extract['curcid'];
        }

        $this->load->view('/blogapi/ApArticle.shtml', $extract);
    }

    function ApRemmendList($recommend) {
        $recommend = intval($recommend);
        //推荐值不属于2-3则提示错误
        if (($recommend != 2) && ($recommend != 3)) {
            cnfolAlert('您要访问的推荐文章信息不存在');
            cnfolLocation();
            exit;
        }
        $data['Recomend'] = $recommend;
        $limitgroups = &config_item('limitgroups');
        if (FALSE != $limitgroups) {
            $data['MemberGroups'] = $limitgroups;
        }
        $pagelimit = 60; //最多翻页60
        $page = intval($this->input->get_post('page'));
        $page = ($page > $pagelimit || $page < 1) ? 1 : $page;
        $data['StartNo'] = channelarticlepagesize * ($page - 1);
        $data['QryCount'] = channelarticlepagesize;
        $this->load->model('channel_socket');

        $extract['TagArtList'] = $this->channel_socket->getRecommendArticle($data);
        $extract['TagTitle'] = ($recommend == 2) ? '名家看市' : '高手看盘';
        $extract['channelTitle'] = $extract['TagTitle'] . '文章列表 _ 中金在线';
        $extract['RssURL'] = config_item('base_url') . '/rss/r' . $recommend . 'List.xml';

        //翻页信息
        $baseLink = config_item('base_url') . '/r' . $recommend . 'List/';
        $this->load->helper('channal');
        $extract['pagebar'] = drawpagebar($data['QryCount'] * $pagelimit, $page, $data['QryCount'], $baseLink);

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['header'] = $blocks['channalhead'];
        $extract['footer'] = $blocks['channalfoot'];
        $extract['shtml'] = $this->config->item('shtml_path');
        $extract['baseurl'] = $this->config->item('base_url');
        $extract['TagID'] = $recommend;

        $this->load->view('blogapi/ApRemmendList.shtml', $extract);
    }

    /* 获取系统分类列表 */

    function getSysTagList() {
        $systaglist = &config_item('sysTagList');
        echo json_encode($systaglist);
    }

    /* 博文推送接口 */

    function postArticleData() {
        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $userid = intval($this->input->get_post('userid'));
        if (!is_int($userid) || $userid <= 0) {
            $data['errno'] = 'userid';
            $data['error'] = '缺少userid';
            echo json_encode($data);
            exit;
        }

        //签名
        $flashcode = $this->input->post("flashcode");
        //error_log(date('Y-m-d H:i:s').$userid.'||'.$flashcode.'||'.md5('weibo2blog'.$userid).PHP_EOL,3,'/home/www/html/logs/flashcode.log');
        if ($flashcode != md5('weibo2blog' . $userid)) {
            $data['errno'] = 'verify';
            $data['error'] = '数据请求异常，拒绝服务';
            echo json_encode($data);
            exit;
        }

        $addtime = $mcache->get('Article_Add_User_' . $userid);
        $this->load->model('memberblog_socket');
        if (!empty($addtime) && (((time() - $addtime['LastTime'] ) <= addarticletime) && ($addtime['IsClose'] == 0))) {
            $addtime['IsClose'] = 1;
            $mcache->set('Article_Add_User_' . $userid, $addtime, 5);
            $data['errno'] = 'ipfilter';
            $data['error'] = '系统发现您的博客异常，您的IP已被封闭' . $userid;
            echo json_encode($data);
            exit;
        }

        //获取最近创建的博客
        $result = $this->memberblog_socket->getMemberBlogListByUserID(array('QryData' => $userid, 'StartNo' => 0, 'QryCount' => 10));
        if (empty($result) || empty($result['Record']) || $result['RetRecords'] == '0') {
            $data['errno'] = 'checkblog';
            $data['error'] = '您还未开通博客！';
            echo json_encode($data);
            exit;
        }
        if ($result['RetRecords'] > 1) {
            $bloglist = array_sort($result['Record'], 'MemberID', 'desc');
            $bloginfo = $bloglist[0];
        } else {
            $bloginfo = $result['Record'];
        }
        unset($result);
        $MemberID = $bloginfo['MemberID'];

        //标签的处理
        $tagStr = $this->input->post('tag', true);
//        error_log(date('YmdHis') . '||' . $tagStr . '||', 3, '/home/www/html/logs/tagstr2.log');
        $tagStr = htmlspecialchars(strip_tags(trim($tagStr)));

        if (!empty($tagStr)) {
            $tags = explode(',', $tagStr);
            $tags = is_string($tags) ? array(0 => $tags) : $tags;
            $tag = array_unique($tags);
            $tagorder = array();
            if (count($tags) > eacharticletaglimit) {
                $data['errno'] = 'tagerr';
                $data['error'] = '每篇文章最多允许有5个标签';
                echo json_encode($data);
                exit;
            }
            foreach ($tags as $key => $tag) {
                if (strlen($tag) > eachtaglengthlimit) {
                    $data['errno'] = 'tagerr';
                    $data['error'] = '单个标签长度应该在' . (eachtaglengthlimit / 3) . '个字以内';
                    echo json_encode($data);
                    exit;
                } else if (strlen(trim($tag)) == 0) {
                    unset($tags[$key]);
                }
                $tagorder[] = 0;
            }

            $this->load->model('articletags_socket');
            $param['UserIDs'] = $userid;
            $param['StartNo'] = -1;

            $TtlRecords = $this->articletags_socket->getArticleTagList($param);
            if (articletagcntlimit < $TtlRecords) {
                $data['errno'] = 'tagerr';
                $data['error'] = '您使用的标签数目已经超出了' . articletagcntlimit . '个的限制，请删除无效标签！';
                echo json_encode($data);
                exit;
            }
            unset($param);
            $param['UserID'] = $userid;
            $param['OrderNos'] = join(',', $tagorder);
            $param['TagNames'] = join(',', $tags);
            error_log(print_r($param, true) . PHP_EOL, 3, '/home/www/html/logs/tagstr2.log');
            $tagList = $this->articletags_socket->addArticleTag($param);
            if ($tagList == false) {
                $data['errno'] = 'savetag';
                $data['error'] = '标签保存失败';
                echo json_encode($data);
                exit;
            }
            $tagList = (isset($tagList['TagID'])) ? array(0 => $tagList) : $tagList;
            foreach ($tagList as $val) {
                $tagIDs[] = $val['TagID'];
            }
            $tagStr = join(',', $tagIDs);
        }


        /* $titlesign = $mcache->get('Article_Add_Title_'.$MemberID);
          $tTitle    = htmlEncode(addslashes($this->input->post('title')));
          $titlecode = md5($tTitle);
          if($titlesign == $titlecode)
          {
          $data['errno']	=	'title';
          $data['error']	=	'在'.(addarticlelimittime/60).'分钟内不可发表相同文章！';
          echo json_encode($data);
          exit;
          }else
          {
          //记录文件标题
          $mcache->set('Article_Add_Title_'.$MemberID, $titlecode, addarticlelimittime);
          }
          unset($titlesign, $titlecode,$tTitle); */


        unset($param);
        $param['SysTagID'] = intval($this->input->post('systagid'));
        if (!is_int($param['SysTagID']) || !array_key_exists($param['SysTagID'], config_item('sysTagList'))) {
            $param['SysTagID'] = 1459;
        }

        $param['Recommend'] = 0;
        $param['IsUsed'] = 0;
        $param['Status'] = 1; //默认博文不显示在频道页和首页  (1为不显示，0为显示)
        $param['IsDel'] = 0;

        //先审后发这里调整 0-否(預設)， 1-垃圾箱用户自己删除   2–待審核，重点监控  3-彻底删除后台删除  4-系统回收站（系统自动删除）不显示  5-优先审核
        $isby = file_get_contents(URL_CRONTAB . '/expansion/CheckStatus/banperiods.txt');
        $isby = isset($isby) ? $isby : 0;

        if ($isby == 1) {
            $param['IsDel'] = 2;
        }

        //用户组信息的处理
        $groups = trim($bloginfo['GroupID'], ',');
        if ($groups != "") {
            $recommend = config_item('recommendgroup');
            $limittag = config_item('limittags');
            $isuse = config_item('isuse');
            $groups = explode(',', $groups);
            $groups = (is_string($groups)) ? array(0 => $groups) : $groups;

            foreach ($groups as $grp) {
                if (isset($recommend[$grp])) {
                    if (!in_array($param['SysTagID'], $limittag)) {
                        $param['Recommend'] = $recommend[$grp];
                    }
                    $param['IsUsed'] = 0;
                    $param['Status'] = 0;
                    $param['IsDel'] = 0;
                    $isVIP = true;
                } else if ($grp == $isuse) {
                    $param['Recommend'] = 0;
                    $param['IsUsed'] = 1;
                    $param['Status'] = 0;
                    $isVIP = true;

                    if ($isby == 1) {
                        $param['IsDel'] = 5;
                        $param['Status'] = 1;
                    }
                } else if ($grp == config_item('adgroup')) {
                    $param['SysTagID'] = config_item('adgrouptagid');
                } else if ($grp == config_item('autoaudit')) {
                    $param['Status'] = 0;
                    $isVIP = true;
                    if ($isby == 1) {
                        $param['IsDel'] = 5;
                        $param['Status'] = 1;
                    }
                }
            }
        }

        //参数处理
        $param['MemberID'] = $MemberID;
        $param['ArticleID'] = 0;
        $param['Title'] = $this->input->post('title');
        $param['Content'] = $this->input->post('content');
        $param['Summary'] = $this->input->post('summary');

        $param['Property'] = 0;
        $param['ReadStatus'] = intval($this->input->post('readStatus'));
        $param['ReadStatus'] = (is_int($param['ReadStatus'])) ? $param['ReadStatus'] : 0;
        $param['SelfRecommend'] = intval($this->input->post('memberRecommend'));
        $param['SelfRecommend'] = (is_int($param['SelfRecommend'])) ? $param['SelfRecommend'] : 0;
        $param['SortID'] = intval($this->input->post('sortId'));
        $param['SortID'] = (is_int($param['SortID'])) ? $param['SortID'] : 18295;
        $param['IP'] = $this->input->ip_address();

        $param['TagIDs'] = $tagStr;
        $param['TrackBack'] = '';
        $param['Summary'] = (trim($param['Summary']) == "") ? getsummary($param['Content'], limitsumautolen, 1) : $param['Summary'];

        $param['LastCommentDate'] = date('Y-m-d H:i:s');
        $param['GiftPrice'] = $this->input->post('GiftPrice') != false ? intval($this->input->post('GiftPrice')) : 0;


        if (strlen($param['Title']) > limitarticlemaxtitlelen || strlen($param['Title']) < limitarticlemintitlelen) {
            $data['errno'] = 'title';
            $data['error'] = '文章标题长度应该在' . limitarticlemintitlelen . '-' . limitarticlemaxtitlelen . '个字节之内';
            $mcache->delete('Article_Add_Title_' . $MemberID);
            echo json_encode($data);
            exit;
        } else if (strlen($param['Content']) > limitarticlecontentmaxlen || strlen($param['Content']) < limitarticlecontentminlen) {
            $data['errno'] = 'content';
            $data['error'] = '文章内容长度应该在' . limitarticlecontentminlen . '-' . limitarticlecontentmaxlen . '个字节之内';
            $mcache->delete('Article_Add_Title_' . $MemberID);
            echo json_encode($data);
            exit;
        } else if ((strlen($param['Summary'])) > limitsumautolen + 500) {
            $data['errno'] = 'summary';
            $data['error'] = '摘要长度应该在' . limitsumautolen . '个字以内';
            $mcache->delete('Article_Add_Title_' . $MemberID);
            echo json_encode($data);
            exit;
        } else if ($param['SysTagID'] == 0) {
            $data['errno'] = 'tagId';
            $data['error'] = '请选择文章分类';
            $mcache->delete('Article_Add_Title_' . $MemberID);
            echo json_encode($data);
            exit;
        } else {
            //临时变量
            $tmpp['Content_c'] = $param['Content'];
            $tmpp['Summary_c'] = $param['Summary'];

            $param['Title'] = htmlEncode($param['Title']);
            $param['Content'] = htmlEncode($param['Content']);
            $param['Summary'] = htmlEncode($param['Summary']);
            $param['IsUTOP'] = 0;

            $this->load->model('blogarticle_socket');
            $Status = $this->blogarticle_socket->addBlogArticle($param, $tmpp);

            if (empty($Status)) {
                $data['errno'] = 'empty';
                $data['error'] = '文章保存失败.';
                $mcache->delete('Article_Add_Title_' . $MemberID);
                echo json_encode($data);
                exit;
            }

            if (!isset($Status['Record']['ArticleID'])) {
                $Status['Record']['ArticleID'] = intval($Status['Description']);
            }

            if ($Status['Code'] == '00') {
                //积分设置
                $args['UserID'] = $userid;
                $args['RewardEName'] = 'addarticle';
                $args['Type'] = 1;
                $this->user_socket->addUserPoint($args);
                unset($args);

                $log_str = '--suc--|MemberID：' . $param['MemberID'] . '|' . $_SERVER['REMOTE_ADDR'] . ' | IsUsed=' . $param['IsUsed'] . ' | isbye=' . $isby . ' | IsDel=' . print_r($param['IsDel'], true) . ' | title=' . print_r($param['Title'], true) . '|'.  serialize($param)."\r\n";
                write_log($log_str, BLOG_INDEX_LOG . '/article/articleaddlog_api_' . date('Ymd') . '.log', __METHOD__);

                //文章最终URL
                $articleurl = config_item('base_url') . '/' . $bloginfo['DomainName'] . '/article/' . strtotime($Status['Record']['AppearTime']) . '-' . $Status['Record']['ArticleID'] . '.html';

                $data['errno'] = 'success';
                if ($param['IsDel'] == 2 || $param['IsDel'] == 5) {
                    $data['isdel'] = $param['IsDel'];
                    $data['error'] = '文章发表成功！您文章需要审核后才会展示！';
                } else if ($param['IsDel'] == 1 || $param['IsDel'] == 3 || $param['IsDel'] == 4) {
                    $data['isdel'] = $param['IsDel'];
                    $data['error'] = '文章已删除，请查看其他文章！';
                } else {
                    $data['error'] = $articleurl;
                }
                echo json_encode($data);
                exit;
            } else if ($Status['Code'] == '200036') {
                $data['errno'] = $Status['Code'];
                $data['error'] = '文章保存成功，请等待审核'; //中文分词，暂时没用
            } else if ($Status['Code'] == '200037') {
                $data['errno'] = $Status['Code'];
                $data['error'] = '文章被系统自动删除';   //中文分词，暂时没用
                $mcache->delete('Article_Add_Title_' . $MemberID);
            } else {
                $data['errno'] = $Status['Code'];    //失败，直接退出
                $data['error'] = '文章保存失败';
                $mcache->delete('Article_Add_Title_' . $MemberID);
                echo json_encode($data);
                exit;
            }
        }
    }

}

//end class
?>