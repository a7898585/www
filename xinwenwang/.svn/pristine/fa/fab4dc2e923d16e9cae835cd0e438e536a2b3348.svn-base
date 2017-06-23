<?php

namespace Port\Model;
use Think\Model;

final class UsersMessageModel extends Model {

    final public function getList($uid,$page=1,$limit=20){
        $where  = array();
//        $where['is_root'] = '1';
        if($uid){
            $where['uid'] = $uid;
            $where['_logic'] = 'or';
        }
        $count = $this->where($where)->count();
        if($count>0){
            $list = $this->field('id,show_name,title,desc,pic_url,link_url,is_root,add_time,isread')
                ->where($where)->page($page,$limit)->order('add_time desc')->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['add_time'] = date("Y-m-d",$item['add_time']);
                }
            }else{
                $list = array();
            }
            $result =array('total'=>intval($count),'data_list'=>$list);
        }else{
            $result =array('total'=>0,'data_list'=>array());
        }
        return $result;


    }
}
?>