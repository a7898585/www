<?php
/**
 * MembersDomainSalesModel.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-18
 */

namespace Common\Model;


use Think\Model;
use Common\Extend\Domain;
class MembersDomainSalesModel extends Model{
    /*
     * 域名添加出售
     */
    public function AddSale($data){
        $check=$this->where(array('domain'=>$data['domain']))->find();
        if($check){
            return false;
        }
        $data['length']=Domain::getDomainNameLengthForWord($data['domain']);
        $data['base']=Domain::getDomainName($data['domain']);
        $data['seller_price']=$data['seller_price']*100;
        return $this->data($data)->add();
    }
    /*
     * 下架操作
     */
    public function Del($domain){
        if (is_numeric($domain)) {
            $check = $this->find($domain);
        } else {
            $check = $this->where(array('domain' => $domain))->find();
        }
        //询价情况，可以直接下架，所有的询价直接驳回
        if($check['type']=='3'){
           $where['sale_id']=$check['id'];
           $where['status']='0';
           M("DomainTradeLog")->data(array('status'=>'5'))->where($where)->Save();
        }elseif($check['times']>0){
            return "域名已经有人出价，不能下架";
        }
        $r=$this->where(array('id'=>$check['id'],'mid'=>session('MEMBERINFO.id')))->delete();
        if($r){
            M("MembersDomainNotice")->where(array('sale_id' => $check['id']))->delete();
            $result=M("MembersDomains")->data(array('status'=>'100'))->where(array('domain'=>$check['domain']))->save();
            if(!$result){
                return "系统出错，请稍后重试,error:002";
            }
        }
        return $r?'1':"域名下架失败";
    }
    /*
     * 更新出售信息
     */
    public function Update($data,$id){
        $check=$this->find($id);
        if($check=='2' && $check['times']>0){
            return "域名已经有人出价，不能更改";
        }
        $data['seller_price']=$data['seller_price']*100;
        $r=$this->where(array('id'=>$id,'mid'=>session('MEMBERINFO.id')))->data($data)->save();
        return $r?$r:"域名更新失败"; 
    }
}