<?php

namespace Home\Widget;

use Think\Controller;

class CateWidget extends Controller {

    public function _initialize() {
        
    }

    /**
     * 友情链接
     */
    public function friend_links() {
        $data = M('FriendLinks')->field('id,title')->where(array('status' => '1'))->order('sort,id desc')->select();
        $this->assign('data', $data);
        $this->display('Widget:Cate/friend_links');
    }

    /**
     * 首页轮播图片
     */
    public function index_photo() {
        $data = M('Photos')->field('id,title,url,pic_url')->where(array('status' => '1', 'type' => '0'))->order('sort,id desc')->limit(5)->select();
        $this->assign('data', $data);
        $this->display('Widget:Cate/index_photo');
    }

    /**
     * 合作伙伴
     */
    public function index_partner() {
        $data = M('Photos')->field('id,title,pic_url')->where(array('status' => '1', 'type' => '1'))->order('sort,id desc')->limit(5)->select();
        $this->assign('data', $data);
        $this->display('Widget:Cate/index_partner');
    }

    /**
     * 域名代购
     */
    public function purchase() {
        $this->display('Widget:Cate/purchase');
    }

}

