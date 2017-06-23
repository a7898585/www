<?php

namespace Member\Controller;

class ShopController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'sell_domain');
    }

    public function index() {
        
    }

    /**
     * 我的米铺
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['mid'] = session('MEMBERINFO.id');
            $data['url'] = 'http://' . session('MEMBERINFO.id') . '.micang.com';
            if ($data['id']) {
                $data['uptime'] = time();
                $res = M('MembersShop')->save($data);
            } else {
                $data['addtime'] = time();
                $res = M('MembersShop')->add($data);
            }
            if (!$res) {
                $this->ajaxReturn(array('status' => 500, 'message' => '米铺资料操作失败，请重试。'));
            } else {
                $this->ajaxReturn(array('status' => 200, 'message' => '米铺资料操作成功。'));
            }
        }
        $membersShop = M('MembersShop')->where(array('mid' => session('MEMBERINFO.id')))->find();
        $this->assign('membersShop', $membersShop);
        if (!$membersShop['id']) {
            $nId = M('MembersShop')->order('id desc')->getField('id');
            $nId +=1;
        } else {
            $nId = $membersShop['id'];
        }
        $this->assign('nId', $nId);
        //生成上传相关参数
        $upYun = getUpaiyunConfig('shop');
        $this->assign('policy', $upYun['policy']);
        $this->assign('sign', $upYun['signature']);
        $this->display();
    }

}