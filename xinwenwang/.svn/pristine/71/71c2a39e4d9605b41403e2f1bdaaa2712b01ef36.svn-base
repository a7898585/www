<?php

namespace Port\Model;
use Think\Model;

final class DingyueModel extends Model {
    /**
     * 订阅列表
     * @return array|mixed
     */
    final public function getShowList(){
        $mkey = 'dingyue_show_list';
        $result = S($mkey);
        if($result==false)
        {
            $type_list = M('DingyueType')->field('id,name')->order('order_id desc')->select();
            $temp_list = M('Dingyue')->where(array('is_show'=>'1'))->field('id,type_id,name,pic,desc,is_tuijian,counts')->order('order_id desc')->select();
            $list = array();
            foreach($temp_list as $item){
                $temp = $item['is_tuijian'];
                $type_id = $item['type_id'];
                unset($item['is_tuijian'],$item['type_id']);
                $item['is_checked'] = 0;
                if($temp=='1'){
                    $list['1000'][]=$item;
                }else{
                    $list[$type_id][]=$item;
                }
                $item['counts'] = $item['counts']." 订阅量";
            }
            $result = array('type_list'=>$type_list,'data_list'=>$list);
            S($mkey,$result);
        }
        return $result;
    }

    /**
     * 检测是否订阅
     * @param $uid
     * @param $sid
     */
    final public function checkDingyue($uid,$sid){
        return $this->where(array('uid'=>$uid,'sid'=>$sid))->count();
    }

    /**
     * 查找是否有数据
     * @param $name
     * @return mixed
     */
    final public function getInfoByName($name){
        $key = 'dingyue_'.$name;
        $result = S($key);
        if($result==false){
            $result = $this->where(array('name'=>$name))->getField('id');
            if(!$result){
                $result = $this->add(array('name'=>$name));
            }
            S($key,$result);
        }
        return $result;
    }

}
?>