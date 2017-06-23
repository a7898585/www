<?php

/**
 * MY_Controller
 *
 * @uses Controller
 * @package CNFOL.com
 * @version $Id$
 * @copyright Copyright (C) 2008 Cnfol.com. All rights reserved.
 * @author Avenger
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class MY_Controller extends Controller {

    var $user = FALSE;
    var $domain;
    var $isCNFOL = false;

    function MY_Controller() {
        parent::Controller();
        header('Content-Type: text/html; charset=utf-8');

        //判断是否是公司IP
        $accessip = &config_item('accessip');
        $curIP = $this->input->ip_address();
        foreach ($accessip as $ip) {
            if (preg_match($ip, $curIP)) {
                //$this->isCNFOL	= true;
            }
        }
        $this->load->model('user_socket');
        $this->domain = '.' . DEFAULT_WEB;
    }

    //检查IP是否合法.
    function _checkIP($type = '') {
        if ($this->isCNFOL === true)
            return;

        $mcache = &load_class('Memcache');
        $mcache->addServer();
        $UserIpInfo = $mcache->get('Article_Add_User_' . $this->_getUserID());

        $admmsg = '您的IP已被管理员限制操作，请联系管理员。';
        $sysmsg = '系统发现您的博客异常，您的IP已被限制操作。(' . $this->_getUserID() . ')';

        if (filter_ip()) {
            if ($type == 'comment') {
                $data['errno'] = 'ipfilter';
                $data['error'] = $admmsg;
                echo json_encode($data);
                exit;
            } else {
                cnfolAlert($admmsg);
                cnfolLocation();
            }
        }

        if ($UserIpInfo['IsClose'] == 1) {
            if ($type == 'comment') {
                $data['errno'] = 'ipfilter';
                $data['error'] = $sysmsg;
                echo json_encode($data);
                exit;
            } else {
                cnfolAlert($sysmsg);
                cnfolLocation();
            }
        }
    }

    /*
      | 判定用户登录状态
      +-------------------------------
     */

    function _checkUserlogin($point = '') {
        if (!$this->_getUserID()) {
            return false;
        }

        $uid = $this->_getUserID();

        $mkKeyStr = $this->_mkMd5str($uid);
        if ($this->_getMd5str() !== $mkKeyStr) {
            return false;
        }

        if (config_item('logintry')) { //是否开启后踢前功能
            $this->load->model('user_socket');
            $sUser = $this->user_socket->getUserKeyStr(array('UserID' => $uid, 'Operator' => '0'));

            $param['UserID'] = $uid;
            $res = $this->user_socket->haveUserBaseInfo($param);

            if ($res['Status'] == 1) {

                setcookie("cookie[passport][userId]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][username]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][nickname]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][money]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][keys]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][logtime]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][keystr]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][cache]", "", time() - 3600, "/", ".cnfol.com");
                setcookie("cookie[passport][auto]", "", time() - 3600, "/", ".cnfol.com");

                cnfolAlert("您的帐号不存在");
                echo '<script>window.close();</script>';
            }

            if (empty($sUser['KeyStr'])) {
                error_log(date('YmdHis') . ' || ' . $uid . ' || empty || ' . $this->_getKeyStr() . PHP_EOL, 3, '/home/www/html/logs/loginkey' . date('Ymd') . '.log');
            } else {

                if ($sUser['KeyStr'] != $this->_getKeyStr()) {

                    error_log(date('YmdHis') . ' || ' . $uid . ' || ' . $sUser['KeyStr'] . ' || ' . $this->_getKeyStr() . PHP_EOL, 3, '/home/www/html/logs/loginkey' . date('Ymd') . '.log');
                    $localurl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    $urls = explode('/', $localurl);

                    if ($urls[1] == 'register') {
                        $returnurl = $urls[0];
                    } elseif ($urls[2] == 'article' && $urls[3] == 'Add') {
                        $returnurl = $urls[0] . '/' . $urls[1];
                    } elseif ($urls[2] == 'config') {
                        $returnurl = $urls[0] . '/' . $urls[1];
                    } else {
                        $returnurl = $localurl;
                    }


                    $mcache = &load_class('Memcache');
                    $mcache->addServer();

                    $ckey = config_item('K2036');
                    $ckey = str_replace('{UserID}', $uid, $ckey);
                    $mcache->delete($ckey);


                    cnfolAlert("您的帐号(" . $this->_getNickName() . ")在别处登录，您已被迫退出。如果这不是您本人的操作，建议您修改密码");
                    cnfolLocation(config_item('passporturl') . 'login/loginout?return=http://' . $returnurl);
                    exit(-1);
                }
            }
        }

        $this->user['userid'] = $this->_getUserID();
        $this->user['nickname'] = $this->_getNickName();
        $this->user['username'] = $this->_getUserName();

        $this->onLineRefresh($this->user['userid']); //在线刷新

        /*
          $data = $this->_getMsgCount($this->user['userid']);

          $this->user['msgtotal'] = $data['TtlRecords'];
          $this->user['msgunread'] = $data['UnReads'];
          if ($this->user['userid'] > 0) {
          //收到邮件数
          $json_info = @file_get_contents(config_item('passporturl') . '/api/msginfo/getusermsgcount?uid=' . $this->user['userid'] . '&key=' . md5($this->user['userid'] . 'hello_cnfol'));
          $json_arr = @json_decode($json_info, true);
          $userInfo['msg'] = $json_arr['unread'];
          $this->user['msg'] = $userInfo['msg'] ? $userInfo['msg'] : 0;
          }
          unset($data);echo $res['Point'];exit;
         */

        if ($point == 'getPoint') {
            if ($res['Point'] == 0) {
                return true;
            }
            return $res['Point'];
        }

        return $this->user['userid'];
    }

    //在线刷新
    function onLineRefresh($uid = 0) {
        $info = $this->_getUserInfoByUid($uid);

        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $ckey = config_item('K2033');
        $ckey = str_replace('{uid}', $uid, $ckey);
        $return = $mcache->set($ckey, $info['user_gender'], EXPIRETIME_1);
    }

    //判断是否在线
    function isOnLine($uid = 0) {

        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $ckey = config_item('K2033');
        $ckey = str_replace('{uid}', $uid, $ckey);
        return $mcache->get($ckey);
    }

    //对于未登入的用户做跳转
    function _checkLogin($param = '') {
        $result = $this->_checkUserlogin($param);

        if (false == $result) {
            cnfolAlert("您没有登录，请先登入再做后续操作");
            cnfolLocation();
            exit(-1);
        }

        if ($param == 'getPoint' && $result != false) {
            return $result;
        }
    }

    //验证博客是否属于某个人的博客
    function _checkOwnBlog($ownermemberid, $memberid) {
        if ($ownermemberid != $memberid) {
            //没权限跳转
            cnfolAlert("您没有操作他人博客的权限");
            cnfolLocation();
            exit(-1);
        }
    }

    //获取统计的URL
    function _getviewURL($bloginfo, $isowner = false, $articleid = '0') {
        $duid = $bloginfo['UserID'];
        $mid = $bloginfo['MemberID'];
        $dom = $bloginfo['DomainName'];
        $bn = $bloginfo['NickName']; //博客名指代用户昵称
        $mid = $bloginfo['MemberID'];
        $aid = $articleid;
        $createtime = $bloginfo['articletime'];
        $title = $bloginfo['title'];
        if ($isowner == false) {
            $vuid = $this->_getUserID();
            $un = $this->_getUserName();
            $nn = $this->_getNickName();
        } else {
            $vuid = '0';
            $un = '0';
            $nn = '0';
        }
        $viewurl = createviewurl($duid, $mid, $dom, $bn, $aid, $vuid, $un, $nn, $createtime, $title);
        return $viewurl;
    }

    //获取个人博客列表
    function _getBlogListByUid($uid = 0) {
        //获取个人博客列表	
        $data['QryData'] = ($uid == 0) ? $this->_getUserID() : $uid;
        $data['StartNo'] = 0;
        $data['QryCount'] = 10;
        $this->load->model('memberblog_socket');
        $bloglist = $this->memberblog_socket->getMemberBlogListByUserID($data);

        return $bloglist;
    }

    //获取个人博客列表中第一个正常博客
    function _getBlogListByUidFirst($uid = 0) {
        //获取个人博客列表	
        $data['QryData'] = ($uid == 0) ? $this->_getUserID() : $uid;
        $data['StartNo'] = 0;
        $data['QryCount'] = 10;
        $this->load->model('memberblog_socket');
        $bloglist = $this->memberblog_socket->getMemberBlogListByUserID($data);

        if (!$bloglist) {
            return false;
        }

        if ($bloglist['RetRecords'] == 1) {
            $bloglist['Record'] = array($bloglist['Record']);
        }

        if (!empty($bloglist) && $bloglist['RetRecords'] > 0) {
            foreach ($bloglist['Record'] as $value) {
                if ($value['Status'] == 0) {
                    return $value;
                }
            }
        }

        return false;
    }

    //从个人博客列表中过滤掉被关闭的博客
    function _getBlogListFilter($bloglist) {
        if ($bloglist['RetRecords'] == 1) {
            $bloglist['Record'] = array($bloglist['Record']);
            return $bloglist['Record'];
        }

        foreach ($bloglist['Record'] as $key => $value) {
            if ($value['Status'] == 1) {
                unset($bloglist['Record'][$key]);
            }
        }
        return $bloglist['Record'];
    }

    //根据域名获取博客信息
    function _getBlogInfoByDomain($domain, $force = 1) {
        $data['QryData'] = $domain;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByDomainName($data);

        //判断是否是私有博客
        if ($this->_getUserID() != $bloginfo['UserID'] && $bloginfo['Status'] == 3) {
            cnfolAlert("您没有访问该博客的权限!");
            cnfolLocation();
            exit(-1);
        }


        if (!$bloginfo || $bloginfo['Status'] == 1 || $bloginfo['Status'] == 2) {
//            error_log(PHP_EOL . '/--new-con1-/' . print_r($data, true) . '----/'. print_r($bloginfo, true)  .  '----/' . date('H:i'), 3, '/home/html/logs/con.log');
            cnfolAlert("您访问的博客不存在或被管理员关闭!");
            cnfolLocation();
            exit(-1);
        }
        return $bloginfo;
    }

    function _getBlogInfoByMemberID($mid) {
        $data['QryData'] = $mid;
        $this->load->model('memberblog_socket');
        $bloginfo = $this->memberblog_socket->getMemberBlogByMemberID($data);

        if (!$bloginfo) {
            cnfolAlert("您访问的博客不存在或被管理员关闭!");
            cnfolLocation();
            exit(-1);
        }
        return $bloginfo;
    }

    //获取博客配置信息
    function _getBlogConfig($memberid, $force = 1) {
        $this->load->model('memberblog_socket');
        $data['MemberID'] = $memberid;
        $blogconfig = $this->memberblog_socket->getBlogConfig($data);
        if ($force == 1 && (!$blogconfig)) {
            cnfolAlert("博客配置信息获取失败");
            cnfolLocation();
            exit(-1);
        }
        return $blogconfig;
    }

    //验证用户权限
    function _checkUser($userid) {
        if ($this->_getUserID() != $userid) {
            cnfolAlert("您还没有管理他人博客的权限");
            cnfolLocation();
            exit(-1);
        }
    }

    //直接跳转到博客首页
    function _jumpIndex($userid) {

        if (!$this->_getUserID()) {
            cnfolAlert("您还未登录");
            cnfolLocation();
            exit(-1);
        } else if ($this->_getUserID() != $userid) {
            cnfolAlert("您还没有管理他人博客的权限");
            cnfolLocation();
            exit(-1);
        }
    }

    //验证博客权限
    function _checkAccess($blogaccess, $accesskeys) {
        if (is_array($blogaccess)) {
            $accesslist = &config_item('accesskey');
            if (isset($accesslist[$accesskeys]) && in_array($accesslist[$accesskeys], $blogaccess)) {
                return true;
            }
        }
        cnfolAlert("您还没有操作该模块的权限");
        rollbackto();
        exit(-1);
    }

    //验证当前博客与登入用户是否一致
    function _checkOwnUser($userid) {
        if (!$this->_getUserID())
            return false;
        if ($this->_getUserID() != $userid)
            return false;

        $mkKeyStr = $this->_mkMd5str($this->_getUserID());
        if ($this->_getMd5str() !== $mkKeyStr) {
            return false;
        }

        return true;
    }

    /*
      | 生成key信息
      +-------------------------------
     */

    function _mkMd5str($userid) {
        $md5str = strtoupper(md5($this->domain . $userid . LOGIN_LIMIT_KEY));
        return $md5str;
    }

    /*
      | 获取用户Key信息
      +-------------------------------
     */

    function _getMd5str() {
        $cookie = $this->input->cookie('cookie');
        if (isset($cookie['passport']['cache']))
            return $cookie['passport']['cache'];

        return false;
    }

    /*
      | 获取用户ID
      +-------------------------------
     */

    function _getUserID() {
        $cookie = $this->input->cookie('cookie');
        if ($cookie && isset($cookie['passport']['userId'])) {
            return $cookie['passport']['userId'];
        } else {
            return 0;
        }
    }

    /*
      | 获取登录验证KEY
      +-------------------------------
     */

    function _getKeyStr() {
        $cookie = $this->input->cookie('cookie');
        if ($cookie && isset($cookie['passport']['keystr'])) {
            return $cookie['passport']['keystr'];
        } else {
            return 0;
        }
    }

    /*
      | 获取用户名
      +-------------------------------
     */

    function _getUserName() {
        $cookie = $this->input->cookie('cookie');
        if (isset($cookie['passport']['username'])) {
            return $cookie['passport']['username'];
        } else {
            return false;
        }
    }

    /*
      | 获取昵称
      +-------------------------------
     */

    function _getNickName() {
        $cookie = $this->input->cookie('cookie');
        if (isset($cookie['passport']['nickname'])) {
            return $cookie['passport']['nickname'];
        } else {
            return false;
        }
    }

    /*
      | 获取用户的基本信息
      +-----------------------------------
      | @param uid 用户ID
      |
      | @return 略
      | @access protected
     */

    function _getUserInfoByUid($uid) {
        $param['UserID'] = $uid;

        $res = $this->user_socket->getUserBaseInfo($param);

        $user['username'] = $res['UserName'];
        $user['nickname'] = $res['NickName'];
        $user['money'] = $res['Golden'];
        $user['point'] = $res['Point'];

        $param['UserID'] = $res['UserID'];
        $tmp = $this->user_socket->getUserInfo($param);

        if ($tmp) {
            $user['userId'] = $tmp['UserID'];
            $user['user_gender'] = $tmp['Sex'];
            $user['user_signature'] = $tmp['Signature'];
        }

        return $user;
    }

    /*
      | 获取用户的站内消息数目
      +-----------------------------------
      | @param uid 用户ID
      |
      | @return 略
      | @access protected
     */

    function _getMsgCount($uid) {
        $MsgCount = $this->user_socket->getMsgCount($uid);
        return $MsgCount;
    }

    /*
      | 获取用户详细信息
      +-----------------------------------
      | @param uid 用户ID
      |
      | @return 略
      | @access protected
     */

    function _getUserInfo($uid, $segment = '') {
        $param['UserID'] = $uid;
        $user = $this->user_socket->getUserInfo($param);

        if ($user) {
            if (isset($user[$segment]))
                return $user[$segment];
            else
                return false;
        }
        return false;
    }

    /*
      | 获取用户等级信息
      +-----------------------------------
      | @param uid 用户ID
      |
      | @return 略
      | @access protected
     */

    function _getGradeImgByUid($uid) {
        $param['UserID'] = $uid;
        $grade = $this->user_socket->getUserGrade($param);

        $string = "<a href='javascript:void(0)' title='等级：" . $grade['GradeName'] . "'>";
        $taiY = floor($grade['GradeID'] / 16);
        $yueL = floor(($grade['GradeID'] - $taiY * 16) / 4);
        $xingX = floor($grade['GradeID'] - $taiY * 16 - $yueL * 4);
        if ($taiY > 0) {
            for ($i = 0; $i < $taiY; $i++) {
                $string .="<img src='http://i0.cnfolimg.com/uploads/v5.0/blog/sun.png' border='0'/>";
            }
        }
        if ($yueL > 0) {
            for ($i = 0; $i < $yueL; $i++) {
                $string .="<img src='http://i0.cnfolimg.com/uploads/v5.0/blog/moon.png' border='0'/>";
            }
        }
        if ($xingX > 0) {
            for ($i = 0; $i < $xingX; $i++) {
                $string .="<img src='http://i0.cnfolimg.com/uploads/v5.0/blog/star.png' border='0'/>";
            }
        }
        if ($grade['GradeID'] == 0) {
            $string .="<img src='http://i0.cnfolimg.com/uploads/v5.0/blog/star.png' border='0' title='就快一级了，加油哦！'/>";
        }
        $string .= "</a>";
        return $string;
    }

    //粉丝，我关注的，相互关注数
    function _getFriend($uid = 0) {
        $data['UserID'] = ($uid == 0) ? $this->_getUserID() : $uid;

        $tmpRs = $this->user_socket->getFriendContact($data);
        //$this->friendsnumber= array_merge($this->user, $tmpRs);	
        return $tmpRs;
    }

    //查看是否已加为好友
    function _isFriend($userid, $fuserid) {
        if ($userid != $fuserid && $userid != 0) {
            $TransmitFlag = $this->user_socket->getFriendsStatus(array('UserID' => $userid, 'FUserIDs' => $fuserid));
            return $TransmitFlag['FriendStatus'];
        }
        return false;
    }

    //热门博客，热门文章统计
    function _hotBlogArticle($data, $userid) {
        $hotBlogArticle = '';

        if ($this->_getUserID() == $userid) {
            return;
        }

        if (is_numeric($data['guestType'])) {
            $data['guestType'] = '1'; //表示注册用户
        } else {
            $data['guestType'] = '0'; //表示游客
        }

        foreach ($data as $value) {
            $hotBlogArticle.=$value . ',';
        }

        $hotBlogArticle = trim($hotBlogArticle, ',');

        error_log($hotBlogArticle . "$$--**>>", 3, '/home/www/html/logs/hotBlogArticle_' . date("Y-m-d H") . '.log');
    }

    //博客标签统计
    function _hotBlogTag($data, $userid) {
        $hotBlogArticle = '';

        if ($this->_getUserID() == $userid) {
            return;
        }


        if (empty($data['0']) || empty($data)) {
            return;
        }

        foreach ($data as $value) {
            if (!empty($value)) {
                error_log($value . "$$--**>>", 3, '/home/www/html/logs/hotBlogTag_' . date("Y-m-d H") . '.log');
            }
        }
    }

    //关键字统计
    function _hotBlogKeyWord($data, $articleuserid) {
        $hotBlogArticle = '';

        if ($this->_getUserID() == $articleuserid) {
            return;
        }

        foreach ($data as $key => $value) {
            $hotBlogArticle.=$value . ',';
        }


        $hotBlogArticle = trim($hotBlogArticle, ',');

        error_log($hotBlogArticle . "$$--**>>", 3, '/home/www/html/logs/hotBlogUser_' . date("Y-m-d H") . '.log');
    }

    //获取文章转载次数
    function _getTransshipmentNum($data) {

        $this->load->model('blogarticle_socket');

        $bloginfo = $this->blogarticle_socket->getTransshipmentNum($data);

        if (!$bloginfo) {
            return 0;
        }
        return $bloginfo;
    }

    //获取文章收藏次数
    function _getArticleCollectNum($data) {

        $this->load->model('blogarticle_socket');
        $bloginfo = $this->blogarticle_socket->getArticleCollectNum($data);

        if (!$bloginfo) {
            return 0;
        }
        return $bloginfo;
    }

    //用户所在组是否需要验证验证码
    function _checkValidate($uid, $GroupID = '0') {
        //$this->load->model('memberblog_socket');
        if ($this->isCNFOL === true) {
            return true;
        }

        if (empty($uid)) {
            return false;
        }

        if ($GroupID == '0') {
            $blogConfig = $this->_getBlogListByUidFirst($uid);
        } else {
            $blogConfig['GroupID'] = $GroupID;
        }

        //return $blogConfig;
        //用户组信息的处理
        $groups = trim($blogConfig['GroupID'], ',');

        $isVIP = false;
        if ($groups != "") {
            $recommend = config_item('recommendgroup');
            $isuse = config_item('isuse');
            $groups = explode(',', $groups);
            $groups = (is_string($groups)) ? array(0 => $groups) : $groups;

            foreach ($groups as $grp) {
                if (isset($recommend[$grp])) {
                    $isVIP = true;
                } else if ($grp == $isuse) {
                    $isVIP = true;
                } else if ($grp == config_item('autoaudit')) {
                    $isVIP = true;
                }
            }
        }

        if ($isVIP === false) {
            return false;
        }
        return true;
    }

    //浏览过该文章的人还浏览过用（验证是否是记录采用组、高手看盘、名家看市组的用户）
    function _checkGuestEverBrowse($uid) {

        if (empty($uid)) {
            return false;
        }

        $blogConfig = $this->_getBlogListByUidFirst($uid);
        //return $blogConfig;
        //用户组信息的处理
        $groups = trim($blogConfig['GroupID'], ',');

        $isVIP = false;
        if ($groups != "") {
            $recommend = config_item('recommendgroupshow');
            $groups = explode(',', $groups);
            $groups = (is_string($groups)) ? array(0 => $groups) : $groups;

            foreach ($groups as $grp) {
                if (isset($recommend[$grp])) {
                    $isVIP = true;
                }
            }
        }

        if ($isVIP === false) {
            return false;
        }
        return true;
    }

    //判断是否是博主自己的博客页面
    function ismy($ownUserid, $currentUserid = '') {

        if ($ownUserid == '' || $currentUserid == '') {
            return false;
        } else {
            return ($ownUserid == $currentUserid) ? true : false;
        }
    }

    //删除草稿箱memcache
    function _delDraftsCache($MemberID = '') {
        if (empty($MemberID)) {
            return;
        }

        $mcache = &load_class('Memcache');
        $mcache->addServer();

        $ckey = config_item('K2020');
        $ckey = str_replace('{MemberID}', $MemberID, $ckey);
        $mcache->delete($ckey);
    }

    //删除我的博客首页1-15条缓存
    function delMyIndex() {
        $mcache = &load_class('Memcache');
        $mcache->addServer();

        //$indexckeynum = config_item('K1076');
        //$indexckeynum = str_replace('{MemberID}', $this->user['userid'], $indexckeynum);
        //$mcache->delete($indexckeynum);

        $indexckey = config_item('K1077');
        $indexckey = str_replace('{MemberID}', $this->user['userid'], $indexckey);
        $mcache->delete($indexckey);
    }

    //保存文章seo关键字
    function saveSeo($articleid, $result) {
        if ($articleid == '' || $result == '') {
            return;
        }

        $this->load->model('blogarticle_socket');
        $bloginfo = $this->blogarticle_socket->saveArtSeo(array('ArticleID' => $articleid, 'keyNames' => $result));
        return $bloginfo;
    }

    //修改文章seo关键字
    function editSeo($articleid, $result) {
        if ($articleid == '' || $result == '') {
            return;
        }

        $this->load->model('blogarticle_socket');
        $bloginfo = $this->blogarticle_socket->editArtSeo(array('ArticleID' => $articleid, 'keyNames' => $result));
        return $bloginfo;
    }

    //获取最新5篇文章
    function getNewestArt($date, $MemberID = 0) {

        $this->load->model('blogarticle_socket');
        $bloginfo = $this->blogarticle_socket->getMemberArticleFirst($date, $MemberID);


        $return = array();
        $title = '';
        if (count($bloginfo['Record']) > 0) {
            foreach ($bloginfo['Record'] as $value) {
                $title.=$value['Title'] . '，';
            }

            $firstTitleId = $bloginfo['Record']['0']['ArticleID'];
        }
        $title = trim($title, '，');

        $return['title'] = $title;
        $return['firstTitleId'] = $firstTitleId;

        return $return;
    }

    //获取keywords
    function getArtKeyWords($UserID, $MemberID = 0) {
        $dataNew['UserID'] = $UserID;
        $dataNew['StartNo'] = 0;
        $dataNew['QryCount'] = 5;

        $str = $this->getNewestArt($dataNew, $MemberID);




        //获取标签
        $data['UserIDs'] = $UserID;
        $data['StartNo'] = 0;
        $data['QryCount'] = 9;
        $this->load->model('articletags_socket');
        $taglist = $this->articletags_socket->getArticleMyTagList($data);

        $tagStr = '';
        $i = 1;
        if ($taglist) {
            foreach ($taglist['Record'] as $value) {
                if ($i > 3) {
                    break;
                }
                $tagStr.=$value['Name'] . '，';
                $i++;
            }

            $tagStr = trim($tagStr, '，');
        }
        //获取标签
        //获取最新一篇文章关键词
        if ($tagStr == '') {
            $this->load->model('blogarticle_socket');
            $tagStr = $this->blogarticle_socket->getArtSeo(array('ArticleID' => $str['firstTitleId']));

            $tagStr = $tagStr['Record']['KeyName'];
        }
        //获取最新一篇文章关键词

        return array('tagStr' => $tagStr, 'title' => $str['title']);
    }

}

?>
