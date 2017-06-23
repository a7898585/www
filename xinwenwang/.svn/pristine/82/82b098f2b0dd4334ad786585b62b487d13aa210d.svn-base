<?php

namespace Admin\Controller;

class UsersController extends AdminCommonController {
    public function _initialize() {
        parent::_initialize();
        $this->assign('nav','pro');
    }
    final public function upload_photo() {
        $temp = uploadPhoto($_FILES['file']);
        $this->ajaxReturn($temp);
    }
    final public function index(){
        $where = array();
        $name = I('get.name','','trim');
        if($name){
            $where['username']=array('like',$name.'%');
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Users');
        $list = $conn->where($where)->order('add_time desc')->page($page['pageNum'], $page['numPerPage'])
            ->select();
        $page['totalCount'] =$conn->where($where)->count();
        $pager = showPage($page['totalCount'],$page['numPerPage']);
        $this->assign('list',$list);
        $this->assign('pager',$pager);
        $this->assign('page',$page);
        $this->display();
    }
    final public function info(){
        $id = I('get.id');
        if(IS_POST){
            $data = array(
                'username'=>I('post.username'),
                'email'=>I('post.email'),
                'singn'=>I('post.singn','','trim'),
                'head_pic'=>I('post.head_pic'),
                'status'=>I('post.status'),
            );
            $password = I('post.password');
            if($password){
                $data['password'] = md5($password);
            }
            if($id){
                $temp = M('Users')->where(array('id'=>$id))->save($data);
            }else{
                $data['add_time'] = time();
                $temp = M('Users')->add($data);
            }
            if(!$temp&&M('Users')->getDbError()){
                $this->error('操作失败!');
            }else{
                $this->success('操作成功!');
            }
            exit;
        }
        if($id){
            $info = M('Users')->where(array('id'=>$id))->find();
        }else{
            $info['status'] = '1';
        }
        $this->assign('info',$info);
        $this->display();
    }


}