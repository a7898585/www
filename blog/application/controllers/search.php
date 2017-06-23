<?php

/* * **********************
 * 功能：   博客个人主页
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Search extends MY_Controller {

    function Search() {
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
        $searchType = $this->input->get_post('searchtype');
        $searchValue = trim($this->input->get_post('searchvalue'));
        if ($this->input->get_post('charset') == 'utf-8') {
            $searchType = mb_convert_encoding($searchType, "UTF-8", "GBK");
            $searchValue = mb_convert_encoding($searchValue, "UTF-8", "GBK");
        }
        $page = intval($this->input->get_post('page'));
        $template = 'channel_searchblogs.shtml';

        $data['channelTitle'] = $this->lang->language['title_tags'];
        $data['channelKeywords'] = $this->lang->language['keywords_tags'];
        $data['channelDescription'] = $this->lang->language['description_tags'];

        if ($searchType == 'title') {
            $str = $searchValue;
            $str = urlencode($str);
            $str1 = array(urlencode('%2F'), urlencode('%23'), urlencode('%26'));
            $str2 = array('%2F', '%23', '%26');
            $str = str_replace($str2, $str1, $str);
            $tips = '正在搜索中，请稍后....';
            echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html>
                 <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                 <title>跳转页面</title></head>
                 <body onLoad="javascript:document.E_FORM.submit()">
                 <form name="E_FORM" method="post" action="http://search.cnfol.com/' . $str . '/blog/1/10"></form>
                 ' . $tips . '</body></html>';
            exit;
        }


        $Type = ($searchType == 'blogname') ? 'blogname' : (($searchType == 'nickname') ? 'nickname' : '');
        if ($Type == '') {
            $Type = ($searchType == 'tags') ? 'tags' : '';
            $template = 'channel_searcharts.shtml';
        }

        $page = ($page < 1) ? 1 : $page;
        $pagesize = 50;

        if ((strlen($searchValue) < 4) || ($Type == '')) {
            $data['TolCnt'] = 0;
        } else {
            $this->load->model('channel_socket');
            if ($Type == 'blogname') {
                $param['BlogName'] = $searchValue;
                $data['TolCnt'] = $this->channel_socket->getSearchBlog($param);
                if ($data['TolCnt'] > 0) {
                    $page = ($page < 1 || ($page > ceil($data['TolCnt'] / $pagesize))) ? 1 : $page;

                    $param['StartNo'] = ($page - 1) * $pagesize;
                    $param['QryCount'] = $pagesize;
                    $data['SearchRes'] = $this->channel_socket->getSearchBlog($param);
                }
            } else if ($Type == 'nickname') {
                $param['NickName'] = $searchValue;
                $data['TolCnt'] = $this->channel_socket->getSearchBlog($param);
                if ($data['TolCnt'] > 0) {
                    $page = ($page < 1 || ($page > ceil($data['TolCnt'] / $pagesize))) ? 1 : $page;

                    $param['StartNo'] = ($page - 1) * $pagesize;
                    $param['QryCount'] = $pagesize;
                    $data['SearchRes'] = $this->channel_socket->getSearchBlog($param);
                }
            } else {
                $param['TagName'] = $searchValue;
                $data['TolCnt'] = $this->channel_socket->getSearchTagArticle($param);
                if ($data['TolCnt'] > 0) {
                    $page = ($page < 1 || ($page > ceil($data['TolCnt'] / $pagesize))) ? 1 : $page;

                    $param['StartNo'] = ($page - 1) * $pagesize;
                    $param['QryCount'] = $pagesize;
                    $data['SearchRes'] = $this->channel_socket->getSearchTagArticle($param);
                }
            }
        }
        //翻页信息
        $baseLink = config_item('base_url') . '/ajaxblogsearch/' . urlencode($searchValue) . '/' . $searchType;
        $this->load->library('pagebarsnew');
        $this->pagebarsnew->Page($data['TolCnt'], $page, $pagesize, $baseLink, '/');
        $data['pagebar'] = $this->pagebarsnew->upDownList();

        $blocks = &$this->config->item('block');
        $data['header'] = $blocks['channalhead'];
        $data['footer'] = $blocks['channalfoot'];
        $data['shtml'] = $this->config->item('shtml_path');
        $data['baseurl'] = $this->config->item('base_url');
        $this->load->view('channal/' . $template, $data);
    }

}

//end class
?>