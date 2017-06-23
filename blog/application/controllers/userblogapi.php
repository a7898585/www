<?php

/* * **********************
 * 功能：   博客对用户中心接口
 * author： jianglw
 * *********************** */

class Userblogapi extends MY_Controller {

    public function __construct() {
        parent::MY_Controller();
    }

    /**
     * 获取个人博客信息列表
     * @param type $userid
     * @return type
     */
    function getBlogList($userid) {
        $return = $this->_getBlogListByUid($userid);
        if (is_array($return['Record']['0'])) {
            $bloglist = $return['Record'];
        } else {
            $bloglist = array($return['Record']);
        }
        return $bloglist[0];
    }

    /*
      | 获取博客meneberid
      +---------------------------------------
      | @param   null
     */

    function getMemberIdByUid($userid) {
        $blog = $this->getBlogList($userid);
        return $blog['MemberID'];
    }

    /**
     * 获取博客名称
     * @param type $userid
     */
    function getDomainNameByUid($userid) {
        $blog = $this->getBlogList($userid);
        return $blog['DomainName'];
    }

    /**
     * 获取博客名称接口
     * @param type $userid
     */
    function getDomainNameApi($userid) {
        $name = $this->getDomainNameByUid($userid);
        if (!empty($name)) {
            $return = array('flag' => 0, 'msg' => 'suc', 'DomainName' => $name);
        } else {
            $return = array('flag' => -1, 'msg' => 'empty');
        }
        echo json_encode($return);
    }

    /**
     *  用户中心他人信息 最近访客接口
     * @param type $userid
     */
    function blogvisitorApi($userid) {
        if ($userid) {
            $memberid = $this->getMemberIdByUid($userid);
            if ($memberid) {
                $data['MemberIDs'] = $memberid;
                $this->load->model('memberblog_socket');
                $stat1 = $this->memberblog_socket->getMemberBlogStat($data);
                $stat2 = $this->memberblog_socket->getMemberBlogStatByCache($data);

                $data = array();
                $data['visitor'] = (empty($stat2['TodayClick']) ? $stat1['TodayVisit'] : $stat2['TodayClick']);
                $data['visitor_total'] = (empty($stat2['TotalClick']) ? $stat1['Totalvisit'] : $stat2['TotalClick']);
                $data['totalArticle'] = $stat1['TotalArticle'];
                //最近访客
                $this->load->model('userblogapi_socket');
                $visivor = $this->userblogapi_socket->get_blogvisitor($memberid);

                $data['visitor_list'] = isset($visivor['VUsers']) ? unserialize($visivor['VUsers']) : array();
                foreach ($data['visitor_list'] as $k => $user) {
                    $data['visitor_list'][$k]['url'] = config_item('base_url') . '/returnbolg/' . $user['userid'] . '.html';
                    $data['visitor_list'][$k]['picurl'] = getuserhead($user['userid']);
                }
                if (!is_array($visivor) || count($visivor) < 1) {
                    $data['visitor_list'] = array();
                }
                $return = array('flag' => 0, 'msg' => 'suc', 'data' => $data);
            } else {
                $return = array('flag' => -1, 'msg' => '该用户未开通博客！');
            }
            echo json_encode($return);
        } else {
            $return = array('flag' => -1, 'msg' => '用户id为空！');
            echo json_encode($return);
        }
    }

    /**
     * 添加文章动态接口
     */
    function addArticleApi() {
        $IN = parse_incoming();
        $this->load->model('userblogapi_socket');
        $param = array(
            'UserID' => $IN['userid'],
            'DynamicTitle' => $IN['title'],
            'DynamicComment' => $IN['content'],
            'DynamicTime' => date('Y-m-d H:i:s', $IN['time']),
            'DynamicUrl' => $IN['arturl'],
            'AppID' => 1,
            'ParentPostID' => $IN['articleid']
        );
//        print_r($param);
        $Status = $this->userblogapi_socket->addBlogArticleApi($param);

        if (empty($Status)) {
            $data['errno'] = 'empty';
            $data['error'] = '添加失败.';
            echo json_encode($data);
            exit;
        }
        if ($Status['Code'] == '00') {
            $data['error'] = '添加成功';
            $data['errno'] = 'success';
        } else {
            $data['errno'] = 'error';
            $data['error'] = '添加失败.';
        }

        echo json_encode($data);
        exit;
    }

    /**
     * 黄金文章列表(博客被采用的文章)（高手点金） 
     */
    function goldlistGsdjApi() {
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $total = intval($this->input->get_post('total'));

        $data['QryCount'] = (is_int($total) && $total > 0) ? $total : MYBLOG_MORE_PAGESIZE;
        $data['StartNo'] = ($page - 1) * $data['QryCount'];

        $this->load->model('blogarticle_socket');
        $artlist = $this->blogarticle_socket->getUseTagArticleList(1453, $data['StartNo'], $data['QryCount']);
        if (empty($artlist['Record'][0])) {
            $result = array('flag' => '01', 'info' => '无数据返回');
            echo(json_encode($result));
            exit;
        }
        $result = array('TtlRecords' => $artlist['RetRecords'], 'flag' => '00', 'info' => '操作成功', 'data' => $artlist['Record']);

        if ($this->input->get_post('sign') == '1') {
            print_r($result);
        } elseif ($_GET['callback']) {
            echo $_GET['callback'] . '(' . json_encode($result) . ')';
        } else {
            echo(json_encode($result));
        }
    }

    /**
     * 获取审核博客的文章列表,不包含采用组（黄金博客用） 今日精选
     */
    function goldlistJrjxApi() {
        $page = intval($this->input->get_post('page'));
        $page = (is_int($page) && $page > 0) ? $page : 1;
        $total = intval($this->input->get_post('total'));

        $data['QryCount'] = (is_int($total) && $total > 0) ? $total : MYBLOG_MORE_PAGESIZE;
        $data['StartNo'] = ($page - 1) * $data['QryCount'];

        $this->load->model('blogarticle_socket');
        $artlist = $this->blogarticle_socket->getGoldenBlogTagArticleList(1453, $data['StartNo'], $data['QryCount']);
        if (empty($artlist['Record'][0])) {
            $result = array('flag' => '01', 'info' => '无数据返回');
            echo(json_encode($result));
            exit;
        }
        $result = array('TtlRecords' => $artlist['RetRecords'], 'flag' => '00', 'info' => '操作成功', 'data' => $artlist['Record']);

        if ($this->input->get_post('sign') == '1') {
            print_r($result);
        } elseif ($_GET['callback']) {
            echo $_GET['callback'] . '(' . json_encode($result) . ')';
        } else {
            echo(json_encode($result));
        }
    }

}
