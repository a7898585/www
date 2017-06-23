<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Widget;

use Think\Controller;

class CateWidget extends Controller {

    public $city_id = 0;

    public function _initialize() {
        $city = cookie('CITY');
        $this->city_id = $city['area_id'];
    }

    /**
     * 菜单列表
     * @param type $nav
     */
    public function menu($nav = 'i') {
        $this->assign('nav', $nav);
        $this->display('Widget:Cate/menu');
    }

    /**
     * 友情链接
     */
    public function links() {
        $links = D('FriendLinks')->getListAll();
        $this->assign('links', $links);
        $this->display('Widget:Cate/links');
    }

    /**
     * 合作机构
     */
    public function banks($count) {
        $banks = D('Bank')->getListByArea($this->city_id, $count);
        $this->assign('banks', $banks);
        $this->display('Widget:Cate/banks');
    }

    /**
     * 热门城市
     */
    public function hot_city() {
        $hot_city = D('Area')->getListByHot(27);
        $this->assign('hot_city', $hot_city);
        $this->display('Widget:Cate/hot_city');
    }

    /**
     * 推荐贷款
     */
    public function recommend_daikuan($count) {
//        $pro_list = D('BankProducts')->getRecommend($this->city_id, $count);
//        $this->assign('pro_list', $pro_list);
        $this->assign('cookie', cookie('CITY'));
        $this->display('Widget:Cate/recommend_daikuan');
    }

    public function news($class_type = 0, $count, $len = '20') {
        $news_list = D('News')->getListAll($this->city_id, $class_type, $count);

        $this->assign('list', $news_list);
        $this->assign('len', $len);
        $this->display('Widget:Cate/news');
    }

    public function news1($class_type = 0, $count, $len = '20') {
        $news_list = D('News')->getListAll($this->city_id, $class_type, $count);
        $this->assign('list', $news_list);
        $this->assign('len', $len);
        $this->display('Widget:Cate/news1');
    }

    public function news2($class_type = 0, $count, $len = '20') {
        $news_list = D('News')->getListAll($this->city_id, $class_type, $count);
        $this->assign('list', $news_list);
        $this->assign('len', $len);
        $this->assign('class_type', $class_type);
        $this->display('Widget:Cate/news2');
    }

    public function news3($class_type = 0, $count, $len = '20') {
        $news_list = D('News')->getListAll($this->city_id, $class_type, $count);
        $this->assign('list', $news_list);
        $this->assign('len', $len);
        $this->assign('class_type', $class_type);
        $this->display('Widget:Cate/news3');
    }

    /*
     * 问答
     * @param int $type 问题类型
     * @param int $limit 限制条数
     * 调用:  {:W('Cate/Wenda',array($type,$limit))}
     */

    public function wenda($type, $limit) {
        if ($type)
            $where['type'] = $type;
        $item = D('Question')->getList($where, 1, $limit);
        $this->assign('list', $item['list']);
        $this->display('Widget:Cate/wenda');
    }

    public function wenda1($type, $limit) {
        $item = D('QuestionAnswers')->getListByType($type, $limit);
        $this->assign('list', $item);
        $this->display('Widget:Cate/wenda1');
    }

    /*
     * 我的位置
     * @param array  $arr 数组
     * 调用:  {:W('Cate/Position',array($position))}
     * $position = array(
      array('name'=>'我的位置1','link'=>'链接'),
      array('name'=>'我的位置2','link'=>'链接')
      );
     */

    public function position($position) {
        $this->assign('position', $position);
        $this->assign('count', count($position) - 1);
        $this->display('Widget:Cate/position');
    }

    /*
     * 获取信贷经理列表
     * @param int $limit 限制条数
     * 调用：{:W('Cate/xindai_list',array($limit))}
     */

    public function xindai_list($limit) {
        if (!$limit)
            $limit = 1;
        $result = D('MemberXindai')->where(array('status' => 1, 'city' => $this->city_id))->field('id, bank_id, thumb_b, realname')->limit($limit)->select();
        foreach ($result as &$item) {
            $item['bank_name'] = D('Bank')->where(array('id' => $item['bank_id']))->getField('name');
        }
        $this->assign('list', $result);
        $this->display('Widget:Cate/xindai_list');
    }

    public function fast_apply() {
        $this->display('Widget:Cate/fast_apply');
    }
    
    public function getReComendList($info){
        print_r($info);exit;
    }

}

