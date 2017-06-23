<?php

/**
 * ReportController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-15
 */

namespace Admin\Controller;

class ReportController extends AdminCommonController {

    public function member() {
        //30天用户注册数
        $days = M('TotalMemberForDay')->order(array('day' => 'DESC'))->limit(30)->getField('day,total');
        ksort($days, SORT_STRING);
        $this->assign('day30key', array_keys($days));
        $this->assign('day30value', array_values($days));
        //每周用户注册数
        $weeks = M('TotalMemberForWeek')->order(array('week' => 'DESC'))->limit(54)->getField('week,total');
        ksort($weeks, SORT_STRING);
        $this->assign('weekkey', array_keys($weeks));
        $this->assign('weekvalue', array_values($weeks));
        //每周用户注册数
        $months = M('TotalMemberForMonth')->order(array('month' => 'DESC'))->limit(12)->getField('month,total');
        ksort($months, SORT_STRING);
        $this->assign('monthkey', array_keys($months));
        $this->assign('monthvalue', array_values($months));
        $this->display();
    }

    public function domain() {
        //30天域名注册数
        $days = M('TotalDomainForDay')->order(array('day' => 'DESC'))->limit(30)->getField('day,total');
        ksort($days, SORT_STRING);
        $this->assign('day30key', array_keys($days));
        $this->assign('day30value', array_values($days));
        //每周域名注册数
        $weeks = M('TotalDomainForWeek')->order(array('week' => 'DESC'))->limit(54)->getField('week,total');
        ksort($weeks, SORT_STRING);
        $this->assign('weekkey', array_keys($weeks));
        $this->assign('weekvalue', array_values($weeks));
        //每周域名注册数
        $months = M('TotalDomainForMonth')->order(array('month' => 'DESC'))->limit(12)->getField('month,total');
        ksort($months, SORT_STRING);
        $this->assign('monthkey', array_keys($months));
        $this->assign('monthvalue', array_values($months));
        $this->display();
    }

    public function recharge() {
        //30天域名注册数
        $days = M('TotalChargeForDay')->order(array('day' => 'DESC'))->limit(30)->getField('day,total');
        ksort($days, SORT_STRING);
        $this->assign('day30key', array_keys($days));
        $this->assign('day30value', array_values($days));
        //每周域名注册数
        $weeks = M('TotalChargeForWeek')->order(array('week' => 'DESC'))->limit(54)->getField('week,total');
        ksort($weeks, SORT_STRING);
        $this->assign('weekkey', array_keys($weeks));
        $this->assign('weekvalue', array_values($weeks));
        //每周域名注册数
        $months = M('TotalChargeForMonth')->order(array('month' => 'DESC'))->limit(12)->getField('month,total');
        ksort($months, SORT_STRING);
        $this->assign('monthkey', array_keys($months));
        $this->assign('monthvalue', array_values($months));
        $this->display();
    }

}