<?php

/**
 * ConsumeController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-03
 */

namespace Member\Controller;

use Common\Extend\PageForMember;

class ConsumeController extends MemberCommonController {

    /**
     * 消费明细
     * @author Jansen
     * @since 2015-10-03
     */
    public function bill($p = 1) {
        $this->assign('m_tab', 'recharge');
        $total = M('MembersConsumeDetail')->where(array('mid' => session('MEMBERINFO.id')))->count();
        $consumes = M('MembersConsumeDetail')->where(array('mid' => session('MEMBERINFO.id')))->order(array('time' => 'DESC'))->page($p)->select();
        $this->assign('consumes', $consumes);
        $pager = new PageForMember($total);
        $pager->url = '/consume/bill?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    /**
     * 冻结明细
     * @param type $p
     */
    public function lock($p = 1) {
        $this->assign('m_tab', 'recharge');
        $mid = session('MEMBERINFO.id');
        $where['_string'] = '(from_mid = ' . $mid . ' AND lock_from >0 ) or (to_mid =' . $mid . ' and lock_to>0)';
        $total = M('DomainTradeLog')->where($where)->count();
        $consumes = M('DomainTradeLog')->where($where)->order(array('id' => 'DESC'))->page($p)->select();
        $this->assign('consumes', $consumes);
        $pager = new PageForMember($total);
        $pager->url = '/consume/lock?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    /**
     * 月度报表
     */
    public function report() {
        $data['register'] = array();
        $year = I('get.year');
        $year = $year ? $year : date('Y');
        $month = date('n');
        $where['mid'] = session('MEMBERINFO.id');
        for ($i = 1; $i <= $month; $i++) {
            $monthTime = $this->getMonthDate($year, $i);
            $where['type'] = array('eq', 'register');
            $where['time'] = array('BETWEEN', array($monthTime[0], $monthTime[1]));
            $data['register'][$i]['total'] = M('MembersConsumeDetail')->where($where)->count();
            $data['register'][$i]['money'] = M('MembersConsumeDetail')->where($where)->sum('money');
            $where['type'] = array('eq', 'bidding');
            $data['bidding'][$i]['total'] = M('MembersConsumeDetail')->where($where)->count();
            $data['bidding'][$i]['money'] = M('MembersConsumeDetail')->where($where)->sum('money');
            $where['type'] = array('eq', 'renew');
            $data['renew'][$i]['total'] = M('MembersConsumeDetail')->where($where)->count();
            $data['renew'][$i]['money'] = M('MembersConsumeDetail')->where($where)->sum('money');

            $data['all_total'][$i] = $data['register'][$i]['total'] + $data['bidding'][$i]['total'] + $data['renew'][$i]['total'];
            $data['all_money'][$i] = $data['register'][$i]['money'] + $data['bidding'][$i]['money'] + $data['renew'][$i]['money'];
        }

        $this->assign('data', $data);
        $this->display();
    }

    private function getMonthDate($year, $month) {
        $month = sprintf("%02d", intval($month));
        $year = str_pad(intval($year), 4, "0", STR_PAD_RIGHT);
        $month > 12 || $month < 1 ? $month = 1 : $month = $month;
        $firstday = strtotime($year . $month . "01000000");
        $firstdaystr = date("Y-m-01", $firstday);
        $lastday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
        return array($firstday, $lastday);
    }

}