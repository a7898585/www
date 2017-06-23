<?php

/* * **********************
 * 功能：   博客个人相册
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Album extends MY_Controller {

    function Album() {
        parent::MY_Controller();
        $this->pagesize = $this->config->item("showc");
    }

    /**
     * @ 每个博客最大3个相册
     * @ 添加博客相册
     * */
    function AddAlbum($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        $extract['baseurl'] = &config_item('base_url');
        $this->load->view('album/album_add.shtml', $extract);
    }

    /**
     * @ 每个博客最大3个相册
     * @ 添加博客相册
     * */
    function PhotoUpload($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);

        $this->load->model('blogalbum_socket');
        $data['UserID'] = $extract['bloginfo']['UserID'];
        $data['RelationID'] = $extract['bloginfo']['MemberID'];
        $data['Property'] = -1;
        $data['StartNo'] = 0;
        $data['QryCount'] = blogalbumlimit;
        $extract['album'] = $this->blogalbum_socket->getblogalbum($data);

        $extract['baseurl'] = &config_item('base_url');
        $extract['AlbumID'] = ($this->input->get_post('AlbumID') == '') ? '0' : $this->input->get_post('AlbumID');
        $this->load->view('album/photo_upload.shtml', $extract);
    }

    /**
     * @ 编辑博客相册
     * */
    function EditAlbum($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        //没有权限
        $this->_checkUser($extract['bloginfo']['UserID']);
        $data['AlbumID'] = intval($this->input->get_post('albumID'));
        $data['RelationID'] = $extract['bloginfo']['MemberID'];
        $data['UserID'] = $extract['bloginfo']['UserID'];
        $data['Property'] = -1;
        $data['StartNo'] = 0;
        $data['QryCount'] = 1;
        $this->load->model('blogalbum_socket');
        $albuminfo = $this->blogalbum_socket->getAlubmInfoById($data);
        if (empty($albuminfo)) {
            echo "您要编辑的相册信息在此不能编辑";
            echo '<script>setTimeout("top.g_pop.close();",1000);</script>';
            exit;
        }
//        print_r($albuminfo);
        $extract['albumid'] = $data['AlbumID'];
        $extract['albuminfo'] = $albuminfo;
        $extract['baseurl'] = &config_item('base_url'); //编辑添加共享模板
        $extract['flag'] = $this->input->get_post('flag');

        $this->load->view('album/album_add.shtml', $extract);
    }

    /**
     * 通过Ajax获取信息
     * return json
     * @ 相册操作的相关动作 增删改
     * */
    function AlbumAction($domainname) {
        $this->_checkLogin();
        //通过博客名获取博客信息	
        $bloginfo = $this->_getBlogInfoByDomain($domainname);
        //没有权限
        $this->_checkUser($bloginfo['UserID']);
        $act = $this->input->get_post('act');
        $this->load->model('blogalbum_socket');
        $returnurl = config_item('base_url') . '/' . $bloginfo['DomainName'] . '/albumlist';
        switch ($act) {
            case 'addalbum': //增修相册
                $albumid = intval($this->input->post('alubmid'));
                $param['GroupIDs'] = trim($bloginfo['GroupID'], ',');
                $blogaccess = $this->memberblog_socket->getAccessList($param);

                if ($albumid == 0) {
                    $this->_checkAccess($blogaccess, 'AddAlbum');
                } else {
                    $this->_checkAccess($blogaccess, 'EditAlbum');
                }
                $name = $this->input->post('name', TRUE);
                $summary = $this->input->post('summary', TRUE);
                if (strlen($name) < 3 || strlen($name) > 100) {
                    echo "相册名称应该在3-100个字节以内";
                    echo '<script>setTimeout("parent.g_pop.close();",1000);</script>';
                    exit;
                }
                if (strlen($summary) > 200) {
                    echo "相册简介应该在0-200个字节以内";
                    echo '<script>setTimeout("parent.g_pop.close();",1000);</script>';
                    exit;
                }
                $data['UserID'] = $bloginfo['UserID'];
                $data['RelationID'] = $bloginfo['MemberID'];
                $data['Property'] = -1;
                $data['StartNo'] = -1;

                $tmpCnt = $this->blogalbum_socket->getblogalbum($data);

                $albumid = intval($this->input->post('alubmid'));
                if (intval($tmpCnt) >= blogalbumlimit && $albumid <= 0) {
                    echo "每个博客最多允许有" . blogalbumlimit . "个相册";
                    echo '<script>setTimeout("parent.g_pop.close();",1000);</script>';
                    exit;
                }
                unset($data);
                $data['AlbumID'] = $albumid;
                $data['RelationID'] = $bloginfo['MemberID'];
                $data['UserID'] = $this->user['userid'];
                $data['Name'] = htmlEncode($name);
                $data['Summary'] = htmlEncode($summary);
                $data['Property'] = intval($this->input->post('property'));
                if ($this->blogalbum_socket->addAlbum($data)) {
                    echo ($data['AlbumID'] > 0) ? "相册编辑提交成功" : "博客相册创建成功";
                    echo '<script>setTimeout("parent.location.href=\'' . $returnurl . '\';",2000);</script>';
                    exit;
                } else {
                    echo ($data['AlbumID'] > 0) ? "相册编辑提交失败" : "博客相册创建失败";
                    echo '<script>setTimeout("parent.g_pop.close();",1000);</script>';
                    exit;
                }
                break;
            case 'setphoto':
                $param['GroupIDs'] = trim($bloginfo['GroupID'], ',');
                $blogaccess = $this->memberblog_socket->getAccessList($param);
                $this->_checkAccess($blogaccess, 'EditAlbum');

                $flashCode = $this->input->get_post('vCode');
                unset($data);
                $data['AlbumID'] = $this->input->get_post('albumid');
                $data['CoverID'] = intval($this->input->post('coverid'));
                $data['UserID'] = $bloginfo['UserID'];
                $data['RelationID'] = $bloginfo['MemberID'];
                if ($data['CoverID'] < 1) {
                    echo "图片信息传递不合法";
                    exit;
                } else if (getVerifyStr($data['AlbumID'] . $bloginfo['UserID']) != $flashCode) {
                    echo "信息传递不合法";
                    exit;
                } else {
                    if ($this->blogalbum_socket->addAlbum($data)) {
                        echo "succ";
                        exit;
                    } else {
                        echo "相册封面设置失败";
                        exit;
                    }
                }
                break;
            case 'delalbum':
                $param['GroupIDs'] = trim($bloginfo['GroupID'], ',');
                $blogaccess = $this->memberblog_socket->getAccessList($param);
                $this->_checkAccess($blogaccess, 'DelAlbum');

                $flashCode = $this->input->get_post('flashCode');
                $data['AlbumID'] = intval($this->input->post('albumID'));
                $data['UserID'] = $bloginfo['UserID'];
                $data['MemberID'] = $bloginfo['MemberID'];

                if ($flashCode != getVerifyStr($bloginfo['UserID'] . $data['AlbumID'])) {

                    //echo '<script>setTimeout("parent.g_pop.close();",1000);</script>';
                    echo(json_encode(array('error' => '传递参数信息非法', 'erron' => '1')));
                    exit;
                }
                $this->load->model('blogalbum_socket');
                $flag = $this->blogalbum_socket->delAlbum($data);
                $error = ($flag == true) ? "博客相册删除成功" : "博客相册删除失败";
                echo(json_encode(array('error' => $error, 'erron' => '1')));
                exit;
                break;

            default:
                break;
        }
    }

    /**
     * @ 相册列表页
     * */
    function AlbumList($domainname) {
        $this->_checkUserlogin();
        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']);
        //创建点击统计url
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], $extract['isowner']);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);
        $this->load->model('blogalbum_socket');
        $data['UserID'] = $extract['bloginfo']['UserID'];
        $data['RelationID'] = $extract['bloginfo']['MemberID'];
        $data['Property'] = (!$extract['isowner']) ? 0 : -1;  //如果是博主，显示所有相册
        /* --------博客信息--------------- */
        $extract['userinfo'] = $this->_getUserInfoByUid($extract['bloginfo']['UserID']);
        /* --------统计各文章或各博客主页被访问次数--------------- */
        $this->_hotBlogArticle(array('domainname' => $domainname, 'appearTime' => '', 'articleID' => '', 'guestType' => $this->user['userid']), $extract['bloginfo']['UserID']);
        /* ------------------start粉丝，我关注的，相互关注数  ----------------------------------- */
        $extract['friendsnumber'] = $this->_getFriend($extract['bloginfo']['UserID']);
        $extract['isFrends'] = $this->_isFriend($this->user['userid'], $extract['bloginfo']['UserID']);
        /* ------------------end粉丝，我关注的，相互关注数  ------------------------------------- */

        /* ----------start博主的文章数量----------------------------------- */
        $data['MemberIDs'] = $extract['bloginfo']['MemberID'];
        $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
        $extract['TotalArticle'] = $stat1['TotalArticle'];
        /* ------------end博主的文章数量------------------------------------ */
        /* 实名认真 -start */
        $params['UserID'] = $extract['bloginfo']['UserID'];
        $extract['auth'] = $this->user_socket->realNameAuth($params);
        /** 实名认真 -end */
        /* -------end-博客信息--------------- */

        $data['StartNo'] = 0;
        $data['QryCount'] = blogalbumlimit;  //默认6个
        $albumList = $this->blogalbum_socket->getblogalbum($data);
        //print_r($data);
        //print_r($albumList);
        $result = array();
        foreach ($albumList['list'] as $value) {
            if ($value['Property'] == 1) {
                if ($extract['isowner'] == FALSE) {
                    unset($value);
                }
            }
            if ($value) {
                $result['list'][] = $value;
            }
        }
        $result['count'] = count($result['list']);
//        print_r($result);
        $extract['album'] = $result;

        $extract['user'] = $this->user;
        $extract['userid'] = $extract['user']['userid'];
        $blocks = &$this->config->item('block');
        $extract['blocks'] = $blocks;
        $extract['block'] = $blocks['articlelist'];
        $extract['tmp_jointly'] = $blocks['jointly'];
        $extract['default'] = config_item('default');
        $extract['title'] = $extract['bloginfo']['BlogName'] . '相册列表_' . $extract['bloginfo']['NickName'];
        $extract['devmyblogloginheader'] = $blocks['devmyblogloginheader'];
        $extract['devmyblogcommonright'] = $blocks['devmyblogcommonright'];
        $extract['peronalfoot'] = $blocks['devmyblogcommonfooter'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 3;
        /* --------右边公共栏加载--------------- */
        $extract['modulepath'] = &config_item('module_path');
        $extract['show_renewvisitor'] = $blocks['show_renewvisitor'];
        $extract['cuttrent_domainname'] = $domainname;
        $extract['pagesize'] = $this->pagesize;
        /* ------end--右边公共栏加载--------------- */
        $extract['navConfig'] = 'album';
        $extract['logintool'] = $blocks['logintool'];
        $extract['user'] = $this->user;

        $extract['isonline'] = $this->isOnLine($extract['bloginfo']['UserID']); //判断是否在线

        $this->load->view('album/devalbum_list.shtml', $extract);
    }

}

//end class
?>