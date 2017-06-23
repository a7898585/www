<?php
/**
 * DomainWhoisQueueModel.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-18
 */

namespace Common\Model;

use Think\Model;

class DomainWhoisQueueModel extends Model{
    //入队列
    public function push($domain, $template, $registrar){
        $data['domain'] = $domain;
        $data['template'] = $template;
        $data['add_time'] = time();
        $data['status'] = '0';
        $data['registrar'] = $registrar;
        $result = $this->data($data)->add();
        return $result?true:false;
    }
    //取队列
    public function get(){
        $where['status'] = '0';
        $order['add_time'] = 'ASC';
        $queueInfo = $this->where($where)->order($order)->find();
        if (!is_array($queueInfo) || empty($queueInfo)){
            return false;
        }
        $data['id'] = $queueInfo['id'];
        $data['status'] = '1';
        $result = $this->data($data)->save();
        return $result?$queueInfo:false;
    }
    //出队列
    public function pop($id){
        $result = $this->where(array('id'=>$id))->delete();
        return $result?true:false;
    }
}