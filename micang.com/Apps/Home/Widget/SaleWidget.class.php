<?php

namespace Home\Widget;

use Think\Controller;

class SaleWidget extends Controller {

    public function _initialize() {
        
    }

    public function sale_list($type = '1', $limit = '6') {
        $data = M('MembersDomainSales')->field('id,summary,domain,end_time,seller_price,buyer_price')->where(array('type' => $type))->order('id desc')->limit($limit)->select();
      
        $this->assign('data', $data);
        $this->assign('type', $type);
        $this->display('Widget:Sale/sale_list');
    }

    public function sale_list_domain($type = '1', $limit = '6') {
        $data = M('MembersDomainSales')->field('id,domain,end_time,seller_price,buyer_price,summary')->where(array('type' => $type))->order('id desc')->limit($limit)->select();
      
        $this->assign('data', $data);
        $this->assign('type', $type);
        $this->display('Widget:Sale/sale_list_domain');
    }

    /*
     * 1元起拍
     */

    public function certify($limit = '6') {
        $data = M('MembersDomainSales')->field('id,summary,domain,end_time,buyer_price')->where(array('seller_price' =>100))->order('id desc')->limit($limit)->select();
        $this->assign('data', $data);
        $this->assign('type', '0');
        $this->display('Widget:Sale/sale_list');
    }
    
    
     public function news_list($type = '1', $limit = '3') {
        $data = M('MembersDomainSales')->field('id,domain,end_time,seller_price,buyer_price')->where(array('type' => $type))->order('id desc')->limit($limit)->select();
        $this->assign('data', $data);
        $this->assign('type', $type);
        $this->display('Widget:Sale/news_list');
    }

}

