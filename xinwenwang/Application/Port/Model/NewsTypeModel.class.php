<?php

namespace Port\Model;
use Think\Model;

final class NewsTypeModel extends Model {

    /**
     * 资讯列表
     * @param $area_id 地域
     * @param $class_type 分类
     * @param int $limit
     * @return mixed
     */
    final public function getList($type_id,$page=1, $limit=20) {
        $mkey = "newsList_".$type_id.'_'.$page.'_'.$limit;
        $result = S($mkey);
        if($result===false)
        {
            $type_id = intval($type_id);
            $page = max($page,1);
            $limit = intval($limit);
            $where = array();
            switch($type_id){
                case C('TUIJIAN_TYPE'):
                    $where['is_tuijian'] = array('eq','1');
                    break;
                case C('HOT_TYPE'):
                    $where['is_hot'] = array('eq','1');
                    break;
                default:
                    $where['type_id'] = $type_id;
                    break;
            }
            $count = $this->where($where)->count();
            $list = $this->field('id,title,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time')->where($where)->page($page,$limit)->select();
            if($count){
                foreach($list as &$item){
                    $item['url'] = 'http://m.xinwenwang.com/detail/'.$item['id'];
                    unset($item['id']);
                }
                $result =array('total'=>$count,'data_list'=>$list);
                S($mkey,$result);
            }

        }
        return $result;
    }
}
?>