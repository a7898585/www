<?php

/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Home\Controller;

use Think\Controller;

//前台公共基类模块
class HomeCommonController extends Controller {

    protected function _initialize() {
        if (session('MEMBERINFO.id') > 0) {
            $this->assign('trolley_num', $this->getTrolleyNum());
            $this->assign('essage_num', $this->getMessageNum());
        }
    }

    // 空方法
    final public function _empty() {
        send_http_status(404);
        $this->display('Public/404');
        exit();
    }

    //取购物车数量
    private function getTrolleyNum() {
        return M('MembersDomainCart')->where(array('mid' => session('MEMBERINFO.id')))->count();
    }

    //获取信息数
    private function getMessageNum() {
        return D('Message')->getCountByMid(session('MEMBERINFO.id'));
    }

}

