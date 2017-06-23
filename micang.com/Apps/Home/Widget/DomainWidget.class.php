<?php

namespace Home\Widget;

use Think\Controller;

class DomainWidget extends Controller {

    public function _initialize() {
        
    }

    /**
     * 精品域名 首页样式
     * @param type $type
     * @param type $limit
     */
    public function do_list($type = '1', $limit = '6') {
        $data = M('MembersDomains')->field('id,domain,domain_note')->where(array('domain_type' => $type))->order('id desc')->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:Domain/do_list');
    }

    /**
     * 精品域名 查询结果
     * @param type $type
     * @param type $limit
     */
    public function do_list2($type = '1', $limit = '8') {
        $data = M('MembersDomains')->field('id,domain,domain_note')->where(array('domain_type' => $type))->order('id desc')->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:Domain/do_list2');
    }

    /**
     * 询价域名
     * @param type $type
     * @param type $limit
     */
    public function do_list3($type = '1', $limit = '8') {
        $data = M('MembersDomainSales')->where(array('type' => $type))->order('id desc')->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:Domain/do_list2');
    }

    /**
     * 米铺域名
     * @param type $type
     * @param type $limit
     */
    public function mipu($limit = '8') {
        $data = M()->query('SELECT s.title,s.id,f.domain from mc_members_shop  s  LEFT JOIN (SELECT  mid,domain,COUNT(1) total from mc_members_domains GROUP BY mid) f  on s.mid=f.mid where f.total>0 ORDER BY id desc LIMIT ' . $limit);
        $this->assign('data', $data);
        $this->display('Widget:Domain/mipu');
    }

    /**
     * 米铺域名销量排名
     * @param type $type
     * @param type $limit
     */
    public function mipu_sell($limit = '8') {
        $data = M()->query('SELECT s.title,s.id,f.total from mc_members_shop  s  LEFT JOIN (SELECT  mid,COUNT(1) total from mc_members_domain_sales_finish GROUP BY mid   ) f  on s.mid=f.mid  ORDER BY total desc LIMIT ' . $limit);
        $this->assign('data', $data);
        $this->display('Widget:Domain/mipu_sell');
    }

}

