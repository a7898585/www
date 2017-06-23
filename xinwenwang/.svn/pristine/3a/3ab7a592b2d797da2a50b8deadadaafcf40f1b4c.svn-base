<?php

namespace Port\Model;
use Think\Model;

final class UsersModel extends Model {
    final public function getUidByLoginKey($login_key) {
        $uid = M('UsersLogin')->where(array('login_key'=>$login_key))->getField('uid');
        return $uid;
    }

    /**
     * 注册用户
     * @param $data
     */
    final public function register($username,$password,$email,$device_token){
        $data = array(
            'username'=>$username,
            'password'=>md5($password),
            'email'=>$email,
            'device_token'=>$device_token,
            'add_time'=>time()
        );
        return $this->add($data);
    }

    /**
     * 判断用户名是否存在
     * @param $username
     * @return mixed
     */
    final public function checkUsername($username){
        return  $this->where(array('username'=>$username))->count();
    }
    final public function checkEmail($email){
        return  $this->where(array('email'=>$email))->count();
    }


    /**
     * 登陆
     */
    final public function getInfoByName($username){
        return $this->where(array('username'=>$username))->find();
    }

    /**
     * 用户登陆key
     * @param $uid
     * @return mixed
     */
    final public function loginLog($uid,$login_type='1'){
//        M('UsersLogin')->where(array('uid'=>$uid,'login_type'=>$login_type))->delete();
        $login_key = md5($uid.rand(1,100));
        $data = array(
            'uid'=>$uid,
            'login_key'=>$login_key,
            'login_type'=>$login_type,
            'add_time'=>time()
        );
        $temp = M('UsersLogin')->add($data);
        if($temp){
            return $login_key;
        }else{
            return 0;
        }
    }
    final public function addCollect($uid,$news_id){
        $data = array(
            'uid'=>$uid,
            'news_id'=>$news_id,
            'add_time'=>time()
        );
        M('UsersCollect')->data($data)->add();
    }
    final public function myFans($uid,$page=1,$limit=20){
        $where  = array();
        $where['xwuf.uid'] = $uid;
        $count = M('UsersFans xwuf')->where($where)->count();
        if($count>0){
            $list = M('UsersFans xwuf')->join('LEFT JOIN xw_users xwu ON xwu.id=xwuf.fuid')
                ->field('xwu.id,xwu.username,xwu.head_pic,xwu.singn,xwuf.status')
                ->where($where)->page($page,$limit)->order('xwuf.add_time desc')->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['head_pic'] = setUpUrl($item['head_pic']);
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
    final public function hotFans($uid,$limit=15){
        $where['uid']  = array('eq' , $uid);
        $ids = M('UsersFans')->where($where)->getField('fuid',true);
        if($ids){
            $ids = implode(',', $ids);
            $ids = $ids.','.$uid;
        }
        $wheres = array();
        if($ids){
            $wheres['id'] = array('not in' , $ids);
        }

        $list = M('Users')->field('id as uid,username,head_pic,singn')->where($wheres)->order('RAND()')->limit($limit)->select();
        if(count($list)){
            foreach($list as &$item){
                $item['head_pic'] = setUpUrl($item['head_pic']);
            }
        }else{
            $list = array();
        }
        return $list;
    }

}
?>