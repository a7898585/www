<?php

/* * **********************
 * 功能：   手机博客   更多  文章列表
 * author： jianglw
 * add：  2013-09-11

 * *********************** */

class More extends MY_Controller {

    function More() {
        parent::MY_Controller();
        $this->cache->expire = EXPIRETIME1;
        $this->load->model("blogmore_socket");
        $this->load->model("blogarticle_socket");
    }

    /**
     * 名家高手
     * 财经时评recommend=2            
     * 高手看市recommend=3
     */
    function masterHand() {
        $userid = $_COOKIE['cookie']['passport']['userId'];
        if ($userid) {
            //获取个人博客列表
            $bloglist = $this->_getBlogListByUid($userid);
            $extract['bloginfo']['DomainName'] = getPrimariBlogDomain($bloglist);
        }
        $extract['moreName'] = '名家高手';
        $extract['userid'] = $userid;

        $mjDataNum = $this->blogarticle_socket->getRecommendArticleList('4', '-1', 10);
        $FlagCode = $mjDataNum['FlagCode'];

        $page = 1;
        $pagesize = 10;
        $start = ($page - 1) * $pagesize; //代表到第几页
        $mjData = $this->blogarticle_socket->getRecommendArticleList('4', $start, $pagesize, $FlagCode);

        //$gsData = $this->blogarticle_socket->getRecommendArticle('2', $start, $pagesize);

        $extract['list'] = $mjData['Record'];

        $blocks = &$this->config->item('block');
        $extract['title'] = '名家高手-频道文章列表-中金博客';
        
        $extract['moreType'] = 'moreMasterHand';
        
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['personalhead'] = $blocks['personalhead'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $extract['baseurl'] = &config_item('base_url');

        $this->load->view('channal/master_hand.shtml', $extract);
    }

    /**
     * 获取更多名家高手文章
     */
    function moreMasterHand() {
        $baseurl = &config_item('base_url');

        $mjDataNum = $this->blogarticle_socket->getRecommendArticleList('4', '-1', 10);
        $FlagCode = $mjDataNum['FlagCode'];

//    	error_log(print_r($FlagCode,true), 3, config_item('logPath').'a111.log');

        $pagesize = MYBLOG_MORE_PAGESIZE;
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $start = ($page - 1) * $pagesize;
        $mjData = $this->blogarticle_socket->getRecommendArticle('4', $start, $pagesize, $FlagCode);
        $str = '';
        if (!empty($mjData['Record'])) {
            foreach ($mjData['Record'] as $val) {
                $artTitle = filter($val['Title']);
                $artContent = filter(filter($val['Summary']));
                if (strlen($artTitle) > 28) {
                    $val['Title'] = utf8_str($artTitle, 28) . '...';
                }
                if (strlen($artContent) > 70) {
                    $val['Summary'] = utf8_str($artContent, 70);
                }
                $arturl = $baseurl . '/' . $val['DomainName'] . '/article/' . strtotime($val['AppearTime']) . '-' . $val['ArticleID'] . '.html';
                $str .= '<div class="Lst-o-new Mt10 Pl10"><div class="Lst-new-tit F16 Red_color">';
                if ($val['Recommend'] == 2 || $val['Recommend'] == 3 || $val['IsUsed'] == 1) {
                    $str .= '<font class="Fl">[荐]</font>';
                }
                if ($val['IsTop'] == 1) {
                    $str .= '<font class="Fl">[顶]</font>';
                }
                $str .= '<a href="' . $arturl . '" class="Fl">' . $val['Title'] . '</a>';
                if ($val['PictureUrl'] != '0' && !empty($val['PictureUrl'])) {
                    $str .= '<span class="Bh_img Fl"></span>';
                }
                $str .= '<time class="Fr Hui_color Pr10 F10">' . timeop($val['AppearTime']) . '</time></div>';
                $str .= '<p class="Lst-new-desc F14 Pr10"><a href="' . $arturl . '">' . $val['Summary'] . '</a></p><h4 class="Hb_author F12 Hui_color">作者：' . $val['NickName'] . '</h4></div>';
            }
            $type = 1;
            $currentpage = $page + 1;
        } else {
            $currentpage = $page;
            $type = 2;
        }
        echo json_encode(array('data' => $str, 'error' => $type, 'page' => $currentpage));
    }

    /**
     * 获取 分类标签 文章
     * @param type $tag
     */
    function getUseTagList($tag) {
        
        
        $TagInfo = $this->blogmore_socket->getTagInfo($tag);
        $mjDataNum = $this->blogarticle_socket->getRecommendArticleList('4', '-1', 10);
        $FlagCode = $mjDataNum['FlagCode'];
        $Data = $this->blogarticle_socket->getSyaTagArticleList($TagInfo['tagid'], 0, 10, $FlagCode);

        $extract['list'] = $Data['Record'];
        $blocks = &$this->config->item('block');
        $extract['title'] = $TagInfo['tagname'] . '-频道文章列表-中金博客';
        $extract['commonheader'] = $blocks['commonheader'];
        $extract['personalhead'] = $blocks['personalhead'];
        $extract['commonfooter'] = $blocks['commonfooter'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['moreType'] = 'moreUseTagList';
        $extract['tagType'] = $tag;
        
        $userid = $_COOKIE['cookie']['passport']['userId'];
        if ($userid) {
            //获取个人博客列表
            $bloglist = $this->_getBlogListByUid($userid);
            $extract['bloginfo']['DomainName'] = getPrimariBlogDomain($bloglist);
        }
        $extract['moreName'] = $TagInfo['tagname'];
        $extract['userid'] = $userid;

        $this->load->view('channal/master_hand.shtml', $extract);
    }

    /**
     * 获取更多 分类标签 文章
     */
    function moreUseTagList() {
        $baseurl = &config_item('base_url');
        $pagesize = MYBLOG_MORE_PAGESIZE;
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $tagType = $this->input->get_post('tagType');
        $TagInfo = $this->blogmore_socket->getTagInfo($tagType);
        $start = ($page - 1) * $pagesize;
        $mjData = $this->blogarticle_socket->getSyaTagArticleList($TagInfo['tagid'], $start, $pagesize);
//        var_dump($mjData);
        $str = '';
        if (!empty($mjData['Record'])) {
            foreach ($mjData['Record'] as $val) {
                $artTitle = filter($val['Title']);
                $artContent = filter(filter($val['Summary']));
                if (strlen($artTitle) > 28) {
                    $val['Title'] = utf8_str($artTitle, 28);
                }
                if (strlen($artContent) > 70) {
                    $val['Summary'] = utf8_str($artContent, 70);
                }
                $arturl = $baseurl . '/' . $val['DomainName'] . '/article/' . strtotime($val['AppearTime']) . '-' . $val['ArticleID'] . '.html';
                $str .= '<div class="Lst-o-new Mt10 Pl10"><div class="Lst-new-tit F16 Red_color">';
                if ($val['Recommend'] == 2 || $val['Recommend'] == 3 || $val['IsUsed'] == 1) {
                    $str .= '<font class="Fl">[荐]</font>';
                }
                if ($val['IsTop'] == 1) {
                    $str .= '<font class="Fl">[顶]</font>';
                }
                $str .= '<a href="' . $arturl . '" class="Fl">' . $val['Title'] . '</a>';
                if ($val['PictureUrl'] != '0' && !empty($val['PictureUrl'])) {
                    $str .= '<span class="Bh_img Fl"></span>';
                }
                $str .= '<time class="Fr Hui_color Pr10 F10">' . timeop($val['AppearTime']) . '</time></div>';
                $str .= '<p class="Lst-new-desc F14 Pr10"><a href="' . $arturl . '">' . $val['Summary'] . '</a></p><h4 class="Hb_author F12 Hui_color">作者：' . $val['NickName'] . '</h4></div>';
            }
            $type = 1;
            $currentpage = $page + 1;
        } else {
            $currentpage = $page;
            $type = 2;
        }
        echo json_encode(array('data' => $str, 'error' => $type, 'page' => $currentpage));
    }

}
