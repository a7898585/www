<?php

namespace Home\Model;
use Think\Model;

final class UsersModel extends Model {
    final public function login($u){
        $user = $this->where(array('email'=>$u))->find();
        return $user;
    }
    final public function checkUsername($username){
        return $this->where(array('username'=>$username))->count();
    }
    final public function checkEmail($email){
        return $this->where(array('email'=>$email))->count();
    }
    final public function register($username,$email,$password,$head_pic){
        $data = array(
            'username'=>$username,
            'email'=>$email,
            'password'=>md5($password),
            'head_pic'=>$head_pic,
            'add_time'=>time()
        );
        $temp = $this->data($data)->add();
        if(!$temp&&$this->getDbError()){
            return false;
        }else{
            return $temp;
        }
    }
    final public function getInfoById($id){
        return $this->where(array('id'=>$id))->find();
    }
//`comment_sum` smallint(6) NOT NULL DEFAULT '0',
//`good_sum` smallint(6) DEFAULT '0',
//`bad_sum` smallint(6) DEFAULT '0',
//`collect_sum` smallint(6) DEFAULT '0',

}
?>