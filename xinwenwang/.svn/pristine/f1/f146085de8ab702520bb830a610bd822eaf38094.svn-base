<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Widget;

use Think\Controller;

class CateWidget extends Controller {

    public function _initialize() {
        
    }

    public function recomend() {
        $recomendUsers = D('User')->getList(array('is_hide' => 1, 'is_hot' => 1), 1, 4);
        $this->assign('recomendUsers', $recomendUsers);
        $this->display('Widget:Cate/recomend');
    }

}

