<?php
namespace Port\Controller;
use Port\Model\NewsModel;
use Port\Model\NewsVotingModel;

class NewsController extends PortCommonController {
    public function _initialize(){
        parent::_initialize();
    }
    final public function lists(){
        $page = $this->responce['page'];//默认1
        $limit = $this->responce['limit'];//默认20
        $type_id = $this->responce['type_id'];
        $order = "update_time desc";
        $news_model = new NewsModel();
        $where = array();
        if($type_id==1800||empty($type_id)){
            $where['type_id'] = array('in', $news_model->getIds());
        }else if($type_id==1801){
            $where['update_time']  = array('between',array(strtotime('-3 day'),time()));
            $order = 'good_sum desc';
        }else{
            $where['type_id'] = $type_id;
        }


        $res = $news_model->getWhereList($where,$page,$limit,$order);
        $resp['pic_list'] = array();
        if(!$page>1){
            $res['pic_list'] = array(
                array('url'=>'http://www.baidu.com','source_url'=>'http://www.baidu.com','id'=>123,'img_url'=>'http://pic.qiushibaike.com/system/pictures/9842/98427818/medium/app98427818.jpg'),
                array('url'=>'http://www.baidu.com','source_url'=>'http://www.baidu.com','id'=>123,'img_url'=>'http://pic.qiushibaike.com/system/pictures/9842/98427818/medium/app98427818.jpg'),
                array('url'=>'http://www.baidu.com','source_url'=>'http://www.baidu.com','id'=>123,'img_url'=>'http://pic.qiushibaike.com/system/pictures/9842/98427818/medium/app98427818.jpg'),
                array('url'=>'http://www.baidu.com','source_url'=>'http://www.baidu.com','id'=>123,'img_url'=>'http://pic.qiushibaike.com/system/pictures/9842/98427818/medium/app98427818.jpg'),
            );
        }
        responseString(1,$res,'');
    }

    /**
     *投票
     * param 1 0
     */
    final public function voting(){
        $param = $this->responce['param'];
        $news_id = $this->responce['news_id'];
        $nv = new NewsVotingModel();
        $temp = $nv->addVoting($this->uid,$news_id,$param);
        if($temp){
            responseString(1,array(),'');
        }else{
            responseString(0,array(),'删除失败');
        }
    }

    /**
     * 评论
     */
    final public function add_comment(){
        $news_id = $this->responce['news_id'];
        $uid = $this->uid;
        $contents = $this->responce['contents'];
        $news_title = $this->responce['news_title'];
        $news_source_url = $this->responce['news_source_url'];
        D('News')->updCommentSum($news_id);
        $data = array(
            'uid'=>$uid,
            'content'=>$contents,
            'news_id'=>$news_id,
            'news_title'=>$news_title,
            'news_source_url'=>$news_source_url,
            'add_time'=>time()
        );
        M('NewsComment')->data($data)->add();
        responseString('1',array(),'');
    }

    /**
     * 收藏
     */
    final public function add_collect(){
        $news_id = $this->responce['news_id'];
        $uid = $this->uid;
        M('UsersCollect')->data(array('news_id'=>$news_id,'uid'=>$uid,'add_time'=>time()))->add();
        responseString('1',array(),'');
    }
    final public function del_collect(){
        $news_id = $this->responce['news_id'];
        $uid = $this->uid;

        M('UsersCollect')->where(array('news_id'=>array('exp','in('.$news_id.')'),'uid'=>$uid))->delete();
        responseString('1',array(),'');
    }
    final public function my_collect(){
        $page = $this->responce['page'];//默认1
        $limit = $this->responce['limit'];//默认20
        $uid = $this->uid;
        $m  = new NewsModel();
        $list = $m->getCollectList($uid,$page,$limit);
        responseString('1',$list,'');
    }

    final public function hot_comment(){
        $nav = $this->responce['nav'];
        $uid = 0;
        if($nav==1){
            $uid = $this->uid;
        }
        $page = $this->responce['page'];
        $limit = $this->responce['limit'];

        $m = new NewsModel();
        $list = $m->hotComments($uid,$page,$limit);
        responseString('1',$list,'');
    }


}


