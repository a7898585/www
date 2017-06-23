<?php

/*
 * 定时执行
 * @Created on 2015/09/21
 * @Author Jansen<6206574@qq.com>
 *
 */
namespace Task\Controller;
use Think\Controller;
use Common\Extend\Whois;
class CronController extends TaskCommonController{
    /*
     * 检测拍卖结束
     */
    public function close_paimai(){
        $m = M("MembersDomainSales")->where(array('type'=>'2', 'end_time'=>array('lt', time())))->find();
        if($m){
            M()->startTrans();
            $result = DelDomainSales($m['id']);
            if($result && $m['buyer_mid']){
                //有人拍卖，生成一条交易日志
                $data['sale_id'] = $m['id'];
                $data['domain'] = $m['domain'];
                $data['from_mid'] = $m['mid'];
                $data['to_mid'] = $m['buyer_mid'];
                $data['money'] = $m['buyer_price'];
                $data['type'] = '2';
                $data['status'] = '0';
                $data['update_time'] = time();
                $data['memo'] = '';
                $data['lock_from'] = $m['lock_money'];
                $data['lock_to'] =Bond($m['buyer_price']);
                $result = M("DomainTradeLog")->data($data)->add();
                if($result){
                    M()->commit();
                    exit("saleid:{$m['id']}拍卖关闭成功");
                }
                M()->rollback();
                exit("saleid:{$m['id']}拍卖关闭失败");
            }else{
                //无人拍卖，域名重新标记为状态100
                $result = M('MembersDomains')->data(array('status'=>'100'))->where(array('domain'=>$m['domain']))->save();
                if (!$result) {
                    M('MembersDomains')->rollback();
                    return '流拍，域名标记状态100失败';
                }
                M()->commit();
                exit("saleid:{$m['id']}流拍");
            }
        }
        exit("none");
    }
    /*
     * 一口价到期了没卖掉，结束任务
     *
     */
    public function close_yikoujia(){
        $m = M("MembersDomainSales")->where(array('type'=>'1', 'end_time'=>array('lt', time())))->find();
        if($m){
            M()->startTrans();
            $result = DelDomainSales($m['id']);
            if($result){
                //域名状态标记会100
                $result=M("MembersDomains")->data(array('status'=>'100'))->where(array("id"=>$m['id']))->save();
                if(!$result){
                    M()->rollback();
                    exit("10");
                }
                M()->commit();
                exit("0");
            }
            M()->rollback();
            exit("15");
        }
        exit("30");
    }
    /*
     * 检测卖家超时，默认违约，账户没钱，默认通过
     * 3天
     */
    public function time_out_seller(){
        $where['status'] = '0';
        $where['update_time'] = array('lt', time() - 86400 * 3);
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
     //   echo M("DomainTradeLog")->getLastSql();exit();
        if(!$tradeDetail){
            exit('none');
        }
        switch($tradeDetail['type']){
            case '2': // 竞价
                M()->StartTrans();
                $result = WeiYue($tradeDetail['id'], 'from');
                if($result == '1'){
                    M()->commit();
                    exit("Log:{$tradeDetail['id']},卖家超时违约");
                }
                M()->rollback();
                if(strpos($result, '账户余额不足')){
                    // 卖家没钱，交易标记为等待买家确认
                    $result = M("DomainTradeLog")->where(array('id'=>$tradeDetail['id']))->data(array('status'=>'1'))->save();
                    if($result){
                        exit("Log:{$tradeDetail['id']},卖家超时违约,账户没钱，进入等待买家确认状态");
                    }
                    exit("系统出错");
                }
                exit();
            case '3': // 出价，默认卖家拒绝报价
                $result = M("DomainTradeLog")->where(array('id'=>$tradeDetail['id']))->data(array('status'=>'5'))->save();
                if($result){
                    exit("Log:{$tradeDetail['id']},卖家超时,默认拒绝报价");
                }
                exit("系统出错");
            default:
                exit();
        }
        exit('none');
    }
    /*
     * 检测买家超时，默认违约,扣除保证金
     * 3天
     */
    public function time_out_buyer(){
        $where['status'] = '1';
        $where['update_time'] = array('lt', time() - 86400 * 3);
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if(!$tradeDetail){
            exit('none');
        }
        switch($tradeDetail['type']){
            case '2': // 竞价，默认扣除冻结款
                M()->StartTrans();
                $result = WeiYue($tradeDetail['id'], 'to');
                if($result == '1'){
                    M()->commit();
                    exit("Log:{$tradeDetail['id']},买家超时违约");
                }
                M()->rollback();
                exit('系统出错');
                break;
            case '3': // 出价，默认买家反悔了，不想买，不用扣款
                $result = M("DomainTradeLog")->where(array('id'=>$tradeDetail['id']))->data(array('status'=>'3'))->save();
                if($result){
                    exit("Log:{$tradeDetail['id']},买家超时,默认反悔,不扣款");
                }
                exit("系统出错");
                break;
            default:
                exit();
        }
    }
    /*
     * 检测 等待域名接入,没有接入的,扣除冻结款
     * 4天
     */
    public function time_out_transfer(){
        $where['status'] = '6';
        $where['update_time'] = array('lt', time() - 86400 * 4);
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if(!$tradeDetail){
            exit('10');
        }
        M()->StartTrans();
        $result = WeiYue($tradeDetail['id'], 'from');
        if($result == '1'){
            M()->commit();
            exit("Log:{$tradeDetail['id']},卖家域名转入超时违约");
        }
        M()->rollback();
        exit('10');
    }
    /*
     * 检测 买家付款超时
     * 3天
     */
    public function time_out_pay(){
        $where['status'] = '7';
        $where['update_time'] = array('lt', time() - 86400 * 3);
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if(!$tradeDetail){
            exit('none');
        }
        M()->StartTrans();
        $result = WeiYue($tradeDetail['id'], 'to');
        if($result == '1'){
            M()->commit();
            exit("Log:{$tradeDetail['id']},卖家域名转入超时违约");
        }
        M()->rollback();
        exit('买家超时');
    }
    
    /*
     * 新添加的域名，更新whois
     */
    public function whois(){
        $domainDetail=M("MembersDomains")->where(array('is_transfer'=>'-1'))->find();
        if(!is_array($domainDetail))    exit('finish.');
        $whoisApi = new Whois();
        $whoisInfo = $whoisApi->query($domainDetail['domain']);
        if ($whoisInfo['status'] == 404){//没注册，删除
            //todo 采用删除，不保险
            M("MembersDomains")->where(array('id'=>$domainDetail['id']))->delete();
            exit('deleted for '.$domainDetail['domain']);
        }
        if ($whoisInfo['status'] == 200){
            M()->startTrans();
            $data['register_time'] = $whoisInfo['data']['com']['reg_time'];
            $data['expire_time'] = $whoisInfo['data']['com']['exp_time'];
            $data['registrar'] = 'other';
            $data['is_transfer'] = '0'; //未接入
            $result = M("MembersDomains")->where(array('id'=>$domainDetail['id']))->data($data)->save();
            if (!$result){
                M()->rollback();
                exit('error(1) for '.$domainDetail['domain']);
            }
            unset($data, $result);
            $data['mid'] = $domainDetail['mid'];
            $data['domain'] = $domainDetail['domain'];
            $data['email'] = $whoisInfo['data']['reg']['email'];
            $data['status'] = '0';
            $result = M("DomainEmailVerify")->data($data)->add();
            if (!$result){
                M()->rollback();
                exit('error(2) for '.$domainDetail['domain']);
            }
            M()->commit();
            exit('success for '.$domainDetail['domain']);
        }
        exit('whois failed for '.$domainDetail['domain']);
    }
}



