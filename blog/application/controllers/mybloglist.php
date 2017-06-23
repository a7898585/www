<?php

/* * **********************
 * 功能：   博客个人文章
 * author： lifeng
 * *********************** */

class MyBlogList extends MY_Controller {

    function MyBlogList() {
        parent::MY_Controller();
    }

    //我的博客页面定时获取最新文章列表用
    function getMsg() {
        $this->_checkUserlogin();
        $dyData = $brData = $msgData = '';

        $domainname = $this->input->get_post('domainname');
        $this->load->model('blogarticle_socket');

        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $UserID = $this->user['userid'];
        $rs = $mcache->get('getmyblogmsg_' . $UserID);
        $limittime = isset($rs['limittime']) ? $rs['limittime'] : time() - 24 * 60 * 60;


        //动态
        $dyTime = isset($rs['dyTime']) ? $rs['dyTime'] : $limittime;

        $dyNum = $this->blogarticle_socket->getDynamicCount(array('UserID' => $UserID, 'ViewTime' => date('Y-m-d H:i:s', $dyTime)));

        //if($UserID=='1296')
        //{
        //error_log($dyNum.print_r(date('Y-m-d H:i:s',$rs['dyTime']),true).'|'.date('Y-m-d H:i:s', $dyTime).'\r\n', 3, DEFAULT_PATH.'/logs/adyTime.log');
        //}

        if ($dyNum > 0) {
            //$dyData = '<a href="'.config_item('base_url').'/index.php/news/newlist?limit='.$dyNum.'&domainname='.$domainname.'&rtime=1" class="notInd">有<span class="coSmart"><strong>'.$dyNum.'</strong></span>条内容，点击查看!</a>';
            $dyData = config_item('base_url') . '/index.php/news/newlist?limit=' . $dyNum . '&domainname=' . $domainname . '&rtime=1';
        } else {
            $dyTime = time();
        }

        $rs['dyNum'] = $dyNum;
        $rs['dyTime'] = $dyTime;
        $rs['dyData'] = $dyData;
        $rs['limittime'] = $limittime;

        $mcache->set('getmyblogmsg_' . $UserID, $rs);

        echo json_encode($rs);
    }

    /**
     * 博客文章保存到草稿箱
     * */
    function Action($domainname) {

        $this->_checkIP();

        if (false == $this->_checkUserlogin()) {
            $data['errno'] = 'login';
            $data['error'] = '登入信息验证失败';
            echo json_encode($data);
            exit;
        }

        $MemberID = $this->input->post("memid");
        $flashCode = $this->input->post("flashCode");

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
        $act = $this->input->get('act');
        switch ($act) {
            case 'add':
                //$addtime = $mcache->get('Article_Add_User_' . $this->user['userid']);//上线后改回
                $this->load->model('memberblog_socket');
                if (!empty($addtime) && (((time() - $addtime['LastTime'] ) <= addarticletime) && ($addtime['IsClose'] == 0))) {

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
                $title = preg_replace("/\s+/i", ' ', $title);
                $titlecode = md5(str_replace(' ', '', $title));

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

                $param['MemberID'] = $MemberID;
                $param['ArticleID'] = 0;
                $param['Title'] = $title;
                $param['Content'] = $this->input->post('content');
                $param['Summary'] = $this->input->post('summary');

                //preg_match('/<img\s*(height\=\"\w*\"\s*alt=\"\"\s*)?src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i',$param['Content'],$matchesPic);
                //$param['PictureUrl'] = $matchesPic['0'];
                $param['PictureUrl'] = articlePicture(array('Content' => $param['Content']));

                if ($param['PictureUrl']) {
                    $param['PictureUrl'] = htmlEncode($param['PictureUrl']);
                    $param['IsMultimedia'] = 1;
                } else {
                    $param['PictureUrl'] = 0;
                }

                preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $param['Content'], $matchesEmbed);
                $param['Multimedia'] = $matchesEmbed['1'];


                if ($param['Multimedia']) {
                    if ($param['IsMultimedia'] == '1') {
                        $param['IsMultimedia'] = 3;
                    } else {
                        $param['IsMultimedia'] = 2;
                    }
                }

                $param['Property'] = 5; //草稿
                $param['ReadStatus'] = 3; //草稿
                $param['IsDel'] = 5; //草稿

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
                } else if ($param['SysTagID'] == 0) {
                    $data['errno'] = 'tagId';
                    $data['error'] = '请选择文章分类';
                    echo json_encode($data);
                    exit;
                } else {

                    //临时变量
                    $tmpp['Content_c'] = remove_invisible_code($param['Content']);
                    $tmpp['Summary_c'] = remove_invisible_code($param['Summary']);

                    $param['Content'] = htmlEncode(remove_invisible_code($param['Content']));
                    $param['Summary'] = htmlEncode(remove_invisible_code($param['Summary']));
                    $param['image'] = htmlEncode(remove_invisible_code($param['image']));

                    $param['Title'] = htmlEncode($param['Title']);
                    $param['IsUTOP'] = 0;



                    $this->load->model('blogarticle_socket');
                    $Status = $this->blogarticle_socket->addBlogArticle($param, $tmpp);
                    //error_log(print_r($param,true), 3, '/home/httpd/logs/a23132.log');


                    if (empty($Status)) {
                        $data['errno'] = 'empty';
                        $data['error'] = '文章保存失败.';
                        echo json_encode($data);
                        exit;
                    }

                    if (!isset($Status['Record']['ArticleID'])) {
                        $Status['Record']['ArticleID'] = intval($Status['Description']);
                    }
                    $log_str = '--111--|domain：' . $domainname . '|' . $_SERVER['REMOTE_ADDR'] . ' | ' . $this->input->ip_address() . ' | IsUsed=' . $param['IsUsed'] . ' | isbye=' . $isby . ' | IsDel=' . print_r($param['IsDel'], true) . ' | blogconfig:' . print_r($groups, true) . '|title=' . print_r($title, true) . "\r\n";
                    write_log($log_str, BLOG_INDEX_LOG . '/article/articleaddlog_draf_' . date('Ymd') . '.log', __METHOD__);

                    if ($Status['Code'] == '00') {



                        $data['errno'] = 'success';

                        if ($param['IsDel'] == 5) {
                            $data['isdel'] = $param['IsDel'];
                            $data['error'] = '文章发表成功！您文章需要审核后才会展示！';
                        } else if ($param['IsDel'] == 1 || $param['IsDel'] == 3 || $param['IsDel'] == 4) {
                            $data['isdel'] = $param['IsDel'];
                            $data['error'] = '文章已删除，请查看其他文章！';
                        } else {

                            $data['error'] = '文章发表成功';
                        }

                        $this->_delDraftsCache($MemberID);
                    } else if ($Status['Code'] == '200036') {
                        $data['errno'] = $Status['Code'];
                        $data['error'] = '文章保存成功，请等待审核';

                        $this->_delDraftsCache($MemberID);
                    } else if ($Status['Code'] == '200037') {
                        $data['errno'] = $Status['Code'];
                        $data['error'] = '文章被系统自动删除';
                    } else {
                        $data['errno'] = $Status['Code'];    //失败，直接退出
                        $data['error'] = '文章保存失败';
                        echo json_encode($data);
                        exit;
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
                    //$tagStr = $this->_taghandle($tagStr);//保存草稿的时候不保存标签 
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


                $param['SysTagID'] = intval($this->input->post('tagIdEdit'));
                if (!is_int($param['SysTagID']) || !array_key_exists($param['SysTagID'], config_item('sysTagList'))) {
                    $param['SysTagID'] = 1459;
                }

                $param['AppearTime'] = date("Y-m-d H:i:s", $this->input->post('draftsAppeartime'));

                if (!isset($param['AppearTime']) || !preg_match("/^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$/i", $param['AppearTime'])) {
                    $data['errno'] = 'artid';
                    $data['error'] = '文章编辑信息提交丢失';
                    echo json_encode($data);
                    exit;
                }


                $param['ArticleID'] = intval($this->input->post('draftsArticleid'));
                if (!is_int($param['ArticleID']) && $param['ArticleID'] < 0) {
                    $data['errno'] = 'artid';
                    $data['error'] = '文章编辑信息提交丢失';
                    echo json_encode($data);
                    exit;
                }

                $param['MemberID'] = $MemberID;
                $param['Title'] = $this->input->post('title');
                $param['Title'] = preg_replace("/\s+/i", ' ', $param['Title']);

                $param['Content'] = $this->input->post('content');

                //preg_match('/<img\s*(height\=\"\w*\"\s*alt=\"\"\s*)?src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)[^>]*>/i',$param['Content'],$matchesPic);
                //$param['PictureUrl'] = $matchesPic['0'];
                $param['PictureUrl'] = articlePicture(array('Content' => $param['Content']));

                if ($param['PictureUrl']) {
                    $param['PictureUrl'] = htmlEncode($param['PictureUrl']);
                    $param['IsMultimedia'] = 1;
                } else {
                    $param['PictureUrl'] = 0;
                    $param['IsMultimedia'] = 0;
                }

                preg_match('/<embed\s*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i', $param['Content'], $matchesEmbed);
                $param['Multimedia'] = $matchesEmbed['1'];

                if ($param['Multimedia']) {

                    if ($param['IsMultimedia'] == '1') {
                        $param['IsMultimedia'] = 3;
                    } else {
                        $param['IsMultimedia'] = 2;
                    }
                }

                $param['Summary'] = $this->input->post('summary');
                $param['ReadStatus'] = 3; //草稿
                $param['IsDel'] = 5; //草稿
                $param['SelfRecommend'] = intval($this->input->post('memberRecommend'));
                $param['SelfRecommend'] = (is_int($param['SelfRecommend'])) ? $param['SelfRecommend'] : 0;
                $param['SortID'] = intval($this->input->post('sortId'));
                $param['SortID'] = (is_int($param['SortID'])) ? $param['SortID'] : 18295;
                $param['IP'] = $this->input->ip_address();
                $param['TagIDs'] = $tagStr;
                $param['TrackBack'] = $trackback;
                $param['Summary'] = (trim($param['Summary']) == "") ? getsummary($param['Content'], limitsumautolen, 1) : $param['Summary'];
                $param['LastCommentDate'] = date('Y-m-d H:i:s');
                $param['GiftPrice'] = $this->input->post('GiftPriceEdit') != false ? intval($this->input->post('GiftPriceEdit')) : 0;

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
                    $param['image'] = htmlEncode(remove_invisible_code($param['image']));
                    $param['Title'] = htmlEncode($param['Title']);

                    $param['Content'] = preg_replace("/position(\s)*:(\s)*absolute/i", 'position:', $param['Content']);

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

                    $data['articleid'] = strval($param['ArticleID']);
                    $data['appeartime'] = strval($param['AppearTime']);

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
        $tagStr = htmlspecialchars(strip_tags(trim($tagStr)));

        $tags = explode(',', $tagStr);
        $tags = is_string($tags) ? array(0 => $tags) : $tags;
        $tag = array_unique($tags);
        $tagorder = array();
        if (count($tags) > eacharticletaglimit) {
            $data['errno'] = 'tagerr';
            $data['error'] = '每篇文章最多允许有5个标签';
            return;
        }

        foreach ($tags as $key => $tag) {
            if (strlen($tag) > eachtaglengthlimit) {
                $data['errno'] = 'tagerr';
                $data['error'] = '单个标签长度应该在' . (eachtaglengthlimit / 3) . '个字以内';
                return;
            } else if (strlen(trim($tag)) == 0) {
                unset($tags[$key]);
            }

            $tagorder[] = 0;
        }
        $this->load->model('articletags_socket');
        $param['UserIDs'] = $this->user['userid'];
        $param['StartNo'] = -1;


        $TtlRecords = $this->articletags_socket->getArticleTagList($param);
        if (articletagcntlimit < $TtlRecords) {
            $data['errno'] = 'tagerr';
            $data['error'] = '您使用的标签数目已经超出了' . articletagcntlimit . '个的限制，请删除无效标签！';
            return;
        }
        unset($param);
        $param['UserID'] = $this->user['userid'];
        $param['OrderNos'] = join(',', $tagorder);
        $param['TagNames'] = join(',', $tags);
        $tagList = $this->articletags_socket->addArticleTag($param);

        if ($tagList == false) {
            $data['errno'] = 'savetag';
            $data['error'] = '标签保存失败';
            return;
        }
        $tagList = (isset($tagList['TagID'])) ? array(0 => $tagList) : $tagList;
        foreach ($tagList as $val) {
            $tagIDs[] = $val['TagID'];
        }
        $tagStr = join(',', $tagIDs);
        return $tagStr;
    }

}

//end class
?>