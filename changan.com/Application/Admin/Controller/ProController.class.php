<?php

namespace Admin\Controller;

use Common\Model\ProTypeModel;

class ProController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'pro');
    }

    final public function index() {
        $where = array();
        $pro_type_id = I('get.pro_type_id');
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_type_id', 0, 'intval');
        if ($name) {
            $where['bxp.title'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxm.company_type_id'] = $company_type_id;
        }
        if ($pro_type_id) {
            $where['bxp.pro_type_id'] = $pro_type_id;
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Pro bxp');
        $list = $conn->where($where)
                ->field("bxp.id,bxp.title,bxp.update_time,bxp.status,bxpt.name as pro_type_name,bxc.short_name as company_name,bxp.company_id")
                ->join('LEFT JOIN bx_pro_type bxpt ON bxpt.id=bxp.pro_type_id')
                ->join('LEFT JOIN bx_company bxc ON bxc.id=bxp.company_id')->order('bxp.id desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $type_list = M('CompanyType')->field('id,name')->select();
        $this->assign('type_list', $type_list);
        $ptm = new ProTypeModel();
        $types = $ptm->getShowList();
        $this->assign('pro_types', $types);
        $this->display();
    }

    final public function type() {
        $where = array();
        $id = I('get.id');
        $name = I('get.name', '', 'trim');
        if ($name) {
            $where['name'] = array('like', '%' . $name . '%');
        }
        if ($id) {
            $where['id'] = $id;
        }
        $pageNo = max(1, I('get.p', 1, 'intval'));
        $limitRow = C('PAGE_LISTROW');
        $totalRows = D('ProType')->count();
        $data = D('ProType')->getList($where, $pageNo, $limitRow, 'parent_id asc,id asc');
        foreach ($data as $k => $value) {
            if ($value['parent_id'] > 0) {
                $t = D('ProType')->getInfo($value['parent_id']);
                $data[$k]['p_name'] = $t['name'];
            } else {
                $data[$k]['p_name'] = '无';
            }
        }
        $this->assign('items', $data);
        $this->assign('pager', showAdminPage($totalRows, $limitRow));
        $this->assign('totalRows', $totalRows);
        $types = D('ProType')->getShowList();
        $this->assign('pro_types', $types);
        $this->display();
    }

    final public function addtype() {
        $id = I('get.id');
        if (IS_POST) {
            $id = I('post.id');
            $param = I('post.');
            $param['ctime'] = date('Y-m-d H:i:s');
            $param['desc'] = I('post.desc', '', 'htmlspecialchars_decode');
            if ($id) {
                $p = M('ProType')->where(array('pinyin' => $param['pinyin'], 'id' => array('neq', $id)))->getField('id');
                if ($p) {
                    $this->error('拼音重复，请重新输入');
                }
                $r = M('ProType')->where(array('id' => $id))->save($param);
                $key = 'bx_pro_type_' . $id;
                S($key, null);
            } else {
                $p = M('ProType')->where(array('pinyin' => $param['pinyin']))->getField('id');
                if ($p) {
                    $this->error('拼音重复，请重新输入');
                }
                $r = M('ProType')->add($param);
            }

            if (!$r && M('ProType')->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功', '/pro/type');
            }
            exit;
        }
        $info = M('ProType')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $types = D('ProType')->getLevelProType();
        $this->assign('pro_types', $types);
        $this->assign('wenda_types', D('WendaType')->getList());
        $this->assign('news_types', D('NewsType')->field('id,name')->where(array('parent_id' => 1000))->select());
        $this->display();
    }

    final public function add() {
        $id = I('get.id');
        if (IS_POST) {
            $id = I('post.id');
            $data = array();
            $data['title'] = I('post.title', '');
            $data['company_id'] = I('post.company_id', '');
            $data['pro_type_id'] = I('post.pro_type_id', '');
            $data['insure_years'] = I('post.insure_years', '');
            $data['insure_object'] = I('post.insure_object', '');
            $data['price'] = I('post.price', 0, 'intval');
            $data['coverage'] = I('post.coverage', '');
            $data['insure_times'] = I('post.insure_times', '');
            $data['pay_type'] = I('post.pay_type', '');
            $data['feature'] = I('post.feature', '');
            $data['order_id'] = I('post.order_id', 0, 'intval');
            $data['is_hot'] = I('post.is_hot', '', 'trim');
            $data['update_time'] = time();

            M('Pro')->startTrans();
            if ($id) {
                $temp = M('Pro')->where(array('id' => $id))->save($data);
            } else {
                $data['add_time'] = time();
                $temp = M('Pro')->add($data);
                $id = $temp;
                $wid = M('Wenda')->where(array('pro_id' => 0))->getField('id');
                M('Wenda')->where(array('id' => $wid))->save(array('pro_id' => $id));
            }
            if (!$temp && M('Pro')->getDbError()) {
                M('Pro')->rollback();
                $this->error('操作失败');
            } else {
                $projects_name = I('post.projects_name');
                $projects_desc = I('post.projects_desc');
                $projects_money = I('post.projects_money');
                $dataAll = array();
                foreach ($projects_name as $key => $item) {
                    if ($item) {
                        $dataAll[] = array('pro_id' => $id, 'name' => $projects_name[$key], 'money' => $projects_money[$key], 'desc' => $projects_desc[$key]);
                    }
                }
                M('ProProjects')->where(array('pro_id' => $id))->delete();
                M('ProProjects')->addAll($dataAll);

                if (I('post.notices')) {
                    M('ProNotice')->where(array('product_id' => $id))->delete();
                    M('ProNotice')->add(array('product_id' => $id, 'content' => htmlspecialchars_decode(I('post.notices'))));
                }
                M('Pro')->commit();
                S('product_' . $id, null);
                $this->success('操作成功');
            }
            exit;
        }
        //
        $info = M('Pro')->where(array('id' => $id))->find();
        $company = M('Company')->where(array('id' => $info['company_id']))->find();
        $info['company_name'] = $company['name'];
        $info['notices'] = M('ProNotice')->where(array('product_id' => $info['id']))->getField('content');
        $this->assign('info', $info);
        $projects = M('ProProjects')->where(array('pro_id' => $id))->select();
        $this->assign('projects', $projects);


        $ptm = new ProTypeModel();
        $types = $ptm->getShowList();
        $this->assign('pro_types', $types);
        $this->assign('pre_id', M('Pro')->where(array('id' => array('gt', $id)))->order('id')->getField('id'));
        $this->assign('next_id', M('Pro')->where(array('id' => array('lt', $id)))->order('id desc')->getField('id'));
        $this->display();
    }

}