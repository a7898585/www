<?php

/**
 * NewsController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-19
 */

namespace Home\Controller;

use Common\Extend\PageForHome;

class NewsController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('navtap', 'news');
    }

    public function index() {
        $seo = array(
            'title' => C('NEWS_TITLE'),
            'key' => C('NEWS_KEYWORDS'),
            'des' => C('NEWS_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    public function index2() {
        $cid = 7;
        $category = M('NewsCategory')->select();
        $this->assign('category', $category);
        $total = M('News')->where(array('cid' => $cid))->count();
        $news = M('News')->field('id,title,summary,create_time')->where(array('cid' => $cid))->order(array('create_time' => 'DESC'))->page(1)->select();
        $this->assign('news', $news);
        $pager = new PageForHome($total);
        $pager->url = '/news/c' . $cid . '?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('cid', $cid);
        $seo = array(
            'title' => C('NEWS_TITLE'),
            'key' => C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->display('lists');
    }

    public function lists($cid, $p = 1) {
        $limit = 15;
        $category_arr = M('NewsCategory')->select();
        $category = array();
        if (!empty($category_arr)) {
            foreach ($category_arr as $value) {
                $category[$value['id']] = $value;
            }
        }
        $this->assign('category', $category);
        $total = M('News')->where(array('cid' => $cid))->count();
        $news = M('News')->field('id,title,summary,create_time')->where(array('cid' => $cid))->order(array('create_time' => 'DESC'))->page($p)->limit($limit)->select();
        $this->assign('news', $news);
        $pager = new PageForHome($total, $limit);
        $pager->url = '/news/c' . $cid . '?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('cid', $cid);
        $seo = array(
            'title' => str_replace('{$key}', $category[$cid]['name'], C('NEWS_LIST_TITLE')),
            'key' => str_replace('{$key}', $category[$cid]['name'], C('NEWS_LIST_KEYWORDS')),
            'des' => str_replace('{$key}', $category[$cid]['name'], C('NEWS_LIST_DESC'))
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    public function detail($id) {
       $category_arr = M('NewsCategory')->select();
        $category = array();
        if (!empty($category_arr)) {
            foreach ($category_arr as $value) {
                $category[$value['id']] = $value;
            }
        }
        $this->assign('category', $category);
        $newsInfo = M('News')->where(array('id' => $id))->find();
        $this->assign('newsInfo', $newsInfo);
        $newsList = M('News')->where(array('cid' => $newsInfo['cid']))->order('id desc')->limit(3)->select();
        $this->assign('newsList', $newsList);
        $seo = array(
            'title' => str_replace('{$key}', $newsInfo['title'], C('NEWS_DETAIL_TITLE')),
            'key' => str_replace('{$key}', $newsInfo['title'], C('NEWS_DETAIL_KEYWORDS')),
            'des' => str_replace('{$key}', $newsInfo['title'], C('NEWS_DETAIL_DESC'))
        );
        $this->assign('seo', $seo);
        $this->display();
    }

}