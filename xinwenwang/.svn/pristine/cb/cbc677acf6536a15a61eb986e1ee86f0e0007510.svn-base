<?php

namespace Home\Widget;

use Think\Controller;

class UserWidget extends Controller {

    public function _initialize() {
        //  $this->city_id = cookie('CITY')['id'];
    }

    //我的收藏
    public function mycollect() {
        $user = $this->USER;
        $page = max(I('request.p'), 1);
        $collect = D('UsersCollect')->getMyCollect($user['id'], $page);
        $this->assign('collect', $collect['list']);
        $this->assign('page_html', $collect['page_html']);
        $this->display('Widget:User/collect');
    }

    //我的订阅
    public function mydingyue() {
        //用户的订阅列表
        // $user = cookie('user_info');
        $user = $this->USER;
        $page = max(I('request.p'), 1);
        $limit = 10;
        $start = ($page - 1) * $limit;
        $total = M('UsersDingyue')->where(array('uid' => $user['id']))->count();
        if ($total > 0) {
            $data = M('UsersDingyue')->field('id as did,sid,sort_id')->where(array('uid' => $user['id']))->order('sort_id desc')->page($page, $limit)->select();
            foreach ($data as $key => $value) {
                $value['count'] = M('News')->where(array('dingyue_id' => $value['sid']))->count();
                $info = M('News')->field('intro,dingyue_id,id,title,source_name')->where(array('dingyue_id' => $value['sid']))->order('update_time desc')->find();
                $value = array_merge($value, $info);
                $data[$key] = $value;
            }
        }

        $this->assign('page_html', showHomePage($total, $limit));
        $this->assign('dingyue', $data);
        $this->display('Widget:User/dingyue');
    }

    //我的评论
    public function mycomment() {
        //我的评论
        //        $user = cookie('user_info');

        $user = $this->USER;
        $page = max(I('request.p'), 1);
        $limit = 10;
        if (I('request.type') == 2) {
            $where = array('uid' => $user['id'], 'is_follow' => 1);
        } else {
            $where = array('uid' => $user['id']);
        }
        $total = D('news_comment')->where($where)->count();
        if ($total > 0) {
            if (I('request.type') == 2) {
                $comid = array();
                $comidArr = D('news_comment')->field('id')->where($where)->select();
                foreach ($comidArr as $value) {
                    $comid[] = $value['id'];
                }
                $where = array('com_id' => array('in', implode(',', $comid)));
            }
            $comment = D('news_comment')->where($where)->order('id desc')->page($page, $limit)->select();
//            echo D('news_comment')->getLastSql();
            foreach ($comment as $value) {
                $value['news_msg'] = D('News')->field('type_id,img_list')->where(array('id' => $value['news_id']))->find();
                if (I('request.type') == 2) {
                    $user = M('Users')->field('username,head_pic')->where(array('id' => $value['uid']))->find();
                    $value['username'] = $user['username'];
                    $value['head_pic'] = $user['head_pic'];
                }
                $newcomment[] = $value;
            }
            foreach ($newcomment as $value) {
                $temp = json_decode($value['news_msg']['img_list']);
                $value['news_msg']['pic'] = $temp[0] ? $temp[0] : '';
                $value['news_msg']['typename'] = getTypeName($value['news_msg']['type_id']);
                $comments[] = $value;
            }
        }
        $this->assign('page_html', showHomePage($total, $limit));
        $this->assign('comment', $comments);
        $this->display('Widget:User/comment');
    }

    //我的好友
    public function myfriends() {
        //我的好友
        //        $user = cookie('user_info');
        $user = $this->USER;
        $page = max(I('request.p'), 1);
        $limit = 10;
        $start = ($page - 1) * $limit;
        $sql = 'select * from (select * from xw_users where id in '
                . '(SELECT fuid from xw_users_fans where uid =' . $user['id'] . ')'
                . ' order by add_time desc) as u ';
        $friends_comment = D('news_comment')->query($sql);
        $this->assign('page_html', showHomePage(count($friends_comment), $limit));

        $friends_comment = array_slice($friends_comment, $start, $limit);
        $model = new \Home\Model\UsersModel();
        foreach ($friends_comment as $value) {
            $value['comment'] = M('NewsComment')->field('news_title,news_id')->where(array('uid' => $value['id']))->find();
        }

        $this->assign('friends', $friends_comment);
        $this->display('Widget:User/friends');
    }

    /**
     * 好友推荐
     * $show  display;fetch
     */
    public function recomend($page = '1', $limit = '6') {
        $user = $this->USER;
//        $page = max(I('request.p'), 1);
        $start = ($page - 1) * $limit;
        $sql = 'select * from (select * from xw_users where id not in '
                . '(SELECT fuid from xw_users_fans where uid =' . $user['id'] . ')'
                . ' order by add_time desc) as u  limit ' . $start . ',' . $limit;
//        echo $sql;
        $recomend = D('news_comment')->query($sql);
//        foreach ($recomend as $value) {
//            $value['comment'] = M('NewsComment')->field('news_title,news_id')->where(array('uid' => $value['id']))->find();
//        }
//        print_r($recomend);exit;
        if (!empty($recomend)) {
            $this->assign('recomend', $recomend);
            $this->display('Widget:User/recomend');
        }
    }

}

