<?php

namespace Mobile\Model;
use Think\Model;

final class UserModel extends Model {

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $page = 1, $limit = 20, $order = 'ctime desc') {
        return $this->where($where)->order($order)->page($page, $limit)->select();
    }
    final public function getDailirenList($where,$start=0,$count=10,$order='bxu.ctime desc',$is_page=0){
        $where['bxu.is_hide'] = '1';
        $list = $this->table('bx_user bxu')->where($where)
                ->field('bxu.id,bxu.name,bxu.nickname,bxu.tel,bxu.company_id,bxu.business_time')
            ->join('LEFT JOIN bx_user_photo bxup ON bxup.uid=bxu.id')->page($start, $count)->order($order)->select();
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