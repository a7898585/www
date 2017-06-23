<?php

/* * **********************
 * 功能：   博客文章分类
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Articletag extends MY_Controller {

    function Articletag() {
        parent::MY_Controller();
        $this->load->library("MP_Cache");
        $this->pagesize = $this->config->item("showc");
    }

    /**
     * @ 博客文章分类列表
     * */
    function TagList($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $IN = parse_incoming();
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
        $this->load->model('articletags_socket');
        unset($data);
        if ($IN['act'] == 'search' && $IN[tagName] != '输入关键字查找') {
            $data['UserIDs'] = $this->user['userid'];
            $data['Names'] = $IN['tagName'];
            $extract['tempList'] = $this->articletags_socket->getArticleTagList($data);
        } else {
            $data['UserIDs'] = $this->user['userid'];
            $data['StartNo'] = -1;
            $extract['tempCnt'] = $this->articletags_socket->getArticleTagList($data);
            $data['StartNo'] = ($page - 1) * articletagpagesize;
            if ($data['StartNo'] > $extract['tempCnt']) {
                $page = 1;
            }
            $data['StartNo'] = ($page - 1) * articletagpagesize;
            $data['QryCount'] = articletagpagesize; //默认每个用户最多50个标签
            //$data['FlagCode']		= $tempInfo['FlagCode'];

            if ($extract['tempCnt'] > 0) {
                $extract['tempList'] = $this->articletags_socket->getArticleTagList($data);
            } else {
                $extract['tempList']['RetRecords'] = 0;
            }

            //翻页函数
            $this->load->library('pagebarsnew');
            $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/articletag/List';

            $this->pagebarsnew->Page($extract['tempCnt'], $page, $data['QryCount'], $baseLink, '/');
            $extract['pagebar'] = $this->pagebarsnew->upDownList();
        }
        $extract['user'] = $this->user;
        $extract['userid'] = $this->user['userid'];
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']); //判断是否自己的
        $blocks = &$this->config->item('block');
        $extract['block'] = $blocks['articletaglist'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['articletaglisttitle'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;
        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * Ajax调用返回Json
     * @增删修改动作
     * */
    function Action($domainname) {
        $this->_checkLogin();
        $MemberID = $this->input->post('memid');
        $flashCode = $this->input->post('flashCode');
        if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
            $data['errno'] = "verify";
            $data['error'] = "验证信息非法，禁止操作";
            echo json_encode($data);
            exit(-1);
        }
        $act = $this->input->post('act');


        switch ($act) {
            case 'deltag':
                $TagID = $this->input->post('TagID');
                $param['TagIDs'] = $TagID;
                $param['UserIDs'] = $this->user['userid'];
                $this->load->model('articletags_socket');
                if ($this->articletags_socket->delArticleTag($param)) {
                    $data['errno'] = "success";
                    $data['error'] = "文章标签删除成功";
                } else {
                    $data['errno'] = "failed";
                    $data['error'] = "文章标签删除失败";
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
      文章发表调用标签
     */

    function AjaxTagList() {
        $this->_checkLogin();
        $param['UserIDs'] = $this->user['userid'];
        $param['Status'] = 0;
        $param['StartNo'] = 0;
        $param['QryCount'] = 20;
        //$param['AjaxList'] = 1;
        $this->load->model('articletags_socket');
        $data = $this->articletags_socket->getArticleTagList($param);
        if ($data['RetRecords'] > 0) {
            $str = '';
            $data = (isset($data['Record']['TagID'])) ? array(0 => $data['Record']) : $data['Record'];
            foreach ($data as $tag) {
                if (trim($tag['Name']) == "")
                    continue;
                $str .= '<a href="javascript:;" onclick="ShowTag(\'' . $tag['Name'] . '\')" style="text-decoration: underline;line-height:25px;height:25px" id="tagid' . $tag['TagID'] . '">' . $tag['Name'] . '</a>，&nbsp;';
            }
            echo $str;
        }
        else {
            echo '您目前没有添加任何标签可供使用';
        }
    }

    /**
     * @tag 文章列表
     * */
    function TagArticleList($domainname) {
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
        $tagID = intval($this->input->get_post('tagid'));
        $data['MemberID'] = $extract['bloginfo']['MemberID'];

        $data['StartNo'] = -1;
        $data['TagID'] = $tagID;
        $this->load->model('blogarticle_socket');
        $tmpInfo = $this->blogarticle_socket->getBlogTagArticle($data);
        $tmpCnt = $tmpInfo['TtlRecords'];


        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            $data['IsSummary'] = $extract['blogconfig']['ShowMode'];
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tmpInfo['FlagCode'];
            //print_r($data);

            $extract['artlist'] = $this->blogarticle_socket->getBlogTagArticle($data);

            if ($tmpCnt == 1) {
                if (empty($extract['artlist']['Record'][0])) {
                    $extract['artlist']['Record'] = array($extract['artlist']['Record']);
                }
            }
        } else {
            $extract['artlist'] = false;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        //翻页信息
        $url = preg_replace('/' . $tagID . '\/\d+/', $tagID, $this->input->server('REQUEST_URI'));
        $baseLink = config_item('base_url') . $url;

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
     * @tag 文章列表-新版
     * */
    function newTagArticleList($domainname) {

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
        $tagID = intval($this->input->get_post('tagid'));
        $data['MemberID'] = $extract['bloginfo']['MemberID'];

        $data['StartNo'] = -1;
        $data['TagID'] = $tagID;


        $extract['recommend'] = '';
        $recommend = '';
        if ($this->input->get_post('recommend')) {
            $data['SelfRecommend'] = $this->input->get_post('recommend');
            $data['AllRecommend'] = '1'; //取后台和自己推荐的
            $extract['recommend'] = '1';
            $recommend = '/recommend-1';
        }


        $ismut = '';
        $extract['ismut'] = 0;
        if ($this->input->get_post('ismut') != '') {
            $data['IsMultimedia'] = $this->input->get_post('ismut');
            $ismut = '/ismut-' . $this->input->get_post('ismut');
        }

        $istop = '';
        if ($this->input->get_post('istop') != '') {
            $data['IsTop'] = $this->input->get_post('istop');
            $istop = '/istop-' . $this->input->get_post('istop');
        }

        $data['MemberID'] = $extract['bloginfo']['MemberID'];

        $this->load->model('blogarticle_socket');
        $tmpInfo = $this->blogarticle_socket->getBlogTagArticle($data);


        if ($data['IsMultimedia'] == 1) {
            $key = 'ismul_1';
        } else if ($data['IsMultimedia'] == 2) {
            $key = 'ismul_2';
        } else if ($data['SelfRecommend'] != '') {
            $key = 'Recommend';
        } else if ($data['IsTop'] != '') {
            $key = 'istop';
        } else {
            $key = 'all';
        }

        $tmpCnt = $tmpInfo['TagID_' . $data['TagID'] . $key];

        if ($page > ceil($tmpCnt / $extract['blogconfig']['DisplayNumber'])) {
            $page = 1;
        }
        if ($tmpCnt > 0) {
            //$data['IsSummary'] = $this->pagesize['articlelist'];
            $data['StartNo'] = ($page - 1) * $extract['blogconfig']['DisplayNumber']; //代表到第几页
            $data['QryCount'] = $extract['blogconfig']['DisplayNumber'];
            $data['FlagCode'] = $tmpInfo['FlagCode'];

            $extract['artlist'] = $this->blogarticle_socket->getBlogTagArticle($data);

            if ($tmpCnt == 1) {
                if (empty($extract['artlist']['Record'][0])) {
                    $extract['artlist']['Record'] = array($extract['artlist']['Record']);
                }
            }
            $param['BlogType'] = 1;
            $param['StartNo'] = -1;
            $this->load->model('articlecomment_socket');
            foreach ($extract['artlist']['Record'] as $v) {

                $param['ArticleID'] = $v['ArticleID'];
                $tempInfo = $this->articlecomment_socket->getArtCommentList($param);
                $num[$v['ArticleID']] = $tempInfo['TtlRecords'];
            }
        } else {
            $extract['artlist'] = false;
        }

        $extract['urlprefix'] = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'];
        //翻页信息
        $url = preg_replace('/' . $tagID . '\/\d+/', $tagID, $this->input->server('REQUEST_URI'));
        $baseLink = config_item('base_url') . $url . $ismut . $recommend . $istop;

        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($tmpCnt, $page, $extract['blogconfig']['DisplayNumber'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        $extract['modulepath'] = config_item('module_path');
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . $blocks['articlelisttitle'] . '-' . $extract['bloginfo']['NickName'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['num'] = $num;
        $extract['tagid'] = $tagID;
        $extract['show_draft'] = $blocks['show_draft'];

        $this->load->view("article/blog_articlelist_tag.shtml", $extract);
    }

    /**
     * @ 我的标签
     * */
    function myTagList($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $this->load->model('articletags_socket');
        unset($data);
        $data['UserIDs'] = $extract['bloginfo']['UserID'];
        $data['StartNo'] = -1;

        $extract['tempCnt'] = $this->articletags_socket->getArticleMyTagList($data);

        $data['StartNo'] = 0;
        $data['QryCount'] = 9;


        if ($extract['tempCnt'] > 0) {
            $extract['tempList'] = $this->articletags_socket->getArticleMyTagList($data);
        } else {
            echo('暂无标签...');
            exit;
        }

        $isMy = $this->ismy($this->user['userid'], $data['UserIDs']); //判断是否是博主自己的博客页面

        $str = '';
        foreach ($extract['tempList']['Record'] as $value) {

            $dot = '';
            $tagname = $value['Name'];
            if (utf8_strlen($value['Name']) > 8) {
                $dot = '..';
                $tagname = utf8_str($value['Name'], 6, 'false');
            }

            $del = $isMy ? '<i onClick=Dialog("DeterminePop");$("#currenttagid").val(' . $value['TagID'] . '); ></i>' : '';
            $str.='<a href="' . config_item("base_url") . '/' . $domainname . '/tag/' . $value["TagID"] . '" class="tagidTurnPage" title="' . $value['Name'] . '" target="_blank">' . $tagname . $dot . $del . '</a>';
        }

        echo($str);
    }

    /*
      文章发表调用热门标签
     */

    function AjaxHotTagList() {
        //$this->_checkLogin();
        $param['StartNo'] = 0;
        $param['QryCount'] = 20;
        //$this->load->model('articletags_socket');
        //$data = $this->articletags_socket->getHotTagList($param);
        $data = getHotTag('././shtml/blog_publish_article_hot.shtml');

        if ($data) {
            $str = '';
            $i = 1;

            foreach ($data as $value) {

                $dot = '';
                if (strlen($value) > 12) {
                    $dot = ' ...';
                }

                if ($i < 5) {

                    $str .= '<a href="javascript:;" class="Armbq" name="Clkadd"><span onclick=ShowTag("' . $value . '"); >' . utf8_str($value, 8, 'false') . $dot . '</span><i class="hotTagShow" ></i></a>';
                } else {
                    $str .='<a href="javascript:;" class="Armbq" name="Clkadd"  style="display:none;"><span onclick=ShowTag("' . $value . '"); >' . utf8_str($value, 8, 'false') . $dot . '</span><i class="hotTagHidden" ></i></a>';
                }
                $i++;
            }
            echo $str . '<a href="javascript:;" id="restoreHot" class="Armbq" name="Clkadd"  style="color:red;display:none;" onclick="cancelHotTag();">还原热门标签</a>';
        } else {
            echo '暂无热门标签可供使用';
        }
    }

    function searchResult() {
        $IN = parse_incoming();
        //$this->_checkLogin();
        $data['UserIDs'] = $this->user['userid'];
        $data['Names'] = $IN['tagName'];
        var_dump($data);
        $this->load->model('articletags_socket');
        $rs = $this->articletags_socket->getArticleTagList($data);
        var_dump($rs);
    }

}

//end class
?>