<?php
/**
 * MembersDomainsModel.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-18
 */

namespace Common\Model;


use Think\Model;

class MembersDomainsModel extends Model{
    /*
     * 检查域名是否属于该用户
     */
    public function CheckMidDomain($mid,$domain){
        $where['mid']=$mid;
        $where['domain']=$domain;
        $result=$this->where($where)->find();
        if($result)
            return true;
        return false;
    }
    /*
     * 域名详情
     */
    public function Detail($id){
        return $this->find($id);
    }
    /*
     * 添加域名
     */
    public function addDomain($data){
        return $this->data($data)->add();
    }
}