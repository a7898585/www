<?php

/**
 * CountController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-16
 */

namespace Task\Controller;

class CountController extends TaskCommonController {

    public function member_for_day($date = null) {
        if (is_null($date)) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        $where['register_time'] = array('BETWEEN', array(($date . ' 00:00:00'), ($date . ' 23:59:59')));
        $total = M('Members')->where($where)->count();
        $data['day'] = $date;
        $data['total'] = $total;
        M('TotalMemberForDay')->data($data)->add();
        exit('finish');
    }

    public function member_for_week($week = null) {
        if (is_null($week)) {
            if (date('W') == '1') {
                $weekTime = $this->getWeekDate(date('Y') - 1, 52);
            } else {
                $weekTime = $this->getWeekDate(date('Y'), date('W') - 1);
            }
        } else {
            $weekTmp = explode('-', $week);
            $weekTime = $this->getWeekDate($weekTmp[0], $weekTmp[1]);
        }
        $where['register_time'] = array('BETWEEN', array(($weekTime[0] . ' 00:00:00'), ($weekTime[1] . ' 23:59:59')));
        $total = M('Members')->where($where)->count();
        $data['week'] = is_null($week) ? date('Y-W') : $week;
        $data['total'] = $total;
        M('TotalMemberForWeek')->data($data)->add();
        exit('finish');
    }

    public function member_for_month($month = null) {
        if (is_null($month)) {
            if (date('n') == '1') {
                $monthTime = $this->getMonthDate(date('Y') - 1, 1);
                $month = (date('Y') - 1) . '-01';
            } else {
                $monthTime = $this->getMonthDate(date('Y'), date('n') - 1);
                $month = date('Y') . '-' . (date('n') - 1);
            }
        } else {
            $monthTmp = explode('-', $month);
            $monthTime = $this->getMonthDate($monthTmp[0], $monthTmp[1]);
        }
        $where['register_time'] = array('BETWEEN', array(($monthTime[0] . ' 00:00:00'), ($monthTime[1] . ' 23:59:59')));
        $total = M('Members')->where($where)->count();
        $data['month'] = $month;
        $data['total'] = $total;
        M('TotalMemberForMonth')->data($data)->add();
        exit('finish');
    }

    public function domain_for_day($date = null) {
        if (is_null($date)) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        $where['register_time'] = array('BETWEEN', array(($date . ' 00:00:00'), ($date . ' 23:59:59')));
        $total = M('MembersDomains')->where($where)->count();
        $data['day'] = $date;
        $data['total'] = $total;
        M('TotalDomainForDay')->data($data)->add();
        exit('finish');
    }

    public function domain_for_week($week = null) {
        if (is_null($week)) {
            if (date('W') == '1') {
                $weekTime = $this->getWeekDate(date('Y') - 1, 52);
            } else {
                $weekTime = $this->getWeekDate(date('Y'), date('W') - 1);
            }
        } else {
            $weekTmp = explode('-', $week);
            $weekTime = $this->getWeekDate($weekTmp[0], $weekTmp[1]);
        }
        $where['register_time'] = array('BETWEEN', array(($weekTime[0] . ' 00:00:00'), ($weekTime[1] . ' 23:59:59')));
        $total = M('MembersDomains')->where($where)->count();
        $data['week'] = is_null($week) ? date('Y-W') : $week;
        $data['total'] = $total;
        M('TotalDomainForWeek')->data($data)->add();
        exit('finish');
    }

    public function domain_for_month($month = null) {
        if (is_null($month)) {
            if (date('n') == '1') {
                $monthTime = $this->getMonthDate(date('Y') - 1, 1);
                $month = (date('Y') - 1) . '-01';
            } else {
                $monthTime = $this->getMonthDate(date('Y'), date('n') - 1);
                $month = date('Y') . '-' . (date('n') - 1);
            }
        } else {
            $monthTmp = explode('-', $month);
            $monthTime = $this->getMonthDate($monthTmp[0], $monthTmp[1]);
        }
        $where['register_time'] = array('BETWEEN', array(($monthTime[0] . ' 00:00:00'), ($monthTime[1] . ' 23:59:59')));
        $total = M('MembersDomains')->where($where)->count();
        $data['month'] = $month;
        $data['total'] = $total;
        M('TotalDomainForMonth')->data($data)->add();
        exit('finish');
    }

    /**
     * 每日充值统计
     * @param type $date
     */
    public function recharge_for_day($date = null) {
        if (is_null($date)) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        $where['status'] = array('eq', '1');
        $where['notify_time'] = array('BETWEEN', array(($date . ' 00:00:00'), ($date . ' 23:59:59')));
        $moneyArr = M('MembersRechargeDetail')->field('money')->where($where)->select();
        $total = 0;
        if (!empty($moneyArr)) {
            foreach ($moneyArr as $val) {
                $total+=$val['money'];
            }
        }
        $data['day'] = $date;
        $data['total'] = $total/100;
        M('TotalChargeForDay')->data($data)->add();
        exit('finish');
    }

    /**
     * 每周充值统计
     * @param type $week
     */
    public function recharge_for_week($week = null) {
        if (is_null($week)) {
            if (date('W') == '1') {
                $weekTime = $this->getWeekDate(date('Y') - 1, 52);
            } else {
                $weekTime = $this->getWeekDate(date('Y'), date('W') - 1);
            }
        } else {
            $weekTmp = explode('-', $week);
            $weekTime = $this->getWeekDate($weekTmp[0], $weekTmp[1]);
        }
        $where['status'] = array('eq', '1');
        $where['notify_time'] = array('BETWEEN', array(($weekTime[0] . ' 00:00:00'), ($weekTime[1] . ' 23:59:59')));
        $moneyArr = M('MembersRechargeDetail')->field('money')->where($where)->select();
        $total = 0;
        if (!empty($moneyArr)) {
            foreach ($moneyArr as $val) {
                $total+=$val['money'];
            }
        }
        $data['week'] = is_null($week) ? date('Y-W') : $week;
        $data['total'] = $total/100;
        M('TotalChargeForWeek')->data($data)->add();
        exit('finish');
    }

    /**
     * 每月充值统计
     * @param type $month
     */
    public function recharge_for_month($month = null) {
        if (is_null($month)) {
            if (date('n') == '1') {
                $monthTime = $this->getMonthDate(date('Y') - 1, 1);
                $month = (date('Y') - 1) . '-01';
            } else {
                $monthTime = $this->getMonthDate(date('Y'), date('n') - 1);
                $month = date('Y') . '-' . (date('n') - 1);
            }
        } else {
            $monthTmp = explode('-', $month);
            $monthTime = $this->getMonthDate($monthTmp[0], $monthTmp[1]);
        }
        $where['status'] = array('eq', '1');
        $where['notify_time'] = array('BETWEEN', array(($monthTime[0] . ' 00:00:00'), ($monthTime[1] . ' 23:59:59')));
        $moneyArr = M('MembersRechargeDetail')->field('money')->where($where)->select();
        echo M('MembersRechargeDetail')->getlastsql();
        $total = 0;
        if (!empty($moneyArr)) {
            foreach ($moneyArr as $val) {
                $total+=$val['money'];
            }
        }
        $data['month'] = $month;
        $data['total'] = $total/100;
        M('TotalChargeForMonth')->data($data)->add();
        exit('finish');
    }

    private function getWeekDate($year, $week) {
        $firstdayofyear = mktime(0, 0, 0, 1, 1, $year);
        $firstweekday = date('N', $firstdayofyear);
        $firstweenum = date('W', $firstdayofyear);
        if ($firstweenum == 1) {
            $day = (1 - ($firstweekday - 1)) + 7 * ($week - 1);
            $startdate = date('Y-m-d', mktime(0, 0, 0, 1, $day, $year));
            $enddate = date('Y-m-d', mktime(0, 0, 0, 1, $day + 6, $year));
        } else {
            $day = (9 - $firstweekday) + 7 * ($week - 1);
            $startdate = date('Y-m-d', mktime(0, 0, 0, 1, $day, $year));
            $enddate = date('Y-m-d', mktime(0, 0, 0, 1, $day + 6, $year));
        }

        return array($startdate, $enddate);
    }

    private function getMonthDate($year, $month) {
        $month = sprintf("%02d", intval($month));
        $year = str_pad(intval($year), 4, "0", STR_PAD_RIGHT);
        $month > 12 || $month < 1 ? $month = 1 : $month = $month;
        $firstday = strtotime($year . $month . "01000000");
        $firstdaystr = date("Y-m-01", $firstday);
        $lastday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
        return array(date('Y-m-d', $firstday), date('Y-m-d', $lastday));
    }

}