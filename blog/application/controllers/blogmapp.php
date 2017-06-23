<?php

/* * **********************
 * 功能：   小窗口
 * author ：wuyq
 * add：    2010-01-22
 * modify   2010-04-20
 * *********************** */

class Blogmapp extends MY_Controller {

    function Blogmapp() {
        parent::MY_Controller();
        header("Content-Type: text/html; charset=utf-8");
    }

    /**
     *
     * 功能：获取推荐用户
     *
     * */
    function recomend() {
        //$uid=$this -> _getUserID();  
        $this->load->model('blogmapp_socket');

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $num = intval($this->input->get_post('num'));
        $num = (is_int($num) && $num > 0) ? $num : 5;

        $data['UserID'] = $this->input->get_post('uid') != '' ? $this->input->get_post('uid') : '';
        $data['StartNo'] = -1;
        $tempInfo = $this->blogmapp_socket->getRecomend($data);
        //print_r($data);
        //print_r($tempInfo);
        if ($tempInfo['TtlRecords'] > 0) {
            $data['StartNo'] = ($page - 1) * $num;
            $data['QryCount'] = $num;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract = $this->blogmapp_socket->getRecomend($data);
            //print_r($data);
            //print_r($extract);
            if (count($extract['Record']) > 0) {
                foreach ($extract['Record'] as $key => $value) {
                    if (strlen(file_get_contents(getUserHead($value['UserID']))) == 4699) {
                        $extract['Record'][$key]['head'] = 'http://head.cnfolimg.com/man.png';
                    } else {
                        $extract['Record'][$key]['head'] = getUserHead($value['UserID'], 96);
                    }
                }
            }

            $extract2 = array('TtlRecords' => $tempInfo['TtlRecords'], 'flag' => '00', 'info' => '操作成功');
            unset($extract['FlagCode']);
            if (count($extract) > 0 && !empty($extract['Record']['0'])) {
                $extract = array_merge($extract, $extract2);
            } else {
                $extract = array('flag' => '01', 'info' => '无数据返回');
            }
        } else {
            $extract = array('flag' => '01', 'info' => '无数据返回');
        }
        //print_r($extract);

        if ($this->input->get_post('sign') == '1') {
            print_r($extract);
        } else {
            echo(json_encode($extract));
        }
    }

    /**
     *
     * 功能：获取自己已添加的名家高手
     *
     * */
    function getPersonal() {
        //$uid=$this -> _getUserID();  
        $this->load->model('blogmapp_socket');

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $num = intval($this->input->get_post('num'));
        $num = (is_int($num) && $num > 0) ? $num : 5;

        $data['UserID'] = $this->input->get_post('uid') != '' ? $this->input->get_post('uid') : '';
        $data['StartNo'] = -1;
        $tempInfo = $this->blogmapp_socket->getPersonal($data);


        if ($tempInfo['TtlRecords'] > 0) {
            $data['StartNo'] = ($page - 1) * $num;
            $data['QryCount'] = $num;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract = $this->blogmapp_socket->getPersonal($data);
            unset($extract['FlagCode']);

            if (count($extract['Record']) > 0) {
                foreach ($extract['Record'] as $key => $value) {


                    if (strlen(file_get_contents(getUserHead($value['UserID']))) == 4699) {
                        $extract['Record'][$key]['head'] = 'http://head.cnfolimg.com/man.png';
                    } else {
                        $extract['Record'][$key]['head'] = getUserHead($value['UserID'], 96);
                    }
                }
            }

            $extract2 = array('TtlRecords' => $tempInfo['TtlRecords'], 'flag' => '00', 'info' => '操作成功');
            $extract = array_merge($extract, $extract2);
        } else {
            $extract = array('flag' => '01', 'info' => '无数据返回');
        }
        //print_r($extract);
        //error_log(print_r($this->input->get_post('num'),true).'|'.print_r($extract,true), 3, '/home/httpd/logs/a11111111.log');
        echo(json_encode($extract));
    }

    /**
     *
     * 功能：订阅博主
     *
     * */
    function addRecomend() {

        $this->load->model('blogmapp_socket');

        $myuid = intval($this->input->get_post('uid'));
        $fuid = intval($this->input->get_post('fuid'));
        if (empty($myuid) || empty($fuid)) {
            echo(json_encode(array('flag' => '02', 'info' => '参数缺失')));
            exit;
        }

        $data['UserID'] = $myuid;
        $data['FriendUserID'] = $fuid;
        $tempInfo = $this->blogmapp_socket->addRecomend($data);

        if ($tempInfo['Code'] == '00') {
            $rs = array('flag' => '00', 'info' => '操作成功');
        } else {
            $rs = array('flag' => $tempInfo['Code'], 'info' => '操作失败');
        }
        //print_r($rs);
        echo(json_encode($rs));
    }

    /**
     *
     * 功能：取消订阅
     *
     * */
    function delRecomend() {

        $this->load->model('blogmapp_socket');

        $myuid = intval($this->input->get_post('uid'));
        $fuid = intval($this->input->get_post('fuid'));
        if (empty($myuid) || empty($fuid)) {
            echo(json_encode(array('flag' => '02', 'info' => '参数缺失')));
            exit;
        }

        $data['UserID'] = $myuid;
        $data['FriendUserID'] = $fuid;
        $tempInfo = $this->blogmapp_socket->delRecomend($data);

        if ($tempInfo['Code'] == '00') {
            $rs = array('flag' => '00', 'info' => '操作成功');
        } else {
            $rs = array('flag' => $tempInfo['Code'], 'info' => '操作失败');
        }
        //print_r($rs);
        echo(json_encode($rs));
    }

    /**
     *
     * 功能：模糊查询名家高手
     *
     * */
    function searchRecomend() {

        $this->load->model('blogmapp_socket');

        $myuid = intval($this->input->get_post('uid'));
        $myuid = (is_int($myuid)) ? $myuid : '';
        $nickname = trim($this->input->get_post('nickname'));
        if ($nickname == '') {
            echo(json_encode(array('flag' => '02', 'info' => '参数缺失')));
            exit;
        }

        $data['UserID'] = $myuid;
        $data['UserName'] = $nickname;
        $tempInfo = $this->blogmapp_socket->searchRecomend($data);


        if ($tempInfo['Code'] == '00') {
            $rs = array('flag' => '00', 'info' => '操作成功');

            if ($tempInfo['RetRecords'] == 1) {
                $tempInfo['Record'] = array($tempInfo['Record']);
            }

            if (empty($tempInfo['Record'])) {
                echo(json_encode(array('flag' => '03', 'info' => '无您要的数据')));
                exit;
            }
            foreach ($tempInfo['Record'] as $key => $value) {
                if (strlen(file_get_contents(getUserHead($value['UserID']))) == 4699) {
                    $tempInfo['Record'][$key]['head'] = 'http://head.cnfolimg.com/man.png';
                } else {
                    $tempInfo['Record'][$key]['head'] = getUserHead($value['UserID'], 96);
                }
            }

            $rs = array_merge($tempInfo, $rs);
        } else {
            $rs = array('flag' => $tempInfo['Code'], 'info' => '操作失败');
        }
        //print_r($rs);
        if ($this->input->get_post('sign') == '1') {
            print_r($rs);
        } else {
            echo(json_encode($rs));
        }
    }

    /*
     * 文章列表
     */

    function articleList($domainname) {
        //$this->user['userid'] = $this->_getUserID();

        $extract['bloginfo'] = $this->_getBlogInfoByDomainApp($domainname);

        $page = intval($this->input->get_post('page'));
        $total = intval($this->input->get_post('total'));
        $page = (is_int($page) && $page > 0) ? $page : 1;

        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $this->load->model('blogarticle_socket');

        $tempInfo = $this->blogarticle_socket->getMemberArticleList($data);
        $tmpCnt = $tempInfo['all'];
        $tmpCntAll = $tmpCnt;
        if ($total) {
            $tmpCnt -= $total;
        }
        $end = '';
        if ($page == ceil($tmpCnt / 15)) {
            $end = '1';
        }
        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {

            $data['StartNo'] = ($page - 1) * MYBLOG_MORE_PAGESIZE;
            $data['QryCount'] = MYBLOG_MORE_PAGESIZE;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleList($data);
            unset($extract['artlist']['FlagCode']);
            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            if (empty($extract['artlist']['Record'][0])) {
                $extract['artlist'] = array('flag' => '01', 'info' => '无数据返回');
                echo(json_encode($extract['artlist']));
                exit;
            }
        } else {
            $extract['artList'] = array('flag' => '01', 'info' => '无数据返回');
            echo(json_encode($extract['artlist']));
            exit;
        }

        $rs = array('TtlRecords' => $tmpCntAll, 'flag' => '00', 'info' => '操作成功', 'end' => $end);

        if ($this->input->get_post('sign') == '1') {
            print_r(array_merge($extract['artlist']));
        } else {
            echo(json_encode(array_merge($extract['artlist'], $rs)));
        }
    }

    //获取各博主最新发表的文章
    function dynamicGet() {
        $this->load->model("blogarticle_socket");
        $this->load->model("memberblog_socket");
        $userids = $this->input->get_post('userids');
        $userids = trim($userids, ',');
        if (empty($userids)) {
            $rs = array('flag' => '01', 'info' => '无数据返回');
            echo(json_encode($rs));
            exit;
        }
        $userids = explode(',', $userids);


        if (count($userids) > 500) {
            $rs = array('flag' => '01', 'info' => '请求数据超出正常范围');
            echo(json_encode($rs));
            exit;
        }

        $script = array();

        foreach ($userids as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $res[$v] = $this->memberblog_socket->getMemberBlogListByUserID(array('QryData' => $v, 'StartNo' => 0, 'QryCount' => 1));



            $count[$v] = $res[$v][RetRecords];

            if ($res[$v][RetRecords] == 1) {
                $list = array($res[$v]['Record']);

                foreach ($list as $key => $val) {

                    $bloginfo[$v] = $this->_getBlogInfoByDomainAppDynamic($val['DomainName']);


                    if ($bloginfo[$v] == '03') {
                        $script[] = array('bloggerID' => $v, 'title' => '', 'AppearTime' => '', 'ArticleID' => '');
                        continue;
                    }



                    $data['MemberIDs'] = $bloginfo[$v]['MemberID'];

                    $data['MemberID'] = $bloginfo[$v]['MemberID'];
                    $data['StartNo'] = 0; //代表到第几页
                    $data['QryCount'] = 1;
                    $data['Dynamic'] = 1;


                    $articleResult = $this->blogarticle_socket->getMemberArticleList($data);

                    if ($this->input->get_post('sign') == 1) {
                        print_r($articleResult['Record']);
                        echo('<br>');
                    }
                    $article[$data['MemberID']] = $articleResult['Record'];


                    if (!empty($article[$data['MemberID']]['Title']) && trim($article[$data['MemberID']]['Title']) != '') {
                        //$script[$v]="<a href=".$this->config->item('base_url')."/".$val['DomainName']."/article/".strtotime($article[$data['MemberID']]['AppearTime'])."-".$article[$data['MemberID']]['ArticleID'].".html  target=_blank >".$article[$data['MemberID']]['Title']."</a>";
                        $script[] = array('bloggerID' => $v, 'title' => $article[$data['MemberID']]['Title'], 'AppearTime' => $article[$data['MemberID']]['AppearTime'], 'ArticleID' => $article[$data['MemberID']]['ArticleID'], 'NickName' => $val['NickName'], 'DomainName' => $val['DomainName']);
                    }
                }
            }
        }

        if (count($script) == 0) {
            $rs = array('flag' => '01', 'info' => '无数据返回');
            echo(json_encode($rs));
            exit;
        }

        $rs = array('flag' => '00', 'info' => '操作成功');

        if ($this->input->get_post('sign') == '1') {
            print_r($script);
        } else {
            echo(json_encode(array_merge(array('Record' => $script), $rs)));
        }
        exit;
    }

    //获取收藏的文章
    function getCollect($domainname) {
        $this->load->model("blogarticle_socket");
        $this->load->model("memberblog_socket");
        $num = 10;

        $extract['bloginfo'] = $this->_getBlogInfoByDomainApp($domainname);
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $data['SortID'] == '18296';
        $data['Property'] = 4;

        $tempInfo = $this->blogarticle_socket->getMemberArticleListSort($data);

        $tmpCnt = $tempInfo['all'];

        if ($tmpCnt > 0 || $tempInfo['UTopCnt'] > 0) {
            $data['StartNo'] = ($page - 1) * $num; //代表到第几页
            $data['QryCount'] = $num;
            $data['FlagCode'] = $tempInfo['FlagCode'];

            $extract['artlist'] = $this->blogarticle_socket->getMemberArticleListSort($data);
            unset($extract['artlist']['FlagCode']);
            if ($extract['artlist']['RetRecords'] == 1) {
                $extract['artlist']['Record'] = array('0' => $extract['artlist']['Record']);
            }

            $rs = array('TtlRecords' => $tmpCnt, 'flag' => '00', 'info' => '操作成功');
            echo(json_encode(array_merge(array('Record' => $script), $rs)));
            //print_r(array_merge($extract['artlist'],$rs));
        } else {
            $rs = array('flag' => '01', 'info' => '无数据返回');
            echo(json_encode($rs));
        }
    }

    /**
     *
     * 功能：收藏文章
     *
     * */
    function articleCollect() {

        $loginmemberid = $this->input->get_post('hostmid'); //登陆用户博客memberid
        $memberid = $this->input->get_post('armid'); //当前文章博主memberid
        $articletime = $this->input->get_post('arttime'); //当前文章页文章发表时间
        $articleid = $this->input->get_post('artid'); //当前文章id

        if (empty($loginmemberid) || empty($memberid) || empty($articletime) || empty($articleid)) {
            echo(json_encode(array('flag' => '02', 'info' => '参数缺失')));
            exit;
        }

        $this->load->model('blogarticle_socket');

        $datas['MemberID'] = $memberid;
        $datas['ArticleID'] = $articleid;
        $datas['AppearTime'] = date("Y-m-d H:i:s", $articletime);
        $article = $this->blogarticle_socket->getBlogArticleByID($datas, 'view');
        //error_log(print_r($article,true), 3, '/home/www/html/logs/a1111123133133333333.log');

        $data['MemberID'] = $loginmemberid;
        $data['Title'] = $article['Title'];
        $data['Summary'] = $article['Summary'];
        $data['TrackBack'] = $article['TrackBack'];
        $data['Property'] = '4'; //收藏
        $data['ReadStatus'] = '3'; //收藏
        $data['Prime'] = $article['Prime'];
        $data['SelfRecommend'] = $article['SelfRecommend'];
        $data['IsUTop'] = '0';
        $data['SysTagID'] = $article['SysTagID'];
        $data['SortID'] = '18296'; //收藏
        $data['GiftPrice'] = $article['GiftPrice'];
        if ($data['GiftPrice'] > 0) {
            echo(json_encode(array('error' => '0')));
        }
        $data['Content'] = $article['Content'];


        preg_match('/<img\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $data['Content'], $matchesPic);
        $data['PictureUrl'] = $matchesPic['0'];


        if ($data['PictureUrl']) {
            $data['PictureUrl'] = htmlEncode($data['PictureUrl']);
            $data['IsMultimedia'] = 1;
        } else {
            $data['PictureUrl'] = 0;
        }

        preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $data['Content'], $matchesEmbed);
        $data['Multimedia'] = $matchesEmbed['1'];


        if ($data['Multimedia']) {

            if ($data['IsMultimedia'] == '1') {
                $data['IsMultimedia'] = 3;
            } else {
                $data['IsMultimedia'] = 2;
            }
        }


        $data['Content'] = '原文地址：<a href="/' . $article['Domainname'] . '/article/' . $articletime . '-' . $articleid . '.html" target="_blank" style="color:#FF791C;cursor:hand;">' . $article['Title'] . '</a><br>' . $data['Content'];
        $data['Content'] = '原文发布时间：<span style="color:#FF791C;">' . $datas['AppearTime'] . '</span><br>' . $data['Content'];

        $data['Content'] = htmlEncode(remove_invisible_code($data['Content']));

        $data['FocusArtMemberID'] = $memberid; // 被转发或被收藏文章博客ID
        $data['FocusArtAppearTime'] = $datas['AppearTime']; //被转发或被收藏文章发表时间
        $data['FocusArtArticleID'] = $articleid; //被转发或被收藏 文章ID

        $extract['return'] = $this->blogarticle_socket->articlecollect($data);


        if ($extract['return'] == '1') {
            $this->delMyIndexCache($memberid, $loginmemberid, '1');
            echo(json_encode(array('flag' => '00', 'info' => '操作成功')));
        } else {
            echo(json_encode(array('flag' => $extract['return'], 'info' => '操作失败')));
        }
    }

    //展示博客文章详细信息以及预览
    function postinfo() {

        $domainname = $this->input->get_post('domainname');
        $artstr = $this->input->get_post('artid');
        if (strpos($artstr, '-') === false) {
            $appearTime = '2011-01-01 00:00:00'; //兼容2011年以前文章 
            $articleID = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID = $temp[1];
        }

        $realarticleID = intval($this->input->get_post('articleid'));
        $realappearTime = $this->input->get_post('appeartime');
        if (!$realappearTime) {
            $realappearTime = '2011-01-01 00:00:00';
        }

        $articleID = intval($articleID);
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomainApp($domainname);
        $extract['bloginfo']['articletime'] = $temp[0];


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

            //print_r($data);

            preg_match_all('/<img\s*([^>]*)?src\s*\=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i', $extract['article']['Content'], $matchesEmbed);
            //print_r($matchesEmbed);
            if (count($matchesEmbed['2']) > 0 && !empty($matchesEmbed['2']['0'])) {
                $extract['article']['allimg'] = $matchesEmbed['2'];
            } else {
                $extract['article']['allimg'] = array();
            }

            if (empty($extract['article'])) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息不存在，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['IsDel'] == 2 || $extract['article']['IsDel'] == 5) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息正在审核中，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['IsDel'] == 1) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息已经被博主删除，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['IsDel'] == 3 || $extract['article']['IsDel'] == 4) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息已经被管理员删除，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['ReadStatus'] == 3) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息被设置成私有，只有博客主自己可以查阅')));
                exit(-1);
            }


            if ($extract['article']['GiftPrice'] > 0) { //礼物数大于0
                echo(json_encode(array('flag' => '05', 'info' => '该文章信息不允许查看')));
                exit(-1);
            }
        }
        $rs = array('flag' => '00', 'info' => '操作成功');
        $extract['article']['Title'] = filter($extract['article']['Title']);
        if ($this->input->get_post('sign') == 1) {

            $extract['article']['Content'] = $this->_matchLink($extract['article']['Content']);
            print_r($extract['article']);
        } else {
            $extract['article']['Content'] = $this->_matchLink($extract['article']['Content']);
            echo(json_encode(array_merge($extract['article'], $rs)));
        }
    }

    function _matchLink($content = '') {
        //文章页地址替换
        //preg_match_all('/(<a[^>]+)http\:\/\/blog\.cnfol\.com\/[A-Za-z0-9\-\_]+\/article\/([0-9]+\-[0-9]+)\.html/i',$extract['article']['Content'],$link);//只匹配a标签中的
        preg_match_all('/http\:\/\/(new\.)?blog\.cnfol\.com\/[A-Za-z0-9\-\_]+\/article\/[0-9]+\-[0-9]+\.html/i', $content, $arlink);

        $newAddr = 'http://3g.blog.cnfol.com';
        $bkyy_article = 'bkyy_article';

        $arlinkNum = count($arlink['0']);

        if ($arlinkNum < 1) {
            return $content;
        }

        for ($i = 0; $i < $arlinkNum; $i++) {
            $content = preg_replace('/http\:\/\/(new\.)?blog\.cnfol\.com\/([A-Za-z0-9\-\_]+)\/article\/([0-9]+\-[0-9]+)\.html/i', $newAddr . '/$2/' . $bkyy_article . '/$3.html', $content, 1);
        }

        //文章页地址替换

        /*
          //博主主页地址替换
          preg_match_all('/http\:\/\/(new\.)?blog\.cnfol\.com\/[A-Za-z0-9\-\_]+(\/)?[^A-Za-z0-9\-\_\/]+/i',$content,$link);

          $linkNum=count($link['0']);

          if($linkNum<1)
          {
          return $content;
          }

          for($i=0;$i<$linkNum;$i++)
          {
          $content=preg_replace('/http\:\/\/(new\.)?blog\.cnfol\.com\/([A-Za-z0-9\-\_]+)(\/)?([^A-Za-z0-9\-\_\/]+)/i',$newAddr.'/$1$2$3',$content,1);
          }
          //博主主页地址替换
         */

        return $content;
    }

    /**
     * 
     * @ 获取谋篇文章的评论列表
     * */
    function CommentList() {

        $domainname = $this->input->get_post('domainname');
        //根据文章id获取文章详情
        $artstr = $this->input->get_post('artid');
        if (strpos($artstr, '-') === false) {
            $appearTime = '2011-01-01 00:00:00'; //兼容2011年以前文章 
            $articleID2 = $artstr;
        } else {
            $temp = explode('-', $artstr);
            $appearTime = date("Y-m-d H:i:s", $temp[0]);
            $articleID2 = $temp[1];
        }

        $articleid = $articleID2;

        $realarticleID = intval($this->input->get_post('articleid'));
        $realappearTime = $this->input->get_post('appeartime');
        if (!$realappearTime) {
            $realappearTime = '2011-01-01 00:00:00';
        }

        $articleID2 = intval($articleID2);
        //获取博客信息
        $extract['bloginfo'] = $this->_getBlogInfoByDomainApp($domainname);
        $extract['bloginfo']['articletime'] = $temp[0];


        //获取该博客文章信息
        if ($articleID2 != 0 || $realarticleID != 0) {
            $data['MemberID'] = $extract['bloginfo']['MemberID'];
            if ($realarticleID != 0) {
                $data['ArticleID'] = $realarticleID;
                $data['AppearTime'] = $realappearTime;
            } else {
                $data['ArticleID'] = $articleID2;
                $data['AppearTime'] = $appearTime;
            }
            $this->load->model('blogarticle_socket');
            $extract['article'] = $this->blogarticle_socket->getBlogArticleByID($data, 'view');


            if (empty($extract['article'])) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息不存在，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['IsDel'] == 2 || $extract['article']['IsDel'] == 5) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息正在审核中，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['IsDel'] == 1) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息已经被博主删除，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['IsDel'] == 3 || $extract['article']['IsDel'] == 4) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息已经被管理员删除，请查看其他文章')));
                exit(-1);
            }

            if ($extract['article']['ReadStatus'] == 3) {
                echo(json_encode(array('flag' => '03', 'info' => '该文章信息被设置成私有，只有博客主自己可以查阅')));
                exit(-1);
            }


            if ($extract['article']['GiftPrice'] > 0) { //礼物数大于0
                echo(json_encode(array('flag' => '05', 'info' => '该文章信息不允许查看')));
                exit(-1);
            }
        }

        $extract['bloginfo'] = $this->_getBlogInfoByDomainApp($domainname);
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $VerifyStr = getVerifyStr($extract['blogconfig']['AllowComment'] . $extract['bloginfo']['MemberID'] . $articleid);


        if ($articleid <= 0) {
            echo(json_encode(array('flag' => '03', 'info' => '暂无网友对此文章进行相关评论', 'AllowComment' => $extract['blogconfig']['AllowComment'], 'flashCode' => $VerifyStr, 'userid' => $extract['bloginfo']['UserID'], 'memberid' => $extract['bloginfo']['MemberID'])));
            exit(-1);
        }

        $page = intval($this->input->get_post('page'));
        $pagesize = $this->input->get_post('num');
        $pagesize = ($pagesize > 50 || $pagesize < 5) ? commonpagesize : $pagesize;

        $data['BlogType'] = 1;
        $data['ArticleID'] = $articleid;
        $data['StartNo'] = -1;
        $this->load->model('blogmapp_socket');
        $tempInfo = $this->blogmapp_socket->getArtCommentListMobile($data);

        //$this->load->model('articlecomment_socket');
        //$tempInfo = $this->articlecomment_socket->getArtCommentList($data);
        $tmpCnt = $tempInfo['TtlRecords'];

        if ($tmpCnt < 1) {
            echo(json_encode(array('flag' => '03', 'info' => '暂无网友对此文章进行相关评论', 'AllowComment' => $extract['blogconfig']['AllowComment'], 'flashCode' => $VerifyStr, 'userid' => $extract['bloginfo']['UserID'], 'memberid' => $extract['bloginfo']['MemberID'])));
            exit(-1);
        }


        $allPage = ceil($tmpCnt / $pagesize);

        if (empty($page)) {
            $page = 1;
        }

        //$page = (is_int($page) && $page > 0) ? $page : 1;
        $page = ($page <= ceil($tmpCnt / $pagesize)) ? $page : '0';

        if ($page == 0) {
            echo(json_encode(array('flag' => '05', 'info' => '无更多评论', 'Record' => array(), 'AllowComment' => $extract['blogconfig']['AllowComment'], 'flashCode' => $VerifyStr, 'userid' => $extract['bloginfo']['UserID'], 'memberid' => $extract['bloginfo']['MemberID'])));
            exit(-1);
        }

        $data['StartNo'] = ($page - 1) * $pagesize;
        $i = $tmpCnt - ($page - 1) * $pagesize;
        $j = ($page - 1) * $pagesize + 1;
        $data['QryCount'] = $pagesize;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $data['OrderBy'] = 1;

        $data = $this->blogmapp_socket->getArtCommentListMobile($data);

        if (isset($data['RetRecords']) && $data['RetRecords'] > 0) {
            $data['Record'] = ($data['RetRecords'] == 1) ? array('0' => $data['Record']) : $data['Record'];
            $title = '';
            foreach ($data['Record'] as $key => $val) {

                if ($val['UserID'] != 0) {
                    preg_match('/cnfol\.com\/(.*)/i', $val['Address'], $matchesPic);
                    $data['Record'][$key]['Address'] = $matchesPic['1'];
                } else {
                    $data['Record'][$key]['Address'] = '';
                }
                $title = $data['Record'][$key]['Title'];
                $data['Record'][$key]['Content'] = strip_tags($data['Record'][$key]['Content']);
                $data['Record'][$key]['Content'] = str_replace('&nbsp;', '', $data['Record'][$key]['Content']);
                if ($this->input->get_post('sign') == 1) {
                    echo($data['Record'][$key]['Content'] . '<br>');
                }
            }
        } else {
            echo(json_encode(array('flag' => '03', 'info' => '暂无网友对此文章进行相关评论', 'AllowComment' => $extract['blogconfig']['AllowComment'], 'flashCode' => $VerifyStr, 'userid' => $extract['bloginfo']['UserID'], 'memberid' => $extract['bloginfo']['MemberID'])));
            exit(-1);
        }

        unset($data['FlagCode']);


        $rs = array('TtlRecords' => $tmpCnt, 'flag' => '00', 'allPage' => $allPage, 'info' => '操作成功', 'AllowComment' => $extract['blogconfig']['AllowComment'], 'flashCode' => $VerifyStr, 'userid' => $extract['bloginfo']['UserID'], 'memberid' => $extract['bloginfo']['MemberID']);
        if ($this->input->get_post('sign') == 1) {
            print_r(array_merge($data, $rs, $extract['article'], array('memberid' => $extract['bloginfo']['MemberID'], 'userid' => $extract['bloginfo']['UserID'])));
        } else {
            echo(json_encode(array_merge($data, $rs, $extract['article'], array('AllowComment' => $extract['blogconfig']['AllowComment'], 'flashCode' => $VerifyStr))));
        }
    }

    /**
     * @ 文章评论发表
     * */
    function commentAction() {
        $this->load->model('user_socket');

        $Content = $this->input->get_post('content');
        $ContentWord = strip_tags($Content, '<img><p><br/><br>');

        /*
          //判断内容重复
          if (checkCommentSign($Content)) {

          echo(json_encode(array('flag'=>'03','info'=>'不允许发表内容相同的评论，请重新编辑！')));
          exit;
          }
         */

        if (strlen($ContentWord) > 1500 || strlen($ContentWord) < 1) {

            echo(json_encode(array('flag' => '031', 'info' => '评论内容长度应该在1-1500个字节之内')));
            exit;
        }
        $flashCode = $this->input->get_post('flashCode');
        $allComent = $this->input->get_post('allowcomment');
        $MemberID = $this->input->get_post('memberid');
        $articleID = $this->input->get_post('articleId');
        $title = $this->input->get_post('articleSubject');

        if ($flashCode != getVerifyStr($allComent . $MemberID . $articleID)) {

            echo(json_encode(array('flag' => '032', 'info' => '评论验证信息传递错误')));
            exit;
        }

        if ($allComent == 0) {

            echo(json_encode(array('flag' => '033', 'info' => '该博客已经关评论功能')));
            exit;
        }


        //非匿名的情况下获取正在发表评论的博客域名信息
        $domain = '';

        $userid = 0;
        $nickname = '中金在线网友';

        $this->load->model('articlecomment_socket');
        unset($data);
        $data['BlogType'] = 1;
        $data['CommentID'] = 0;
        $data['ArticleID'] = $articleID;
        $data['ArtMemberID'] = $MemberID;
        $data['Subject'] = htmlEncode($title);
        $data['UserID'] = $userid;
        $data['NickName'] = $nickname;
        $data['Content'] = htmlEncode($Content);

        $data['articleUserid'] = $this->input->get_post('userid');

        $data['IP'] = $this->input->ip_address();
        $data['Status'] = 0;
        $data['Address'] = config_item('base_url') . '/' . $domain;
        $data['ArticleAppearTime'] = $this->input->get_post('appeartime');


        $flag = $this->articlecomment_socket->aupdComment($data);

        if ($flag['Code'] == '00') {
            $error['flag'] = '00';
            $error['info'] = '操作成功';


            if ($data['Status'] == 1) {
                $error['flag'] = '01';
                $error['info'] = '评论发表成功，您的评论需要审核后才会展示！';
            }


            $articleBao = '1';
        } else if ($flag['Code'] == '200036' || $flag['Code'] == '200037') {
            $error['flag'] = $flag['Code'];
            $error['info'] = '评论发表成功，等待审核中';

            $articleBao = '1';
        } else {
            $error['flag'] = '02';
            $error['info'] = '您已被该博主设为禁止评论';
        }


        if ($articleBao == 1) {
            $this->delMyIndex();
            systemBao($flag['Record']['CommentID'], 'articleCommentActual.php'); //保10洁过滤
        }


        //print_r($error);
        if ($this->input->get_post('sign') == 1) {
            print_r($error);
        } else {
            echo json_encode($error);
        }
    }

    //获取验证码
    function verifycode() {
        $t = $this->input->get('t');
        $userid = $this->input->get_post('uid');
        $this->load->library('SimpleCaptcha');
        $this->simplecaptcha->resourcesPath = APPPATH . 'resources';
        $this->simplecaptcha->imageFormat = 'png';
        if ($t == 1) {
            $this->simplecaptcha->minSize = 14;
            $this->simplecaptcha->maxSize = 18;
            $this->simplecaptcha->width = 120;
            $this->simplecaptcha->height = 25;
            $this->simplecaptcha->minWordLength = 4;
            $this->simplecaptcha->maxWordLength = 6;
            $this->simplecaptcha->Yperiod = 0.1;
            $this->simplecaptcha->Yamplitude = 255;
            $this->simplecaptcha->Xperiod = 0.1;
            $this->simplecaptcha->Xamplitude = 400;
            $this->simplecaptcha->scale = 3;
            $this->simplecaptcha->spacing = -1;
            $this->simplecaptcha->numeric = true;
        } else {
            $this->simplecaptcha->minSize = 14;
            $this->simplecaptcha->maxSize = 18;
            $this->simplecaptcha->width = 90;
            $this->simplecaptcha->height = 25;
            $this->simplecaptcha->spacing = 1;
            $this->simplecaptcha->Yperiod = 1;
            $this->simplecaptcha->Yamplitude = 0.1;
            $this->simplecaptcha->Xperiod = 11;
            $this->simplecaptcha->Xamplitude = 5;
            $this->simplecaptcha->minWordLength = 4;
            $this->simplecaptcha->maxWordLength = 4;
            $this->simplecaptcha->scale = 3;
        }
        $this->simplecaptcha->CreateImage($userid);
    }

    /**
     *
     * 功能：登录
     *
     * */
    function login() {
        $this->_checkUserlogin();

        $this->load->view('/module/login.shtml', $data);
    }

    //根据域名获取博客信息
    function _getBlogInfoByDomainApp($domain, $force = 1) {
        $data['QryData'] = $domain;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        if (!$bloginfo || $bloginfo['Status'] == 1 || $bloginfo['Status'] == 2) {
            echo(json_encode(array('flag' => '03', 'info' => '您访问的博客不存在或被管理员关闭!')));
            exit;
        }
        return $bloginfo;
    }

    //根据域名获取博客信息
    function _getBlogInfoByDomainAppDynamic($domain, $force = 1) {
        $data['QryData'] = $domain;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        if (!$bloginfo || $bloginfo['Status'] == 1 || $bloginfo['Status'] == 2) {
            //echo(json_encode(array('flag'=>'03','info'=>'您访问的博客不存在或被管理员关闭!')));
            return '03';
        }
        return $bloginfo;
    }

    //删除我的博客首页1-15条缓存
    function delMyIndexCache($memberid = 0, $loginmemberid = 0, $collect = 0) {

        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $indexckey = config_item('K1077');
        $indexckey = str_replace('{MemberID}', $this->user['userid'], $indexckey);
        $mcache->delete($indexckey);

        if ($memberid != 0 && $loginmemberid != 0) {
            $loginindexckey = str_replace('{MemberID}', $loginmemberid, config_item('K1015'));
            $mcache->delete($loginindexckey);
            $currentindexckey = str_replace('{MemberID}', $memberid, config_item('K1015'));
            $mcache->delete($currentindexckey);
        }

        if ($collect == '1') {
            $ckey = config_item('K1042');
            $ckey = str_replace('{MemberID}', $loginmemberid, $ckey);
            $mcache->delete($ckey);
        }
    }

}

//end class