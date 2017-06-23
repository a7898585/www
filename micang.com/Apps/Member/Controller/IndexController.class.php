<?php

/*
 * 会员中心
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Member\Controller;

//会员中心模块
class IndexController extends MemberCommonController {

    public function index() {
        //获取级别
        $memberLevels = M('MemberLevels')->getField('id,name');
        //取用户资料
        $memberInfo = M('Members')->where(array('id' => session('MEMBERINFO.id')))->find();
        $this->assign('memberLevel', $memberLevels[$memberInfo['level']]);
        //统计域名总数
        $domainCount['total'] = M('MembersDomains')->where(array('mid' => session('MEMBERINFO.id')))->count();
        $this->assign('domainCount', $domainCount);
        $this->assign('memberInfo', $memberInfo);
        $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->find();
        $this->assign('profile', $profile);
        $memberMoney = M('MembersMoney')->where(array('mid' => session('MEMBERINFO.id')))->find();
        $this->assign('memberMoney', $memberMoney);
        
        $this->display();
    }

    public function logout() {
        session('MEMBERINFO', null);
        redirect('http://' . str_replace('member.', 'www.', I('server.HTTP_HOST')) . '/public/login');
    }

}

