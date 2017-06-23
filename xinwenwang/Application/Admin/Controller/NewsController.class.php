<?php

namespace Admin\Controller;

use Think\Controller;

class NewsController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'news');
    }

    final public function index() {
        $this->display();
    }

    final public function news_type() {
        $name = I('get.name');
        $where = array('is_city' => '0');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $list = M('NewsType')->order('order_id desc')->where($where)->select();
        $this->assign('list', $list);
        $this->display();
    }

    final public function news_subject() {
        $name = I('get.name');
        $where = array('is_city' => '0', 'is_subject' => 1);
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $list = M('NewsType')->order('order_id desc')->where($where)->select();
        $this->assign('list', $list);
        $this->display();
    }

    final public function delNewsType() {
        $result = D('NewsType')->where(array('id' => I('get.id')))->delete();
        if ($result) {
            $this->success('删除新闻分类成功。', I('server.HTTP_REFERER'));
        } else {
            $this->error('删除新闻分类失败，请重试。');
        }
    }

    final public function add() {
        $id = I('get.id');
        if (IS_POST) {
            $data = array(
                'title' => I('post.title'),
                'show_type' => I('post.show_type'),
                'order_id' => I('post.order_id'),
                'is_show' => I('post.is_show'),
                'is_default' => I('post.is_default'),
                'is_subject' => I('post.is_subject'),
            );
            if ($id) {
                $temp = M('NewsType')->where(array('id' => $id))->save($data);
            } else {
                $data['is_city'] = I('get.is_city');
                $data['add_time'] = time();
//                print_r($data);
                $temp = M('NewsType')->data($data)->add();
            }
            if (!$temp || M('NewsType')->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功');
            }
            exit;
        }
        $info = M('NewsType')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 城市新闻
     */
    final public function news_city_type() {
        $name = I('get.name');
        $where = array('is_city' => '1');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $list = M('NewsType')->order('order_id desc')->where($where)->select();
        $this->assign('list', $list);
        $this->display();
    }

    final public function delNewsCityType() {
        $result = D('NewsType')->where(array('id' => I('get.id')))->delete();
        if ($result) {
            $this->success('删除城市分类成功。', I('server.HTTP_REFERER'));
        } else {
            $this->error('删除城市分类失败，请重试。');
        }
    }

    /**
     * 新闻列表
     */
    final public function news_list() {
        $where = array();
        $title = I('get.title', '', 'trim');
        $show_type = I('get.show_type', '0', 'intval');
        $type_id = I('get.type_id', '0', 'intval');
        if ($title) {
            $where['xwn.title'] = array('like', $title . '%');
        }
        if ($show_type) {
            $where['xwn.show_type'] = $show_type;
        }
        if ($type_id) {
            $where['xwn.type_id'] = $type_id;
        }

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('News xwn');
        $list = $conn->field('xwn.id,xwn.type_id,xwn.title,xwn.show_type,xwn.comment_sum,xwn.good_sum,xwn.update_time,xwn.add_time,
        xwn.is_hot,xwn.is_new,xwn.is_tuijian,xwn.is_show')->where($where)
                ->order('xwn.update_time desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
//        echo $conn->getLastSql();
        $page['totalCount'] = $conn->where($where)->count();

        $pager = showPage($page['totalCount'], $page['numPerPage']);

        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);

        $type_list = M('NewsType')->where(array('is_show' => '1', 'is_city' => '0'))->select();
        $this->assign('type_list', $type_list);
        $this->display();
    }

    final public function delNews() {
        //日志2016年1月26日 18:27:06ydl
        $res = D('News')->where(array('id' => I('get.id')))->find();
        $logdata = array( 
            'uid'=>$_SESSION['admin_user']['id'],
            'uname'=>$_SESSION['admin_user']['username'],
            'status'=>3,
            'c_time'=>time(),
            'content'=>"管理员:".$_SESSION['admin_user']['username']."在".date("Y-m-d H:i:s", time())."删除 '".$res['title']."'");
        M()->table('xw_admin_log')->data($logdata)->add();
        
        $result = D('News')->where(array('id' => I('get.id')))->delete();
        if ($result) {
            D('NewsKey')->where(array('news_id' => I('get.id')))->delete();
            $this->success('删除新闻成功。', I('server.HTTP_REFERER'));
        } else {
            $this->error('删除新闻失败，请重试。');
        }
    }

    final public function news_info() {
        $id = I('get.id');
        if (IS_POST) {
            $type_id = explode('+', I('post.type_id'));
            $data = array(
                'title' => I('post.title'),
                'type_id' => $type_id[1],
                'subject_id' => I('post.subject_id'),
                'source_name' => I('post.source_name'),
                'source_url' => I('post.source_url'),
                'show_type' => I('post.show_type'),
                'keyword' => I('post.keyword'),
                'img_list' => json_encode(I('post.img_list', '', '')),
                'html' => I('post.html', '', 'htmlspecialchars_decode'),
                'is_hot' => I('post.is_hot'),
                'is_tuijian' => I('post.is_tuijian'),
                'update_time' => time()
            );
            $data['intro'] = msubstr(strip_tags($data['html']), 0, 120);
            $id = I('post.id');
            if ($id) {
                $temp = M('News')->where(array('id' => $id))->save($data);
                //日志2016年1月26日 18:27:06ydl
                $logdata = array( 
                    'uid'=>$_SESSION['admin_user']['id'],
                    'uname'=>$_SESSION['admin_user']['username'],
                    'status'=>2,
                    'c_time'=>time(),
                    'content'=>"管理员:".$_SESSION['admin_user']['username']."在".date("Y-m-d H:i:s", time())."修改 '".I('post.title')."'");
                M()->table('xw_admin_log')->data($logdata)->add();
            } else {
                $data['add_time'] = time();
                $temp = M('News')->data($data)->add();
                $data['id'] = $temp;
                addNewsKey($data);
                //日志2016年1月26日 18:27:06ydl
                $logdata = array( 
                    'uid'=>$_SESSION['admin_user']['id'],
                    'uname'=>$_SESSION['admin_user']['username'],
                    'status'=>1,
                    'c_time'=>time(),
                    'content'=>"管理员:".$_SESSION['admin_user']['username']."在".date("Y-m-d H:i:s", time())."增加 '".I('post.title')."'");
                M()->table('xw_admin_log')->data($logdata)->add();
            }
            if (!$temp && M('News')->getDbError()) {
                $this->error('操作失败');
            } else {
                if ($data['subject_id'] > 0) {
                    $ckey = 'subject_list_num5';
                    $fkey = "subject_newsList_first_" . $data['subject_id'];
                    $mkey = "subject_newsList_" . $data['subject_id'];
                    unsetMemcache($ckey);
                    unsetMemcache($fkey);
                    unsetMemcache($mkey);
                } else {
                    $key = 'index_list_n1_updatetime_desc';
                    unsetMemcache($key);
                    $fkey = 'news_list_' . $data['type_id'] . '_1_50_update_time_desc';
                    unsetMemcache($fkey);
                    $tkey = 'news_type_name_t' . $data['type_id'];
                    unsetMemcache($tkey);
                }
                $this->success('操作成功');
            }
            exit;
        }
        if ($id) {
            $info = M('News xwn')->join('LEFT JOIN xw_news_type xwnt ON xwnt.id=xwn.type_id')->field('xwn.*,xwnt.title as type_name')->where(array('xwn.id' => $id))->find();
            $info['img_list'] = json_decode($info['img_list']);
        }
        if (I('get.type_id')) {
            $info['type_id'] = I('get.type_id');
            $info['type_name'] = getTypeName(I('get.type_id'));
        }
        if (I('get.show_type')) {
            $info['show_type'] = I('get.show_type');
        }
        $this->assign('info', $info);
        $type_list = M('NewsType')->where(array('is_show' => '1', 'is_subject' => '0', 'is_city' => '0'))->select();
        $this->assign('type_list', $type_list);
        $this->assign('subject_type_list', M('NewsType')->where(array('is_subject' => '1', 'is_show' => '1', 'is_city' => '0'))->select());
        $this->assign('show_type', showTypes());
        $this->display();
    }

}