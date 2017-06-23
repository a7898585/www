<?php
namespace Port\Controller;

use Think\Log;

class ChannelController extends PortCommonController {
    public function _initialize(){
        parent::_initialize();
    }
    final public function index(){
        responseString(1,12,12);
    }
    final public function lists(){
        $id = $this->uid;
        if($id){
            $ids = M('Users')->where(array('id'=>$id))->getField('channel_list');
        }
        if($ids){

            $where['id'] = array('in',$ids);
            $where['is_show'] = '1';
            $where['is_city'] = '0';
            $default = M('NewsType')->field('id,title,show_type,is_default,is_city,is_show')->where($where)->select();


            $wheres['id'] = array('not in',$ids);
            $wheres['is_city'] = '0';
            $wheres['is_show'] = '1';
            $other = M('NewsType')->field('id,title,show_type,is_default,is_city,is_show')->where($wheres)->select();

        }else{
            $types = M('NewsType')->field('id,title,show_type,is_default')->where(array('is_city'=>'0','is_show'=>'1'))->select();
            $default = array();
            $other = array();
            foreach($types as $item){
                $t =$item['is_default'];
                if($t=='1'){
                    $default[] = $item;
                }else{
                    $other[] = $item;
                }
            }
        }
       //var_dump($default);
        responseString(1,array('default_list'=>$default,'other_list'=>$other),'');
    }


    /**
     * 保存信息
     */
    final public function save(){
        $id = $this->uid;
        $data_list = $this->responce['data_list'];
        $data_list = json_decode($data_list,true);
        $ids = implode(',',$data_list);
        $data = array(
            'channel_list'=>$ids
        );
        $temp = M('Users')->data($data)->where(array('id'=>$id))->save();
        
        if(!$temp&&M('Users')->getDbError()){
            responseString(0,$data_list,'保存失败');
        }
        responseString(1,array(),'');
    }

}


