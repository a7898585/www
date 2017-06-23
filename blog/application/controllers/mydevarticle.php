<?php

/* * **********************
 * 功能：   博客个人文章
 * author： lifeng
 * *********************** */

class Article extends MY_Controller {

    function Article() {
        parent::MY_Controller();
    }

    //展示博客文章详细信息以及预览
    function postinfo($domainname) {
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

        /* --------统计各文章或各博客主页被访问次数--------------- */
        //$this->_hotBlogArticle(array('domainname'=>$domainname,'appearTime'=>$temp[0],'articleID'=>$articleID,'guestType'=>$this->user['userid']),$extract['bloginfo']['UserID']);
        /* ----------------------------------------------------- */


        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);


        $num = $this->_getFriend($extract['bloginfo']['UserID']);
        $extract['friendsnumber'] = $num;

        $TransmitFlag = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        $extract['isFrends'] = $TransmitFlag;

        /* $this->load->library('session');
          if($this->session->userdata('ViewAdmin')=='admin'){
          $isHD = true;
          } */
        session_start();
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
            //d( $extract['article']);
            //print_r($extract['article']);
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
                        //http://passport.cnfol.com/giftapi/checkGift?UserID=355031&GiftID=1&GiftCnt=100&SourceTypeID=1&SourceTabID=255
                        $param = 'UserID=' . $this->user['userid'];
                        $param .= '&GiftID=1';
                        $param .= '&GiftCnt=' . $extract['article']['GiftPrice'];
                        $param .= '&SourceTypeID=1';
                        $param .= '&SourceTabID=' . $extract['article']['ArticleID'];
                        $extract['article']['CheckGift'] = file_get_contents('http://passport.cnfol.com/giftapi/checkGift?' . $param);
                        error_log(date('Y-m-d H:i:s', time()) . ' | ' . __FILE__ . ' | ' . __METHOD__ . ' | ' . print_r($param, true) . ' | CheckGift:' . $extract['article']['CheckGift'] . "\r\n", 3, '/home/www/html/logs/article_' . date('Ymd') . '.log');
                        //echo  'http://passport.cnfol.com/giftapi/checkGift?' . $param;
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

            //处理标签
            $extract['tag'] = array();
            $tmptagids = explode(',', $extract['article']['TagID']);
            $tmptags = explode(',', $extract['article']['TagName']);


            /* --------统计各文章标签浏览次数--------------- */
            $this->_hotBlogTag($tmptags, $extract['bloginfo']['UserID']);
            /* ----------------------------------------------------- */

            foreach ($tmptagids as $key => $tagid) {
                if (isset($tmptags[$key]) && trim($tmptags[$key]) != "")
                    $extract['tag'][] = array($tagid, $tmptags[$key]);
            }
            unset($param);
            $param['ArticleID'] = $data['ArticleID'];
            $param['MemberID'] = $extract['bloginfo']['MemberID'];
            $param['AppearTime'] = $extract['article']['AppearTime'];
            //获取文章统计
            $extract['articlestat'] = $this->blogarticle_socket->getBlogArticleStatByID($param);
        }

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
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner'], $articleID);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);


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
        if ($extract['article']['GiftPrice'] > 0) {
            $extract['description'] = '您必须一次性赠送≥' . $extract['article']['GiftPrice'] . '朵鲜花才可查看该篇文章';
        } else {
            $extract['description'] = getsummary($extract['article']['Content'], 250, 1);
        }

        unset($tmp);

        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];

        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 2;
        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        $extract['lastpage'] = 1;

        if ($articleID > 0) {
            //获取评论最后一页页码
            $data2['BlogType'] = 1;
            $data2['ArticleID'] = $articleID;
            $data2['StartNo'] = -1;
            $this->load->model('articlecomment_socket');
            $tempInfo = $this->articlecomment_socket->getArtCommentList($data2);
            $tmpCnt = $tempInfo['TtlRecords'];
            $extract['lastpage'] = intval($tmpCnt / $extract['blogconfig']['CommentNumber']) + 1;

            if ($CommentID > 0) { //从后台点击某评论跳转到文章页时，当前的评论id
                $extract['curcid'] = $this->articlecomment_socket->getPageNoByCommentID($articleID, $CommentID, $extract['blogconfig']['CommentNumber']);
            } else {
                $extract['curcid'] = 0;
            }

            if ($extract['curcid'] > 0) {
                $extract['lastpage'] = $extract['curcid'];
            }
        }

        $extract['shtml'] = $this->config->item('shtml_path');
        $this->load->view('article/devarticle_show.shtml', $extract);
    }

    /**
     * @ 个人博客文章列表 管理页
     * */
    function managelist($domainname) {
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

        $this->load->model('blogarticle_socket');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        unset($data);
        $sortID = intval($this->input->get_post('sortid'));
        if ($sortID > 0) {
            $data['SortID'] = $sortID;
        }
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['IsSummary'] = $extract['blogconfig']['ShowMode'];
        $data['StartNo'] = -1;
        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['TtlRecords'];
        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            //取文章信息
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber'];
            $data['QryCount'] = managelistsize;  //管理页固定一页20条
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artList'] = $this->blogarticle_socket->getMemberArticleList($data);
        } else {
            $extract['artList']['RetRecords'] = 0;
        }
        //翻页信息
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/manage/article/List';
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, managelistsize, $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articlelist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articlelisttitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @ 个人博客文章列表
     * */
    function managecommentlist($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        //$extract['viewurl']     = $this->_getviewURL($extract['bloginfo'],true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $this->load->model('blogarticle_socket');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;


        $tempInfo = $this->blogarticle_socket->getCommentArticleList($data);

        $tmpCnt = $tempInfo['TtlRecords'];
        if ($page > ceil($tmpCnt / managelistsize)) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = managelistsize;
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artList'] = $this->blogarticle_socket->getCommentArticleList($data);
        } else {
            $extract['artList'] = false;
        }

        //翻页信息
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/manage/comment/List';
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['commentarticlelist'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['commentarticlelisttitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @ 编辑博客文章
     * */
    function Edit($domainname) {
        $this->_checkIP();
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //权限认证
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'EditArticle');

        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取该博客文章信息
        $artstr = $this->input->post('articleid');
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

        if (empty($extract['article'])) {
            cnfolAlert("您要编辑的博客文章不存在！");
            cnfolLocation();
            exit;
        }

        $extract['article']['TagName'] = trim($extract['article']['TagName'], ',');
        $extract['article']['Summary'] = html_entity_decode($extract['article']['Summary'], ENT_NOQUOTES, 'UTF-8');

        $extract['user'] = $this->user;

        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articleedit'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articleedittitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['systaglist'] = &config_item('sysTagList');
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $extract['ismanage'] = true; //如果是管理页面，不加载统计代码
        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @ 新增博客文章列表
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
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articleadd'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articleaddtitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['imgsite'] = &config_item('imgsite');
        $extract['systaglist'] = &config_item('sysTagList');
        $extract['isconfig'] = 1;

        $extract['isvalidate'] = true;

        /* $mcache   = &load_class('Memcache');
          $mcache->addServer();
          if($this->isCNFOL === false){
          $Check_ip = $mcache->get('Article_Add_IP_'.$this->input->ip_address());
          if($Check_ip['Date'] == date("Ymd",time()) && $Check_ip['Num'] >= addarticleip)
          {
          $extract['isvalidate'] = true;
          }

          $Check_Member = $mcache->get('Article_Add_Member_'.$extract['bloginfo']['MemberID']);
          if($Check_Member['Date'] == date("Ymd",time()) && $Check_Member['Num'] >= addarticlemember)
          {
          $extract['isvalidate'] = true;
          }
          } */

        $blogConfig = $this->memberblog_socket->getMemberBlogbyDomainName(array('QryData' => $domainname));
        $groups = trim($blogConfig['GroupID'], ',');
        if ($groups != "") {
            $recommend = config_item('recommendgroup');
            $isuse = config_item('isuse');
            $autoaudit = config_item('autoaudit');
            $groups = explode(',', $groups);
            $groups = (is_string($groups)) ? array(0 => $groups) : $groups;

            foreach ($groups as $grp) {
                if (isset($recommend[$grp])) {
                    $extract['isvalidate'] = false;
                } else if ($grp == $isuse) {
                    $extract['isvalidate'] = false;
                } else if ($grp == $autoaudit) {
                    $extract['isvalidate'] = false;
                }
            }
        }

        if ($this->isCNFOL === true) {
            $extract['isvalidate'] = false;
        }

        $extract['ismanage'] = true; //如果是管理页面，不加载统计代码
        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @博客个人文章列表
     * */
    function Index($domainname) {
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

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

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

        $this->load->view('article/article_list.shtml', $extract);
    }

    /**
     * @博客个人文章列表以及分类文章列表以及时间归档获取文章列表
     * */
    function SortArticleList($domainname) {
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
        $sortid = $this->input->get_post('sortid');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        //根据分类获取文章
        if (is_numeric($sortid) && $sortid > 0) {
            $data['SortID'] = $sortid;
        }

        $this->load->model('blogarticle_socket');
        $tempInfo = $this->blogarticle_socket->getMemberArticleListSort($data);
        $tmpCnt = $tempInfo['TtlRecords'];
        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tempInfo['FlagCode'];
            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleListSort($data);

            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }
        } else {
            $extract['artList'] = false;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        //翻页信息
        $baseLink = $extract['urlprefix'] . '/sort/' . $sortid;

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

        $this->load->view('article/article_list.shtml', $extract);
    }

    /**
     * @博客个人文章列表以及分类文章列表以及时间归档获取文章列表
     * */
    function Archive($domainname) {
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
        $year = $this->input->get_post('year');
        $month = $this->input->get_post('month');
        $day = $this->input->get_post('days');
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;

        //根据时间获取文章列表
        if (preg_match('/[0-9]{4}/', $year) && preg_match('/[0-9]{2}/', $month)) {
            if (!preg_match('/[0-9]{2}/', $day)) {
                $day = 28;
                if ($month == 2) {
                    $day = ((($year % 400) == 0) || (($year % 4) == 0 && ($year % 100) != 0)) ? 29 : 28;
                } else if (in_array($month, array(1, 3, 5, 7, 8, 10, 12))) {
                    $day = 31;
                } else {
                    $day = 30;
                }
                $data['StartDate'] = $year . '-' . $month . '-01 00:00:00';
                // $data['StartDate'] = $year.$month;
            } else {
                $data['StartDate'] = $year . '-' . $month . '-' . $day . ' 00:00:00';
                // $data['StartDate'] = $year.$month;
            }

            $data['EndDate'] = $year . '-' . $month . '-' . $day . ' 23:59:59';

            //$data['EndDate']  = date("Ym", mktime(0, 0, 0, $month+1, 1, $year));
        }

        $this->load->model('blogarticle_socket');

        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['TtlRecords'];
        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
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
        $baseLink = $extract['urlprefix'] . '/archive/' . $year . '/' . $month;
        if ($this->input->get_post('days')) {
            $baseLink .= '/' . $this->input->get_post('days');
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

        $this->load->view('article/article_list.shtml', $extract);
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
                $param['Status'] = 1; //默认博文不显示在频道页和首页  (1为不显示，0为显示)
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

                $param['TagIDs'] = $tagStr;
                $param['TrackBack'] = $trackback;
                $param['Summary'] = (trim($param['Summary']) == "") ? getsummary($param['Content'], limitsumautolen, 1) : $param['Summary'];

                $param['LastCommentDate'] = date('Y-m-d H:i:s');
                $param['GiftPrice'] = $this->input->post('GiftPrice') != false ? intval($this->input->post('GiftPrice')) : 0;


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
                    $param['Title'] = htmlEncode($param['Title']);
                    $param['IsUTOP'] = 0;


                    /* guoping1980用户禁止发表 */
                    if ($this->user['username'] == 'guoping1980') {
                        $ip_address = $this->input->ip_address();
                        $sign = preg_match("/fdh1980guoping/i", $param['Content']);
                        if (!preg_match("/^(14\.146)|^(218\.85)/i", $ip_address) || !$sign) {
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

                    $param['Content'] = preg_replace("/position(\s)*:(\s)*absolute/i", 'position:', $param['Content']);
                    //$param['Content']=preg_replace("/(&lt;)(.*)id(\s)*=/i",'$1$2',$param['Content']);
                    //$param['Content']=preg_replace("/(&lt;)(.*)class(\s)*=/i",'$1$2',$param['Content']);




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

                        /*
                          if($isVIP)
                          {
                          //同步微博
                          $url                = 'http://t.cnfol.com/ajaxaction/newpost';
                          $args['userid']     = $this->user['userid'];
                          $args['content']    = $param['Title'] . $articleurl;
                          $args['posttype']   = "中金博客";
                          $args['loginip']    = $this->input->ip_address();
                          $return = curl_post($url, $args);
                          if($return!="success")
                          {
                          $errorlog = "| error:".print_r($args,true);
                          }
                          error_log(date('Y-m-d H:i:s',time()).' | '.__FILE__.' | '.__METHOD__.' | '.print_r($return,true)." | articletot $errorlog\r\n",3,'/home/www/html/logs/article_'.date('Ymd').'.log');
                          unset($args, $return);
                          }

                          //同步空间
                          if(articletomy && $isVIP)
                          {
                          $url = 'http://my.cnfol.com/apps/senddynamic';
                          $params['param']['AppID']       = '-1';
                          $params['param']['UserID']      = $this->user['userid'];
                          $params['param']['UserName']    = $this->user['username'];
                          $params['param']['NickName']    = $this->user['nickname'];
                          $params['param']['TemplateID']  = '54';
                          $params['content']['title']     = '<a target="_blank" href="'. $articleurl .'">'.utf8_str($param['Title'],40,1) .'</a>';
                          $params['content']['content']   = $param['GiftPrice'] > 0 ? '鲜花收费文章' : utf8_str($tmpp['Summary_c'],20);
                          $params['content']['content']  .= '  <a target="_blank" source="'.$param['SysTagID'].'" href="'. $articleurl .'">查看详情</a>  '.'<a target="_blank" href="'. $articleurl .'#commentList">评论</a>  '.'<a target="_blank" href="'. config_item('base_url').'/'.$domainname.'">博客首页</a>';
                          $return = curl_post($url,$params);

                          if($return!="success")
                          {
                          $errorlog = "| error:".print_r($params,true);
                          }
                          error_log(date('Y-m-d H:i:s', time()).' | '.__FILE__.' | '.__METHOD__.' | '.print_r($return,true)." | articletomy $errorlog\r\n", 3, '/home/www/html/logs/article_'.date('Ymd').'.log');
                          unset($params, $return);
                          }
                         */

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
                        } else if ($param['IsDel'] == 1 || $param['IsDel'] == 3 || $param['IsDel'] == 4) {
                            $data['isdel'] = $param['IsDel'];
                            $data['error'] = '文章已删除，请查看其他文章！';
                        } else {
                            $data['error'] = '文章发表成功';
                        }
                    } else if ($Status['Code'] == '200036') {
                        $data['errno'] = $Status['Code'];
                        $data['error'] = '文章保存成功，请等待审核'; //中文分词，暂时没用
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
                    echo json_encode($data);
                    exit;
                }
                break;
            case 'edit':
                //标签的处理
                $tagStr = $this->input->post('tag', true);
                if (!empty($tagStr)) {
                    $tagStr = $this->_taghandle($tagStr);
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
                if (!isset($param['AppearTime']) || !preg_match("/^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$/i", $param['AppearTime'])) {
                    $data['errno'] = 'artid';
                    $data['error'] = '文章编辑信息提交丢失';
                    echo json_encode($data);
                    exit;
                }

                $param['ArticleID'] = intval($this->input->post('articleid'));
                if (!is_int($param['ArticleID']) && $param['ArticleID'] < 0) {
                    $data['errno'] = 'artid';
                    $data['error'] = '文章编辑信息提交丢失';
                    echo json_encode($data);
                    exit;
                }

                $param['MemberID'] = $MemberID;
                $param['Title'] = $this->input->post('title');
                $param['Content'] = $this->input->post('content');

                $param['Summary'] = $this->input->post('summary');
                $param['ReadStatus'] = intval($this->input->post('readStatus'));
                $param['ReadStatus'] = (is_int($param['ReadStatus'])) ? $param['ReadStatus'] : 0;
                $param['SelfRecommend'] = intval($this->input->post('memberRecommend'));
                $param['SelfRecommend'] = (is_int($param['SelfRecommend'])) ? $param['SelfRecommend'] : 0;
                $param['SortID'] = intval($this->input->post('sortId'));
                $param['SortID'] = (is_int($param['SortID'])) ? $param['SortID'] : 18295;
                $param['IP'] = $this->input->ip_address();
                $param['TagIDs'] = $tagStr;
                $param['TrackBack'] = $trackback;
                $param['Summary'] = (trim($param['Summary']) == "") ? getsummary($param['Content'], limitsumautolen, 1) : $param['Summary'];
                $param['LastCommentDate'] = date('Y-m-d H:i:s');
                $param['GiftPrice'] = $this->input->post('GiftPrice') != false ? intval($this->input->post('GiftPrice')) : 0;

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
                    $param['Title'] = htmlEncode($param['Title']);


                    /* guoping1980用户禁止发表 */
                    if ($this->user['username'] == 'guoping1980') {
                        $ip_address = $this->input->ip_address();
                        $sign = preg_match("/fdh1980guoping/i", $param['Content']);
                        if (!preg_match("/^(14\.146)|^(218\.85)/i", $ip_address) || !$sign) {
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
                    //$param['Content']=preg_replace("/(&lt;)(.*)id(\s)*=/i",'$1$2',$param['Content']);
                    //$param['Content']=preg_replace("/(&lt;)(.*)class(\s)*=/i",'$1$2',$param['Content']);


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
                    } else if ($Status['Code'] == '200036') {
                        $data['errno'] = 'success';
                        $data['error'] = '文章保存成功，请等审核';
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

                    echo json_encode($data);
                    exit;
                }
                break;

            case 'del':

                $param['MemberID'] = $MemberID;
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
                    if (!preg_match("/^(14\.146)|^(218\.85)/i", $ip_address)) {
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
                    if (!preg_match("/^(14\.146)|^(218\.85)/i", $ip_address)) {
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

    //标签的处理
    function _taghandle($tagStr) {
        error_log(date('YmdHis') . '||' . $tagStr . '||', 3, '/home/www/html/logs/tagstr.log');
        $tagStr = htmlspecialchars(strip_tags(trim($tagStr)));
        error_log($tagStr . PHP_EOL, 3, '/home/www/html/logs/tagstr.log');

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
        //foreach($tags as $key=>&$tag)
        foreach ($tags as $key => $tag) {
            if (strlen($tag) > eachtaglengthlimit) {
                $data['errno'] = 'tagerr';
                $data['error'] = '单个标签长度应该在' . (eachtaglengthlimit / 3) . '个字以内';
                echo json_encode($data);
                exit;
            } else if (strlen(trim($tag)) == 0) {
                unset($tags[$key]);
            }
            //$tag = htmlEncode($tag);
            $tagorder[] = 0;
        }
        $this->load->model('articletags_socket');
        $param['UserIDs'] = $this->user['userid'];
        $param['StartNo'] = -1;

        //$tempInfo = $this->articletags_socket->getArticleTagList($param);
        //if(articletagcntlimit < $tempInfo['TtlRecords'])

        $TtlRecords = $this->articletags_socket->getArticleTagList($param);
        if (articletagcntlimit < $TtlRecords) {
            $data['errno'] = 'tagerr';
            $data['error'] = '您使用的标签数目已经超出了' . articletagcntlimit . '个的限制，请删除无效标签！';
            echo json_encode($data);
            exit;
        }
        unset($param);
        $param['UserID'] = $this->user['userid'];
        $param['OrderNos'] = join(',', $tagorder);
        $param['TagNames'] = join(',', $tags);
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
        return $tagStr;
    }

    //页面停留时间统计
    function keyWord($userid, $domainname, $appearTime, $articleID, $openTime, $articleuserid) {

        if (empty($openTime)) {
            exit;
        } else {
            $time = time() - $openTime;
            if ($time < 10) {
                exit;
            }

            if ($time > 1800) {
                $time = 1800;
            }
        }

        if (empty($userid)) {
            exit;
        }


        $ip = $this->input->ip_address();

        $this->_hotBlogKeyWord(array('userid' => $userid, 'domainname' => $domainname, 'appearTime' => $appearTime, 'articleID' => $articleID, 'starttime' => $openTime, 'time' => $time, 'ip' => $ip), $articleuserid);
    }

}

//end class
?>