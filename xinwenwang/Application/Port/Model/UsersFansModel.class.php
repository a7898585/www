<?php

namespace Port\Model;
use Think\Model;

final class UsersFansCollectModel extends Model {
    /**
     * 关注
     * @param mixed|string $uid
     * @param array $fuid
     */
    final public function add($uid,$fuid){
        $data = array(
            'uid'=>$uid,
            'fuid'=>$fuid,
            'status'=>'1',
            'add_time'=>time()
        );
        $this->data($data)->add();
    }

    /**
     * 取消关注
     * @param $uid
     * @param $fuid
     * @return mixed
     */
    final public function del($uid,$fuid){
        return $this->where(array('uid'=>$uid,'fuid'=>$fuid))->delete();
    }

    final public function getLists($uid,$page=1,$limit=20){
        $where = array('uid'=>$uid);
        $count = $this->where(array($where))->count();
        $list = $this->where($where)->page($page,$limit)->select();
    }
}
?>