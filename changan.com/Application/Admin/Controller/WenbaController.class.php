<?php

namespace Admin\Controller;

use Common\Model\AreaModel;
use Common\Model\CompanyModel;
use Common\Model\CompanyTypeModel;
use Common\Model\NewsModel;
use Common\Model\NewsTypeModel;

class WenbaController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'wenba');
    }

    final public function wenba_type() {
        $list = M('WendaType')->where(array('is_hide' => '1'))->order('id desc')
                ->select();
        $this->assign('list', $list);
        $this->display();
    }

    final public function wenba_type_add() {
        $id = I('get.id');
        if (IS_POST) {
            $data = array(
                'name' => I('post.name', ''),
                'pinyin' => I('post.pinyin', ''),
                'order_id' => I('post.order_id'),
                'utime' => date('Y-m-d H:i:s'),
                'is_hide' => I('post.is_hide')
            );
            if ($id) {
                $temp = M('WendaType')->where(array('id' => $id))->save($data);
            } else {
                $data['ctime'] = date('Y-m-d H:i:s');
                $temp = M('WendaType')->add($data);
            }
            if (!$temp && M('WendaType')->getDbError()) {
                $this->ajaxReturn(array('code' => 201, 'msg' => '操作失败'));
            } else {
                $this->ajaxReturn(array('code' => 200, 'msg' => '操作成功'));
            }
        }
        $info = M('WendaType')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $this->display();
    }

    final public function wenba_del() {
        if (IS_POST) {
            $id = I('post.id', '0');
            if (!id)
                $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误,无法删除'));
            $temp = M('Wenda')->where(array('id' => $id))->count();
            if (!$temp)
                $this->ajaxReturn(array('code' => 202, 'msg' => '无效删除ID,无法删除'));
            M('Wenda')->startTrans();
            $temp = M('Wenda')->where(array('id' => $id))->delete();
            if (!$temp && M('Wenda')->getDbError()) {
                M('Wenda')->rollback();
            }
            $temp = M('WendaReply')->where(array('wenda_id' => $id))->delete();
            if (!$temp && M('WendaReply')->getDbError()) {
                M('Wenda')->rollback();
            }
            M('Wenda')->commit();
            $this->ajaxReturn(array('code' => 200));
        }
        exit;
    }

    final public function reply() {
        $where = array();
        $key = I('get.key', '', 'trim');
        if ($key) {
            if (is_numeric($key)) {
                $where['bxwr.wenda_id'] = $key;
            } else {
                $where['bxw.name'] = array('like', '%' . $key . '%');
            }
        }

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('WendaReply bxwr');
        $list = $conn->where($where)
                ->field("bxwr.id,bxwr.uid,bxwr.wenda_id,bxwr.is_best,bxwr.status,bxwr.is_hide,bxwr.utime,bxw.name,bxu.name as username")
                ->join('LEFT JOIN bx_user bxu ON bxu.id=bxwr.uid')
                ->join('LEFT JOIN bx_wenda bxw ON bxw.id=bxwr.wenda_id')
                ->join('LEFT JOIN bx_wenda_type bxwt ON bxwt.id = bxw.type_id')
                ->order('bxwr.is_best desc,bxwr.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();

        $page['totalCount'] = $conn->where($where)->join('LEFT JOIN bx_user bxu ON bxu.id=bxwr.uid')
                        ->join('LEFT JOIN bx_wenda bxw ON bxw.id=bxwr.wenda_id')
                        ->join('LEFT JOIN bx_wenda_type bxwt ON bxwt.id = bxw.type_id')->count();
        $pager = showWebPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function reply_del() {
        if (IS_POST) {
            $id = I('post.id');
            $count = M('WendaReply')->where(array('id' => $id))->count();
            if (!$count & M('WendaReply')->getDbError()) {
                $this->ajaxReturn(array('code' => 201, 'msg' => '不存在!!'));
            }
            $temp = M('WendaReply')->where(array('id' => $id))->delete();
            if (!$temp && M('WendaReply')->getDbError()) {
                $this->ajaxReturn(array('code' => 202, 'msg' => '删除失败!!!'));
            } else {
                $this->ajaxReturn(array('code' => 200));
            }
        }
        exit;
    }

    final public function index() {
        $cm = new NewsModel();
        $ntm = new NewsTypeModel();
        $p = max(1, I('get.p', 1, 'intval'));
        $type_id = I('get.type_id', 0);
        $title = I('get.title', '', 'trim');
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_type_id', 0, 'intval');
        if ($name) {
            $where['bxm.title'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxm.company_type_id'] = $company_type_id;
        }
        $where = array();
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Wenda bxw');
        $list = $conn->where($where)
                ->field("bxw.id,bxw.name,bxw.pro_id,bxwt.name as type_name,bxu.name as username,bxw.daili_id,bxw.reply_num,bxw.utime,bxw.ctime,bxw.ask_id,bxw.uid,bxw.is_hide")
                ->join('LEFT JOIN bx_user bxu ON bxu.id=bxw.uid')
                ->join('LEFT JOIN bx_wenda_type bxwt ON bxwt.id = bxw.type_id')
                ->order('bxw.id desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showWebPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $type_list = M('WendaType')->order('ctime desc')->select();
        $this->assign('types', $type_list);
        $this->display();
    }

    final public function add() {
        $nm = new NewsModel();
        $ntm = new NewsTypeModel();
        $am = new AreaModel();
        $id = I('get.id');
        if (IS_POST) {
            $data = array();
            $data['title'] = I('post.title', '', 'trim');
            $data['type_id'] = I('post.type_id', '', 'trim');
            $data['keyword'] = I('post.keyword', '', 'trim');
            $data['author'] = I('post.author', '', 'trim');
            $data['desc'] = I('post.desc', '', 'trim');
            $data['content'] = I('post.content', '', '');
            $data['order_id'] = I('post.order_id', '', 'trim');
            $data['city_id'] = I('post.city_id', '', 'trim');
            $data['is_hide'] = I('post.is_hide', '', 'intval');
            $data['is_hot'] = I('post.is_hot', '', 'trim');

            if ($id) {
                $data['add_time'] = date('Y-m-d H:i:s');
                $temp = $nm->where(array('id' => $id))->save($data);
            } else {
                $data['add_time'] = date('Y-m-d H:i:s');
                $temp = $nm->add($data);
            }
            if (!$temp && $nm->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功');
                exit;
            }
        }
        $info = M('News')->where(array('id' => $id))->find();
        if (!$info) {
            $info['is_hide'] = '0';
            $info['add_time'] = date('Y-m-d H:i:s');
        }
        $this->assign('info', $info);
        $type_list = $ntm->getListSelect();
        $this->assign('types', $type_list);
        $domian_list = $am->getList(array('is_domain' => '1'));
        $this->assign('domain_list', $domian_list);
        $this->display();
    }

}