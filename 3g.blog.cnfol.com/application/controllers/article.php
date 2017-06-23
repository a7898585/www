<?php

/* * **********************
 * 功能：   博客个人文章
 * author： jianglw
 * *********************** */

class Article extends MY_Controller {

    function Article() {
        parent::MY_Controller();
    }

    /**
     * @ 新增博客文章
     * */
    function Add($domainname) {
        $this->_checkIP();
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //权限认证
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);

        $this->_checkAccess($blogaccess, 'AddArticle');
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);

        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();

        //获取个人博客列表过滤掉被关闭的
        $extract['bloglistfilter'] = $this->_getBlogListFilter($extract['bloglist']);

        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //是否编辑
        $IN = parse_incoming();
        $artstr = $IN['articleid'];
        $blocks = &$this->config->item('block');
        if ($artstr) {
            if (strpos($artstr, '-') === false) {
                $appearTime = '2011-01-01 00:00:00';
                $articleID = $artstr;
            } else {
                $temp = explode('-', $artstr);
                $appearTime = date("Y-m-d H:i:s", $temp[0]);
                $articleID = $temp[1];
            }

            $data['MemberID'] = $extract['bloginfo']['MemberID'];
            $data['ArticleID'] = $articleID;
            $data['AppearTime'] = $appearTime;

            $this->load->model('blogarticle_socket');
            $extract['article'] = $this->blogarticle_socket->getBlogArticleByID($data, 'edit');
            //print_r($extract['article']);
            if (empty($extract['article'])) {
                cnfolAlert("您要编辑的博客文章不存在！");
                cnfolLocation();
                exit;
            }
            $extract['article']['TagName'] = trim($extract['article']['TagName'], ',');
            $extract['article']['Summary'] = html_entity_decode($extract['article']['Summary'], ENT_NOQUOTES, 'UTF-8');
        }
        if ($this->input->get('draftsEdit') == '1') {
            $blocks['articleaddtitle'] = '编辑草稿';
        }
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']); //判断是否自己的
        $extract['title'] = $extract['bloginfo']['NickName'] . '-' . $blocks['articleaddtitle'] . '-' . $extract['bloginfo']['BlogName'];
        $extract['isvalidate'] = $this->_checkValidate(1, $extract['bloginfo']['GroupID']); //验证是否要输入验证码
        $extract['imgsite'] = &config_item('imgsite');
        $extract['systaglist'] = &config_item('sysTagList');

        $extract['user'] = $this->user;
        $extract['baseurl'] = &config_item('base_url');
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];

        $this->load->view('article/article_add.shtml', $extract);
    }

    /**
     * 展示博客文章详细信息
     */
    function articleDetail($domainname) {
        $this->_checkUserlogin();

        $artstr = $this->input->get_post('artid');
        if (strpos($artstr, '-') === false) {
            $appearTime = '2011-01-01 00:00:00'; //兼容2011年以前文章 
            $articleID = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID = $temp[1];
        }
        $realarticleID = intval($this->input->post('articleid'));
        $realappearTime = $this->input->post('appeartime');
        if (!$realappearTime) {
            $realappearTime = '2011-01-01 00:00:00';
        }
        $CommentID = intval($this->input->get_post('commentid'));
        $isHD = false;
        $articleID = intval($articleID);
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['bloginfo']['articletime'] = $temp[0];

        $extract['loginuserid'] = $this->user['userid'];
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);

        $num = $this->_getFriend($extract['bloginfo']['UserID']);
        $extract['friendsnumber'] = $num;

        $TransmitFlag = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        $extract['isFrends'] = $TransmitFlag;

        @session_start();
        if (isset($_SESSION['ViewAdmin']) && $_SESSION['ViewAdmin'] == 'admin')
            $isHD = true;

        //获取该博客文章信息
        if ($articleID != 0 || $realarticleID != 0) {
            $data['MemberID'] = $extract['bloginfo']['MemberID'];
            if ($realarticleID != 0) {
                $data['ArticleID'] = $realarticleID;
                $data['AppearTime'] = $realappearTime;
            } else {
                $data['ArticleID'] = $articleID;
                $data['AppearTime'] = $appearTime;
            }
            $this->load->model('blogarticle_socket');
            $extract['article'] = $this->blogarticle_socket->getBlogArticleByID($data, 'view');

            if (empty($extract['article'])) {
                cnfolAlert('该文章信息不存在，请查看其他文章');
                cnfolLocation(config_item('base_url') . '/' . $domainname);
                exit(-1);
            }

            if ($isHD === false) { //来访者不是从后台来的，进行相应的判断
                if ($extract['isowner'] === false) { //来访者不是博主自己，进行相应的判断
                    if (($extract['article']['IsDel'] == 2 || $extract['article']['IsDel'] == 5) && $this->isCNFOL === false) {
                        cnfolAlert('该文章信息正在审核中，请查看其他文章');
                        cnfolLocation(config_item('base_url') . '/' . $domainname);
                        exit(-1);
                    }

                    if ($extract['article']['IsDel'] == 1) {
                        cnfolAlert('该文章信息已经被博主删除，请查看其他文章');
                        cnfolLocation(config_item('base_url') . '/' . $domainname);
                        exit(-1);
                    }

                    if ($extract['article']['IsDel'] == 3 || $extract['article']['IsDel'] == 4) {
                        cnfolAlert('该文章信息已经被管理员删除，请查看其他文章');
                        cnfolLocation(config_item('base_url') . '/' . $domainname);
                        exit(-1);
                    }

                    if ($extract['article']['ReadStatus'] == 3) {
                        cnfolAlert('该文章信息被设置成私有，只有博客主自己可以查阅');
                        cnfolLocation(config_item('base_url') . '/' . $domainname);
                        exit(-1);
                    }


                    if ($extract['article']['ReadStatus'] == 1 && $this->user === false) {
                        $extract['loginform'] = 1;
                    }

                    if ($extract['article']['GiftPrice'] > 0 && $this->user['userid'] > 0) { //非博主，礼物数大于0，进行鲜花判断
                        $param = 'UserID=' . $this->user['userid'];
                        $param .= '&GiftID=1';
                        $param .= '&GiftCnt=' . $extract['article']['GiftPrice'];
                        $param .= '&SourceTypeID=1';
                        $param .= '&SourceTabID=' . $extract['article']['ArticleID'];
                        $extract['article']['CheckGift'] = file_get_contents('http://passport.cnfol.com/giftapi/checkGift?' . $param);
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . __FILE__ . ' | ' . __METHOD__ . ' | ' . print_r($param, true) . ' | CheckGift:' . $extract['article']['CheckGift'] . "\r\n", 3, '/home/www/html/logs/article_' . date('Ymd') . '.log');

                        unset($param);
                    }
                } else { //来访者是博主，只判断文章是否存在
                    if ($extract['article']['IsDel'] == 1 || $extract['article']['IsDel'] == 3 || $extract['article']['IsDel'] == 4) {
                        cnfolAlert('该文章信息已经删除，请查看其他文章');
                        cnfolLocation(config_item('base_url') . '/' . $domainname);
                        exit(-1);
                    }

                    if (empty($extract['article'])) {
                        cnfolAlert('该文章信息不存在，请查看其他文章');
                        cnfolLocation(config_item('base_url') . '/' . $domainname);
                        exit(-1);
                    }
                }
            } else { //后台访问，绕过所有限制，鲜花限制
                $extract['article']['GiftPrice'] = 0;
                $extract['article']['CheckGift'] = 1;
            }

            unset($param);
            $param['ArticleID'] = $data['ArticleID'];
            $param['MemberID'] = $extract['bloginfo']['MemberID'];
            $param['AppearTime'] = $extract['article']['AppearTime'];
            //获取文章统计
//            print_r($param);
            $extract['articlestat'] = $this->blogarticle_socket->getBlogArticleStatByID($param);
        }
//        print_r($extract['articlestat']);

        if ($articleID === 0) {
            //预览
            $extract['article']['ArticleID'] = $realarticleID;
            $extract['article']['Title'] = $this->input->post('title', TRUE);
            $extract['article']['AppearTime'] = date('Y-m-d H:i:s');
            if (isset($extract['tag'])) {
                unset($extract['tag']);
            }
            $tmptags = explode(',', $this->input->post('tag', TRUE));
            foreach ($tmptags as $tag) {
                if (trim($tag) != "") {
                    $extract['tag'][] = array(0, $tag);
                }
            }
            $extract['article']['Domainname'] = $domainname;
            $extract['article']['Content'] = $this->input->post('content');
            $extract['article']['Recommend'] = 0;
            $extract['article']['GiftPrice'] = 0;
            $extract['articlestat'] = 0;
        }


        $extract['article']['Title'] = filter($extract['article']['Title']);
        $extract['article']['Content'] = str_replace('display:none', '', filter($extract['article']['Content']));

        //创建点击统计url
        $extract['bloginfo']['title'] = $extract['article']['Title'];

        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner'], $articleID);


        //获取个人博客信息列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //	var_dump($extract['blogconfig']);

        $extract['modulepath'] = config_item('module_path');

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');

        $tmp = str_replace('{nick}', $extract['bloginfo']['NickName'], $this->lang->language['title_articlie']);
        $extract['title'] = str_replace('{title}', $extract['article']['Title'], $tmp);

        $tmp_golden = str_replace('{nick}', $extract['bloginfo']['NickName'], $this->lang->language['title_articlie_golden']); //黄金栏目专用
        $extract['title_golden'] = str_replace('{title}', $extract['article']['Title'], $tmp_golden);

        $extract['keywords'] = $this->lang->language['keywords_articlie'];
        $extract['keywords_golden'] = $this->lang->language['keywords_articlie_golden']; //黄金栏目专用
        $extract['description'] = getsummary($extract['article']['Content'], 250, 1);
        unset($tmp);

        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 2;
        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        $extract['lastpage'] = 1;

        //访问量
        if ($extract['isowner'] == false) {
            $this->load->model('memberblog_socket');
            $dataStat['MemberIDs'] = $extract['bloginfo']['MemberID'];
            $stat1 = $this->memberblog_socket->getMemberBlogStat($dataStat);
            $stat2 = $this->memberblog_socket->getMemberBlogStatByCache($dataStat);
            $extract['totalVisit'] = empty($stat2['TotalClick']) ? $stat1['Totalvisit'] : $stat2['TotalClick'];
        }
        if ($_COOKIE['blogartvote' . $extract['article']['ArticleID']] == 1) {
            $extract['blogartvote'] = 1;
        }
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $extract['otherpersonhead'] = $blocks['otherpersonhead'];
        //是否收藏该文章缓存
        $mcache = &load_class('Memcache');
        $ckey = config_item('SC0001');
        $ckey = str_replace('{MemberID}', $extract['bloginfo']['MemberID'], $ckey);
        $ckey = str_replace('{AticleId}', $extract['article']['ArticleID'], $ckey);
        $collectaticle = $mcache->get($ckey, 1);
        $extract['collect_aticle'] = $collectaticle['info'];
//        var_dump($collectaticle);
        $this->load->view('article/article_show.shtml', $extract);
    }

    /*
     * 博客 文章列表
     */

    function articleList($domainname) {
        $this->_checkUserlogin();
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        //获取参数
        $data['UserID'] = $extract['bloginfo']['UserID'];
        $extract['UserID'] = $data['UserID'];
        $page = 1;

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $data['Platform'] = 1;
        $this->load->model('blogarticle_socket');
        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['all'];

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

            $data['StartNo'] = ($page - 1) * MYBLOG_INDEX_PAGESIZE;

            $data['QryCount'] = MYBLOG_INDEX_PAGESIZE;
            $data['FlagCode'] = $tempInfo['FlagCode'];
//            print_r($data);
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
        if ($tmpCnt > MYBLOG_INDEX_PAGESIZE) {
            $extract['articleMore'] = 1;
        }
        $blocks = &$this->config->item('block');
        $extract['user'] = $this->user;
        $extract['title'] = $extract['bloginfo']['NickName'] . '-' . $blocks['articlelist'] . '-' . $extract['bloginfo']['BlogName'];

        $blocks = &$this->config->item('block');
        $extract['baseurl'] = &config_item('base_url');
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];

        $this->load->view('article/article_list.shtml', $extract);
    }

    /*
     * ajax调用更多内容 文章列表
     */

    function ajaxMoreArticle() {
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($this->input->get('domainname'));
        $data['UserID'] = $this->input->get('currentid') != '' ? $this->input->get('currentid') : $extract['bloginfo']['UserID'];
        $extract['UserID'] = $data['UserID'];

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 2;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $data['Platform'] = 1;
        $this->load->model('blogarticle_socket');
        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['all'];
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * MYBLOG_MORE_PAGESIZE;
            $data['QryCount'] = MYBLOG_MORE_PAGESIZE;
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);

            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            $content = array();
            $baseurl = &config_item('base_url');
            if (!empty($extract['artlist']['Record'][0])) {
                foreach ($extract['artlist']['Record'] as $art) {
                    $artTitle = filter($art['Title']);
                    $artContent = filter(filter($art['Summary']));
                    if (strlen($artTitle) > 30) {
                        $artTitle = utf8_str($artTitle, 30);
                    }
                    if (strlen($artContent) > 70) {
                        $artContent = utf8_str($artContent, 70);
                    }
                    $arturl = $baseurl . '/' . $extract['bloginfo']['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                    $time = timeopMyblog($art['AppearTime']);
                    $isJian = 0;
                    if ($art['Recommend'] == 2 || $art['Recommend'] == 3 || $art['IsUsed'] == 1) {
                        $isJian = 1;
                    }
                    $con['title'] = $artTitle;
                    $con['content'] = $artContent;
                    $con['arturl'] = $arturl;
                    $con['time'] = $time;
                    $con['isContainImg'] = $art['PictureUrl'];
                    $con['isZhuan'] = $art['Property'] == 2 ? '1' : '0';
                    $con['isJian'] = $isJian;
                    $con['isDing'] = $art['IsTop'] == 1 ? '1' : '0';
                    $content[] = $con;
                }
                $type = 1;
                $currentpage = $page + 1;
            } else {
                $currentpage = $page;
                $type = 2; //无列表
            }
            echo json_encode(array('data' => $content, 'error' => $type, 'page' => $currentpage));
        }
    }

    /**
     * 博客草稿箱列表
     */
    function DraftboxList($domainname) {
        $this->_checkIP();
        $this->_checkLogin();

        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname); //通过博客名获取博客信息	
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner'], '0');
        $extract['bloglist'] = $this->_getBlogListByUid(); //获取个人博客列表
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']); //获取博客配置信息

        $data['MemberID'] = $extract['bloginfo']['MemberID'];

        $this->load->model('blogarticle_socket');
        $tempInfo = $this->blogarticle_socket->getMemberDraftboxList($data);

        $tmpCnt = $tempInfo['UTopCnt'];
        $extract['artlist'] = $tempInfo;
        if ($tmpCnt == 1) {
            $extract['artlist']['Record'] = array(0 => $extract['artlist']['Record']);
        }
        $extract['totleRecords'] = $tmpCnt;
        $extract['user'] = $this->user;
        $extract['userid'] = $this->user['userid'];

        $blocks = &$this->config->item('block');
        $extract['title'] = $extract['bloginfo']['NickName'] . '-' . $blocks['draftboxtitle'] . '-' . $extract['bloginfo']['BlogName'];

        $extract['commonheader'] = $blocks['commonheader'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $extract['baseurl'] = &config_item('base_url');

        $this->load->view('article/article_draftlist.shtml', $extract);
    }

    /**
     * 增 删 改
     * @ 博客文章关联动作
     * */
    function Action($domainname) {
        $this->_checkIP();
        $isVIP = false; //是否同步微博，空间，验证码
        if (false == $this->_checkUserlogin()) {
            $data['errno'] = 'login';
            $data['error'] = '登入信息验证失败';
            echo json_encode($data);
            exit;
        }

        $MemberID = $this->input->post("memid");
        $flashCode = $this->input->post("flashCode");
        $mcache = &load_class('Memcache');
        $mcache->addServer();

        if (empty($flashCode) || empty($MemberID)) {
            error_log(print_r($flashCode, true) . '|' . $MemberID . '\r\n', 3, '/home/httpd/logs/aguesteverbrowse.log');
            $data['errno'] = 'verify';
            $data['error'] = '数据请求异常，拒绝服务.';
            echo json_encode($data);
            exit;
        }
        if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
            $data['errno'] = 'verify';
            $data['error'] = '数据请求异常，拒绝服务';
            echo json_encode($data);
            exit;
        }
        $act = $this->input->post('act');

        //草稿箱发表后从草稿箱删除
        $drafts = 0;
        $draftsArticleid = $this->input->post('draftsArticleid');
        if ($draftsArticleid != 0 && is_numeric($draftsArticleid)) {
            $act = 'edit';
            $drafts = 1;
        }
        //草稿箱发表后从草稿箱删除
        switch ($act) {
            case 'add':
                $addtime = $mcache->get('Article_Add_User_' . $this->user['userid']);
                $this->load->model('memberblog_socket');
                if (!empty($addtime) && (((time() - $addtime['LastTime'] ) <= addarticletime) && ($addtime['IsClose'] == 0))) {
                    //$this->memberblog_socket->updateBlogMember(array('MemberID'=>$MemberID,'Status'=>'1'));
                    $addtime['IsClose'] = 1;
                    $mcache->set('Article_Add_User_' . $this->user['userid'], $addtime, 5);
                    $data['errno'] = 'ipfilter';
                    $data['error'] = '系统发现您的博客异常，您的IP已被封闭' . $this->user['userid'];
                    echo json_encode($data);
                    exit;
                }

                //标签的处理
                $tagStr = $this->input->post('tag', true);
                if (!empty($tagStr)) {
                    $tagStr = $this->_taghandle($tagStr);
                }

                $title = $this->input->post('title');
                $title = preg_replace("/\s+/i", ' ', $title);
                $titlecode = md5(str_replace(' ', '', $title));

                $titlesign = $mcache->get('Article_Add_Title_' . $MemberID . '_' . $titlecode);

                if ($titlesign == $titlecode) {
                    $data['errno'] = 'title';
                    $data['error'] = '在' . (addarticlelimittime / 60) . '分钟内不可发表相同文章！';
                    echo json_encode($data);
                    exit;
                } else {
                    //记录文件标题
                    $mcache->set('Article_Add_Title_' . $MemberID . '_' . $titlecode, $titlecode, addarticlelimittime);
                }
                unset($titlesign);


                //处理引用公告
                $trackback = trim($this->input->post('trackback', TRUE), ';');
                $trackbacklist = array();
                if (!empty($trackback)) {
                    $trackbacklist = explode(';', $trackback);
                    $trackbacklist = is_string($trackbacklist) ? array(0 => $trackbacklist) : $trackbacklist;
                    foreach ($trackbacklist as $key => $val) {
                        if (!preg_match('/https?:\/\/(.*)/i', $val)) {
                            unset($trackbacklist);
                        }
                    }
                    $trackbacklist = array_unique($trackbacklist);
                    $trackback = join(';', $trackbacklist);
                }
                unset($param);

                $param['SysTagID'] = intval($this->input->post('tagId'));
                if (!is_int($param['SysTagID']) || !array_key_exists($param['SysTagID'], config_item('sysTagList'))) {
                    $param['SysTagID'] = 1459;
                }

                $param['Recommend'] = 0;
                $param['IsUsed'] = 0;
                $param['Status'] = 0; //默认博文不显示在频道页和首页  (1为不显示，0为显示)//上线后改回1
                $param['IsDel'] = 0;

                //先审后发这里调整 0-否(預設)， 1-垃圾箱用户自己删除   2–待審核，重点监控  3-彻底删除后台删除  4-系统回收站（系统自动删除）不显示  5-优先审核
                $isby = file_get_contents(URL_CRONTAB . '/expansion/CheckStatus/banperiods.txt');
                $isby = isset($isby) ? $isby : 0;

                if ($isby == 1) {
                    $param['IsDel'] = 2;
                }

                $blogConfig = $this->memberblog_socket->getMemberBlogbyDomainName(array('QryData' => $domainname));

                //用户组信息的处理
                $groups = trim($blogConfig['GroupID'], ',');
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

                //除名家，高手，采用组等，和中金内网用户，必须验证验证码信息

                if ($isVIP === false && $this->isCNFOL === false) {
                    $verifycode = $this->input->get_post('validate');
                    $this->load->library('SimpleCaptcha');
                    if (!$this->simplecaptcha->validate($verifycode, $this->user['userid'])) {
                        $data['errno'] = 'validate';
                        $data['error'] = '验证码信息错误';
                        $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                        echo json_encode($data);
                        exit;
                    }
                }

                $param['MemberID'] = $MemberID;
                $param['ArticleID'] = 0;
                $param['Title'] = $title;
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

                preg_match('/<img\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $param['Content'], $matchesPic);
                $param['PictureUrl'] = $matchesPic['0'];


                if ($param['PictureUrl']) {
                    $param['PictureUrl'] = htmlEncode($param['PictureUrl']);
                    $param['IsMultimedia'] = 1;
                } else {
                    $param['PictureUrl'] = 0;
                }

                preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $param['Content'], $matchesEmbed);
                $param['Multimedia'] = $matchesEmbed['1'];


                if ($param['Multimedia']) {

                    if ($param['IsMultimedia'] == '1') {
                        $param['IsMultimedia'] = 3;
                    } else {
                        $param['IsMultimedia'] = 2;
                    }
                }


                $param['TagIDs'] = $tagStr;
                $param['TrackBack'] = $trackback;
                $param['Summary'] = (trim($param['Summary']) == "") ? getsummary($param['Content'], limitsumautolen, 1) : $param['Summary'];

                $param['LastCommentDate'] = date('Y-m-d H:i:s');
                $param['GiftPrice'] = intval($this->input->post('GiftPrice'));

                if (strlen($param['Title']) > limitarticlemaxtitlelen || strlen($param['Title']) < limitarticlemintitlelen) {
                    $data['errno'] = 'title';
                    $data['error'] = '文章标题长度应该在' . limitarticlemintitlelen . '-' . limitarticlemaxtitlelen . '个字节之内';
                    $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                    echo json_encode($data);
                    exit;
                } else if (strlen($param['Content']) > limitarticlecontentmaxlen || strlen($param['Content']) < limitarticlecontentminlen) {
                    $data['errno'] = 'content';
                    $data['error'] = '文章内容长度应该在' . limitarticlecontentminlen . '-' . limitarticlecontentmaxlen . '个字节之内';
                    $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                    echo json_encode($data);
                    exit;
                } else if ((strlen($param['Summary'])) > limitsumautolen + 500) {
                    $data['errno'] = 'summary';
                    $data['error'] = '摘要长度应该在' . limitsumautolen . '个字以内';
                    $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                    echo json_encode($data);
                    exit;
                } else if ($param['SysTagID'] == 0) {
                    $data['errno'] = 'tagId';
                    $data['error'] = '请选择文章分类';
                    $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                    echo json_encode($data);
                    exit;
                } else {

                    //临时变量
                    $tmpp['Content_c'] = remove_invisible_code($param['Content']);
                    $tmpp['Summary_c'] = remove_invisible_code($param['Summary']);

                    $param['Content'] = htmlEncode(remove_invisible_code($param['Content']));

                    $param['Summary'] = htmlEncode(remove_invisible_code($param['Summary']));
                    $param['image'] = htmlEncode(remove_invisible_code($param['image']));
                    $param['Title'] = htmlEncode($param['Title']);
                    $param['IsUTOP'] = 0;


                    /* guoping1980用户禁止发表 */
                    if ($this->user['username'] == 'guoping1980') {
                        $ip_address = $this->input->ip_address();
                        $sign = preg_match("/fdh1980guoping/i", $param['Content']);
                        if (!preg_match("/^(14\.146)|^(218\.85)|^(219\.137)/i", $ip_address) || !$sign) {
                            $data['errno'] = '110';    //禁止发表，直接退出
                            $data['error'] = '你已被监控...';

                            $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);

                            error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_add_346713_' . date('Ymd') . '.log');
                            echo json_encode($data);
                            exit;
                        }

                        $param['Content'] = preg_replace("/fdh1980guoping/i", '', $param['Content']);
                    }

                    /* jifan761018用户禁止发表 */
                    if ($this->user['username'] == 'jifan761018') {
                        $ip_address = $this->input->ip_address();
                        if (!preg_match("/^(124\.)|^(110\.)|^(27\.)|^(120\.)|^(117\.)/i", $ip_address)) {
                            $data['errno'] = '110';    //禁止发表，直接退出
                            $data['error'] = '你已被监控...';
                            error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_add_jifan761018_' . date('Ymd') . '.log');
                            echo json_encode($data);
                            exit;
                        }
                    }

                    //error_log(print_r($param,true), 3, '/home/httpd/logs/a23132.log');

                    $param['Content'] = preg_replace("/position(\s)*:(\s)*absolute/i", 'position:', $param['Content']);
                    $this->load->model('blogarticle_socket');
                    $Status = $this->blogarticle_socket->addBlogArticle($param, $tmpp);

                    if (empty($Status)) {
                        $data['errno'] = 'empty';
                        $data['error'] = '文章保存失败.';
                        $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                        echo json_encode($data);
                        exit;
                    }

                    if (!isset($Status['Record']['ArticleID'])) {
                        $Status['Record']['ArticleID'] = intval($Status['Description']);
                    }

                    if ($Status['Code'] == '00') {
                        //积分设置
                        $args['UserID'] = $this->user['userid'];
                        $args['RewardEName'] = 'addarticle';
                        $args['Type'] = 1;
                        $this->user_socket->addUserPoint($args);
                        unset($args);

                        //文章最终URL
                        $articleurl = config_item('base_url') . '/' . $domainname . '/article/' . strtotime($Status['Record']['AppearTime']) . '-' . $Status['Record']['ArticleID'] . '.html';

                        //除名家，高手，采用组等，和中金内网用户，增加刷贴验证
                        if ($isVIP === false && $this->isCNFOL === false) {
                            //记录当前用户发文章的时间
                            $rs = array('UserID' => $this->user['userid'], 'LastTime' => time(), 'IP' => $this->input->ip_address(), 'IsClose' => 0);
                            $mcache->set('Article_Add_User_' . $this->user['userid'], $rs, 5);
                        }

                        $data['errno'] = 'success';

                        if ($param['IsDel'] == 2 || $param['IsDel'] == 5) {
                            $data['isdel'] = $param['IsDel'];
                            $data['error'] = '文章发表成功！您文章需要审核后才会展示！';

                            //this
                            $articleBao = '1';
                            //this
                        } else if ($param['IsDel'] == 1 || $param['IsDel'] == 3 || $param['IsDel'] == 4) {
                            $data['isdel'] = $param['IsDel'];
                            $data['error'] = '文章已删除，请查看其他文章！';
                        } else {
                            $data['error'] = '文章发表成功';

                            //this
                            $articleBao = '1';
                            //this
                        }
                    } else if ($Status['Code'] == '200036') {
                        $data['errno'] = $Status['Code'];
                        $data['error'] = '文章保存成功，请等待审核'; //中文分词，暂时没用
                        //this
                        $articleBao = '1';
                        //this
                    } else if ($Status['Code'] == '200037') {
                        $data['errno'] = $Status['Code'];
                        $data['error'] = '文章被系统自动删除';   //中文分词，暂时没用
                        $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                    } else {
                        $data['errno'] = $Status['Code'];    //失败，直接退出
                        $data['error'] = '文章保存失败';
                        $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                        echo json_encode($data);
                        exit;
                    }

                    /* guoping1980用户日志 */
                    if ($this->user['username'] == 'guoping1980') {
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . ' | ' . $this->user['username'] . ' \ ' . $Status['Code'] . '\r\n', 3, '/home/www/html/logs/article_add_346713_' . date('Ymd') . '.log');
                    }


                    if ($param['TrackBack']) {
                        $tb_data['url'] = $articleurl;
                        $tb_data['title'] = $param['Title'];
                        $tb_data['excerpt'] = $param['Summary'];
                        $tb_data['blog_name'] = $this->input->post('blogName');
                        sendping($param['TrackBack'], $tb_data);
                    }
                    $data['articleid'] = strval($Status['Record']['ArticleID']);
                    $data['appeartime'] = strval(strtotime($Status['Record']['AppearTime']));


                    //this
                    if ($articleBao == 1) {

                        $this->delMyIndex();
                        systemBao($data['articleid'], 'articleactual.php'); //保10洁过滤
                    }
                    //this

                    echo json_encode($data);
                    exit;
                }
                break;
            case 'edit':
                //标签的处理

                $tagStr = $this->input->post('tag', true);
                if (!empty($tagStr)) {

                    if ($drafts != '1') {
                        $tagStr = $this->_taghandle($tagStr);
                    }
                }
                //处理引用公告
                $trackback = trim($this->input->post('trackback', TRUE), ';');
                if (!empty($trackback)) {
                    $trackback = explode(';', $trackback);
                    $trackback = is_string($trackback) ? array(0 => $trackback) : $trackback;
                    foreach ($trackback as $key => $val) {
                        if (!preg_match('/https?:\/\/(.*)/i', $val)) {
                            unset($trackback[$key]);
                        }
                    }
                    $trackback = array_unique($trackback);
                    $trackback = join(';', $trackback);
                }
                unset($param);
                $param['SysTagID'] = intval($this->input->post('tagId'));
                if (!is_int($param['SysTagID']) || !array_key_exists($param['SysTagID'], config_item('sysTagList'))) {
                    $param['SysTagID'] = 1459;
                }

                $param['AppearTime'] = $this->input->post('appeartime');

                if (empty($param['AppearTime'])) {
                    $param['AppearTime'] = $this->input->post('draftsAppeartime');
                }

                //草稿箱发表后从草稿箱删除
                $draftsAppeartime = $this->input->post('draftsAppeartime');
                if ($draftsAppeartime != 0 && is_numeric($draftsAppeartime)) {
                    $param['AppearTime'] = date("Y-m-d H:i:s", $draftsAppeartime);
                }
                //草稿箱发表后从草稿箱删除

                if (!isset($param['AppearTime']) || !preg_match("/^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$/i", $param['AppearTime'])) {
                    $data['errno'] = 'artid';
                    $data['error'] = '文章编辑信息提交丢失';
                    echo json_encode($data);
                    exit;
                }

                $param['ArticleID'] = intval($this->input->post('articleid'));
                if (empty($param['ArticleID'])) {
                    $param['ArticleID'] = $this->input->post('draftsArticleid');
                }


                if (!is_int($param['ArticleID']) && $param['ArticleID'] < 0) {
                    $data['errno'] = 'artid';
                    $data['error'] = '文章编辑信息提交丢失';
                    echo json_encode($data);
                    exit;
                }


                /* -----------保存为草稿箱后点提交需验证码------------ */

                if ($drafts == '1' && $this->input->post('draftsVifdata') != '1') {

                    $this->load->model('memberblog_socket');
                    $blogConfig = $this->memberblog_socket->getMemberBlogbyDomainName(array('QryData' => $domainname));

                    //用户组信息的处理
                    $groups = trim($blogConfig['GroupID'], ',');
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

                    //除名家，高手，采用组等，和中金内网用户，必须验证验证码信息

                    if ($isVIP === false && $this->isCNFOL === false) {
                        $verifycode = $this->input->get_post('validate');
                        $this->load->library('SimpleCaptcha');
                        if (!$this->simplecaptcha->validate($verifycode, $this->user['userid'])) {
                            $data['errno'] = 'validate';
                            $data['error'] = '验证码信息错误';
                            $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                            echo json_encode($data);
                            exit;
                        }
                    }
                }

                /* -----------保存为草稿箱后点提交需验证码------------ */

                $param['MemberID'] = $MemberID;
                $param['Title'] = $this->input->post('title');
                $param['Title'] = preg_replace("/\s+/i", ' ', $param['Title']);

                $param['Content'] = $this->input->post('content');

                preg_match('/<img\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $param['Content'], $matchesPic);
                $param['PictureUrl'] = $matchesPic['0'];


                if ($param['PictureUrl']) {
                    $param['PictureUrl'] = htmlEncode($param['PictureUrl']);
                    $param['IsMultimedia'] = 1;
                } else {
                    $param['PictureUrl'] = 0;
                    $param['IsMultimedia'] = 0;
                }

                preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $param['Content'], $matchesEmbed);
                $param['Multimedia'] = $matchesEmbed['1'];


                if ($param['Multimedia']) {

                    if ($param['IsMultimedia'] == '1') {
                        $param['IsMultimedia'] = 3;
                    } else {
                        $param['IsMultimedia'] = 2;
                    }
                }

                $param['Summary'] = $this->input->post('summary');
                $param['ReadStatus'] = intval($this->input->post('readStatus'));
                $param['ReadStatus'] = (is_int($param['ReadStatus'])) ? $param['ReadStatus'] : 0;
                $param['SelfRecommend'] = intval($this->input->post('memberRecommend'));
                $param['SelfRecommend'] = (is_int($param['SelfRecommend'])) ? $param['SelfRecommend'] : 0;
                $param['SortID'] = intval($this->input->post('sortId'));
                $param['SortID'] = (is_int($param['SortID'])) ? $param['SortID'] : 18295;
                $param['IP'] = $this->input->ip_address();
                if ($drafts != '1') {
                    $param['TagIDs'] = $tagStr;
                }
                $param['TrackBack'] = $trackback;
                $param['Summary'] = (trim($param['Summary']) == "") ? getsummary($param['Content'], limitsumautolen, 1) : $param['Summary'];
                $param['LastCommentDate'] = date('Y-m-d H:i:s');
                $param['GiftPrice'] = intval($this->input->post('GiftPrice'));

                if (strlen($param['Title']) > limitarticlemaxtitlelen || strlen($param['Title']) < limitarticlemintitlelen) {
                    $data['errno'] = 'title';
                    $data['error'] = '文章标题长度应该在' . limitarticlemintitlelen . '-' . limitarticlemaxtitlelen . '个字节之内';
                    echo json_encode($data);
                    exit;
                } else if (strlen($param['Content']) > limitarticlecontentmaxlen || strlen($param['Content']) < limitarticlecontentminlen) {
                    $data['errno'] = 'content';
                    $data['error'] = '文章内容长度应该在' . limitarticlecontentminlen . '-' . limitarticlecontentmaxlen . '个字节之内';
                    echo json_encode($data);
                    exit;
                } else if ((strlen($param['Summary'])) > limitsumautolen + 500) {
                    $data['errno'] = 'summary';
                    $data['error'] = '摘要长度应该在' . limitsumautolen . '个字以内';
                    echo json_encode($data);
                    exit;
                } else {
                    //临时变量
                    $tmpp['Content_c'] = remove_invisible_code($param['Content']);
                    $tmpp['Summary_c'] = remove_invisible_code($param['Summary']);

                    $param['Content'] = htmlEncode(remove_invisible_code($param['Content']));
                    $param['Summary'] = htmlEncode(remove_invisible_code($param['Summary']));
                    $param['image'] = htmlEncode(remove_invisible_code($param['image']));
                    $param['Title'] = htmlEncode($param['Title']);

                    //草稿箱发表后从草稿箱删除
                    if ($drafts == 1) {
                        $param['Property'] = 0;
                        $param['IsDel'] = 0;
                    }
                    //error_log(print_r($param,true), 3, '/home/httpd/logs/a111.log');
                    //草稿箱发表后从草稿箱删除

                    /* guoping1980用户禁止发表 */
                    if ($this->user['username'] == 'guoping1980') {
                        $ip_address = $this->input->ip_address();
                        $sign = preg_match("/fdh1980guoping/i", $param['Content']);
                        if (!preg_match("/^(14\.146)|^(218\.85)|^(219\.137)/i", $ip_address) || !$sign) {
                            $data['errno'] = '110';    //禁止发表，直接退出
                            $data['error'] = '你已被监控...';
                            $mcache->delete('Article_Add_Title_' . $MemberID . '_' . $titlecode);
                            error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_edit_346713_' . date('Ymd') . '.log');
                            echo json_encode($data);
                            exit;
                        }
                        $param['Content'] = preg_replace("/fdh1980guoping/i", '', $param['Content']);
                    }




                    /* jifan761018用户禁止发表 */
                    if ($this->user['username'] == 'jifan761018') {
                        $ip_address = $this->input->ip_address();
                        if (!preg_match("/^(124\.)|^(110\.)|^(27\.)|^(120\.)|^(117\.)/i", $ip_address)) {
                            $data['errno'] = '110';    //禁止发表，直接退出
                            $data['error'] = '你已被监控...';
                            error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_edit_jifan761018_' . date('Ymd') . '.log');
                            echo json_encode($data);
                            exit;
                        }
                    }

                    $param['Content'] = preg_replace("/position(\s)*:(\s)*absolute/i", 'position:', $param['Content']);

                    $this->load->model('blogarticle_socket');

                    $Status = $this->blogarticle_socket->modBlogArticle($param, $tmpp);

                    if (empty($Status)) {
                        $data['errno'] = 'empty';
                        $data['error'] = '文章保存失败';
                        echo json_encode($data);
                        exit;
                    }

                    if ($Status['Code'] == '00') {
                        $data['errno'] = 'success';
                        $data['error'] = '文章编辑修改成功';

                        if ($drafts == 1) {
                            $data['error'] = '文章发表成功';
                        }

                        $articleBao = '1';
                    } else if ($Status['Code'] == '200036') {
                        $data['errno'] = 'success';
                        $data['error'] = '文章保存成功，请等审核';

                        $articleBao = '1';
                    } else if ($Status['Code'] == '200037') {
                        $data['errno'] = 'success';
                        $data['error'] = '文章被系统自动删除';
                    } else {
                        $data['errno'] = $Status['Code'];
                        $data['error'] = '文章保存失败';
                        echo json_encode($data);
                        exit;
                    }

                    /* guoping1980用户日志 */
                    if ($this->user['username'] == 'guoping1980') {
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . ' | ' . $this->user['username'] . ' \ ' . $Status['Code'] . '\r\n', 3, '/home/www/html/logs/article_edit_346713_' . date('Ymd') . '.log');
                    }

                    $ArticleID = $param['ArticleID'];
                    $AppearTime = strtotime($param['AppearTime']);
                    if ($param['TrackBack']) {
                        $tb_data['url'] = config_item('base_url') . '/' . $domainname . '/article/' . $AppearTime . '-' . $ArticleID . '.html';
                        $tb_data['title'] = $param['Title'];
                        $tb_data['excerpt'] = $param['Summary'];
                        $tb_data['blog_name'] = $this->input->post('blogName');
                        sendping($param['TrackBack'], $tb_data);
                    }
                    $data['articleid'] = strval($ArticleID);
                    $data['appeartime'] = strval($AppearTime);


                    if ($articleBao == 1) {
                        $this->delMyIndex();
                        systemBao($data['articleid'], 'articleactual.php'); //保10洁过滤
                    }

                    if ($drafts == 1) {
                        $this->_delDraftsCache($MemberID);
                    }

                    echo json_encode($data);
                    exit;
                }
                break;

            case 'del':

                $param['MemberID'] = $MemberID;

                $param['IsMultimedia'] = $this->input->post('ismut');
                $param['SelfRecommend'] = $this->input->post('recommend');
                $param['SortID'] = $this->input->post('sortid');
                error_log(print_r($_POST, true), 3, '/home/httpd/logs/a23132.log');

                $artstr = $this->input->post('id');
                if (strpos($artstr, '-') === false) {
                    $param['AppearTimes'] = '2011-01-01 00:00:00';
                    $param['ArticleIDs'] = $artstr;
                } else {
                    $temp = explode('-', $artstr);
                    $param['AppearTimes'] = date("Y-m-d H:i:s", $temp[0]);
                    $param['ArticleIDs'] = $temp[1];
                }

                if (empty($param['MemberID']) || $param['ArticleIDs'] < 1) {
                    $data['errno'] = 'param';
                    $data['error'] = '参数传递丢失';
                    echo json_encode($data);
                    exit;
                }



                /* guoping1980用户禁止发表 */
                if ($this->user['username'] == 'guoping1980') {
                    $ip_address = $this->input->ip_address();
                    if (!preg_match("/^(14\.146)|^(218\.85)|^(219\.137)/i", $ip_address)) {
                        $data['errno'] = '110';    //禁止发表，直接退出
                        $data['error'] = '你已被监控...';
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_del_346713_' . date('Ymd') . '.log');
                        echo json_encode($data);
                        exit;
                    }
                }

                /* jifan761018用户禁止发表 */
                if ($this->user['username'] == 'jifan761018') {
                    $ip_address = $this->input->ip_address();
                    if (!preg_match("/^(124\.)|^(110\.)|^(27\.)|^(120\.)|^(117\.)/i", $ip_address)) {
                        $data['errno'] = '110';    //禁止发表，直接退出
                        $data['error'] = '你已被监控...';
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_del_jifan761018_' . date('Ymd') . '.log');
                        echo json_encode($data);
                        exit;
                    }
                }

                $this->load->model('blogarticle_socket');
                if ($this->blogarticle_socket->delBlogArticle($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '博客文章删除成功';

                    $this->delMyIndex();

                    echo json_encode($data);
                    exit;
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '博客文章删除失败';
                    echo json_encode($data);
                    exit;
                }
                break;

            case 'top':

                $param['MemberID'] = $MemberID;
                $param['UpdateType'] = $this->input->post('istop');
                $param['IsMultimedia'] = $this->input->post('ismut');
                $param['SelfRecommend'] = $this->input->post('recommend');

                $artstr = $this->input->post('id');
                if (strpos($artstr, '-') === false) {
                    $param['AppearTime'] = '2011-01-01 00:00:00';
                    $param['ArticleID'] = $artstr;
                } else {
                    $temp = explode('-', $artstr);
                    $param['AppearTime'] = date("Y-m-d H:i:s", $temp[0]);
                    $param['ArticleID'] = $temp[1];
                }

                $msg = $param['UpdateType'] == 1 ? '置顶' : '取消置顶';
                if (empty($param['MemberID']) || $param['ArticleID'] < 1) {
                    $data['errno'] = 'param';
                    $data['error'] = '参数传递丢失';
                    echo json_encode($data);
                    exit;
                }
                /* guoping1980用户禁止发表 */
                if ($this->user['username'] == 'guoping1980') {
                    $ip_address = $this->input->ip_address();
                    if (!preg_match("/^(14\.146)|^(218\.85)|^(219\.137)/i", $ip_address)) {
                        $data['errno'] = '110';    //禁止发表，直接退出
                        $data['error'] = '你已被监控...';
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_top_346713_' . date('Ymd') . '.log');
                        echo json_encode($data);
                        exit;
                    }
                }
                /* jifan761018用户禁止发表 */
                if ($this->user['username'] == 'jifan761018') {
                    $ip_address = $this->input->ip_address();
                    if (!preg_match("/^(124\.)|^(110\.)|^(27\.)|^(120\.)|^(117\.)/i", $ip_address)) {
                        $data['errno'] = '110';    //禁止发表，直接退出
                        $data['error'] = '你已被监控...';
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . $this->input->ip_address() . ' | ' . print_r($param, true) . '\r\n', 3, '/home/www/html/logs/jinzhi_article_top_jifan761018_' . date('Ymd') . '.log');
                        echo json_encode($data);
                        exit;
                    }
                }
                $this->load->model('blogarticle_socket');
                if ($this->blogarticle_socket->topBlogArticle($param)) {

                    $data['errno'] = 'success';
                    $data['error'] = '博客文章' . $msg . '成功';

                    $this->delMyIndex();
                    echo json_encode($data);
                    exit;
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '博客文章' . $msg . '失败';
                    echo json_encode($data);
                    exit;
                }
                break;

            default:
                $data['errno'] = 'act';
                $data['error'] = '您还没选择所要的操作';
                echo json_encode($data);
                exit;
        }
    }

    /**
     * 草稿箱删除
     */
    function delDraf() {
        $this->_checkLogin();
        $tempArray = array();
        $IN = parse_incoming();
        $param['MemberID'] = $IN['memid'];
        $artstr = $IN['id'];
        if (strpos($artstr, ',') === false) {
            $tempArray = (array) $artstr;
        } else {
            $tempArray = explode(',', $artstr);
        }
        foreach ($tempArray as $val) {
            if (strpos($val, '-') === false) {
                $param['AppearTimes'] = '2011-01-01 00:00:00';
                $param['ArticleIDs'] = $val;
            } else {
                $temp = explode('-', $val);
                $param['AppearTimes'] = date("Y-m-d H:i:s", $temp[0]);
                $param['ArticleIDs'] = $temp[1];
            }
            if (empty($param['MemberID']) || $param['ArticleIDs'] < 1) {
                echo '参数传递丢失';
            }
            $this->load->model('blogarticle_socket');
            if ($this->blogarticle_socket->delBlogDrafArticle($param)) {
                $countArray = count($tempArray);
                if ($countArray == 1) {
                    echo 1;
                    exit; //删除成功
                }
            } else {
                echo '草稿箱删除失败';
                exit;
            }
        }
        echo 1;
        exit; //删除成功
    }

}

?>