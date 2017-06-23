<?php

/* * **********************
 * 功能：   博客RSS
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Rss extends MY_Controller {

    function Rss() {
        parent::MY_Controller();
    }

    /**
     * @ 博客推荐文章RSS
     * */
    function Remmend($recommend) {
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
        $data['ShowMode'] = 1;
        $data['StartNo'] = 0;
        $data['QryCount'] = channelarticlersspagesize;
        $this->load->model('channel_socket');
        $TagArtList = $this->channel_socket->getRecommendArticle($data);

        $this->load->library('rss');
        if ($recommend == 2) {
            $title = '中金博客财经时评最新文章';
        } else if ($recommend == 3) {
            $title = '中金博客高手看市最新文章';
        } else {
            $title = '中金博客最新推荐文章';
        }
        $this->rss->SetChannelTitle($title);
        $link = config_item('base_url');
        $this->rss->SetChannelLink($link);
        $description = '中金在线 中金博客 财经博客 ' . $title;
        $this->rss->SetChannelDesc($description);

        if (isset($TagArtList['Record']) && !empty($TagArtList['Record'])) {
            foreach ($TagArtList['Record'] as $art) {
                if ($art['GiftPrice'] > 0)
                    $art['Summary'] = '您必须一次性赠送≥' . $art['GiftPrice'] . '朵鲜花才可查看该篇文章。';
                $arturl = $link . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                $this->rss->AddItem($art['Title'], $arturl, $art['Summary'], $art['AppearTime']);
            }
        }
        $this->rss->Show();
    }

    /**
     * @ 按标签分类的博客文章RSS
     * */
    function TagList($systagid) {
        $SysTag = &config_item('sysTagList');
        if (!array_key_exists($systagid, $SysTag)) {
            cnfolAlert('您要访问的标签信息不存在');
            cnfolLocation();
            exit;
        }
        $data['TagID'] = $systagid;
        $limitgroups = &config_item('limitgroups');
        if (FALSE != $limitgroups) {
            $data['MemberGroups'] = $limitgroups;
        }
        $data['ShowMode'] = 1;
        $data['StartNo'] = 0;
        $data['QryCount'] = channelarticlersspagesize;
        $this->load->model('channel_socket');
        $TagArtList = $this->channel_socket->getSyaTagArticleList($data);
        $TagTitle = $SysTag[$systagid];

        $this->load->library('rss');
        $title = '中金博客最新' . $TagTitle . '文章列表';
        $this->rss->SetChannelTitle($title);
        $link = config_item('base_url');
        $this->rss->SetChannelLink($link);
        $description = '中金在线 中金博客 财经博客 ' . $title;
        $this->rss->SetChannelDesc($description);

        if (isset($TagArtList['Record']) && !empty($TagArtList['Record'])) {
            foreach ($TagArtList['Record'] as $art) {
                if ($art['GiftPrice'] > 0)
                    $art['Summary'] = '您必须一次性赠送≥' . $art['GiftPrice'] . '朵鲜花才可查看该篇文章。';
                $arturl = $link . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                $this->rss->AddItem($art['Title'], $arturl, $art['Summary'], $art['AppearTime']);
            }
        }
        $this->rss->Show();
    }

    /**
     * @ 首页rss
     * */
    function IndexList() {
        $limitgroups = &config_item('limitgroups');
        if (FALSE != $limitgroups) {
            $data['MemberGroups'] = $limitgroups;
        }
        $data['ShowMode'] = 1;
        $data['StartNo'] = 0;
        $data['QryCount'] = channelarticlersspagesize;
        $this->load->model('channel_socket');
        $TagArtList = $this->channel_socket->getSyaTagArticleList($data);
        //d($TagArtList);
        $TagTitle = $SysTag[$systagid];

        $this->load->library('rss');
        $title = '中金博客最新文章列表';
        $this->rss->SetChannelTitle($title);
        $link = config_item('base_url');
        $this->rss->SetChannelLink($link);
        $description = '中金在线 中金博客 财经博客 ' . $title;
        $this->rss->SetChannelDesc($description);

        if (isset($TagArtList['Record']) && !empty($TagArtList['Record'])) {
            foreach ($TagArtList['Record'] as $art) {
                if ($art['GiftPrice'] > 0)
                    $art['Summary'] = '您必须一次性赠送≥' . $art['GiftPrice'] . '朵鲜花才可查看该篇文章。';
                $arturl = $link . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                $this->rss->AddItem($art['Title'], $arturl, $art['Summary'], $art['AppearTime']);
            }
        }
        $this->rss->Show();
    }

    /**
     * @ 直播文章列表
     * */
    function OnlineArticles() {
        $limitgroups = &config_item('limitgroups');
        if (FALSE != $limitgroups) {
            $data['MemberGroups'] = $limitgroups;
        }
        $data['IsPrime'] = 1;
        $data['ShowMode'] = 1;
        $data['StartNo'] = 0;
        $data['QryCount'] = channelarticlersspagesize;
        $this->load->model('channel_socket');
        $TagArtList = $this->channel_socket->getSyaTagArticleList($data);

        $this->load->library('rss');
        $title = '中金博客最新直播文章列表';
        $this->rss->SetChannelTitle($title);
        $link = config_item('base_url');
        $this->rss->SetChannelLink($link);
        $description = '中金在线 中金博客 财经博客 ' . $title;
        $this->rss->SetChannelDesc($description);

        if (isset($TagArtList['Record']) && !empty($TagArtList['Record'])) {
            foreach ($TagArtList['Record'] as $art) {
                if ($art['GiftPrice'] > 0)
                    $art['Summary'] = '您必须一次性赠送≥' . $art['GiftPrice'] . '朵鲜花才可查看该篇文章。';
                $arturl = $link . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                $this->rss->AddItem($art['Title'], $arturl, $art['Summary'], $art['AppearTime']);
            }
        }
        $this->rss->Show();
    }

    /**
     * @ 个人博客文章的RSS 和文章分类RSS
     * */
    function ActicleList($domainname) {
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        //获取博客配置信息
        $blogconfig = $this->_getBlogConfig($bloginfo['MemberID']);
        $default = &config_item('default');
        $sortid = $this->input->get_post('sortid');
        $data['MemberID'] = $bloginfo['MemberID'];
        $data['StartNo'] = 0;
        $data['QryCount'] = $blogconfig['DisplayNumber'];
        $sortname = '';
        if (is_numeric($sortid) && $sortid > 0) {
            $data['SortID'] = $sortid;
            $param['MemberID'] = $bloginfo['MemberID'];
            $param['SortID'] = $sortid;
            if ($default['articlesort'][0] == $sortid) {
                $sortname = $default['articlesort'][1];
            } else {
                $this->load->model('articlesort_socket');
                $sortinfo = $this->articlesort_socket->getSortInfoByID($param);
                $sortname = isset($sortinfo['Name']) ? $sortinfo['Name'] : '';
            }
        }
        $this->load->model('blogarticle_socket');
        $artlist = $this->blogarticle_socket->getMemberArticleList($data);
        if ($artlist['RetRecords'] == 1) {
            $artlist['Record'] = array('0' => $artlist['Record']);
        }
        $this->load->library('rss');
        $title = (isset($data['SortID']) && $data['SortID'] > 0) ? '中金博客' . $sortname . '分类文章列表' : '中金博客最新文章列表';
        $this->rss->SetChannelTitle($bloginfo['DomainName'] . $title);
        $link = config_item('base_url') . '/' . $bloginfo['DomainName'];
        $this->rss->SetChannelLink($link);
        $description = '中金博客 财经博客 ' . $bloginfo['BlogName'] . ' ' . $title;
        $this->rss->SetChannelDesc($description);

        if (!empty($artlist) && !empty($artlist['Record'])) {
            foreach ($artlist['Record'] as $art) {
                //$this->rss->AddItem($art['Title'], $link.'/article/'.$art['ArticleID'].'.html', $art['Summary'], $art['AppearTime']);
                if ($art['GiftPrice'] > 0)
                    $art['Summary'] = '您必须一次性赠送≥' . $art['GiftPrice'] . '朵鲜花才可查看该篇文章。';
                $arturl = $link . '/' . $art['DomainName'] . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html';
                $this->rss->AddItem($art['Title'], $arturl, '', $art['AppearTime']);
            }
        }
        $this->rss->Show();
    }

    /**
     * @ 用户推荐文章
     * */
    function RecommendArticles() {
        $this->load->model('channel_socket');
        $data['StartNo'] = 0;
        $data['QryCount'] = channelarticlersspagesize;

        $artlist = $this->channel_socket->getUserRecommendArticle($data);

        $this->load->library('rss');
        $title = '中金博客用户推荐文章列表';
        $this->rss->SetChannelTitle($title);
        $link = config_item('base_url');
        $this->rss->SetChannelLink($link);
        $description = '中金博客 财经博客 ' . $title;
        $this->rss->SetChannelDesc($description);

        if (!empty($artlist)) {
            foreach ($artlist as $art) {
                $this->rss->AddItem($art['Title'], $art['URL'], '', $art['DataTime']);
            }
        }
        $this->rss->Show();
    }

    /**
     * @ 热门专题
     * */
    function HotTopicArticles() {
        $this->load->model('channel_socket');
        $data['StartNo'] = 0;
        $data['QryCount'] = channelarticlersspagesize;

        $artlist = $this->channel_socket->getHotTopicArticleList($data);

        $this->load->library('rss');
        $title = '中金博客热门专题文章列表';
        $this->rss->SetChannelTitle($title);
        $link = config_item('base_url');
        $this->rss->SetChannelLink($link);
        $description = '中金博客 财经博客 ' . $title;
        $this->rss->SetChannelDesc($description);

        if (!empty($artlist)) {
            foreach ($artlist as $art) {
                $art['Title'] = strip_tags($art['title']);
                preg_match_all('/.*href="([^"]*)".*/', str_replace("'", '"', $art['title']), $match);

                $art['URL'] = $match[1][0];
                $art['DataTime'] = date('Y-m-d H:i:s', $art['lastupdate']);
                $this->rss->AddItem($art['Title'], $art['URL'], '', $art['DataTime']);
            }
        }
        $this->rss->Show();
    }

}

//end class
?>