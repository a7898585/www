<?php

namespace Home\Controller;

use Think\Controller;

class AboutController extends Controller {

    public function _initialize() {
        $this->assign('m_tab', 'about');
    }

    /**
     * 米仓网简介
     */
    public function index() {
        $seo = array(
            'title' => '米仓网简介|' . C('INDEX_TITLE'),
            'key' => '米仓网简介,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 收费标准
     */
    public function fee() {
        $seo = array(
            'title' => '收费标准|' . C('INDEX_TITLE'),
            'key' => '收费标准,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 网站声明
     */
    public function state() {
        $seo = array(
            'title' => '网站声明|' . C('INDEX_TITLE'),
            'key' => '网站声明,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 联系我们
     */
    public function contact() {
        $seo = array(
            'title' => '联系我们|' . C('INDEX_TITLE'),
            'key' => '联系我们,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

}