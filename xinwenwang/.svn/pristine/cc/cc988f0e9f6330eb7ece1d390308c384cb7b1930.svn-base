<?php

namespace Common\Model;
use Org\Util\String;
use Think\Model;

final class NewsModel extends Model {

    /**
     * 资讯列表
     * @param $area_id 地域
     * @param $class_type 分类
     * @param int $limit
     * @return mixed
     */
    final public function getList($type_id,$page=1, $limit=20) {

        $mkey = "newsList_".$type_id.'_'.$page.'_'.$limit;
        $result = S($mkey);
//        if($result===false)
        {
            $type_id = intval($type_id);
            $limit = intval($limit);
            $where = array();
            switch($type_id){
                case C('TUIJIAN_TYPE'):
//                    $where['is_tuijian'] = '1';
                    break;
                case C('HOT_TYPE'):
//                    $where['is_hot'] = '1';
                    break;
                default:
                    $where['type_id'] = $type_id;
                    break;
            }
            $count = $this->where($where)->count();
            if($count>0){
                $list = $this->field('id,title,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,add_time')->where($where)->page($page,$limit)->select();

                foreach($list as &$item){
                    $item['url'] = 'http://port.xinwenwang.com/show/index/id/'.$item['id'];
//                    unset($item['id']);
                    if($item['img_list']){
                        $item['img_list'] = unserialize($item['img_list']);
                    }else{
                        $item['img_list'] = array();
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

    final public function getWhereList($where,$page=1, $limit=20,$order="id desc") {
            $count = $this->where($where)->count();
            if($count>0){
                $list = $this->field('id,title,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,good_sum,bad_sum,comment_sum,collect_sum')->order($order)->where($where)->page($page,$limit)->select();
                if(count($list)){
                    foreach($list as &$item){
                        $item['url'] = C('URL_DOMAIN').'/r'.$item['id'].'/';
                        if($item['img_list']){
                            $item['img_list'] = json_decode($item['img_list']);
                        }else{
                            $item['img_list'] = array();
                        }
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

    final public function getXiaohuaList($where,$page=1, $limit=20) {
        $count = $this->where($where)->count();
        if($count>0){
            $list = $this->field('id,title,show_type,html,comment_sum,source_name,source_url,update_time,add_time')->where($where)->page($page,$limit)->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = C('URL_DOMAIN').'/r'.$item['id'].'/';
                    if($item['img_list']){
                        $item['img_list'] = json_decode($item['img_list']);
                    }else{
                        $item['img_list'] = array();
                    }
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
    final public function getCollectList($uid,$page=1, $limit=20) {
        $where  = array();
        $where['xwuc.uid'] = $uid;
        $count = M('UsersCollect xwuc')->join('LEFT JOIN xw_news xwn ON xwn.id=xwuc.news_id')->where($where)->count();

        if($count>0){
            $list = M('UsersCollect xwuc')->join('LEFT JOIN xw_news xwn ON xwn.id=xwuc.news_id')->field('xwn.id,xwn.title,xwn.img_list,xwn.show_type,xwn.is_hot,xwn.is_new,xwn.is_tuijian,xwn.comment_sum,xwn.source_name,xwn.source_url,xwuc.add_time as update_time')
                ->where($where)->page($page,$limit)->select();
            if(count($list)){
                foreach($list as &$item){
                    foreach($list as &$item){
                        $item['url'] = C('URL_DOMAIN').'/r'.$item['id'].'/';
                        if($item['img_list']){
                            $item['img_list'] = json_decode($item['img_list']);
                        }else{
                            $item['img_list'] = array();
                        }
                        $item['show_time'] = fdate($item['update_time']);
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

    final public function voting(){

    }
    final public function hotComments($uid,$page=1, $limit=20){
        $where  = array();
        $where['xwnc.uid'] = $uid;
        $count = M('NewsComment xwnc')->where($where)->count();
        if($count>0){
            $list = M('NewsComment xwnc')->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                ->field('xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwnc.news_title,xwnc.news_source_url,xwnc.add_time as update_time')
                ->where($where)->page($page,$limit)->order('xwnc.add_time desc')->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = C('URL_DOMAIN').'/r'.$item['news_id'].'/';
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
    final public function getHotnew($where , $limit = 5 , $order = 'comment_sum desc'){
        $list = $this->where($where)->limit($limit)->order($order)->select();
        return !empty($list)?$list:false;
    }

    /**
     * 新闻详情
     * @param $id
     */
    final public function getInfo($id){
        $info = $this->where(array('id'=>$id))->find();
        if($info){
            $info['show_time']=fdate($info['update_time']);
            $info['url'] = C('URL_DOMAIN').'/r'.$id.'/';
            return $info;
        }

    }
    final public function getCommentsByNewsId($news_id,$page,$limit,$order='xwnc.add_time desc'){
        $where  = array();
        $where['xwnc.news_id'] = $news_id;
        $count = M('NewsComment xwnc')->where($where)->count();

        if($count>0){
            $list = M('NewsComment xwnc')->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwnc.news_title,xwnc.news_source_url,xwnc.add_time as update_time,xwnc.ip')
                ->where($where)->page($page,$limit)->order($order)->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = C('URL_DOMAIN').'/r'.$item['news_id'].'/';
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
    final public function addComment($uid,$content,$news_id){
        $news_info = $this->getInfo($news_id);
        $data = array(
            'news_id'=>$news_id,
            'uid'=>$uid,
            'content'=>$content,
            'add_time'=>time(),
            'news_title'=>$news_info['title'],
            'news_source_url'=>$news_info['source_url']
        );
        $temp = M('NewsComment')->add($data);
        if($temp){
            $this->updCommentSum($news_info['id']);
            return $temp;
        }
        return false;
    }
    final public function updates($where=array(),$page,$limit,$order='xwnc.add_time desc'){

        $count = M('NewsComment xwnc')->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
            ->where($where)->count();

        if($count>0){
            $list = M('NewsComment xwnc')
                ->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                ->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
                ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwn.title,xwn.img_list,xwn.show_type,xwnc.add_time as update_time,xwnc.ip')
                ->where($where)->page($page,$limit)->order($order)->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = C('URL_DOMAIN').'/r'.$item['news_id'].'/';
                    $item['show_time'] = fdate($item['update_time']);
                    $item['img_list'] = json_decode($item['img_list']);
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
    final public function getHotComments($where,$page=1,$limit=20,$order='xwnc.add_time desc'){
        $count = M('NewsComment xwnc')->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
            ->where($where)->count();

        if($count>0){
            $list = M('NewsComment xwnc')
                ->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                ->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
                ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwn.title,xwnc.add_time as update_time,xwnc.ip')
                ->where($where)->page($page,$limit)->order($order)->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = C('URL_DOMAIN').'/r'.$item['news_id'].'/';
                    $item['show_time'] = fdate($item['update_time']);
                    $item['img_list'] = json_decode($item['img_list']);
                    $item['head_pic'] = setUpUrl($item['head_pic']);
                    $item['good_sum']  = intval($item['goods']);
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

    /**
     * 订阅列表
     * @param $where
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     */
    final public function getDingYueList($where,$page=1, $limit=20,$order="id desc") {
        $count = $this->where($where)->count();
        if($count>0){
            $list = $this->field('id,title,img_list,html,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,good_sum,bad_sum,comment_sum,collect_sum')->order($order)->where($where)->page($page,$limit)->select();
            if(count($list)){
                foreach($list as &$item){
                    $item['url'] = C('URL_DOMAIN').'/r'.$item['id'].'/';
                    if($item['img_list']){
                        $item['img_list'] = json_decode($item['img_list']);
                    }else{
                        $item['img_list'] = array();
                    }
                    $item['show_time'] = fdate($item['update_time']);
                    $item['desc'] = String::msubstr(strip_tags($item['html']),0,200);
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

    /**
     * 更新点赞数
     * @param $news_id
     * @return bool
     */
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
    final public function usergb($id , $gb) {
        if(empty($id) || empty($gb)){
            return false;
        }
        if($gb == 'good'){
            $temp = M('news_comment')->where(array('id'=>$id))->setInc('goods');
        }else{
            $temp = M('news_comment')->where(array('id'=>$id))->setInc('bads');
        }
        return empty($temp)?false:true;
    }

    //游客评论
    final public function guestComment($news_id,$content){
        $news_info = $this->getInfo($news_id);
        $data = array(
            'news_id'=>$news_id,
            'content'=>$content,
            'add_time'=>time(),
            'news_title'=>$news_info['title'],
            'news_source_url'=>$news_info['source_url'],
            'ip'=>get_online_ip()
        );
        $temp = M('NewsComment')->add($data);
        if($temp){
            $this->updCommentSum($news_info['id'],$news_id);
            return $temp;
        }
        return false;
    }

}
?>