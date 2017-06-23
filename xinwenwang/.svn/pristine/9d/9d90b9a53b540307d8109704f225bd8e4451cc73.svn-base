<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Widget;

use Think\Controller;

class HomeWidget extends Controller {

    public function _initialize() {
        //  $this->city_id = cookie('CITY')['id'];
    }

    public function nav($nav = '') {
        $defaultCategory = cookie('defaultCategory');
        $otherCategory = cookie('otherCategory');
        if (!$defaultCategory) {
            $defaultCategory = defaultCategory();
            cookie('defaultCategory', $defaultCategory);
        }
        if (!$otherCategory) {
            $otherCategory = otherCategory();
            cookie('otherCategory', $otherCategory);
        }
        $this->assign('defaultCategory', $defaultCategory);
        $this->assign('otherCategory', $otherCategory);
        $this->assign('nav', $nav);
        $this->display('Widget:Home/nav');
    }

    public function subject($typeid = '1802') {
        $fkey = "subject_newsList_first_" . $typeid;
        $mkey = "subject_newsList_" . $typeid;
        $where = array(
            '_string' => 'show_type=2 or show_type=3',
            'subject_id' => $typeid,
        );
        $order = 'update_time desc';
        $list_first = getMemcache($fkey);
        if (empty($list_first)) {
            $list_first = D('News')->getWhereInfo($where, $order);
            setMemcache($fkey, $list_first, 60 * 10);
        }
        $where = array(
            'subject_id' => $typeid,
            'id' => array('neq', $list_first['id']),
        );
        $data = getMemcache($mkey);
        if (empty($data['data_list'])) {
            $data = D('News')->getListLimit($where, 7, $order);
            setMemcache($mkey, $data, 60 * 10);
        }
//        echo D('News')->getLastSql();exit;
        $this->assign('list_first', $list_first);
        $this->assign('list', $data['data_list']);
        $this->display('Widget:Home/subject');
    }

    public function hotNews() {
        //今日点击
        $time = time();
        $wheres = array(
            '_string' => 'show_type=2 or show_type=3',
        );
        $wheres['update_time'] = array('gt', $time - 86400 * 7);

        $key = 'news_list_hotnews';
        $jrdj = getMemcache($key);
        if (empty($jrdj['data_list'])) {
            $jrdj = D('News')->getWhereList($wheres, 1, 8, 'good_sum desc,update_time desc');
            setMemcache($key, $jrdj, 60 * 60 * 2);
        }
//        print_r($jrdj['data_list']);
        $this->assign('jrdj_list', $jrdj['data_list']);
        $this->display('Widget:Home/hotNews');
    }

    public function hotComments($type_id = '') {
        if ($type_id) {
            $where = array(
                'type_id' => $type_id,
            );
        } else {
            $where = array();
        }
        $ckey = 'news_comments_' . $type_id . '_p1_n5';
        $comments = getMemcache($ckey);
        if (empty($comments['data_list'])) {
            $comments = D('News')->getHotComments($where, 1, 5);
            setMemcache($ckey, $comments, 60 * 30);
        }
        $this->assign('comments', $comments['data_list']);
        $this->display('Widget:Home/hotComments');
    }

}

