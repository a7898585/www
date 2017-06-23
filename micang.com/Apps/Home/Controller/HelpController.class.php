<?php

namespace Home\Controller;

use Think\Controller;

class HelpController extends Controller {

    public function _initialize() {
        $this->assign('m_tab', 'help');
    }

    /**
     * 新手上路
     */
    public function index() {
        $seo = array(
            'title' => '新手上路|' . C('INDEX_TITLE'),
            'key' => '新手上路,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 操作引导
     */
    public function oper() {
        $seo = array(
            'title' => '操作引导|' . C('INDEX_TITLE'),
            'key' => '操作引导,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 网站协议
     */
    public function protocol() {
        $seo = array(
            'title' => '网站协议|' . C('INDEX_TITLE'),
            'key' => '网站协议,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 在线支付
     */
    public function zf() {
        $seo = array(
            'title' => '在线支付|' . C('INDEX_TITLE'),
            'key' => '在线支付,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('m_tab', 'zffs');
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 银行汇款
     */
    public function yhhk() {
        $seo = array(
            'title' => '银行汇款|' . C('INDEX_TITLE'),
            'key' => '银行汇款,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('m_tab', 'zffs');
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 在线提问
     */
    public function ask() {
        $seo = array(
            'title' => '在线提问|' . C('INDEX_TITLE'),
            'key' => '在线提问,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('m_tab', 'fwzc');
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 合作联系
     */
    public function relation() {
        $seo = array(
            'title' => '合作联系|' . C('INDEX_TITLE'),
            'key' => '合作联系,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('m_tab', 'fwzc');
        $this->assign('seo', $seo);
        $this->display();
    }
 /**
     * 文档下载
     */
    public function download() {
        $seo = array(
            'title' => '文档下载|' . C('INDEX_TITLE'),
            'key' => '文档下载,' . C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('m_tab', 'fwzc');
        $this->assign('seo', $seo);
        $this->display();
    }
}