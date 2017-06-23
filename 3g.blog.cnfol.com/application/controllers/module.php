<?php

/* * **********************
 * 功能：  Ajax调用模块
 * *********************** */

class Module extends MY_Controller {

    function Module() {
        parent::MY_Controller();
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

}

