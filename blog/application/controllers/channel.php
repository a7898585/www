<?php

/* * **********************
 * 功能：   博客频道排行
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Channel extends MY_Controller {

    function Channel() {
        parent::MY_Controller();
        $this->cache->expire = EXPIRETIME1;
    }

    /**
     * 博客频道首页
     * @ return null
     * */
    function Index() {
        $data['channelTitle'] = $this->lang->language['title_index'];
        $data['channelKeywords'] = $this->lang->language['keywords_index'];
        $data['channelDescription'] = $this->lang->language['description_index'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_index.shtml', $data);
    }

    /**
     * 博客频道首页，飞信平台
     * @ return null
     * */
    function News() {
        $data['channelTitle'] = $this->lang->language['title_index'];
        $data['channelKeywords'] = $this->lang->language['keywords_index'];
        $data['channelDescription'] = $this->lang->language['description_index'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_news.shtml', $data);
    }

    /**
     * 按照推荐组获取
     * @ 博客推荐文章（高手看盘、名家看市）
     * @ 2011-09-26 修改为脚本跑
     * */
    function ArticleRecommend($recommend) {
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
        $countNum = file_get_contents('/home/www/html/newblog/shtml/channeltaglist/r' . $recommend . '/countNum.txt');
        $pagelimit = isset($countNum) ? $countNum : 0;

        $page = intval($this->input->get_post('page'));
        $page = ($page > $pagelimit || $page < 1) ? 1 : $page;
        $data['StartNo'] = channelarticlepagesize * ($page - 1);
        $data['QryCount'] = channelarticlepagesize;  //默认60条一页。 如果改动，脚本也要改
        //$this->load->model('channel_socket');
        //$extract['TagArtList'] = $this->channel_socket->getRecommendArticle($data);

        if ($recommend == 2) {
            $extract['channelTitle'] = $this->lang->language['title_R2'];
            $extract['channelKeywords'] = $this->lang->language['keywords_R2'];
            $extract['channelDescription'] = $this->lang->language['description_R2'];
        } else {
            $extract['channelTitle'] = $this->lang->language['title_R3'];
            $extract['channelKeywords'] = $this->lang->language['keywords_R3'];
            $extract['channelDescription'] = $this->lang->language['description_R3'];
        }

        $extract['RssURL'] = config_item('base_url') . '/rss/r' . $recommend . 'List.xml';

        //翻页信息
        $baseLink = config_item('base_url') . '/r' . $recommend . 'List/';
        $this->load->helper('channal');
        $extract['pagebar'] = drawpagebar($data['QryCount'] * $pagelimit, $page, $data['QryCount'], $baseLink);

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['header'] = $blocks['channalhead'];
        $extract['footer'] = $blocks['channalfoot'];
        $extract['categorymenu'] = $blocks['categorymenu'];
        $extract['shtml'] = $this->config->item('shtml_path');
        $extract['baseurl'] = $this->config->item('base_url');
        $extract['ArtListUrl'] = 'channeltaglist/r' . $recommend . '/' . $page . '.shtml';

        if (file_exists($extract['shtml'] . 'runcront_artclickday24rank_r' . $recommend . '.shtml')) {
            $extract['Art24RankUrl'] = 'runcront_artclickday24rank_r' . $recommend . '.shtml';
        } else {
            $extract['Art24RankUrl'] = 'runcront_artclickday24rank.shtml';
        }

        $this->load->view('channal/channal_tagarticlelist.shtml', $extract);
    }

    //系统标签文章列表 2011-09-26 修改为脚本跑
    function tagarticlelist($systagid) {
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

        $countNum = file_get_contents('/home/www/html/newblog/shtml/channeltaglist/' . $systagid . '/countNum.txt');
        $pagelimit = isset($countNum) ? $countNum : 0;

        $page = intval($this->input->get_post('page'));
        $page = ($page > $pagelimit || $page < 1) ? 1 : $page;
        $data['StartNo'] = channelarticlepagesize * ($page - 1);
        $data['QryCount'] = channelarticlepagesize; //默认60条一页。 如果改动，脚本也要改
        if ($systagid == 1471) {
            $data['QryCount'] = 40; //美酒博客每页40条 
        }

        //$this->load->model('channel_socket');
        //$extract['TagArtList'] = $this->channel_socket->getSyaTagArticleList($data);
        $extract['TagID'] = $systagid;
        $extract['TagTitle'] = $SysTag[$systagid];
        $extract['RssURL'] = config_item('base_url') . '/rss/' . $systagid . '.xml';

        //翻页信息
        $baseLink = config_item('base_url') . '/list/' . $systagid . ',';
        $this->load->helper('channal');

        $extract['pagebar'] = drawpagebar($data['QryCount'] * $pagelimit, $page, $data['QryCount'], $baseLink);

        $extract['channelTitle'] = $this->lang->language['title_' . $systagid];
        $extract['channelKeywords'] = $this->lang->language['keywords_' . $systagid];
        $extract['channelDescription'] = $this->lang->language['description_' . $systagid];

        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['header'] = $blocks['channalhead'];
        $extract['footer'] = $blocks['channalfoot'];
        $extract['categorymenu'] = $blocks['categorymenu'];
        $extract['shtml'] = $this->config->item('shtml_path');
        $extract['baseurl'] = $this->config->item('base_url');
        $extract['ArtListUrl'] = 'channeltaglist/' . $systagid . '/' . $page . '.shtml';

        if (file_exists($extract['shtml'] . 'runcront_artclickday24rank_' . $systagid . '.shtml')) {
            $extract['Art24RankUrl'] = 'runcront_artclickday24rank_' . $systagid . '.shtml';
        } else {
            $extract['Art24RankUrl'] = 'runcront_artclickday24rank.shtml';
        }

        if ($systagid == 1471) {
            $this->load->view('channal/channal_mjbkarticlelist.shtml', $extract);
        } else {
            $this->load->view('channal/channal_tagarticlelist.shtml', $extract);
        }
    }

    /**
     * 按投票数排行
     * @ 网友最支持文章
     * */
    function TopVoteArticleList() {
        $data['Rank_24'] = '';
        $data['Rank_48'] = '';
        $data['Rank_168'] = '';
        $rank = intval($this->input->get('rank'));
        if ($rank == 24) {
            $data['incRank'] = 'runcront_artvote_24.shtml';
            $data['Rank_24'] = ' color="red"';
        } else if ($rank == 48) {
            $data['incRank'] = 'runcront_artvote_48.shtml';
            $data['Rank_48'] = ' color="red"';
        } else {
            $data['incRank'] = 'runcront_artvote_168.shtml';
            $data['Rank_168'] = ' color="red"';
        }

        $data['channelTitle'] = $this->lang->language['title_hotvotearticle'];
        $data['channelKeywords'] = $this->lang->language['keywords_hotvotearticle'];
        $data['channelDescription'] = $this->lang->language['description_hotvotearticle'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_hotvotearticle.shtml', $data);
    }

    /**
     * 按投票数排行
     * @ 博客鲜花排行
     * */
    function GiftRankArticleList() {
        $data['Rank_12'] = '';
        $data['Rank_24'] = '';
        $data['Rank_168'] = '';
        $rank = intval($this->input->get('rank'));
        if ($rank == 12) {
            $data['incRank'] = 'runcront_giftrank_12.shtml';
            $data['Rank_12'] = ' color="red"';
        } else if ($rank == 24) {
            $data['incRank'] = 'runcront_giftrank_24.shtml';
            $data['Rank_24'] = ' color="red"';
        } else {
            $data['incRank'] = 'runcront_giftrank_168.shtml';
            $data['Rank_168'] = ' color="red"';
        }

        $data['channelTitle'] = $this->lang->language['title_giftrankarticle'];
        $data['channelKeywords'] = $this->lang->language['keywords_giftrankarticle'];
        $data['channelDescription'] = $this->lang->language['description_giftrankarticle'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_giftrankarticle.shtml', $data);
    }

    /**
     * 	@博客列表
     * */
    function HtmlBlogList() {
        $data['channelTitle'] = $this->lang->language['title_bloglist'];
        $data['channelKeywords'] = $this->lang->language['keywords_bloglist'];
        $data['channelDescription'] = $this->lang->language['description_bloglist'];
        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_htmlbloglist.shtml', $data);
    }

    /**
     * 博客人气排行按点击进行排行
     * @ 人气排行版
     * */
    function OnclickBlogRank() {
        $data['channelTitle'] = $this->lang->language['title_hotclickrank'];
        $data['channelKeywords'] = $this->lang->language['keywords_hotclickrank'];
        $data['channelDescription'] = $this->lang->language['description_hotclickrank'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_hotclickrank.shtml', $data);
    }

    /**
     * 按照博客的更新时间 
     * @ 最新更新200博客前200
     * */
    function UpdateBlogListNew() {
        $data['channelTitle'] = $this->lang->language['title_newupdateblog'];
        $data['channelKeywords'] = $this->lang->language['keywords_newupdateblog'];
        $data['channelDescription'] = $this->lang->language['description_newupdateblog'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_newupdateblog.shtml', $data);
    }

    /**
     * 按照博客的评论数排行
     * @ 今日热门回复排行
     * */
    function HotComentArticleList() {
        $data['channelTitle'] = $this->lang->language['title_dayhotcomment'];
        $data['channelKeywords'] = $this->lang->language['keywords_dayhotcomment'];
        $data['channelDescription'] = $this->lang->language['description_dayhotcomment'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_hotcomment.shtml', $data);
    }

    /**
     * 分为总排行和今日排行
     * @ 博客点击排行 今日点击Top50
     * */
    function TopOnclick() {
        $data['channelTitle'] = $this->lang->language['title_day50topclick'];
        $data['channelKeywords'] = $this->lang->language['keywords_day50topclick'];
        $data['channelDescription'] = $this->lang->language['description_day50topclick'];

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('/channal/channal_topclick.shtml', $data);
    }

    //直播文章
    //2011-09-26 修改为脚本跑
    function OnlineArticleList() {
        $limitgroups = &config_item('limitgroups');
        if (FALSE != $limitgroups) {
            $data['MemberGroups'] = $limitgroups;
        }
        //$pagelimit = 60; //最多翻页60
        $countNum = file_get_contents('/home/www/html/newblog/shtml/channeltaglist/online/countNum.txt');
        $pagelimit = isset($countNum) ? $countNum : 0;
        $page = intval($this->input->get_post('page'));
        //$this->load->model('channel_socket');
        $data['IsPrime'] = 1;  //精华就是直播
        $data['StartNo'] = -1;
        $page = ($page > $pagelimit || $page < 1) ? 1 : $page;
        $data['StartNo'] = channelarticlepagesize * ($page - 1);
        $data['QryCount'] = channelarticlepagesize;  //默认60条一页。 如果改动，脚本也要改
        //$extract['TagArtList'] = $this->channel_socket->getSyaTagArticleList($data);
        $extract['TagTitle'] = "直播";
        $extract['RssURL'] = config_item('base_url') . '/rss/online.xml';

        //翻页信息
        $baseLink = config_item('base_url') . '/shtml/online,';
        $this->load->helper('channal');

        $extract['pagebar'] = drawpagebar($data['QryCount'] * $pagelimit, $page, $data['QryCount'], $baseLink);
        $extract['channelTitle'] = $this->lang->language['title_online'];
        $extract['channelKeywords'] = $this->lang->language['keywords_online'];
        $extract['channelDescription'] = $this->lang->language['description_online'];
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['header'] = $blocks['channalhead'];
        $extract['footer'] = $blocks['channalfoot'];
        $extract['categorymenu'] = $blocks['categorymenu'];
        $extract['shtml'] = $this->config->item('shtml_path');
        $extract['baseurl'] = $this->config->item('base_url');
        $extract['ArtListUrl'] = 'channeltaglist/online/' . $page . '.shtml';
        $extract['Art24RankUrl'] = 'runcront_artclickday24rank.shtml';
        $this->load->view('channal/channal_tagarticlelist.shtml', $extract);
    }

    //手工编辑专题文章
    function HotTopicArticleList() {
        $pagelimit = 60; //最多翻页60
        $page = intval($this->input->get_post('page'));
        $this->load->model('channel_socket');
        $data['StartNo'] = -1;
        $nTolCnt = $this->channel_socket->getHotTopicArticleList($data);

        $tnPage = ceil($nTolCnt / channelarticlepagesize);
        $pagelimit = ($pagelimit > $tnPage) ? $tnPage : $pagelimit;

        $page = ($page > $pagelimit || $page < 1) ? 1 : $page;

        $data['StartNo'] = channelarticlepagesize * ($page - 1);
        $data['QryCount'] = channelarticlepagesize;

        $extract['TagArtList'] = $this->channel_socket->getHotTopicArticleList($data);
        $extract['TagTitle'] = "热门专题";
        $extract['RssURL'] = config_item('base_url') . '/rss/hottopicarts.xml';

        //翻页信息
        $baseLink = config_item('base_url') . '/shtml/hottpicartslist,';
        $this->load->helper('channal');

        $extract['pagebar'] = drawpagebar($data['QryCount'] * $pagelimit, $page, $data['QryCount'], $baseLink);
        $extract['channelTitle'] = $this->lang->language['title_hottpicartslist'];
        $extract['channelKeywords'] = $this->lang->language['keywords_hottpicartslist'];
        $extract['channelDescription'] = $this->lang->language['description_hottpicartslist'];
        $extract['user'] = $this->user;
        $blocks = &$this->config->item('block');
        $extract['header'] = $blocks['channalhead'];
        $extract['footer'] = $blocks['channalfoot'];
        $extract['shtml'] = $this->config->item('shtml_path');
        $extract['baseurl'] = $this->config->item('base_url');
        $extract['TagID'] = 1461;

        $this->load->view('channal/channal_hottopicarticlelist.shtml', $extract);
    }

}

//end class
?>