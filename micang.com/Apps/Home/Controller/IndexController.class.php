<?php

/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Home\Controller;

//前台模块
class IndexController extends HomeCommonController {

    public function index() {
        $seo = array(
            'title' => C('INDEX_TITLE'),
            'key' => C('INDEX_KEYWORDS'),
            'des' => C('INDEX_DESC')
        );
        $this->assign('navtap', 'index');
        $this->assign('seo', $seo);
        $this->display();
    }

}

