<?php

namespace Port\Model;
use Think\Model;

final class NewsModel extends Model {

    /**
     * 资讯列表
     * @param $area_id 地域
     * @param $class_type 分类
     * @param int $limit
     * @return mixed
     */
    final public function getList($type_id,$page=1, $limit=20,$order="update_time desc") {

        $mkey = "newsList_".$type_id.'_'.$page.'_'.$limit;
        $result = S($mkey);

        //if($result===false)
        {
            $type_id = intval($type_id);
            $limit = intval($limit);
            $where = array();
            switch($type_id){
                case C('TUIJIAN_TYPE'):
                    $where['is_tuijian'] = array('eq','1');
                    break;
                case 1801:
                    $where['update_time']  = array('between',array(strtotime('-3 day'),time()));
                    $order = 'good_sum desc';
                    break;
                default:
                    $where['type_id'] = $type_id;
                    break;
            }
//            $where['img_status'] = '1';
            $count = $this->where($where)->count();

            if($count>0){
                $list = $this->field('id,title,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,add_time')->order('add_time desc')->where($where)->page($page,$limit)->order($order)->select();

                foreach($list as &$item){
                    $item['url'] = 'http://port.xinwenwang.com/show/index/id/'.$item['id'];
//                    unset($item['id']);
                    $item['img_list'] = json_decode($item['img_list']);
                    if(count($item['img_list'])==0){
                        $item['img_list'] = array();
                    }
                    foreach($item['img_list'] as &$img){
                        $img = setUpUrl($img);
                    }
                    $item['update_time'] = fdate($item['add_time']);
                }
                $result =array('total'=>intval($count),'data_list'=>$list);
            }else{
                $result =array('total'=>0,'data_list'=>array());
            }
            S($mkey,$result);
        }
        return $result;
    }

    final public function getWhereList($where,$page=1, $limit=20,$order='update_time desc' ,$show_time = true) {
    $count = $this->where($where)->count();
    if($count>0){
        $list = $this->field('id,title,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,add_time')->where($where)->page($page,$limit)->order($order)->select();
        if(count($list)){
            foreach($list as &$item){
                $item['url'] = 'http://port.xinwenwang.com/show/index/id/'.$item['id'];
                $item['img_list'] = json_decode($item['img_list']);
                if(count($item['img_list'])==0){
                    $item['img_list'] = array();
                }
                if(empty($show_time)){
                    if(time() - intval($time) < $time - mktime(0, 0, 0, 0, 0, date('Y'))){
                        $item['update_time'] = date( 'Y年m月d日',$item['add_time']);
                    }else{
                        $item['update_time'] = date( 'm月d日',$item['add_time']);
                    }
                }else{
                    $item['update_time'] = fdate($item['add_time']);
                }
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
    final public function getCollectList($uid,$page=1, $limit=20) {
        $where  = array();
        $where['xwuc.uid'] = $uid;
        $count = M('UsersCollect xwuc')->join('LEFT JOIN xw_news xwn ON xwn.id=xwuc.news_id')->where($where)->count();

        if($count>0){
            $list = M('UsersCollect xwuc')->join('LEFT JOIN xw_news xwn ON xwn.id=xwuc.news_id')->field('xwn.id,xwn.title,xwn.img_list,xwn.show_type,xwn.is_hot,xwn.is_new,xwn.is_tuijian,xwn.comment_sum,xwn.source_name,xwn.source_url,xwuc.add_time as update_time')
                ->where($where)->page($page,$limit)->order('xwuc.add_time desc')->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = 'http://port.xinwenwang.com/show/index/id/'.$item['id'];
                    if($item['img_list']){
                        $item['img_list'] = json_decode($item['img_list'],true);
                    }else{
                        $item['img_list'] = array();
                    }
                    $item['update_time'] = fdate($item['update_time']);
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

    final public function voting(){

    }
    final public function hotComments($uid,$page=1, $limit=20){
        $where  = array();
        if($uid)
        $where['xwnc.uid'] = $uid;
        $count = M('NewsComment xwnc')->where($where)->count();
        if($count>0){
            $list = M('NewsComment xwnc')->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                ->field('xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwnc.news_title,xwnc.news_source_url,xwnc.add_time as update_time')
                ->where($where)->page($page,$limit)->order('xwnc.add_time desc')->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = 'http://port.xinwenwang.com/show/index/id/'.$item['news_id'];
                    $item['head_pic'] = setUpUrl($item['head_pic']);
                    $item['update_time'] = fdate($item['update_time']);
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

    final public function getCommentsByNewsId($news_id,$page,$limit,$order='xwnc.add_time desc'){
        $where  = array();
        $where['xwnc.news_id'] = $news_id;
        $count = M('NewsComment xwnc')->where($where)->count();

        if($count>0){
            $list = M('NewsComment xwnc')->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwnc.news_title,xwnc.news_source_url,xwnc.add_time as update_time')
                ->where($where)->page($page,$count)->order($order)->select();
            if(count($list)){
                foreach($list as &$item){
                     $item['head_pic'] = setUpUrl($item['head_pic']);
//                    $item['url'] = C('URL_DOMAIN').'/r'.$item['news_id'].'/';
                    $item['show_time'] = fdate($item['update_time']);
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

    final public function updCollectSum($news_id,$type=1){
        if($type){
            $temp = $this->data(array('collect_sum'=>array('exp','collect_sum+1')))->where(array('id'=>$news_id))->save();
        }else{
            $temp = $this->data(array('collect_sum'=>array('exp','collect_sum-1')))->where(array('id'=>$news_id))->save();
        }
        if(!$temp&&!$this->getDbError()){
            return false;
        }
        return true;
    }
    final public function updCommentSum($news_id){
        $temp = $this->data(array('comment_sum'=>array('exp','comment_sum+1')))->where(array('id'=>$news_id))->save();
        if(!$temp&&!$this->getDbError()){
            return false;
        }
        return true;
    }
    final public function updGoodSum($news_id){
        $temp = $this->data(array('good_sum'=>array('exp','good_sum+1')))->where(array('id'=>$news_id))->save();
        if(!$temp&&!$this->getDbError()){
            return false;
        }
        return true;
    }
    final public function updBadSum($news_id){
        $temp = $this->data(array('bad_sum'=>array('exp','bad_sum+1')))->where(array('id'=>$news_id))->save();
        if(!$temp&&!$this->getDbError()){
            return false;
        }
        return true;
    }
    final public function getIds(){
        $str = F('shouye_ids');
        if($str) return $str;
        $li = M('NewsType')->where(array('is_show'=>'1','is_city'=>'0'))->getField('id',true);
        $str = implode(',',$li);
        $str = str_replace(',1832','',$str);
        F('shouye_ids',$str);
        return $str;
    }
}
?>