<?php

/* * **********************
 * 功能：   博客友情链接管理
 * author： leicc
 * add：    2010-11-16
 * modify   2010-11-16
 * *********************** */

class Flink extends MY_Controller {

    function Flink() {
        parent::MY_Controller();
    }

    /**
     * @添加页面
     * @添加友情链接
     * */
    function manage($domainname) {
        $this->_checkLogin();

        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //权限认证
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'AddLink');

        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $extract['user'] = $this->user;
        $extract['userid'] = $this->user['userid'];
        $extract['isowner'] = $this->_checkOwnUser($extract['bloginfo']['UserID']); //判断是否自己的
        //获取友情链接分类
        $this->load->model('bloglink_socket');
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['Status'] = 0;
        $data['StartNo'] = 0;
        $data['QryCount'] = linksortcntlimit;
        $extract['sortlist'] = $this->bloglink_socket->getLinkSortList($data);




        //获取友情链接分类

        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $data['IsPublic'] = -1;

        $tempInfo = $this->bloglink_socket->getLinkList($data, false);
        $extract['tmpCnt'] = $tempInfo['TtlRecords'];
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        if ($page > ceil($extract['tmpCnt'] / articlesortpagesize)) {
            $page = 1;
        }

        $data['StartNo'] = ($page - 1) * articlesortpagesize;
        $data['QryCount'] = articlesortpagesize;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $extract['linklist'] = $this->bloglink_socket->getLinkList($data, false);
        if (false == $extract['linklist']) {
            $extract['linklist']['RetRecords'] == 0;
        }

        //翻页函数
        $this->load->library('pagebarsnew');
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/link/List';


        $this->pagebarsnew->Page($extract['tmpCnt'], $page, $data['QryCount'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();







        $blocks = &config_item('block');
        $extract['block'] = $blocks['flinkmanage'];
        //	echo $extract['block'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['flinkaddtitle'];


        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];

        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * @编辑友情链接
     * */
    function Edit($domainname) {
        $this->_checkLogin();

        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        //权限认证
        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'EditLink');

        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取消息
        $extract['user'] = $this->user;

        //获取传递过来的信息
        $extract['link']['LinkID'] = $this->input->get('linkid');
        $extract['link']['Name'] = $this->input->get('name');
        $extract['link']['Path'] = $this->input->get('path');
        $extract['link']['Rank'] = $this->input->get('rank');
        $extract['link']['SortID'] = $this->input->get('sortid');
        $extract['link']['IsPublic'] = $this->input->get('ispublic');

        //获取友情链接分类
        $this->load->model('bloglink_socket');
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['Status'] = 0;
        $data['StartNo'] = 0;
        $data['QryCount'] = linksortcntlimit;
        $extract['sortlist'] = $this->bloglink_socket->getLinkSortList($data);

        $blocks = &config_item('block');
        $extract['block'] = $blocks['flinkadd'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-编辑友情链接';
//		$extract['peronalhead']	= $blocks['personalhead'];
//		$extract['peronalfoot']	= $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $html = $this->load->view('manage/link/manage_flinkedit.shtml', $extract, true);
        echo json_encode(array('errno' => 'success', 'error' => $html));
    }

    /**
     * @友情链接列表
     * */
    function Llist($domainname) {
        $this->_checkLogin();

        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取友情链接分类
        $this->load->model('bloglink_socket');
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['StartNo'] = -1;
        $data['IsPublic'] = -1;

        $tempInfo = $this->bloglink_socket->getLinkList($data, false);

        $extract['tmpCnt'] = $tempInfo['TtlRecords'];
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        if ($page > ceil($extract['tmpCnt'] / articlesortpagesize)) {
            $page = 1;
        }

        $data['StartNo'] = ($page - 1) * articlesortpagesize;
        $data['QryCount'] = articlesortpagesize;
        $data['FlagCode'] = $tempInfo['FlagCode'];
        $extract['linklist'] = $this->bloglink_socket->getLinkList($data, false);

        if (false == $extract['linklist']) {
            $extract['linklist']['RetRecords'] == 0;
        }

        //翻页函数
        $this->load->library('pagebarsnew');
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/link/List';


        $this->pagebarsnew->Page($extract['tmpCnt'], $page, $data['QryCount'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        //用户信息
        $extract['user'] = $this->user;

        $blocks = &config_item('block');
        $extract['block'] = $blocks['flinklist'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['flinklisttitle'];
        $extract['peronalhead'] = $blocks['personalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/manage_index.shtml', $extract);
    }

    /**
     * @添加友情链接分类
     * */
    function AddSort($domainname) {
        $this->_checkLogin();

        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'AddLinkSort');

        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        $extract['user'] = $this->user;

        $blocks = &config_item('block');
        $extract['block'] = $blocks['addlinksort'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['addlinksorttitle'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * @友情链接分类列表
     * */
    function ListSort($domainname) {
        $this->_checkLogin();

        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);
        $this->_checkUser($extract['bloginfo']['UserID']);
        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取友情链接分类
        $this->load->model('bloglink_socket');
        unset($data);
        $data['MemberID'] = $extract['bloginfo']['MemberID'];
        $data['Status'] = 0;
        $data['StartNo'] = -1;
        $tmpInfo = $this->bloglink_socket->getLinkSortList($data);
        $extract['tmpCnt'] = $tmpInfo['TtlRecords'];
        //var_dump($extract['tmpCnt']);
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;

        if ($page > ceil($extract['tmpCnt'] / articlesortpagesize)) {
            $page = 1;
        }
        $data['StartNo'] = ($page - 1) * articlesortpagesize;
        $data['QryCount'] = articlesortpagesize;
        $data['FlagCode'] = $tmpInfo['FlagCode'];
        $extract['lsortlist'] = $this->bloglink_socket->getLinkSortList($data);
        if ($extract['lsortlist'] == false) {
            $extract['lsortlist']['RetRecords'] = 0;
        }

        //翻页函数
        $this->load->library('pagebarsnew');
        $baseLink = config_item('base_url') . '/' . $extract['bloginfo']['DomainName'] . '/link/ListSort';

        $this->pagebarsnew->Page($extract['tmpCnt'], $page, $data['QryCount'], $baseLink, '/');
        $extract['pagebar'] = $this->pagebarsnew->upDownList();

        //用户消息
        $extract['user'] = $this->user;

        $blocks = &config_item('block');
        $extract['block'] = $blocks['flinklistsort'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-' . $blocks['flinklistsorttitle'];
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * @编辑友情链接分类
     * */
    function EditSort($domainname) {
        $this->_checkLogin();

        //通过博客名获取博客信息	
        $extract['bloginfo'] = $this->_getBlogInfoByDomain($domainname);

        $this->_checkUser($extract['bloginfo']['UserID']);
        $param['GroupIDs'] = trim($extract['bloginfo']['GroupID'], ',');
        $blogaccess = $this->memberblog_socket->getAccessList($param);
        $this->_checkAccess($blogaccess, 'EditLinkSort');

        $extract['viewurl'] = $this->_getviewURL($extract['bloginfo'], true);
        //获取个人博客列表
        $extract['bloglist'] = $this->_getBlogListByUid();
        //获取博客配置信息
        $extract['blogconfig'] = $this->_getBlogConfig($extract['bloginfo']['MemberID']);

        //获取友情链接分类信息
        $extract['sort']['SortID'] = $this->input->post('sortid');
        $extract['sort']['Name'] = $this->input->post('name');
        $extract['sort']['Rank'] = $this->input->post('rank');

        //获取消息
        $extract['user'] = $this->user;

        $blocks = &config_item('block');
        $extract['block'] = $blocks['addlinksort'];
        $extract['title'] = $extract['bloginfo']['BlogName'] . '-编辑友情链接分类';
        $extract['peronalhead'] = $blocks['devpersonalhead'];
        $extract['peronalfoot'] = $blocks['personalfoot'];
        $extract['baseurl'] = &config_item('base_url');
        $extract['isconfig'] = 1;

        $this->load->view('manage/devmanage_index.shtml', $extract);
    }

    /**
     * @增修删除的动作
     * */
    function Action($domainname) {
        $this->_checkLogin();
        $data = array();
        $act = $this->input->post('act');
        switch ($act) {
            case 'addSort':
                $MemberID = $this->input->post('MemberID');
                $flashCode = $this->input->post('flashCode');
                if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
                    $data['errno'] = 'verify';
                    $data['error'] = '本次请求验证信息不合法';
                    break;
                }
                $this->load->model('bloglink_socket');
                unset($data);
                $data['MemberID'] = $MemberID;
                $data['Status'] = 0;
                $TolCnt = $this->bloglink_socket->getLinkSortList($data);
                if ($TolCnt['TtlRecords'] > linksortcntlimit) {
                    $data['errno'] = 'linksortlimit';
                    $data['error'] = '您友情链接的数目已经达到上限' . linksortcntlimit . '个';
                    break;
                }
                unset($data);

                $Name = htmlspecialchars(trim($this->input->post('name')));
                $Rank = $this->input->post('rank');
                $param['LinkSortID'] = 0;
                $param['MemberID'] = $MemberID;
                $param['Name'] = $Name;
                $param['Rank'] = $Rank;
                $param['Status'] = 0;

                if ($this->bloglink_socket->aupdLinkSort($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '标签分类添加成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '标签分类添加失败';
                }
                break;
            case 'updSort':
                $SortID = intval($this->input->post('SortID'));
                $MemberID = $this->input->post('MemberID');
                $flashCode = $this->input->post('flashCode');
                if ($flashCode != getVerifyStr($MemberID . $this->user['userid']) || $SortID < 1) {
                    $data['errno'] = 'verify';
                    $data['error'] = '本次请求验证信息不合法';
                    break;
                }
                $Name = htmlspecialchars(trim($this->input->post('name')));
                $Rank = $this->input->post('rank');
                $param['LinkSortID'] = $SortID;
                $param['MemberID'] = $MemberID;
                $param['Name'] = $Name;
                $param['Rank'] = $Rank;
                $param['Status'] = 0;
                $this->load->model('bloglink_socket');
                if ($this->bloglink_socket->aupdLinkSort($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '标签分类更新成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '标签分类更新失败';
                }
                break;
            case 'delSort':
                $SortID = intval($this->input->post('LinkSortID'));
                $MemberID = $this->input->post('MemberID');
                $flashCode = $this->input->post('flashCode');
                if ($flashCode != getVerifyStr($MemberID . $this->user['userid']) || $SortID < 1) {
                    $data['errno'] = 'verify';
                    $data['error'] = '本次请求验证信息不合法';
                    break;
                }
                $param['MemberID'] = $MemberID;
                $param['LinkSortIDs'] = $SortID;
                $this->load->model('bloglink_socket');
                if ($this->bloglink_socket->delLinkSort($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '标签分类删除成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '标签分类删除失败';
                }
                break;
            case 'addLink':
                $MemberID = $this->input->post('MemberID');
                $flashCode = $this->input->post('flashCode');
                if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
                    $data['errno'] = 'verify';
                    $data['error'] = '本次请求验证信息不合法';
                    break;
                }
                $this->load->model('bloglink_socket');
                unset($data);
                $data['MemberID'] = $MemberID;
                $TolCnt = $this->bloglink_socket->getLinkList($data, false);
                if ($TolCnt['TtlRecords'] > linkcntlimit) {
                    $data['errno'] = 'linklimit';
                    $data['error'] = '您友情链接的数目已经达到上限' . linkcntlimit . '个';
                    break;
                }
                unset($data);

                $IsPublic = ($this->input->post('isPublic') > 0) ? 1 : 0;
                $Name = htmlspecialchars(trim($this->input->post('name')));
                $LinkPath = $this->input->post('path');
                $Rank = intval($this->input->post('rank'));
                $LinkSortID = intval($this->input->post('sortId'));
                if (!preg_match('/^https?:\/\/[\d\-\_a-zA-Z]+((\.[\d\-\_a-zA-Z]+)*\/?)+[\.\/\d\-\_a-zA-Z]*$/i', $LinkPath) || strlen($LinkPath) > 250) {
                    $data['errno'] = 'linkPath';
                    $data['error'] = '链接地址信息不符合要求';
                    break;
                }

                if (strlen($Name) > 250 || strlen($Name) < 3) {
                    $data['errno'] = 'linkName';
                    $data['error'] = '链接名称信息不符合要求';
                    break;
                }
                $param['LinkID'] = 0;
                $param['MemberID'] = $MemberID;
                $param['Name'] = $Name;
                $param['Path'] = htmlspecialchars($LinkPath);
                $param['LinkSortID'] = $LinkSortID;
                $param['Rank'] = $Rank;
                $param['IsPublic'] = $IsPublic;
                $param['Status '] = 0;

                if ($this->bloglink_socket->aupdLink($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '友情链接添加保存成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '友情链接添加保存失败';
                }
                break;
            case 'updLink':

                $MemberID = $this->input->post('MemberID');
                $flashCode = $this->input->post('flashCode');
                $LinkID = intval($this->input->post('LinkID'));
                if (($flashCode != getVerifyStr($MemberID . $this->user['userid'])) || $LinkID < 1) {
                    $data['errno'] = 'verify';
                    $data['error'] = '本次请求验证信息不合法';
                    break;
                }
                $IsPublic = ($this->input->post('isPublic') > 0) ? 1 : 0;
                $Name = htmlspecialchars(trim($this->input->post('name')));
                $LinkPath = $this->input->post('path');
                $Rank = intval($this->input->post('rank'));
                $LinkSortID = intval($this->input->post('sortId'));
                $LinkID = intval($this->input->post('LinkID'));
                if (!preg_match('/^https?:\/\/[\d\-\_a-zA-Z]+((\.[\d\-\_a-zA-Z]+)*\/?)+[\.\/\d\-\_a-zA-Z]*$/i', $LinkPath) || strlen($LinkPath) > 250) {
                    $data['errno'] = 'linkPath';
                    $data['error'] = '链接地址信息不符合要求';
                    break;
                }
                if (strlen($Name) > 250 || strlen($Name) < 3) {
                    $data['errno'] = 'linkName';
                    $data['error'] = '链接名称信息不符合要求';
                    break;
                }

                $param['LinkID'] = $LinkID;
                $param['MemberID'] = $MemberID;
                $param['Name'] = $Name;
                $param['Path'] = $LinkPath;
                $param['LinkSortID'] = $LinkSortID;
                $param['Rank'] = $Rank;
                $param['IsPublic'] = $IsPublic;
                $param['Status '] = 0;

                $this->load->model('bloglink_socket');
                if ($this->bloglink_socket->aupdLink($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '友情链接编辑保存成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '友情链接编辑保存失败';
                }
                break;
            case 'delLink':
                $MemberID = $this->input->post('MemberID');
                $flashCode = $this->input->post('flashCode');

                if ($flashCode != getVerifyStr($MemberID . $this->user['userid'])) {
                    $data['errno'] = 'verify';
                    $data['error'] = '本次请求验证信息不合法';
                    break;
                }

                $LinkID = $this->input->post('linkID');

                $param['LinkIDs'] = $LinkID;
                $param['MemberID'] = $MemberID;

                $this->load->model('bloglink_socket');
                if ($this->bloglink_socket->delLink($param)) {
                    $data['errno'] = 'success';
                    $data['error'] = '友情链接删除成功';
                } else {
                    $data['errno'] = 'failed';
                    $data['error'] = '友情链接删除失败';
                }
                break;
            default:
                break;
        }
        echo json_encode($data);
        exit;
    }

}

//end class
?>