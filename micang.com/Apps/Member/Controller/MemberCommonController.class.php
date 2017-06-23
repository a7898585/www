<?php

/*
 * 会员中心
 * @Created on 2015/08/18
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Member\Controller;

use Think\Controller;

//会员中心公共基类模块
class MemberCommonController extends Controller {

    //无需验证登录状态的控制器
    protected static $publicController = array('PUBLIC');

    public function _initialize() {
        $params['m'] = MODULE_NAME;
        $params['c'] = CONTROLLER_NAME;
        $params['a'] = ACTION_NAME;
        $this->assign("params", $params);
        //验证控制器是否需要做登录验证
        if (in_array(strtoupper(CONTROLLER_NAME), self::$publicController)) {
            return true;
        }
        //登录验证
        if (!is_array(session('MEMBERINFO'))) {
            $this->error('您尚未登录，请先登录。', getDoMain('www') . 'public/login?refer_url=' . getDoMain('member', '') . $_SERVER['REQUEST_URI']);
        } else {
            $this->assign('message_num', $this->getMessageNum());
            $this->assign('trolley_num', $this->getTrolleyNum());
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

