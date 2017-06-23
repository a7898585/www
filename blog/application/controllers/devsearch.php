<?php

/* * **********************
 * 功能：   博客个人主页
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class DevSearch extends MY_Controller {

    function __construct() {
        parent::MY_Controller();
    }

    /**
     * @ 博客搜索
     * */
    function index() {
        /* $FromPage = $this->input->server('HTTP_REFERER');
          if(!preg_match("/blog\.cnfol\.com/",$FromPage))
          {
          cnfolLocation(config_item('base_url'));
          } */
        var_dump($_GET);

        $searchType = $this->input->get_post('searchtype');
        $searchValue = trim($this->input->get_post('searchvalue'));
        if ($this->input->get_post('charset') == 'utf-8') {
            $searchType = mb_convert_encoding($searchType, "UTF-8", "GBK");
            $searchValue = mb_convert_encoding($searchValue, "UTF-8", "GBK");
        }

        $data['channelTitle'] = $this->lang->language['title_tags'];
        $data['channelKeywords'] = $this->lang->language['keywords_tags'];
        $data['channelDescription'] = $this->lang->language['description_tags'];



        $Type = ($searchType == 'blogname') ? 'blogname' : (($searchType == 'nickname') ? 'nickname' : '');
        if ($Type == '') {
            $Type = ($searchType == 'tags') ? 'tags' : '';
            $template = 'channel_searcharts.shtml';
        }
        if ((strlen($searchValue) < 4) || ($Type == '')) {
            $data['TolCnt'] = 0;
        } else {
            $this->load->model('channel_socket');
            $page = intval($this->input->get_post('page'));
            $page = ($page < 1) ? 1 : $page;
            $pagesize = 30;
            if ($Type == 'blogname') {
                $param['StartNo'] = -1;
                $param['BlogName'] = $searchValue;
                $totalcount = $this->channel_socket->getSearchBlog($param);
                $m = $totalcount - 1;
                $str = '';
                $str .= 'searchvalue=' . $searchValue . '&searchtype=' . $searchType;
                if ($totalcount > 0) {
                    //	echo 'totalcount';
                    page(&$page, &$pagesize, &$pagecount, &$totalcount, &$start);
                    //echo site_url('devsearch/index');
                    $link = config_item('base_url') . '/index.php/devsearch/index?' . $str . '&page=';
                    $listcount = 5;
                    $pagestr = paging($link, $page, $pagecount, $listcount);

                    $param['StartNo'] = $start;
                    $param['QryCount'] = $pagesize;

                    $data['searchRes'] = $this->channel_socket->getSearchBlog($param);
                }
            } else {
                $param['TagName'] = $searchValue;
                $totalcount = $this->channel_socket->getSearchTagArticle($param);

                if ($totalcount > 0) {
                    echo '$totalcount';
                    page(&$page, &$pagesize, &$pagecount, &$totalcount, &$start);
                    $link = site_url('devsearch/index') . '?page=';
                    $listcount = 5;
                    $pagestr = paging($link, $page, $pagecount, $listcount);
                    $param['StartNo'] = $start;
                    $param['QryCount'] = $pagesize;
                    $data['searchRes'] = $this->channel_socket->getSearchTagArticle($param);
                }
            }
        }
        $data['link'] = $pagestr;
        $data['ddd'] = '22222';
        var_dump($data);
        $this->load->view('manage/why.shtml', $data);
    }

}

//end class
?>
