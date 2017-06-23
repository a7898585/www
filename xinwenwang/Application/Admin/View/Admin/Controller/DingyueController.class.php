<?php
namespace Admin\Controller;

use Common\Model\ProTypeModel;

class DingyueController extends AdminCommonController {
    public function _initialize() {
        parent::_initialize();
        $this->assign('nav','dingyue');
    }
    final public function types() {
        $list = M('DingyueType')->select();
        $this->assign('list',$list);
        $this->display();
    }
    final public function add_type(){
        $id = I('get.id');
        if(IS_POST){
            $data = array(
                'name'=>I('post.name'),
                'order_id'=>I('post.order_id'),
                'add_time'=>time()
            );

            if($id){
                $temp = M('DingyueType')->where(array('id'=>$id))->save($data);;
            }else{
                $temp = M('DingyueType')->add($data);
            }
            if(!$temp&&M('DingyueType')->getDbError()){
                $this->error('操作失败');
            }else{
                $this->success('操作成功');
            }
            exit;
        }
        $info = M('DingyueType')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->display();
    }
    final public function lists(){
        $where = array();
        $name = I('get.name','','trim');
        $type_id = I('get.type_id','0','intval');
        if($name){
            $where['xwd.name']=array('like',$name.'%');
        }
        if($type_id){
            $where['xwd.type_id']= $type_id;
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Dingyue xwd');
        $list = $conn->join('LEFT JOIN xw_dingyue_type xwdt ON xwdt.id=xwd.type_id')
            ->field('xwd.id,xwd.type_id,xwd.name,xwd.is_tuijian,xwd.order_id,xwd.counts,xwd.is_show,xwdt.name as type_name')
            ->where($where)->order('xwd.update_time desc,xwd.id desc')->page($page['pageNum'], $page['numPerPage'])
            ->select();
        $page['totalCount'] =$conn->join('LEFT JOIN xw_dingyue_type xwdt ON xwdt.id=xwd.type_id')->where($where)->count();
        $pager = showPage($page['totalCount'],$page['numPerPage']);

        $this->assign('list',$list);
        $this->assign('pager',$pager);
        $this->assign('page',$page);

        $type_list = M('DingyueType')->order('order_id desc')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
    final public function add(){
        $id = I('get.id');
        if(IS_POST){
            $data = array(
                'name'=>I('post.name'),
                'type_id'=>I('post.type_id'),
                'url'=>I('post.url'),
                'pic'=>I('post.pic'),
                'desc'=>I('post.desc'),
                'is_tuijian'=>I('post.is_tuijian'),
                'order_id'=>I('post.order_id'),
                'counts'=>I('post.counts'),
                'is_show'=>I('post.is_show'),
                'counts'=>I('post.counts'),
                'update_time'=>time(),
            );
            if($id){
                $temp = M('Dingyue')->where(array('id'=>$id))->save($data);;
            }else{
                $data['add_time']=time();
                $temp = M('Dingyue')->add($data);
                M('DingyueType')->where(array('id'=>$data['type_id']))->save(array('counts'=>array('exp','counts+1')));
            }
            if(!$temp&&M('Dingyue')->getDbError()){
                $this->error('操作失败');
            }else{
                $this->success('操作成功');
            }
            exit;
        }
        $info = M('Dingyue')->where(array('id'=>$id))->find();
        $info['pic'] = setUpUrl($info['pic']);
        $this->assign('info',$info);
        $type_list = M('DingyueType')->order('order_id desc')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
}