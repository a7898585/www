<?php

/**
 * NewsController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-20
 */

namespace Admin\Controller;

use Common\Extend\PageForAdmin;

class NewsController extends AdminCommonController {

    public function lists($title = null, $cid = null, $p = 1) {
        $where = array();
        if ($title) {
            $where['title'] = array('LIKE', '%' . $title . '%');
        }
        if ($cid) {
            $where['cid'] = $cid;
        }
        $total = M('News')->where($where)->count();
        $page['data'] = M('News')->where($where)->order(array('create_time' => 'DESC'))->page(max(1, intval($p)))->limit(20)->select();
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        //取分类列表
        $categories = M('NewsCategory')->getField('id,name,show');
        $this->assign('categories', $categories);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            $op = 'add';
            $data['title'] = I('post.title');
            $data['cid'] = I('post.cid');
            $data['from'] = I('post.from');
            $data['author'] = I('post.author');
            $data['summary'] = mb_substr(str_replace(array(' ', '　', '&nbsp;'), '', strip_tags(I('post.content', '', ''))), 0, 200, 'UTF-8');
            $data['content'] = I('post.content', '', '');
            $data['create_time'] = time();
            if (I('get.id')) {
                $data['id'] = I('get.id');
                unset($data['create_time']);
                $op = 'save';
            }
            try {
                $result = M('News')->data($data)->$op();
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '发布失败，请重试。'));
            }
            if (!$result && $op == 'add') {
                $this->ajaxReturn(array('status' => 500, 'message' => '发布失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '9', '资讯发布修改', '资讯发布修改操作,操作ID:' . ($data['id'] ? $data['id'] : $result) . ',标题：' . $data['title'], ($data['id'] ? $data['id'] : $result));

            $this->ajaxReturn(array('status' => 200, 'message' => '发布成功。'));
        }
        if (I('get.id')) {
            $newsInfo = M('News')->where(array('id' => I('get.id')))->find();
            $this->assign('newsInfo', $newsInfo);
        }
        $categories = M('NewsCategory')->field('id,name')->select();
        $this->assign('categories', $categories);
        $this->display();
    }

    public function delete($id) {
        $result = M('News')->where(array('id' => $id))->delete();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除资讯失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '9', '删除资讯', '删除资讯操作,操作ID:' . $id, $id);
        $this->ajaxReturn(array('status' => 200, 'message' => '删除资讯成功。'));
    }

}