<?php

namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller {

    final public function upload_photo() {
        $temp = uploadPhoto($_FILES['file']);
        $this->ajaxReturn($temp);
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
    final public function login(){
        if(IS_POST){
            $username = I('post.username','','trim');
            $password = I('post.password','','md5');
            $info = M('Admin')->where(array('username'=>$username))->find();
            if(!$info){
                $this->error('用户不存在!');
            }
            if($info['password']!=$password){
                $this->error('密码不存在');
            }
            session('admin_user',$info);
            $this->success('登录成功!!!','/');
            exit;
        }
        $this->display();
    }
    final public function login_out(){
        session('admin_user',null);
        $this->error('注销成功');
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