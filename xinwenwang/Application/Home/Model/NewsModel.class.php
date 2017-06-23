<?php

namespace Home\Model;

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
    final public function getList($type_id, $page = 1, $limit = 20) {

        $mkey = "newsList_" . $type_id . '_' . $page . '_' . $limit;
        $result = S($mkey);
        if ($result === false) {
            $type_id = intval($type_id);
            $limit = intval($limit);
            $where = array();
            switch ($type_id) {
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
            if ($count > 0) {
                $list = $this->field('id,title,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,add_time')->where($where)->page($page, $limit)->select();

                foreach ($list as &$item) {
                    $item['url'] = 'http://port.xinwenwang.com/show/index/id/' . $item['id'];
//                    unset($item['id']);
                    if ($item['img_list']) {
                        $item['img_list'] = unserialize($item['img_list']);
                    } else {
                        $item['img_list'] = array();
                    }
                    $item['update_time'] = fdate($item['add_time']);
                }
                $result = array('total' => intval($count), 'data_list' => $list);
            } else {
                $result = array('total' => 0, 'data_list' => array());
            }
            S($mkey, $result);
        }
        return $result;
    }

    final public function getWhereList($where, $page = 1, $limit = 20, $order = "id desc") {
        $count = $this->where($where)->count();
        if ($count > 0) {
            $list = $this->field('id,title,intro,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,good_sum,bad_sum,collect_sum')->order($order)->where($where)->page($page, $limit)->select();

            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['id'] . '/';
                    if ($item['img_list']) {
                        $item['img_list'] = json_decode($item['img_list']);
                    } else {
                        $item['img_list'] = array();
                    }
                    $item['show_time'] = fdate($item['update_time']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    final public function getListLimit($where, $limit = 5, $order = "id desc") {

        $list = $this->field('id,title,intro,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,good_sum,bad_sum,collect_sum')->order($order)->where($where)->limit($limit)->select();
//        echo $this->getLastSql();
        if (count($list)) {
            foreach ($list as &$item) {
                $item['url'] = C('URL_DOMAIN') . '/r' . $item['id'] . '/';
                if ($item['img_list']) {
                    $item['img_list'] = json_decode($item['img_list']);
                } else {
                    $item['img_list'] = array();
                }
                $item['show_time'] = date('m-d', $item['update_time']);
            }
        } else {
            $list = array();
        }
        $result = array('total' => intval(count($list)), 'data_list' => $list);
        return $result;
    }

    final public function getXiaohuaList($where, $page = 1, $limit = 20) {
        $count = $this->where($where)->count();
        if ($count > 0) {
            $list = $this->field('id,title,show_type,html,comment_sum,source_name,source_url,update_time,add_time')->where($where)->page($page, $limit)->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['id'] . '/';
                    if ($item['img_list']) {
                        $item['img_list'] = json_decode($item['img_list']);
                    } else {
                        $item['img_list'] = array();
                    }
                    $item['show_time'] = fdate($item['update_time']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    final public function getCollectList($uid, $page = 1, $limit = 20) {
        $where = array();
        $where['xwuc.uid'] = $uid;
        $count = M('UsersCollect xwuc')->join('LEFT JOIN xw_news xwn ON xwn.id=xwuc.news_id')->where($where)->count();

        if ($count > 0) {
            $list = M('UsersCollect xwuc')->join('LEFT JOIN xw_news xwn ON xwn.id=xwuc.news_id')->field('xwn.id,xwn.title,xwn.img_list,xwn.show_type,xwn.is_hot,xwn.is_new,xwn.is_tuijian,xwn.comment_sum,xwn.source_name,xwn.source_url,xwuc.add_time as update_time')
                            ->where($where)->page($page, $limit)->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    foreach ($list as &$item) {
                        $item['url'] = C('URL_DOMAIN') . '/r' . $item['id'] . '/';
                        if ($item['img_list']) {
                            $item['img_list'] = json_decode($item['img_list']);
                        } else {
                            $item['img_list'] = array();
                        }
                        $item['show_time'] = fdate($item['update_time']);
                    }
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    final public function voting() {
        
    }

    final public function hotComments($uid, $page = 1, $limit = 20) {
        $where = array();
        $where['xwnc.uid'] = $uid;
        $count = M('NewsComment xwnc')->where($where)->count();
        if ($count > 0) {
            $list = M('NewsComment xwnc')->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                            ->field('xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwnc.news_title,xwnc.news_source_url,xwnc.add_time as update_time')
                            ->where($where)->page($page, $limit)->order('xwnc.add_time desc')->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['news_id'] . '/';
                    $item['update_time'] = fdate($item['update_time']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    final public function getHotnew($where, $limit = 5, $order = 'comment_sum desc') {
        $list = $this->where($where)->limit($limit)->order($order)->select();
        return !empty($list) ? $list : false;
    }

    /**
     * 新闻详情
     * @param $id
     */
    final public function getInfo($id) {
        $info = $this->where(array('id' => $id))->find();
        if ($info) {
            $info['show_time'] = fdate($info['update_time']);
            $info['url'] = C('URL_DOMAIN') . '/r' . $id . '/';
            if ($info['img_list']) {
                $info['img_list'] = json_decode($info['img_list']);
            } else {
                $info['img_list'] = array();
            }
            return $info;
        }
    }

    final public function getWhereInfo($where = array(), $order = "update_time desc") {
        $info = $this->where($where)->order($order)->find();
        if ($info) {
            $info['show_time'] = fdate($info['update_time']);
            $info['url'] = C('URL_DOMAIN') . '/r' . $info['id'] . '/';
            if ($info['img_list']) {
                $info['img_list'] = json_decode($info['img_list']);
            } else {
                $info['img_list'] = array();
            }
            return $info;
        }
    }

    final public function getCommentsByNewsId($news_id, $page, $limit, $order = 'xwnc.add_time desc') {
        $where = array();
        $where['xwnc.news_id'] = $news_id;
        $count = M('NewsComment xwnc')->where($where)->count();

        if ($count > 0) {
            $list = M('NewsComment xwnc')->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                            ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwnc.news_title,xwnc.news_source_url,xwnc.add_time as update_time,xwnc.ip')
                            ->where($where)->page($page, $limit)->order($order)->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['news_id'] . '/';
                    $item['show_time'] = fnewdate($item['update_time']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    /**
     * 添加评论
     * @param type $uid
     * @param type $content
     * @param type $news_id  新闻id
     * @param type $com_id   更贴的评论id
     * @return boolean
     */
    final public function addComment($uid, $content, $news_id, $com_id) {
        $news_info = $this->getInfo($news_id);
        $data = array(
            'news_id' => $news_id,
            'com_id' => $com_id,
            'uid' => $uid,
            'content' => $content,
            'add_time' => time(),
            'news_title' => $news_info['title'],
            'news_source_url' => $news_info['source_url']
        );
        $temp = M('NewsComment')->add($data);
        if ($temp) {
            if ($com_id > 0) {
                M('NewsComment')->where(array('id' => $com_id))->save(array('is_follow' => 1));
            }
            $this->updCommentSum($news_info['id']);
            return $temp;
        }
        return false;
    }

    final public function updates($where = array(), $page, $limit, $order = 'xwnc.add_time desc') {

        $count = M('NewsComment xwnc')->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
                        ->where($where)->count();

        if ($count > 0) {
            $list = M('NewsComment xwnc')
                            ->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                            ->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
                            ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwnc.bads,xwn.title,xwn.img_list,xwn.show_type,xwnc.add_time as update_time,xwnc.ip')
                            ->where($where)->page($page, $limit)->order($order)->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['news_id'] . '/';
                    $item['show_time'] = fdate($item['update_time']);
                    $item['img_list'] = json_decode($item['img_list']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    final public function getHotComments($where, $page = 1, $limit = 20, $order = 'xwnc.add_time desc') {
        $count = M('NewsComment xwnc')->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
                        ->where($where)->count();

        if ($count > 0) {
            $list = M('NewsComment xwnc')
                            ->join('LEFT JOIN xw_users xwu ON xwu.id=xwnc.uid')
                            ->join('LEFT JOIN xw_news xwn ON xwnc.news_id = xwn.id')
                            ->field('xwnc.id,xwnc.uid,xwnc.news_id,xwu.username,xwu.head_pic,xwnc.content,xwnc.goods,xwn.title,xwnc.add_time as update_time,xwnc.ip')
                            ->where($where)->page($page, $limit)->order($order)->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['news_id'] . '/';
                    $item['show_time'] = fdate($item['update_time']);
                    $item['img_list'] = json_decode($item['img_list']);
                    $item['head_pic'] = setUpUrl($item['head_pic']);
                    $item['good_sum'] = intval($item['goods']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
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
    final public function getDingYueList($where, $page = 1, $limit = 20, $order = "id desc") {
        $count = $this->where($where)->count();
        if ($count > 0) {
            $list = $this->field('id,title,intro,img_list,show_type,is_hot,is_new,is_tuijian,comment_sum,source_name,source_url,update_time,good_sum,bad_sum,comment_sum,collect_sum')->order($order)->where($where)->page($page, $limit)->select();
            if (count($list)) {
                foreach ($list as &$item) {
                    $item['url'] = C('URL_DOMAIN') . '/r' . $item['id'] . '/';
                    if ($item['img_list']) {
                        $item['img_list'] = json_decode($item['img_list']);
                    } else {
                        $item['img_list'] = array();
                    }
                    $item['show_time'] = fdate($item['update_time']);
                }
            } else {
                $list = array();
            }
            $result = array('total' => intval($count), 'data_list' => $list);
        } else {
            $result = array('total' => 0, 'data_list' => array());
        }
        return $result;
    }

    final public function updCollectSum($news_id, $type = 1) {
        if ($type) {
            $temp = $this->data(array('collect_sum' => array('exp', 'collect_sum+1')))->where(array('id' => $news_id))->save();
        } else {
            $temp = $this->data(array('collect_sum' => array('exp', 'collect_sum-1')))->where(array('id' => $news_id))->save();
        }
        if (!$temp && !$this->getDbError()) {
            return false;
        }
        return true;
    }

    final public function updCommentSum($news_id) {
        $temp = $this->data(array('comment_sum' => array('exp', 'comment_sum+1')))->where(array('id' => $news_id))->save();
        if (!$temp && !$this->getDbError()) {
            return false;
        }
        return true;
    }

    /**
     * 更新点赞数
     * @param $news_id
     * @return bool
     */
    final public function updGoodSum($news_id) {
        $temp = $this->data(array('good_sum' => array('exp', 'good_sum+1')))->where(array('id' => $news_id))->save();
        if (!$temp && !$this->getDbError()) {
            return false;
        }
        return true;
    }

    final public function updBadSum($news_id) {
        $temp = $this->data(array('bad_sum' => array('exp', 'bad_sum+1')))->where(array('id' => $news_id))->save();
        if (!$temp && !$this->getDbError()) {
            return false;
        }
        return true;
    }

    final public function usergb($id, $gb) {
        if (empty($id) || empty($gb)) {
            return false;
        }
        if ($gb == 'good') {
            $temp = M('news_comment')->where(array('id' => $id))->setInc('goods');
        } else {
            $temp = M('news_comment')->where(array('id' => $id))->setInc('bads');
        }
        return empty($temp) ? false : true;
    }

    //游客评论
    final public function guestComment($news_id, $content, $com_id) {
        $news_info = $this->getInfo($news_id);
        $data = array(
            'news_id' => $news_id,
            'com_id' => $com_id,
            'content' => $content,
            'add_time' => time(),
            'news_title' => $news_info['title'],
            'news_source_url' => $news_info['source_url'],
            'ip' => get_online_ip()
        );
        $temp = M('NewsComment')->add($data);
        if ($temp) {
            if ($com_id > 0) {
                M('NewsComment')->where(array('id' => $com_id))->save(array('is_follow' => 1));
            }
            $this->updCommentSum($news_info['id'], $news_id);
            return $temp;
        }
        return false;
    }

    final public function getReadList() {
        $read_list = M('SpidersBaidu')->where(array('is_status' => '0'))->limit(10)->select();
        foreach ($read_list as &$item) {
            $item['title'] = String::msubstr($item['title'], 0, 20, 'utf-8', false);
        }
        return $read_list;
    }

    final public function getRelateList($id, $limit = 10) {
        $read_list = $this->field('id,title')->where(array('type_id' => $id))->limit($limit)->select();
//        foreach ($read_list as &$item) {
//            $item['title'] = String::msubstr($item['title'], 0, 20, 'utf-8', false);
//        }
        return $read_list;
    }

    final public function getReComendList($info, $limit = 10) {
        $id = $info['id'];
        $k = array();
        $keyArr = M('NewsKey')->field('keyword')->where(array('news_id' => $id))->select();
        if (!empty($keyArr)) {
            foreach ($keyArr as $v) {
                $k[] = $v['keyword'];
            }
        } else {
//            addNewsKey($info);
        }
//        print_r($k);
        if (!empty($k)) {
            $read_list = M('NewsKey')->field('news_id as id,title')->where(array('keyword' => array('in', implode(',', $k)), 'news_id' => array('neq', $id)))->group('news_id')->order('addtime desc')->limit($limit)->select();
            return $read_list;
        }
        return false;
    }

    final public function getIds() {
        $str = F('shouye_ids');
        if ($str)
            return $str;
        $li = M('NewsType')->where(array('is_show' => '1', 'is_city' => '0'))->getField('id', true);
        $str = implode(',', $li);
        $str = str_replace(',1832', '', $str);
        $str = str_replace(',1828', '', $str);
        F('shouye_ids', $str);
        return $str;
    }

    public function getPreNextNews($id) {
        $where['is_show'] = '1';
        $prekey = 'news_info_pre_news_' . $id;
        $shang = getMemcache($prekey);
        if (!$shang) {
            $where['id'] = array('lt', $id);
            $shang = $this->field('id,title')->where($where)->order('id desc')->find();
            if ($shang) {
                $shang = "<a href=" . C('URL_DOMAIN') . "/r" . $shang['id'] . ">" . $shang['title'] . "</a>";
                setMemcache($prekey, $shang, '0');
            } else {
                $shang = "已经是第一篇了";
            }
        }
        $nextkey = 'news_info_next_news_' . $id;
        $xia = getMemcache($nextkey);
        if (!$xia) {
            $where['id'] = array('gt', $id);
            $xia = $this->field('id,title')->where($where)->order('id asc')->find();

            if ($xia) {
                $xia = "<a href=" . C('URL_DOMAIN') . "/r" . $xia['id'] . ">" . $xia['title'] . "</a>";
                setMemcache($nextkey, $xia, '0');
            } else {
                $xia = "已经是最后一篇了";
            }
        }
        return array('shang' => $shang, 'xia' => $xia);
    }

    /**
     * Sphinx 文章搜索
     * @param type $wordsString
     * @param type $field
     * @param type $page
     * @param type $pagenum
     * @return type
     */
    public function getSearchList($wordsString, $field = '*', $page = 1, $pagenum = 20, $order = 'update_time desc') {
        $offset = ($page - 1) * $pagenum;
        $sphinx = new \Common\Extend\Sphinx;
        $sphinx->SetServer(C('SPHINX_SERVER'), C('SPHINX_HOST'));
        $sphinx->SetMatchMode(SPH_MATCH_ANY); //使用多字段模式
        $sphinx->SetConnectTimeout(3);
        $sphinx->SetMaxQueryTime(2000);
        $sphinx->SetArrayResult(true);
//        $sphinx->SetFilter('title', array(1));
//     	$sphinx->SetFieldWeights(array("companyname" => 5, "introduce_short" =>2,"introduce"=>1));//设置字段权重
        $sphinx->SetLimits($offset, $pagenum, $max = 0, $cutoff = 0);
        $res = $sphinx->Query($wordsString, "xw_news");
        if ($sphinx->GetLastError()) {
            var_dump($sphinx->GetLastError());
        }
        $in = $sphinx->GetResultId($res);
        $sphinx->Close();
        $total = $res['total'];
        $plist = '';
        if (!empty($total)) {
            $where['id'] = array('exp', 'in(' . $in . ')');
            $plist = $this->where($where)->field($field)->order($order)->select();
            foreach ($plist as &$item) {
                $item['url'] = C('URL_DOMAIN') . '/r' . $item['id'] . '/';
                if ($item['img_list']) {
                    $item['img_list'] = json_decode($item['img_list']);
                } else {
                    $item['img_list'] = array();
                }
                $item['show_time'] = fdate($item['update_time']);
            }
        }
        return array('data' => $plist, 'total' => $total);
    }

}

?>