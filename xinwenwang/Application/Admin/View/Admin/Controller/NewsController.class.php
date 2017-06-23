<?php
namespace Admin\Controller;
use Think\Controller;


class NewsController extends AdminCommonController {
    public function _initialize() {
        parent::_initialize();
        $this->assign('nav','news');
    }
    final public function index() {
        $this->display();
    }
    final public function news_type(){
        $name = I('get.name');
        $where = array('is_city'=>'0');
        if($name){
            $where['title'] = array('like','%'.$name.'%');
        }
        $list = M('NewsType')->order('order_id desc')->where($where)->select();
        $this->assign('list',$list);
        $this->display();
    }
    final public function add(){
        $id= I('get.id');
        if(IS_POST){
            $data = array(
                'title'=>I('post.title'),
                'show_type'=>I('post.show_type'),
                'order_id'=>I('post.order_id'),
                'is_show'=>I('post.is_show'),
                'is_default'=>I('post.is_default'),
            );
            if($id){
                $temp = M('NewsType')->where(array('id'=>$id))->save($data);
            }else{
                $data['is_city']=I('get.is_city');
                $data['add_time'] = time();
                print_r($data);
                $temp = M('NewsType')->data($data)->add();
            }
            if(!$temp||M('NewsType')->getDbError()){
                $this->error('操作失败');
            }else{
                $this->success('操作成功');
            }
            exit;
        }
        $info = M('NewsType')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 城市新闻
     */
    final public function news_city_type(){
        $name = I('get.name');
        $where = array('is_city'=>'1');
        if($name){
            $where['title'] = array('like','%'.$name.'%');
        }
        $list = M('NewsType')->order('order_id desc')->where($where)->select();
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 新闻列表
     */
    final public function news_list(){
        $where = array();
        $title = I('get.title','','trim');
        $show_type = I('get.show_type','0','intval');
        $type_id = I('get.type_id','0','intval');
        if($title){
            $where['xwn.title']=array('like',$title.'%');
        }
        if($show_type){
            $where['xwn.show_type']=$show_type;
        }
        if($type_id){
            $where['xwn.type_id']= $type_id;
        }

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('News xwn');
        $list = $conn->join('LEFT JOIN xw_news_type xwnt ON xwnt.id=xwn.type_id')->field('xwn.id,xwn.type_id,xwn.title,xwn.show_type,xwn.comment_sum,xwn.good_sum,xwnt.title as type_name,xwn.update_time,xwn.add_time,
        xwn.is_hot,xwn.is_new,xwn.is_tuijian,xwn.is_show')->where($where)
            ->order('xwn.add_time desc')
            ->page($page['pageNum'], $page['numPerPage'])
            ->select();
        $page['totalCount'] =$conn->join('LEFT JOIN xw_news_type xwnt ON xwnt.id=xwn.type_id')->where($where)->count();

        $pager = showPage($page['totalCount'],$page['numPerPage']);

        $this->assign('list',$list);
        $this->assign('pager',$pager);
        $this->assign('page',$page);

        $type_list = M('NewsType')->where(array('is_show'=>'1','is_city'=>'0'))->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }

    final public function news_info(){
        $id = I('get.id');
        if(IS_POST){
            $type_id = explode('+',I('post.type_id'));
            $data = array(
                'title'=>I('post.title'),
                'type_id'=>$type_id[1],
                'source_name'=>I('post.source_name'),
                'source_url'=>I('post.source_url'),
                'show_type'=>I('post.show_type'),
                'img_list'=>json_encode(I('post.img_list','','')),
                'html'=>I('post.html','','htmlspecialchars_decode'),
                'is_hot'=>I('post.is_hot'),
                'is_tuijian'=>I('post.is_tuijian'),
                'update_time'=>time(),
            );
            if($id){
                $temp = M('News')->where(array('id'=>$id))->save($data);
            }else{
                $temp = M('News')->data($data)->add();
            }
            if(!$temp&&M('News')->getDbError()){
                $this->error('操作失败');
            }else{
                $this->success('操作成功');
            }
            exit;
        }
        $info = M('News xwn')->join('LEFT JOIN xw_news_type xwnt ON xwnt.id=xwn.type_id')->field('xwn.*,xwnt.title as type_name')->where(array('xwn.id'=>$id))->find();
        $info['img_list'] = json_decode($info['img_list']);
        $this->assign('info',$info);
        $type_list = M('NewsType')->where(array('is_show'=>'1'))->select();
        $this->assign('type_list',$type_list);
        $this->assign('show_type',showTypes());
        $this->display();
    }
}