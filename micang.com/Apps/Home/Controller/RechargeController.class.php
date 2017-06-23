<?php
/**
 * RechargeController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-03
 */

namespace Home\Controller;

use Common\Extend\Alipay\Alipay;
use Think\Log;

class RechargeController extends HomeCommonController{
    /**
     * 支付宝异步通知接收接口
     */
    public function notify(){
        //TODO 代码逻辑
        $partner = C('ALIPAY_CONFIG.PARTNER');
        $seller = C('ALIPAY_CONFIG.SELLER_EMAIL');
        $key = C('ALIPAY_CONFIG.KEY');
        $alipay = new Alipay($partner, $seller, $key);
        $result = $alipay->notify(I('post.'));
        Log::write(var_export(I('post.'), true), Log::INFO, '', C('LOG_PATH').'recharge'.date('Ymd').'.log');
        if (!$result)   exit('fail');
        if (I('post.trade_status') == 'TRADE_CLOSED'){
            $data['id'] = I('post.out_trade_no');
            $data['tid'] = I('post.trade_no');
            $data['status'] = '2';
            M('MembersRechargeDetail')->data($data)->save();
            exit('success');
        }
        if (I('post.trade_status')=='TRADE_FINISHED' || I('post.trade_status')=='TRADE_SUCCESS') {
            unset($result);
            //检查订单是否已处理过
            $detail = M('MembersRechargeDetail')->where(array('id'=>I('post.out_trade_no')))->find();
            if (!is_array($detail)) exit('fail');
            if (in_array($detail['status'], array('1', '2')))   exit('success');
            M('MembersRechargeDetail')->startTrans();
            //更新充值订单明细状态
            $data['id'] = I('post.out_trade_no');
            $data['tid'] = I('post.trade_no');
            $data['status'] = '1';
            $data['notify_time'] = I('post.gmt_payment');
            $data['buyer_email'] = I('post.buyer_email', '');
            $data['buyer_id'] = I('post.buyer_id', '');
            $result = M('MembersRechargeDetail')->data($data)->save();
            if (!$result){
                M('MembersRechargeDetail')->rollback();
                exit('fail');
            }
            //累加用户充值余额
            $inputMoney = I('post.total_fee')*100;
            $data['mid'] = $detail['mid'];
            $data['total_money'] = array('exp', 'total_money+'.$inputMoney);
            $data['recharge_money'] = array('exp', 'recharge_money+'.$inputMoney);
            $result = M('MembersMoney')->data($data)->save();
            if (!$result){
                M('MembersRechargeDetail')->rollback();
                exit('fail');
            }
            //确认下一级等级ID
            $memberInfo = M('Members')->where(array('id'=>$detail['mid']))->find();
            if (!is_array($memberInfo)){
                M('MembersRechargeDetail')->rollback();
                exit('fail');
            }
            if (!in_array($memberInfo['level'], array('level3','level9'))) {
                //计算累计充值金额
                unset($where);
                $where['mid'] = $detail['mid'];
                $where['status'] = '1';
                $totalRechargeMoney = M('MembersRechargeDetail')->where($where)->sum('money');
                //获取2,3级会员升级最低金额
                $levelMoney = M('MemberLevels')->where(array('id'=>array('IN', array('level2','level3'))))->getField('id,upgrade_money');
                $nextLevel = '';
                if ($totalRechargeMoney >= $levelMoney['level3']){
                    $nextLevel = 'level3';
                }elseif ($totalRechargeMoney >= $levelMoney['level2']){
                    $nextLevel = 'level2';
                }
                if ($nextLevel!='' && $memberInfo['level']!=$nextLevel){
                    //升级等级
                    unset($data, $result);
                    $data['id'] = $detail['mid'];
                    $data['level'] = $nextLevel;
                    $result = M('Members')->data($data)->save();
                    if (!$result) {
                        M('MembersRechargeDetail')->rollback();
                        exit('fail');
                    }
                }
            }
            M('MembersRechargeDetail')->commit();
            exit('success');
        }
    }
}