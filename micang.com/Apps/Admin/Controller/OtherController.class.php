<?php

namespace Admin\Controller;

use Common\Extend\PageForAdmin;

final class OtherController extends AdminCommonController {

    final public function _initialize() {
        parent::_initialize();
    }

    /**
     * 首页图片轮播
     */
    final public function photo() {
        $where = array();
        $name = I('get.name', '', 'trim');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $where['type'] = '0';
        $total = M('Photos')->where($where)->count();
        $result = M('Photos')->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 添加首页轮播图片
     */
    final public function add_photo() {
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'trim');
            $data['url'] = I('post.url', '', 'trim');
            $data['sort'] = I('post.sort', 0, 'intval');
            $data['status'] = I('post.status', 1, 'intval');
            $data['pic_url'] = I('post.pic_url', '', 'trim');
            $id = I('post.id');
            if (!$data['url']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '链接地址不能为空。'));
            }
            if (!$data['pic_url']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请上传图片。'));
            }
            $matchesPicHW = @getimagesize($data['pic_url']);
            if (($matchesPicHW[0] < 844 || $matchesPicHW[1] < 373)) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请上传大小为844*373的图片。'));
            }
            if ($id) {
                $result = M('Photos')->where(array('id' => intval($id)))->data($data)->save();
            } else {
                $result = M('Photos')->data($data)->add();
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '添加图片失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '8', '添加修改首页轮播图片', '添加修改首页轮播图片操作,操作ID:' . ($id ? $id : $result) . ',标题：' . $data['title'] . ',字段：' . json_encode($data), ($id ? $id : $result));
            $this->ajaxReturn(array('status' => 200, 'message' => '添加图片成功'));
        }
        $id = I('get.id');
        $this->assign('info', M('Photos')->where(array('id' => $id))->find());
        $upYun = getUpaiyunConfig('photo');
        $this->assign('policy', $upYun['policy']);
        $this->assign('sign', $upYun['signature']);
        $this->display();
    }

    /**
     * 合作伙伴
     */
    final public function partner() {
        $where = array();
        $name = I('get.name', '', 'trim');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $where['type'] = '1';
        $total = M('Photos')->where($where)->count();
        $result = M('Photos')->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 添加首页合作伙伴
     */
    final public function add_partner() {
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'trim');
            $data['sort'] = I('post.sort', 0, 'intval');
            $data['status'] = I('post.status', 1, 'intval');
            $data['pic_url'] = I('post.pic_url', '', 'trim');
            $data['note'] = I('post.note');
            $data['type'] = '1';
            $id = I('post.id');
            if (!$data['pic_url']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请上传图片。'));
            }
            if ($id) {
                $result = M('Photos')->where(array('id' => intval($id)))->data($data)->save();
            } else {
                $result = M('Photos')->data($data)->add();
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '添加图片失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '8', '添加修改首页合作伙伴', '添加修改首页合作伙伴操作,操作ID:' . ($id ? $id : $result) . ',标题：' . $data['title'] . ',字段：' . json_encode($data), ($id ? $id : $result));
            $this->ajaxReturn(array('status' => 200, 'message' => '添加图片成功'));
        }
        $id = I('get.id');
        $this->assign('info', M('Photos')->where(array('id' => $id))->find());
        $upYun = getUpaiyunConfig('photo');
        $this->assign('policy', $upYun['policy']);
        $this->assign('sign', $upYun['signature']);
        $this->display();
    }

    final public function del_photo() {
        $id = I('get.id');
        $result = M('Photos')->where(array('id' => intval($id)))->delete();
        if ($result) {
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '8', '删除图片成功', '删除图片成功操作,操作ID:' . $id);
            $this->ajaxReturn(array('status' => 200, 'message' => '删除图片成功'));
        } else {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除图片失败，请重试。'));
        }
    }

    public function status_photo($id, $status) {
        $data['id'] = $id;
        $data['status'] = $status;
        $result = M('Photos')->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更状态失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '8', '图片变更状态', '图片变更状态操作,操作ID:' . $id . ',状态：' . $status, $id);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更状态成功。'));
    }

}

?>