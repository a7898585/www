<?php

namespace Mobile\Model;
use Think\Model;

final class NewsModel extends Model {


    final public function getList($where,$start=0,$count=10,$order='add_time desc',$is_page=0){
        $where['is_hide'] = '1';
        $list = $this->where($where)->page($start, $count)->order($order)->select();
        if($is_page){
            $totalRow = $this->where($where)->count();
            $pager = showMobilePage($totalRow,$count);
            return array('list'=>$list,'pager'=>$pager);
        }else{
            return $list;
        }
    }
    

}

?>