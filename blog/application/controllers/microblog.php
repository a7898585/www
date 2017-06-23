<?php

/* * **********************
 * 功能：   微薄挂件
 * author： lifeng
 * add：    2011-06-02
 * *********************** */

class MicroBlog extends MY_Controller {

    function MicroBlog() {
        parent::MY_Controller();
    }

    function sendmicroblogcontent() {
        $url = 'http://t.cnfol.com/ajaxaction/newpost';
        $args['userid'] = $this->input->post('userid');
        $args['content'] = $this->input->post('content');
        $args['posttype'] = "中金博客";
        $args['loginip'] = $this->input->ip_address();
        //$args['loginip']    = $_SERVER["REMOTE_ADDR"];

        $data = curl_post($url, $args);

        if ($data == '00') {
            echo "微博信息发表成功";
            exit;
        } else {
            echo "微博信息发表失败，请稍后再试";
            exit;
        }
    }

    function signupmicroblog() {

        $url = 'http://t.cnfol.com/ajaxaction/signup';
        $args['username'] = $this->input->post('username');
        $args['password'] = '000123';
        $args['loginipaddr'] = $this->input->ip_address();
        //print_r($args);exit;
        curl_post($url, $args);

        //echo $data;
    }

    function getmicroblogcontent($uname, $count) {
        $url = 'http://api.t.cnfol.com/statuses/user_timeline.rss';
        $args['uname'] = trim($uname);
        $args['count'] = is_numeric($count) ? $count : 0;
        $vname = $this->user['username'];
        $this->isFriend($args['uname'], $vname);
        $data = $this->show($url, $args);

        if ($data == '') {
            echo 0;
            exit;
        } else {
            echo $data;
            exit;
        }
    }

    //是否关注
    function isFriend($uname, $vname = '') {
        $url = 'http://api.t.cnfol.com/users/show.xml?id=' . $uname;
        $info = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
        $set = $info->user->protected; //是否设置保护
        $set = (array) $set;

        if (isset($set['0']) && $set['0'] == 'true') {
            if ($uname != $vname) {
                $url = 'http://api.t.cnfol.com/friendships/exists.xml?user_a=' . $uname . '&user_b=' . $vname;
                $temp = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
                $temp = (array) $temp;

                if ($temp['0'] != '相互关注' && !strpos($temp['0'], '关注' . $uname)) {
                    echo '<div class="WbCon1">只有和此用户互相关注才能查看信息。
	<a href="http://t.cnfol.com/' . $uname . '" target="_blank">马上关注>></a></div>';
                    exit;
                }
            }
        }
    }

    //获取微博数据
    function show($url, $postargs = '') {
        if (!empty($postargs)) {
            $url = $url . '?id=' . $postargs['uname'] . '&count=' . $postargs['count'];
            $x = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
            $return = '';

            if (empty($x->channel->item)) {
                return $return;
            }

            if (count($x->channel->item) > 0) {
                foreach ($x->channel->item as $v) {
                    $temp['title'] = explode(': ', $v->title);
                    $temp['content'] = $temp['title']['1'];
                    $temp['time'] = $v->pubDate;
                    $temp['link'] = $v->link;
                    $data[] = $temp;
                    unset($temp);
                }
            }

            foreach ($data as $v) {
                $temp = '<div class="WbCon1">' . $this->dealContent($v['content']) . '<br />';
                $temp .= '<span class="WbFont1"><a target="_blank" href="' . $v['link'] . '">' . $this->timeop($v['time']) . '</a></span>  来自<a target="_blank" href="http://t.cnfol.com/">中金微博</a></div>';
                $return .= $temp;
                unset($temp);
            }
            return $return;
        }
    }

    //格式化微博内容
    function dealContent($content, $webaddr = 'http://t.cnfol.com') {
        if (get_magic_quotes_gpc()) {
            $content = stripslashes($content);
        }
        $content = htmlspecialchars_decode($content);

        $urlPartten = '/([^\"|\']){1}(((http|https|ftp):\/\/)(([a-zA-Z0-9\-])+(\.))+([a-zA-Z0-9]){2,4}([\w\/\+=%&\.~\?\:\-\#\;\,]*))/i';
        $content = preg_replace($urlPartten, '$1<a href= "$2" target=\'_blank\' >$2</a>', $content);

        $urlPartten = '/^((http|https|ftp):\/\/)(([a-zA-Z0-9\-])+(\.))+([a-zA-Z0-9]){2,4}([\w\/+=%&\.~?\:\-#\,]*)/i';
        $content = preg_replace($urlPartten, '<a href= "$0" target=\'_blank\' >$0</a>', $content);

        $content = str_replace('：', ':', $content);

        $atPartten = '/([^a-zA-Z0-9\_\-]){1}@(([^\s|\#|\@|\:])+)/i';
        $content = preg_replace($atPartten, '$1@<a href= \'' . $webaddr . '/$2/nickname\' >$2</a>', $content);

        $atPartten = '/^@(([^\s|\#|\@|\:])+)/i';
        $content = preg_replace($atPartten, '@<a href= \'' . $webaddr . '/$1/nickname\' >$1</a>', $content);


        $supPartten = '/([\s]){1}(#(([^\s|\#|\@|\,])+))/i';
        $content = preg_replace($supPartten, '${1}#<a href= \'' . $webaddr . '/tag/all/${3}\' >${3}</a>', $content);

        $supPartten = '/^#((([^\s|\#|\@|\,])+))([\s|\,|\.|，|。|\#|\@]{0,3})/i';
        $content = preg_replace($supPartten, '#<a href= \'' . $webaddr . '/tag/all/${2}\' >${1}</a>${4}', $content);

        return $content;
    }

    //格式化微博时间
    function timeop($inTime) {
        $time = strtotime($inTime);
        $time = $time + 8 * 3600;
        $ntime = time() - $time;

        if ($ntime < 60) {
            return("刚才");
        } elseif ($ntime < 3600) {
            return(intval($ntime / 60) . "分钟前");
        } else {
            return date('Y-m-d H:i', $time);
        }
    }

}

//end class
?>