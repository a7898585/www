<?php

namespace Admin\Controller;

use Think\Upload;

class AppController extends AdminCommonController{
    public function _initialize() {
        parent::_initialize();
    }
    final public function lists() {
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $where = array();
        $list = M('AppFiles')->where($where)->order('add_time desc')
            ->page($page['pageNum'], $page['numPerPage'])
            ->select();
        $page['totalCount'] = M('AppFiles')->where($where)->count();

        $pager = showPage($page['totalCount'],$page['numPerPage']);

        $this->assign('list',$list);
        $this->assign('pager',$pager);
        $this->assign('page',$page);
        $this->display();
    }
    final public function info(){
        $id = I('get.id');
        if(IS_POST){
            $id = I('post.id');
            $data = array(
                'title'=>I('post.title'),
                'channel'=>I('post.channel'),
                'version'=>I('post.version'),
                'code'=>I('post.code'),
                'contents'=>I('post.contents'),
                'status_id'=>I('post.status_id'),
                'add_time'=>time()
            );
            $file_url = '';
            if($_FILES['file_url']){
                $file  = $this->upload_file($_FILES['file_url']);
                if($file['code']==200){
                    $data['file_url'] = $file['file_url'];
                }
            }
            if($id){
                $temp = M('AppFiles')->data($data)->where(array('id'=>$id))->save();
            }else{
                $temp = M('AppFiles')->data($data)->add();
            }
            if(!$temp&&M('AppFiles')->getDbError()){
                $this->error('操作失败');
            }else{
                $this->success('操作成功');
            }
            exit;
        }
        $info = M('AppFiles')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->display();
    }
    private function upload_file($file) {
        $root_path = './Uploads/';
        $config = array(
            'rootPath' => $root_path, //保存根路径
            'exts' => array(), //允许上传的文件后缀
            'saveName' => '', //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'replace' => true,
            'driver'=>'LOCAL'
        );
        $mm = new Upload($config);
        $m = $mm->uploadOne($file);
        $error = $mm->getError();
        if($error){
            return array('code'=>201,'error'=>$error);
        }else{
            $file_url = '/Uploads/'.$m['savepath'].$m['savename'];
            return array('code'=>200,'file_url'=>$file_url);
        }
    }
}