<?php

/* * **********************
 * 功能：  Ajax调用模块
 * *********************** */

class Module extends MY_Controller {

    function Module() {
        parent::MY_Controller();
    }

    function delvisitor() {
        session_start();
        if ($_SESSION['ViewAdmin'] != 'admin') {
            $this->_checkLogin();
        }
        $artid = $this->input->get('artid');
        $deluid = $this->input->get('deluid');
        $this->load->model('blogarticle_socket');
        $result = $this->blogarticle_socket->getArticleVisitor($artid);
        $VUsers = unserialize($result['VUsers']);

        $tmps = array();
        foreach ($VUsers as $vu) {
            if ($vu['userid'] == $deluid) {
                continue;
            }
            $tmps[] = $vu;
        }

        $data['VUsers'] = serialize($tmps);
        $data['ArticleID'] = $artid;
        $result = $this->blogarticle_socket->setArticleVisitor($data);
        unset($data);

        if ($result['Code'] == '00') {
            $data['error'] = '删除成功';
            $data['errno'] = 'succ';
        } else {
            $data['error'] = '失败' . $result['Code'];
            $data['errno'] = 'error';
        }
        echo json_encode($data);
    }

    /**
     * @用户搜索
     * */
    function usearch($domainname) {
        $this->_checkLogin();
        $type = $this->input->get_post('stype');

        if ($type == 'username') {
            $data['UserName'] = $this->input->get_post('sdata', TRUE);
        } else if ($type == 'nickname') {
            $data['NickName'] = $this->input->get_post('sdata', TRUE);
        } else {
            exit(-1);
        }

        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $data['StartNo'] = ($page - 1) * 10;
        $data['QryCount'] = 10;
        $extract = $this->user_socket->searchUser($data);

        $data['StartNo'] = 0;
        $data['QryCount'] = 50;
        $extract_2 = $this->user_socket->searchUser($data);

        //翻页函数
        $this->load->library('pagebarsnew');
        $baseLink = config_item('base_url') . '/' . $domainname . '/module/usearch';
        $baseLink .= '?stype=' . $type;
        $baseLink .= '&sdata=' . $this->input->get_post('sdata', TRUE);

        $this->pagebarsnew->Page($extract_2['count'], $page, 10, $baseLink, '&page=');
        $extract['pagebars'] = $this->pagebarsnew->upDownList();
        $extract['userid'] = $this->user['userid'];

        $this->load->view('module/black_user.shtml', $extract);
    }

    //日历信息获取
    function calendar($domain) {
        $dates = $this->input->get_post('dates');
        $datas = (preg_match('/[0-9]{6}/', $dates)) ? $dates : date('Ym');
        $year = substr($dates, 0, 4);
        $month = intval(substr($dates, 4, 2));
        switch ($month) {
            case '1':
            case '3':
            case '5':
            case '7':
            case '8':
            case '10':
            case '12':
                $d = 31;
                break;
            case '2':
                $d = ((((intval($year) % 4) == 0) && (intval($year) % 100) != 0) || ((intval($year) % 400) == 0)) ? 29 : 28;
                break;
            default:
                $d = 30;
                break;
        }
        $year = (empty($year) || $year > 2038 || $year < 1969) ? date('Y') : $year;
        $month = (empty($month) || $month > 12 || $month < 1) ? date('n') : $month;

        $this->load->model('blogarticle_socket');
        $data['MemberID'] = $this->input->get_post('mid');
        $data['StatDate'] = $year . (($month > 9) ? $month : '0' . $month);
        $data['Type'] = 1;
        $data = $this->blogarticle_socket->getBlogArticleArchive($data);

        $data['ArtDaysList'] = isset($data['ArtDaysList']) ? $data['ArtDaysList'] : 0;
        $array = @explode(',', $data['ArtDaysList']);

        $array = is_array($array) ? $array : array($data['ArtDaysList']);
        $array = array_unique($array);

        $mtime = mktime(0, 0, 0, $month, date('d'), $year);
        $f = date("w", mktime(0, 0, 0, $month, 1, $year)) - 1;

        $last = $year . "-" . $month . "-" . (date("t", $mtime));
        /*         * *下面日历的显示部分* */
        $temp = '<table width="100%" class="ri-01">';
        $temp .= '<tr>';
        $temp .= '<td colspan="7" class="ri-02">';
        $temp .= '<span title="上一年" style="cursor:pointer" onclick=\'javascript:UpdateCalender("';
        if (1970 < (intval($year) - 1)) {
            $temp .= (intval($year) - 1) . $month;
        } else {
            $temp .= '1970' . $month;
        }
        $temp .= '")\'>↑</span>&nbsp;';
        $temp .= '<span title="上个月" style="cursor:pointer"  onclick=\'javascript:UpdateCalender("';
        if (1 <= (intval($month) - 1)) {
            if ((intval($month) - 1) < 10) {
                $temp .= $year . '0' . (intval($month) - 1);
            } else {
                $temp .= $year . (intval($month) - 1);
            }
        } else {
            $temp .= (intval($month) - 1) . '12';
        }
        $temp .= '")\'>←</span>&nbsp;';
        $temp .= $year . '年';
        $temp .= date('m', $mtime) . '月';
        $temp .= '&nbsp;<span title="下个月" style="cursor:pointer" onclick=\'javascript:UpdateCalender("';

        if (12 >= (intval($month) + 1)) {
            if ((intval($month) + 1) < 10) {
                $temp .= $year . '0' . (intval($month) + 1);
            } else {
                $temp .= $year . (intval($month) + 1);
            }
        } else {
            $temp .= (intval($year) + 1) . '-1-';
        }
        $temp .= '")\'>→</span>';
        $temp .= '&nbsp;<span title="下一年" style="cursor:pointer"  onclick=\'javascript:UpdateCalender("';
        if (2037 > (intval($year) + 1)) {
            $temp .= (intval($year) + 1) . $month;
        } else {
            $temp .= '2037' . $month;
        }
        $temp .= '")\'>↓</span>';
        $temp .= '</td>';
        $temp .= '</tr>';
        //下面显示星期
        $temp .= '<tr><td class="ri-04">日</td><td class="ri-03">一</td><td class="ri-03">二</td><td class="ri-03">三</td><td class="ri-03">四</td><td class="ri-03">五</td><td class="ri-04">六</td></tr>';
        //下面是显示的日期
        for ($i = 0; $i < date('t', $mtime) + $f + 1; $i++) {
            if (0 == ($i % 7)) {
                $temp .= "<tr>";
            }
            if ($i > $f) {
                $dd = $i - $f;
                if (in_array($dd, $array)) {
                    $temp .= '<td';
                    if (($i - $f) == intval(date('d')) && $month == date('m') && $year == date('Y')) {
                        $temp .= ' title="今天" class="ri-07"';
                    } else {
                        $temp .= ' class="calendarFind"';
                    }
                    $temp .= '>';
                    $temp .= '<a href="' . config_item('base_url') . '/';
                    if ($domain) {
                        $temp .= $domain . '/archive/';
                    }
                    $temp .= $year . '/' . $month . '/' . (2 == strlen($dd) ? $dd : '0' . $dd) . '/';
                    $temp .= '">' . $dd . '</a>';
                    $temp .= '</td>';
                } else {
                    $temp .= '<td';
                    if ($i - $f == intval(date('d')) && $month == intval(date('m')) && $year == date('Y')) {
                        $temp .= ' title="今天" class="ri-07"';
                    } else {
                        if ($i % 7 == 0 || $i % 7 == 6) {
                            $temp .= ' class="ri-06"';
                        } else {
                            $temp .= ' class="ri-05"';
                        }
                    }
                    $temp .= '>';
                    $temp .= $dd;
                    $temp .= '</td>';
                }
            } else {
                $temp .= '<td class="ri-05">&nbsp;</td>';
            }

            if ($i % 7 == 6) {
                $temp .= '</tr>';
            }
        }
        if ($i % 7 < 6 && $i % 7 > 0) {
            for ($n = 0; $n < (7 - $i % 7); $n++) {
                $temp .= '<td class="ri-05">&nbsp;</td>';
            }
            $temp .= '</tr>';
        }
        if ($i % 7 == 6) {
            $temp .= '<td class="ri-05">&nbsp;</td>';
            $temp .= '</tr>';
        }
        $temp .= '</table>';
        echo $temp;
    }

    //获取用户个性签名以及等级信息
    function getuserprofile() {
        $duserid = $this->input->get_post('duid');
        $data['signature'] = $this->_getUserInfo($duserid, 'Signature');
        $data['gradeimgs'] = $this->_getGradeImgByUid($duserid);
        $tmp = utf8_str(filter_word($data['signature']), 30) != "" ? utf8_str(filter_word($data['signature']), 30) : '博主未填写签名！';

        $str = '<div style="text-align: left; padding: 3px 0px 3px 10px;">等级：' . $data['gradeimgs'] . '</div>　　
		<div style="text-align: left; padding: 3px 0px 3px 10px;">签名：' . $tmp . '</div>　';
        echo $str;
    }

    //获取文章分类统计信息
    function getarticlesortstat() {
        $memberid = $this->input->get_post('mid');
        $default = &config_item('default');  //
        $domain = $this->input->get_post('do');
        $isowner = $this->input->get_post('isowner');
        $this->load->model('articlesort_socket');
        $data['MemberID'] = $memberid;
        $data['StartNo'] = 0;
        $data['QryCount'] = articlesortcntlimit;
        $data['AjaxList'] = 1;

        $artsort = $this->articlesort_socket->getArticleSortList($data);


        if (isset($artsort['RetRecords']) && $artsort['RetRecords'] > 0) {
            $artsort['Record'] = ($artsort['RetRecords'] == 1) ? array(0 => $artsort['Record']) : $artsort['Record'];
            foreach ($artsort['Record'] as $sort) {
                if (!$isowner && intval($sort['SortID']) == '18296') {
                    continue;
                }
                $sort['Name'] = strip_tags($sort['Name']);
                if (intval($sort['SortID']) == $default['articlesort'][0]) {
                    $str = '<li class="wzflsz01">
				<div class="wzflsz02"></div><div class="wzflsz03"><a title="' . filter_word($sort['Name']) . '" href="' . config_item('base_url') . '/' . $domain . '/sort/' . $sort['SortID'] . '/">' . filter_word($sort['Name']) . '(' . intval($sort['ArticleCount']) . ')</a></div>
				</li>' . $str;
                } else {
                    $str .= '<li class="wzflsz01">
				<div class="wzflsz02"></div><div class="wzflsz03"><a title="' . filter_word($sort['Name']) . '" href="' . config_item('base_url') . '/' . $domain . '/sort/' . $sort['SortID'] . '/">' . filter_word($sort['Name']) . '(' . $sort['ArticleCount'] . ')</a></div>
				</li>';
                }
            }
        } else {
            $str .= '<li class="wzflsz01">
				<div class="wzflsz02"></div><div class="wzflsz03"><a title="' . $default['articlesort'][1] . '" href="' . config_item('base_url') . '/' . $domain . '/sort/' . $default['articlesort'][0] . '/">' . $default['articlesort'][1] . '(0)</a></div>
				</li>';
        }
        echo $str;
    }

    //获取博客统计
    function getblogstat() {
        $memberid = $this->input->get_post('mid');
        $this->load->model('memberblog_socket');
        $data['MemberIDs'] = $memberid;
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $stat2 = $this->memberblog_socket->getMemberBlogStatByCache($data);

        @header('Expires:0');
        @header('Pragma:no-cache');
        @header('Cache-Control:no-cache,no-store,max-age=0,s-maxage=0,must-revalidate');
        $str = '<ul class="CountLst"><li>总浏览量: <span id="s_mtclick">' . (empty($stat2['TotalClick']) ? $stat1['Totalvisit'] : $stat2['TotalClick']) . '</li>
			<li>今日浏览：<span id="s_mdclick">' . (empty($stat2['TodayClick']) ? $stat1['TodayVisit'] : $stat2['TodayClick']) . '</li>
			<li>文章总计：' . $stat1['TotalArticle'] . '</li>
			<li>评论总计：' . $stat1['TotalComment'] . '</li></ul>';
        echo $str;
    }

    //文章存档
    function getarticlearchivelist() {
        $memberid = $this->input->get_post('mid');
        $domain = $this->input->get_post('do');
        $this->load->model('blogarticle_socket');
        $data['MemberID'] = $memberid;
        $artarchive = $this->blogarticle_socket->getBlogArticleArchive($data);

        $str = '';
        if (!empty($artarchive)) {
            if (count($artarchive) > 12) {
                //width:auto!important;height:auto!important; _height:300px; _width:204px;max-height:300px;overflow:hidden;  /*ie6 hack*/
                $str .= '<div style="height:300px;overflow:hidden;" id="archiveDiv">';
            }
            $str.='<li><a title="全部文章" href="' . config_item('base_url') . '/' . $domain . '/articlelist/alist">全部文章</a></li>';
            foreach ($artarchive as $archive) {
                if ($archive['StatDate'] == 0)
                    continue;
                $year = substr($archive['StatDate'], 0, 4);
                $month = substr($archive['StatDate'], 4, 2);
                $str .= '<li><a title="' . $year . '年' . $month . '月" href="' . config_item('base_url') . '/' . $domain . '/articlelist/archive/' . $year . '/' . $month . '/">' . $year . '年' . $month . '月(' . $archive['Articles'] . ')</a></li>';
            }
            if (count($artarchive) > 12) {
                $str .= '</div>';
            }
        } else {
            $str = '暂无文章归档信息';
        }
        if (count($artarchive) > 12) {
            $str .= '<a href="javascript:void(0)" onclick="toggleFun(this)" id="archiveBtn" rel="1" class="Expand">点击展开↓';
        }
        echo $str;
    }

    //获取博客个人推荐文章
    function selfrecomentdart() {

        $memberid = $this->input->get_post('mid');
        $domain = $this->input->get_post('do');
        $extract['blogconfig'] = $this->_getBlogConfig($memberid);
        $num = $extract['blogconfig']['RecommendNumber'];

        $this->load->model('blogarticle_socket');
        $data['MemberID'] = $memberid;
        $data['SelfRecommend'] = 1;

        $data['StartNo'] = 0;
        $data['QryCount'] = empty($num) ? commonpagesize : $num;

        $recommendart = $this->blogarticle_socket->getMemberArticleListIndex($data);
        $str = '';
        if (isset($recommendart['RetRecords']) && $recommendart['RetRecords'] > 0) {
            $recommendart['Record'] = ($recommendart['RetRecords'] == 1) ? array('0' => $recommendart['Record']) : $recommendart['Record'];
            $i = 1;
            foreach ($recommendart['Record'] as $art) {
                if ($i > $data['QryCount']) {
                    break;
                }
                if (empty($art['Title'])) {
                    continue;
                }

                $dot = '';
                if (strlen($art['Title']) > 22) {
                    $dot = '...';
                }

                $art['Title'] = filter_word($art['Title']);
                $str .= '<li><a target="_blank" title="' . $art['Title'] . '" href="' . config_item('base_url') . '/' . $domain . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html">' . utf8_str($art['Title'], 22, 'false') . $dot . '</a></li>';
                $i++;
            };
        } else {
            $str = '暂无推荐文章<br />';
        }
        echo $str;
    }

    //获取友情连接树
    function getflinktree() {
        $data['MemberID'] = $this->input->get_post('mid');
        $bloginfo = $this->_getBlogInfoByMemberID($data['MemberID']);
        $isowner = $this->_checkOwnUser($bloginfo['UserID']);

        if ($isowner === true) {
            $data['IsPublic'] = -1; //博主取全部链接
        } else {
            $data['IsPublic'] = 0; //访客取公开链接
        }
        $data['StartNo'] = -1;
        $default = &config_item('default');
        $this->load->model('bloglink_socket');
        $tempInfo = $this->bloglink_socket->getLinkList($data);

        $data['StartNo'] = 0;
        $data['QryCount'] = $tempInfo['TtlRecords'];
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $flink = $this->bloglink_socket->getLinkList($data);
        unset($data);

        $data['MemberID'] = $this->input->get_post('mid');
        $data['StartNo'] = -1;
        $tempInfo = $this->bloglink_socket->getLinkSortList($data);

        $data['QryCount'] = $tempInfo['TtlRecords'];
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $data['StartNo'] = 0;
        $flinksort = $this->bloglink_socket->getLinkSortList($data);

        $str = '';
        if (isset($flinksort['RetRecords']) && $flinksort['RetRecords'] > 0) {
            if ($flinksort['RetRecords'] == 1) {
                $flinksort['Record'] = array($flinksort['Record']);
            }
            foreach ($flinksort['Record'] as $fs) {

                $str .='<span>' . $fs['Name'] . '</span><br/>';
                if (isset($flink['RetRecords']) && $flink['RetRecords'] > 0) {
                    if ($flink['RetRecords'] == 1) {
                        $flink['Record'] = array($flink['Record']);
                    }
                    foreach ($flink['Record'] as $f) {

                        if (isset($f['LinkSortID']) && $f['LinkSortID'] == $fs['LinkSortID'])
                            $str .= '&nbsp;· <a href="' . $f['Path'] . '" target="_blank">' . $f['Name'] . '</a><br/>';
                    }
                }
            }
        }
        //如果取不到分类，或者自定义分类不存在，把所有连接归到默认分类
        else {
            if (isset($flink['RetRecords']) && $flink['RetRecords'] > 0) {
                //默认分类名
                $str .='<span>' . $default['linksort'][1] . '</span><br/>';
                if ($flink['RetRecords'] == 1) {
                    $flink['Record'] = array($flink['Record']);
                }

                foreach ($flink['Record'] as $f) {
                    //if($f['LinkSortID'] == 0 || $f['LinkSortID'] == 5){
                    $str .= '&nbsp;· <a href="' . $f['Path'] . '" target="_blank">' . $f['Name'] . '</a><br/>';
                    //}
                }
            }
        }
        echo $str;
    }

    //获取友情链接分类
    function getflinksortlist() {
        $memberid = $this->input->get_post('mid');
        $data['MemberID'] = $memberid;
        $data['StartNo'] = 0;
        $data['QryCount'] = linksortcntlimit;
        $this->load->model('bloglink_socket');
        $flinksort = $this->bloglink_socket->getLinkSortList($data);
        $default = &config_item('default');
        $str = '';
        if (!empty($flinksort)) {
            $flinksort['Record'] = ($flinksort['RetRecords'] == 1) ? array('0' => $flinksort['Record']) : $flinksort['Record'];
            foreach ($flinksort['Record'] as $lsort) {
                $lsort['Name'] = filter_word($lsort['Name']);
                if ($default['linksort'][0] != intval($lsort['LinkSortID'])) {
                    $str .= '<span onclick="showlinks(\'' . $lsort['LinkSortID'] . '\',\'' . $memberid . '\');" id="flinksort' . $lsort['LinkSortID'] . '">' . utf8_str($lsort['Name'], 32) . '</span><br />';
                } else {
                    $str = '<span onclick="showlinks(\'' . $lsort['LinkSortID'] . '\',\'' . $memberid . '\');" id="flinksort' . $lsort['LinkSortID'] . '">' . utf8_str($lsort['Name'], 32) . '</span><br />' . $str;
                }
            }
        } else {
            $str = '<span>' . $default['linksort'][1] . '</span><br />';
        }
        echo $str;
    }

    //获取友情链接
    function getflinklist() {
        $data['MemberID'] = $this->input->get_post('mid');
        $data['LinkSortID'] = $this->input->get_post('sortid');
        $data['IsPublic'] = 0;
        $data['StartNo'] = 0;
        $data['QryCount'] = commonpagesize;
        $this->load->model('bloglink_socket');

        $flink = $this->bloglink_socket->getLinkList($data);
        $str = '<div id="links' . $data['LinkSortID'] . '">';
        if (isset($flink['RetRecords']) && $flink['RetRecords'] > 0) {
            $flink['Record'] = ($flink['RetRecords'] == 1) ? array('0' => $flink['Record']) : $flink['Record'];
            foreach ($flink['Record'] as $link) {
                $link['Name'] = filter_word($link['Name']);
                $str .= '<a target="_blank" href="' . $link['Path'] . '">' . $link['Name'] . '</a><br>';
            }
        } else {
            $str .= '该分类暂无链接信息<br />';
        }
        $str .= '</div>';
        echo $str;
    }

    //获取首页文章列表信息列表
    function getindexarticlelist($domain) {
        $memberid = $this->input->get_post('mid');
        $num = intval($this->input->get_post('num'));
        $showmode = $this->input->get_post('mod');

        //获取标志是否有权限修改
        $isowner = false;
        if (false !== $this->_checkUserlogin()) {
            $bloginfo = $this->_getBlogInfoByDomain($domain);
            $this->_checkOwnBlog($bloginfo['MemberID'], $memberid);
            $isowner = $this->_checkOwnUser($bloginfo['UserID']);
        }

        $data['MemberID'] = $memberid;
        $data['StartNo'] = 0;
        $data['QryCount'] = empty($num) ? commonpagesize : $num;
        $this->load->model('blogarticle_socket');
        $artlist = $this->blogarticle_socket->getMemberArticleListIndex($data);

        $str = '';
        $default = &config_item('default');
        if (isset($artlist['RetRecords']) && ($artlist['RetRecords'] > 0)) {
            $artidlist = '';
            $urlprefix = config_item('base_url') . '/' . $domain;

            if ($artlist['RetRecords'] == 1) {
                $artlist['Record'] = array('0' => $artlist['Record']);
            }

            foreach ($artlist['Record'] as $art) {
                $art['Title'] = filter_word($art['Title']);
                //$art['Title'] = $art['IsTop']==1 ? '<span style="color:red;">[顶]</span>'.utf8_str($art['Title'],32) : utf8_str($art['Title'],32);

                $art['Title'] = $art['IsTop'] == 1 ? '<span style="color:red;">[顶]</span>' . $art['Title'] : $art['Title'];

                if ($art['Recommend'] == 2 || $art['Recommend'] == 3 || $art['IsUsed'] == 1) {
                    $art['Title'] = '<img align="absmiddle" title="该文章已被采用到博客首页" border="0" alt="该文章已被采用到博客首页" src="http://img.cnfol.com/newblog/Version2/images/tui.png" /> ' . $art['Title'];
                }

                $artidlist .= ',' . $art['ArticleID'];
                $str .= '<div id="Mod_IndexArticle" class="wzlbsz01"><a href="' . $urlprefix . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html">' . $art['Title'] . '</a></div><div class="wzlbsz02">[  ' . $art['AppearTime'] . ' ]</div><div class="wzlbsz03">';

                if ($showmode == 1) {
                    $str .= $art['Summary'];
                }
                $flashCode = ($isowner == true) ? getVerifyStr($bloginfo['UserID'] . $art['ArticleID']) : '0';

                $str .= '</div><div class="wzlbsz04"><a href="' . $urlprefix . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html">阅读全文</a></div><div class="wzlbsz05"><a onclick="javascript:Show(\'Link' . $art['ArticleID'] . '\',\'trackback' . $art['ArticleID'] . '\',\'Manage' . $art['ArticleID'] . '\')" href="javascript:void(0)">分享</a> | <span id="atonclick_' . $art['ArticleID'] . '">0</span>次浏览 | <a onclick="javascript:Show(\'trackback' . $art['ArticleID'] . '\',\'Link' . $art['ArticleID'] . '\',\'Manage' . $art['ArticleID'] . '\');UpdateTrackbackPage(\'' . $art['ArticleID'] . '\',\'1\')" href="javascript:void(0)" co=\'' . $flashCode . '\' id=\'co' . $art['ArticleID'] . '\'>引用通告</a> | 类别：<a href="' . $urlprefix . '/sort/' . $art['SortID'] . '">' . (($art['SortName'] == '') ? $default['articlesort'][1] : $art['SortName']) . '</a>';

                if ($isowner == true) {
                    $str .= ' | <span onclick="javascript:submit_form(\'action_form\',\'' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '\');" style="cursor: pointer;">编辑</span> | <span onclick="javascript:del(\'' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '\');" style="cursor: pointer;">删除</span> | <a target="_blank" href="' . $urlprefix . '/manage/comment/CommentList/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '">评论(' . (isset($art['CommentNumber']) ? $art['CommentNumber'] : '0') . ')</a>';
                    if ($art['IsTop'] == 0)
                        $str .= ' | <a href="javascript:;" onclick="blogtoparticle(\'' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '\',1);">置顶</a>';
                    else
                        $str .= ' | <a href="javascript:;" onclick="blogtoparticle(\'' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '\',0);">取消置顶</a>';
                }
                $str .= '</div><div style="display: none;padding-left:20px;" id="Link' . $art['ArticleID'] . '"><span id="copyLink' . $art['ArticleID'] . '">' . $urlprefix . '/article/' . strtotime($art['AppearTime']) . '-' . $art['ArticleID'] . '.html</span>&nbsp;&nbsp;<br /><a style="cursor: pointer;" onclick="javascript:copy(\'copyLink' . $art['ArticleID'] . '\')" href="javascript:void(0)">复制链接</a><span style="color:#999">(请复制文章连接，您可以粘贴至QQ、MSN、EMAIL等发给您的好友！)</span></div>';

                $str .= '<div style="display: none;padding-left:20px;" id="trackback' . $art['ArticleID'] . '">本文引用地址:<br /><span id="copytrack' . $art['ArticleID'] . '">' . TrackbackUrl($art['ArticleID']) . '</span>&nbsp;&nbsp;<br /><a style="cursor: pointer;" onclick="javascript:copy(\'copytrack' . $art['ArticleID'] . '\')" href="javascript:void(0)">复制链接</a><br><div id="tbl' . $art['ArticleID'] . '">正在请求中...</div></div><div class="wzlbsz06"></div>';
            }
            if ($artlist['RetRecords'] >= $data['QryCount']) {
                $str .= '<div class="wzlbsz11"><a href="' . $urlprefix . '/list/2">更多文章</a></div>';
            }
            $str .= '<div id="ajaxdiv"></div>
            <script language="javascript">
                $(function(){
	            $("#ajaxdiv").load("' . config_item('base_url') . '/ajaxomcount/art/' . $artidlist . '?"+new Date().getTime());
                })
            </script>';
        } else {
            exit('<div class="wzlbsz11"></div>');
        }
        echo $str;
    }

    //获取用户的相册图片
    function getablumphotolist($memberid, $userid) {
        $flashCode = $this->input->get_post('co');

        $str = '<script language="javascript">
					adRotator.initialize("AdRotator");';
        if ($flashCode != getVerifyStr($memberid . $userid)) {
            $str .= 'adRotator.add("http://images.cnfol.com/uploads/mod_blog/1/nothing.gif","","");';
        } else {
            //获取博客相册
            $data['UserID'] = $userid;
            $data['RelationID'] = $memberid;
            $data['StartNo'] = 0;
            $data['QryCount'] = photolistpagesize;
            $this->load->model('blogalbum_socket');
            $return = $this->blogalbum_socket->getAlbumPhoteList($data);
            if (isset($return['list']) && !empty($return['list'])) {
                foreach ($return['list'] as $photo) {
                    $imagurl = $photo['URL'];
                    $str .= 'adRotator.add("' . $imagurl . '","' . filter_word($photo['Name']) . '","' . $photo['AlbumID'] . '");';
                }
            } else {
                $str .= 'adRotator.add("http://images.cnfol.com/uploads/mod_blog/1/nothing.gif","","");';
            }
        }
        $str .= 'adRotator.play();
				</script>';
        echo $str;
    }

    //获取个人博客列表
    function ajaxgetuserbloglist($userid) {
        //$userid = intval($this->input->post('userid'));

        if (false == $this->_checkUserlogin()) {
            $data['error'] = '请先登入再进行后续操作';
            $data['errno'] = 'login';
            echo json_encode($data);

            exit;
        } else if ($this->user['userid'] != $userid) {
            $data['error'] = '拒绝获取他人的博客列表信息';
            $data['errno'] = 'nouser';
            echo json_encode($data);

            exit;
        }
        //获取个人博客列表

        $bloglist = $this->_getBlogListByUid($userid);

        $data['error'] = getPrimariBlogDomain($bloglist);
        $data['errno'] = 'succ';
        echo json_encode($data);
    }

    //获取浏览过该文章的人还浏览过
    function getguesteverbrowse() {
        $articleid = $this->input->get_post('articleid');
        $appeartime = $this->input->get_post('appeartime');

        $this->load->model('blogarticle_socket');
        $data['articleid'] = $articleid;
        $data['appeartime'] = $appeartime;

        $recommendart = $this->blogarticle_socket->getGuestEverBrowse($data);
        $str = '';
        //print_r($recommendart);
        if (isset($recommendart) && count($recommendart) > 0 && $recommendart) {
            foreach ($recommendart as $key => $art) {
                if (!is_numeric($key)) {
                    continue;
                }
                if (!empty($art['Title'])) {
                    $art['Title'] = filter_word($art['Title']);

                    $dot = '';
                    if (strlen($art['Title']) > 22) {
                        $dot = '...';
                    }


                    $str .= '<li><a target="_blank" title="' . $art['Title'] . '" href="' . config_item('base_url') . '/' . $art['DomainName'] . '/article/' . $art['AppearTime'] . '-' . $art['ArticleID'] . '.html">' . utf8_str($art['Title'], 22, 'false') . $dot . '</a></li>';
                }
            };
        } else {
            $str = '暂无记录<br />';
        }
        echo $str;
    }

    /*
     *  我的好友，我的关注
     */

    function rightFans($domainname) {


        $this->load->model("friendmodel");

        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        //粉丝
        $param['UserID'] = $bloginfo['UserID'];
        $param['FType'] = 2;
        $param['StartNo'] = 0;
        $param['QryCount'] = 6;
        $focuse = $this->friendmodel->getFriendList($param);
        $extract['focuse'] = $focuse ? $focuse['Record'] : '';
        $extract['focuseCount'] = $focuse['TtlRecords'];
        unset($param);
        //关注
        $param['UserID'] = $bloginfo['UserID'];
        $param['FType'] = 0;
        $param['StartNo'] = 0;
        $param['QryCount'] = 6;
        $focused = $this->friendmodel->getFriendList($param);
        $extract['focused'] = $focused ? $focused['Record'] : '';
        $extract['focusedCount'] = $focused['TtlRecords'];
        $extract['bloginfo'] = $bloginfo;

        $this->load->view("ajax/rightfans.shtml", $extract);
    }

    /*
     *  共同关注
     */

    function jointly() {

        $this->load->model("friendmodel");

        $param['UserID'] = $this->input->get('UserID');
        $param['VisitantID'] = $this->input->get('VisitantID');

        $param['StartNo'] = 0;
        $param['QryCount'] = 8;
        $return = $this->friendmodel->getJointly($param);

        if ($return) {
            $str = '';
            foreach ($return as $value) {

                $str.='<li>
          				<a href=' . config_item("pass_base_url") . '/otherpersoninfo' . $value['UserID'] . '.html  target="_blank"><img src=' . getUserHead($value['UserID']) . ' class="refid" refid="' . $value['UserID'] . '" style="cursor: pointer;"  /></a>
          				<p><a href=' . config_item("pass_base_url") . '/otherpersoninfo' . $value['UserID'] . '.html target="_blank">' . utf8_str($value['NickName'], 8, 'false') . '</a></p>
        				</li>';
            }
            echo($str);
        } else {
            echo('<li>暂无相互关注的好友</li>');
        }
    }

}

//end class
?>