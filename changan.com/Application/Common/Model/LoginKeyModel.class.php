<?php

namespace Common\Model;

use Think\Model;

final class LoginKeyModel extends Model {
    public function addData($uid){

        $data = array(
            'uid'=>$uid,
            'login_key'=>md5($uid+"_"+time()),
            'add_time'=>time(),
            'expiration_time'=>time()+60*3,
        );
        $temp = $this->add($data);
        if($temp) return $data['login_key'];
    }
    public function checkData($login_key){
        $temp = $this->where(array('login_key'=>$login_key))->find();
        return $temp;

    }

}

?>