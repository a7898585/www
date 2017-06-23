<?php

namespace Admin\Controller;

use Common\Model\AreaModel;
use Common\Model\CompanyModel;
use Common\Model\CompanyTypeModel;
use Common\Model\NewsModel;
use Common\Model\NewsTypeModel;

class NewsController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'news');
    }

    final public function news_type() {
        $list = M('NewsType')->where(array('is_hide' => '1'))->order('parent_id desc')
                ->select();
        $this->assign('list', $list);
        $this->display();
    }

    final public function news_type_add() {
        $id = I('get.id');
        if (IS_POST) {
            $id = I('post.id', '', intval);
            $data = array(
                'name' => I('post.name', ''),
                'pro_id' => I('post.pro_id', '', intval),
                'wenda_id' => I('post.wenda_id', '', intval),
                'parent_id' => I('post.parent_id'),
                'order_id' => I('post.order_id'),
                'utime' => date('Y-m-d H:i:s'),
                'is_hide' => I('post.is_hide')
            );
            if ($id) {
                $temp = M('NewsType')->where(array('id' => $id))->save($data);
            } else {
                $data['ctime'] = date('Y-m-d H:i:s');
                $temp = M('NewsType')->add($data);
            }
            if (!$temp && M('NewsType')->getDbError()) {
//                $this->ajaxReturn(array('code' => 201, 'msg' => '操作失败'));
                $this->error('操作失败');
            } else {
//                $this->ajaxReturn(array('code' => 200, 'msg' => '操作成功'));
                $this->success('操作成功', '/news/news_type');
            }
            exit;
        }
        $info = M('NewsType')->where(array('id' => $id))->find();
        $this->assign('info', $info);

        $types = M('NewsType')->where(array('parent_id' => '0', 'is_hide' => '1'))->select();
        $this->assign('types', $types);
        $types = D('ProType')->getLevelProType();
        $this->assign('pro_types', $types);
        $this->assign('wenda_types', D('WendaType')->getList());
        $this->display();
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
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('News bxn');
        $list = $conn->where($where)
                ->field("bxn.id,bxn.title,bxn.type_id,bxnt.name as type_name,bxn.order_id,bxn.is_hide,bxn.add_time")
                ->join('LEFT JOIN bx_news_type bxnt ON bxnt.id=bxn.type_id')
                ->order('bxn.id desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showWebPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $type_list = $ntm->getListSelect();
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
            $id = I('post.id');
            if ($id) {
                $data['add_time'] = date('Y-m-d H:i:s');

                $temp = $nm->where(array('id' => $id))->save($data);
            } else {
                $t = $nm->where(array('title' => $data['title']))->getField('id');
                if ($t) {
                    $this->error('标题重复，请修改您的标题');
                }
                $data['add_time'] = date('Y-m-d H:i:s');
                $temp = $nm->add($data);
            }
            if (!$temp && $nm->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功');
                S(C('N000'), null);
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