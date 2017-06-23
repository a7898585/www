<?php
/**
 * RepairController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-07
 */

namespace Task\Controller;


use Common\Extend\Whois;

class RepairController extends TaskCommonController{
    /**
     * 修复注册域名中断的情况
     */
    public function register(){
        $queueInfo = M('MembersDomainBrokenQueue')->where(array('type'=>'register'))->order(array('create_time'=>'ASC'))->find();
        if (!is_array($queueInfo) || empty($queueInfo)) exit('finish.');
        //获取WHOIS信息
        $whois = new Whois();
        $whoisInfo = $whois->query($queueInfo['domain']);
        if ($whoisInfo['status'] == 500)    exit('error500_001');
        if ($whoisInfo['status'] == 404){//没有注册的情况
            M('MembersMoney')->startTrans();
            //返还费用
            $data['mid'] = $queueInfo['mid'];
            $data['total_money'] = array('exp', 'total_money+' . $queueInfo['money']);
            $data['trade_money'] = array('exp', 'trade_money+' . $queueInfo['money']);
            try{
                $result = M('MembersMoney')->data($data)->save();
            } finally {
                if (!$result) {
                    M('MembersMoney')->rollback();
                    exit('error404_001');
                }
            }
            //增加用户收入日志
            unset($where, $result, $data);
            $data['mid'] = $queueInfo['mid'];
            $data['type'] = 'system';
            $data['time'] = time();
            $data['money'] = $queueInfo['money'];
            $data['content'] = '域名：' . $queueInfo['domain'] . '注册失败，返还注册费用';
            try{
                $result = M('MembersIncomeDetail')->data($data)->add();
            } finally {
                if (!$result) {
                    M('MembersMoney')->rollback();
                    exit('error404_002');
                }
            }
            //将域名从域名列表中删除
            unset($where, $data, $result);
            $where['mid'] = $queueInfo['mid'];
            $where['domain'] = $queueInfo['domain'];
            $where['registrar'] = $queueInfo['registrar'];
            try{
                $result = M('MembersDomains')->where($where)->delete();
            } finally {
                if (!$result) {
                    M('MembersMoney')->rollback();
                    exit('error404_003');
                }
            }
            //清除平台在厂商的消费日志
            unset($where, $result, $data);
            $where['domain'] = $queueInfo['domain'];
            $where['type'] = 'register';
            $where['registrar'] = $queueInfo['registrar'];
            try{
                $result = M('PlatformConsumeDetail')->where($where)->delete();
            } finally {
                if (!$result) {
                    M('MembersMoney')->rollback();
                    exit('error404_004');
                }
            }
            //清除队列
            unset($where, $data, $result);
            $where['domain'] = $queueInfo['domain'];
            $where['mid'] = $queueInfo['mid'];
            try{
                $result = M('MembersDomainBrokenQueue')->where($where)->delete();
            } finally {
                if (!$result) {
                    M('MembersMoney')->rollback();
                    exit('error404_005');
                }
            }
            M('MembersMoney')->commit();
            exit('ok_'.$queueInfo['domain']);
        }else{
            M('MembersDomains')->startTrans();
            //变更域名状态
            unset($where, $data, $result);
            $where['mid'] = $queueInfo['mid'];
            $where['domain'] = $queueInfo['domain'];
            $where['registrar'] = $queueInfo['registrar'];
            $data['status'] = '100';
            try{
                $result = M('MembersDomains')->where($where)->data($data)->save();
            } finally {
                if (!$result) {
                    M('MembersDomains')->rollback();
                    exit('error200_001');
                }
            }
            //清除队列
            unset($where, $data, $result);
            $where['domain'] = $queueInfo['domain'];
            $where['mid'] = $queueInfo['mid'];
            try{
                $result = M('MembersDomainBrokenQueue')->where($where)->delete();
            } finally {
                if (!$result) {
                    M('MembersDomains')->rollback();
                    exit('error200_002');
                }
            }
            M('MembersMoney')->commit();
            exit('ok_'.$queueInfo['domain']);
        }
    }
}