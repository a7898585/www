<?php

namespace Member\Widget;

use Think\Controller;

class DomainWidget extends Controller {

    public function _initialize() {
        
    }

    /**
     * 我购买的域名
     * @param type $mid
     * @param array $order
     * @param type $limit
     */
    public function my_buy_domain($mid, array $order = array('create_time' => 'DESC'), $limit = '3') {
        $data = M('MembersDomainSalesFinish')->field('id,domain,type')->where(array('buyer_mid' => $mid))->order($order)->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:Domain/my_buy_domain');
    }

    /**
     * 我出售的域名
     * @param type $mid
     * @param array $order
     * @param type $limit
     */
    public function my_sale_domain($mid, array $order = array('create_time' => 'DESC'), $limit = '3') {
        $data = M('MembersDomainSales')->field('id,domain,type')->where(array('mid' => $mid))->order($order)->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:Domain/my_sale_domain');
    }

    /**
     * 我关注的域名
     * @param type $mid
     * @param array $order
     * @param type $limit
     */
    public function my_notice_domain($mid, array $order = array('id' => 'DESC'), $limit = '3') {
        $sales = M('MembersDomainNotice')->field('id,sale_id')->where(array('member_id' => $mid))->order($order)->limit($limit)->select();
        $data = array();
        if (!empty($sales)) {
            foreach ($sales as $value) {
                $data[] = M('MembersDomainSales')->field('id,domain,type')->where(array('id' => $value['sale_id']))->find();
            }
        }
        $this->assign('data', $data);
        $this->display('Widget:Domain/my_notice_domain');
    }

    /**
     * 即将到期的域名
     * @param type $mid
     * @param array $order
     * @param type $limit
     */
    public function expire_domain($mid, array $order = array('id' => 'DESC'), $limit = '3') {
        $data = M('MembersDomains')->field('id,domain,expire_time')->where(array('mid' => $mid, 'expire_time' => array('lt', time() + 3600 * 24 * 30)))->order($order)->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:Domain/expire_domain');
    }

}

