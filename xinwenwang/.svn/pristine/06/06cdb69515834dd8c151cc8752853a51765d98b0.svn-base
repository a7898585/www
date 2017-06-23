<?php
namespace Port\Controller;
use Port\Model\NewsModel;
use Port\Model\UsersModel;
use Think\Controller;
use Org\Util\String;
class ShowController extends PortCommonController {

    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        $id = I('get.id');
        $info = M('News')->where(array('id'=>$id))->find();
        $info['update_time'] = fdate($info['add_time']);
        $news = new NewsModel();
        $comments = $news->getCommentsByNewsId($id,1,10);
        $this->assign('comments',$comments);
        $uid = $this->uid;
        $is_collect = 0;
        if($uid){
            $is_collect = M('UsersCollect')->where(array('news_id'=>$id,'uid'=>$uid))->count();
        }
        $news_list = $news->getWhereList(array('dingyue_id'=>$info['dingyue_id']),1,5,'good_sum desc',false);
        foreach ($news_list['data_list'] as $key => $value) {
            $news_list['data_list'][$key]['title'] = String::msubstr($value['title'], 0,12);
        }
        $this->assign('news_list',$news_list);
        $this->assign('is_collect',$is_collect);
        $this->assign('info',$info);
        $this->display();
    }
    public function info(){
        $id = $this->responce['news_id'];
        $info = M('News')->where(array('id'=>$id))->find();

        $info['update_time'] = fdate($info['add_time']);
        $news = new NewsModel();
        $comments = $news->getCommentsByNewsId($id,1,10);
        $uid = $this->uid;
        $is_collect = 0;
        if($uid){
            $is_collect = M('UsersCollect')->where(array('news_id'=>$id,'uid'=>$uid))->count();
        }
        $news_list = $news->getWhereList(array('dingyue_id'=>$info['dingyue_id']),1,5,'good_sum desc',false);
        foreach ($news_list['data_list'] as $key => $value) {
            $news_list['data_list'][$key]['title'] = String::msubstr($value['title'], 0,12);
        }
        $info['dingyue'] = M('Dingyue')->where(array('id'=>$info['dingyue_id']))->find();
        $info['is_collect'] = $is_collect;
        $info['comments'] = $comments['data_list'];
        $info['news'] = $news_list['data_list'];
        $callback=$_GET['callback'];
//        print_r($info);exit;
        echo $callback."(".json_encode($info).")";
        exit;
    }

    final public function newsTool(){
        $id = I('post.id');
    }
    final public function activities(){

        $this->display();
    }
}


