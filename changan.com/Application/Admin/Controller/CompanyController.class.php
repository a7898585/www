<?php

namespace Admin\Controller;

use Common\Model\AreaModel;
use Common\Model\CompanyModel;
use Common\Model\CompanyTypeModel;
use Think\Upload;

class CompanyController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'company');
    }

    final public function index() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_type_id', 0, 'intval');
        $company_id = I('get.company_id', 0, 'intval');
        if ($name) {
            $where['bxm.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxm.company_type_id'] = $company_type_id;
        }
        if ($company_id) {
            $where['bxm.company_id'] = $company_id;
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Company bxm');
        $list = $conn->where($where)->join('LEFT JOIN bx_company_type bxct ON bxct.id=bxm.company_type_id')->field("bxm.id,bxm.name,bxm.short_name,bxm.company_type_id,bxct.name as company_type_name,bxm.tel")
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);

        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $type_list = M('CompanyType')->field('id,name')->select();
        $this->assign('type_list', $type_list);
        $this->display();
    }

    final public function parent() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_type_id', 0, 'intval');
        if ($name) {
            $where['bxm.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxm.company_type_id'] = $company_type_id;
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('CompanyParent bxm');
        $list = $conn->where($where)->join('LEFT JOIN bx_company_type bxct ON bxct.id=bxm.company_type_id')->field('bxm.*,bxct.name as company_type_name')
                        ->order('id desc')->page($page['pageNum'], $page['numPerPage'])->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);

        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $type_list = M('CompanyType')->field('id,name')->select();
        $this->assign('type_list', $type_list);
        $this->display();
    }

    final public function p_delete() {
        $result = D('CompanyParent')->where(array('id' => I('get.id')))->delete();
        if ($result) {
            $this->success('删除成功。', I('server.HTTP_REFERER'));
        } else {
            $this->error('删除失败，请重试。');
        }
    }

    final public function p_add() {
        $id = I('get.id');
        if (IS_POST) {
            $data = I('post.');
            $data['name'] = I('post.name', '', 'trim');
            $data['company_type_id'] = I('post.company_type_id', '', 'intval');
            $data['status'] = I('post.status', '', 'trim');
            $data['pinyin_f'] = I('post.pinyin_f', '', 'trim');
            $data['pinyin_f'] = strtoupper($data['pinyin_f']);
            $data['content'] = I('post.content', '', 'htmlspecialchars_decode');
            if ($id) {
                $temp = M('CompanyParent')->where(array('id' => $id))->save($data);
                $key = 'company_jigou_' . $id;
                S($key, null);
                M('Company')->where(array('company_id' => $id))->save(array('company_type_id' => $data['company_type_id']));
            } else {
                $data['ctime'] = time();
                $temp = M('CompanyParent')->add($data);
            }
            if (!$temp && M('CompanyParent')->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功', '/company/parent');
                exit;
            }
        }
        $info = M('CompanyParent')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $province = D('Area')->province();
        $this->assign('province', $province);
        if ($info['province_id']) {
            $city = D('Area')->city($info['province_id']);
            $this->assign('city', $city);
        }
        $type_list = D('CompanyType')->getAllList();
        $this->assign('type_list', $type_list);
        $this->display();
    }

    final public function add() {
        $am = new AreaModel();
        $cm = new CompanyModel();
        $ctm = new CompanyTypeModel();
        $id = I('get.id');
        if (IS_POST) {
            $data['name'] = I('post.name', '', 'trim');
            $data['pinyin_f'] = I('post.pinyin_f', '', 'trim');
            $data['pinyin_f'] = strtoupper($data['pinyin_f']);
            $data['short_name'] = I('post.short_name', '', 'trim');
            $data['company_type_id'] = I('post.company_type_id', '', 'intval');
            $data['province_id'] = I('post.province_id', '', 'trim');
            $data['city_id'] = I('post.city_id', '', 'trim');
            $data['website'] = I('post.website', '', 'trim');
            $data['address'] = I('post.address', '', 'trim');
            $data['photo_url'] = I('post.photo_url', '', 'trim');
            $data['tel'] = I('post.tel', '', 'trim');
            $data['order_id'] = I('post.order_id', '0', 'intval');
            $data['desc'] = I('post.desc');
            $data['content'] = htmlspecialchars_decode(I('post.content'));

            if (!$data['desc']) {
                $data['desc'] = msubstr($data['content'], 0, 120);
            }
            $data['is_hot'] = I('post.is_hot', '0', 'intval');
            if ($id) {
                $temp = $cm->where(array('id' => $id))->save($data);
                $key = 'company_' . $id;
                S($key, null);
            } else {
                $temp = $cm->add($data);
            }
            if (!$temp && $cm->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功');
                exit;
            }
        }
        $info = M('Company')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $province = $am->province();
        $this->assign('province', $province);
        if ($info['province_id']) {
            $city = $am->city($info['province_id']);
            $this->assign('city', $city);
        }
        $type_list = $ctm->getAllList();
        $this->assign('type_list', $type_list);
        $this->display();
    }

}