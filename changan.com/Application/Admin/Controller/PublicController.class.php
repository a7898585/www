<?php

namespace Admin\Controller;
use Think\Controller;
use Common\Model\AreaModel;

class PublicController extends Controller {

    final public function login(){
//        M('Admin')->data(array('username'=>'changan','password'=>md5('changan@258'),'add_time'=>time()))->add();
        if(IS_POST){
            $username = I('post.username');
            $password = I('post.password','','md5');
            $user = M('Admin')->where(array('username'=>$username))->find();
            if(!$user){
                $this->error('用户名不存在');
            }
            if($user['password']!=$password){
                $this->error('用户名密码不正确');
            }
            M('Admin')->data(array('last_login_time'=>time(),'id'=>$user['id']))->save();
            session('admin',$user);
            $this->success('登录成功!!!跳转中...','/');
            exit;
        }
        $this->display();
    }
    final public function login_out(){
        session('admin',null);
        $this->success('退出成功!!!跳转中...','/');
    }

    final public function upload_photo() {
        $temp = uploadPhoto($_FILES['file']);
        $this->ajaxReturn($temp);
    }
    final public function area(){
        $areaid=I('get.areaid');
        if($areaid=='0'){
            return false;
        }
        $memcache_key = 'area_'.$areaid;
        $result = S($memcache_key);
        // if($result===false)
        {
            $am = new AreaModel();
            $result = $am->city($areaid);
            if(is_array($result)){
                $result = json_encode($result);
                S($memcache_key,$result);
            }
        }
        echo $result;
        exit;
    }

    final public function autoCompany(){
        $query = I('get.query');
        $list = D('Company')->getAllRow(array('name' => array('like', '%' . $query . '%')), 'id,name');
        $data_all = array();
        foreach ($list as $item) {
            $data_all[] = array('value' => $item['name'], 'data' => $item['id']);
        }
        $array = array();
        $array['query'] = $query;
        $array['suggestions'] = $data_all;
        echo json_encode($array);
        exit;
    }
    final public function upload_img() {
        $temp = uploadPhoto($_FILES['imgFile']);
        $data = array();
        if($temp['status']==1){
            $data =array('error' => 0, 'url' => setUpUrl($temp['url']));
        }
        $this->ajaxReturn($data);
    }
}