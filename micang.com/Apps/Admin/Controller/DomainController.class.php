<?php

/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Admin\Controller;

use Common\Extend\PageForAdmin;

//后台管理员
class DomainController extends AdminCommonController {

    //域名后缀列表
    public function suffix() {
        //开始筛选
        $where = array();
        if (I('get.status') != '') {
            $where['status'] = I('get.status');
        }
        if (I('get.usual') != '') {
            $where['usual'] = I('get.usual');
        }
        if (I('get.name') != '') {
            $where['name'] = array('like', '%' . I('get.name') . '%');
        }
        $DomainSuffix = D('DomainSuffix');
        $total = $DomainSuffix->where($where)->count();
        $result = $DomainSuffix->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('name')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 添加后缀
     */
    final public function add_suffix() {
        if (IS_POST) {
            $data = I('post.');
            $id = I('post.id');
            try {
                if ($id) {
                    $result = M('DomainSuffix')->data($data)->save();
                } else {
                    $id = M('DomainSuffix')->where(array('name' => $data['name']))->getField('name');
                    if ($id) {
                        $result = M('DomainSuffix')->data($data)->save();
                    } else {
                        $result = M('DomainSuffix')->data($data)->add();
                    }
                }
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '添加域名后缀失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '4', '添加域名后缀', '添加域名后缀操作,操作ID:' . $id . ',域名:' . $data['name'], $id);
            $this->ajaxReturn(array('status' => 200, 'message' => '添加域名后缀成功'));
        }
        $name = I('get.name');
        $this->assign('suffix', $name);
        $this->assign('info', M('DomainSuffix')->where(array('name' => $name))->find());
        $this->display();
    }

//更改域名后缀状态
    public function status_suffix($name, $status) {
        $data['name'] = $name;
        $data['status'] = $status;
        $result = M('DomainSuffix')->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更状态失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '4', '更改域名后缀状态', '更改域名后缀状态操作,操作域名:' . $data['name'] . ',操作状态status:' . $data['status']);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更状态成功。'));
    }

    //模板列表
    public function template() {
        //开始筛选
        $where = array();
        if (I('get.status') != '') {
            $where['status'] = I('get.status');
        }
        if (I('get.title') != '') {
            $where['title'] = array('like', '%' . I('get.title') . '%');
        }
        $Members = D('MembersDomainTemplate');
        $total = $Members->where($where)->count();
        $result = $Members->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

//模板审核
    public function check_template($id) {
        if (IS_POST) {
            $data['status'] = I('post.status');
            $data['id'] = $id;
            $res = M('MembersDomainTemplate')->data($data)->save();
            if (!$res) {
                $this->ajaxReturn(array('status' => 500, 'message' => '审核失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '5', '模板审核', '模板审核操作,操作ID:' . $data['id'] . ',操作状态status:' . $data['status'], $data['id']);
            $this->ajaxReturn(array('status' => 200, 'message' => '审核成功。'));
        }
        $templates = M('MembersDomainTemplate')->where(array('id' => $id))->find();
        $this->assign('templates', $templates);
        $username = M('Members')->where(array('id' => $templates['mid']))->getField('username');
        $this->assign('username', $username);
        $profile = M('MembersProfile')->where(array('mid' => $templates['mid']))->find();
        $profile['pic'] = json_decode($profile['idcard_pic'], true);
        $this->assign('profile', $profile);
        $type = I('get.type') ? I('get.type') : '0';
        $type ? $this->display('check_template' . $type) : $this->display();
    }

    //审核
    public function template_status($id, $status) {
        $data['id'] = $id;
        $data['status'] = $status;
        $result = M('MembersDomainTemplate')->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更状态失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '5', '模板审核', '模板审核操作,操作ID:' . $data['id'] . ',操作状态status:' . $data['status'], $data['id']);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更状态成功。'));
    }

    //价格列表
    public function price() {
        //开始筛选
        $where = array();
        if (I('get.suffix') != '') {
            $where['suffix'] = array('eq', I('get.suffix'));
        }
        $p = max(1, I('get.p', 1, 'intval'));
        $pageNum = 50;
        $start = ($p - 1) * $pageNum;
        $Members = D('MemberLevelPriceMap');
        $total = $Members->where($where)->group('suffix')->count();
        $result = $Members->where($where)->order('suffix')->select();
        if (is_array($result)) {
            $data = array();
            foreach ($result as $value) {
                $data[$value['suffix']][$value['level']] = $value;
            }
            $page['data'] = array_slice($data, $start, $pageNum);
        }
        $pager = new PageForAdmin($total, $pageNum);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 添加价格
     */
    public function add_price() {
        if (IS_POST) {
            $data['suffix'] = I('post.suffix');
            $level_arr = I('post.level');
            for ($i = 1; $i < 11; $i++) {
                $price_register[$i] = I('post.price_register_year' . $i);
                $price_renew[$i] = I('post.price_renew_year' . $i);
                $cc_price_register[$i] = I('post.cc_price_register_year' . $i);
                $cc_price_renew[$i] = I('post.cc_price_renew_year' . $i);
            }

            $price_transfer = I('post.price_transfer');
            $redeem = I('post.price_redeem');
            $register = I('post.register');
            $register = ucfirst($register);
            $priceModel = D('DomainSuffixPrice' . $register);

            if (I('post.id') == '') {
                $nameId = $priceModel->where(array('name' => $data['suffix']))->getField('name');
                $suffixId = M('MemberLevelPriceMap')->where(array('suffix' => $data['suffix']))->getField('suffix');
            }
            //添加出厂价
            for ($i = 1; $i < 11; $i++) {
                $cc_data['price_register_year' . $i] = $cc_price_register[$i];
                $cc_data['price_renew_year' . $i] = $cc_price_renew[$i];
            }
            $cc_data['price_transfer'] = I('post.cc_price_transfer');
            $cc_data['price_redeem'] = I('post.cc_price_redeem');
            $cc_data['name'] = $data['suffix'];
            $priceModel->startTrans();
            if (I('post.id') || $nameId) {
                $res1 = $priceModel->data($cc_data)->save();
            } else {
                $res1 = $priceModel->data($cc_data)->add();
                if (!$res1) {
                    $priceModel->rollback();
                    $this->ajaxReturn(array('status' => 500, 'message' => '添加失败，请重试。'));
                }
            }
            //添加会员价格
            if (!empty($level_arr)) {
                foreach ($level_arr as $key => $level) {
                    $data['level'] = $level;
                    for ($i = 1; $i < 11; $i++) {
                        $data['price_register_year' . $i] = $price_register[$i][$key] * 100;
                        $data['price_renew_year' . $i] = $price_renew[$i][$key] * 100;
                    }
                    $data['price_transfer'] = $price_transfer[$key] * 100;
                    $data['price_redeem'] = $redeem[$key] * 100;
                    if (I('post.id') || $suffixId) {
                        $res = M('MemberLevelPriceMap')->data($data)->save();
                    } else {
                        $res = M('MemberLevelPriceMap')->data($data)->add();
                        if (!$res) {
                            $priceModel->rollback();
                            $this->ajaxReturn(array('status' => 500, 'message' => '添加失败，请重试。'));
                        }
                    }
                }
            }
            $priceModel->commit();
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '6', '添加域名价格', '添加域名价格操作,操作域名:' . $data['suffix']);
            $this->ajaxReturn(array('status' => 200, 'message' => '添加成功。'));
        }
        $where = array();
        $id = I('get.id');
        $suffix = I('get.suffix');
        $register = I('get.register');
        if ($id) {
            $where['suffix'] = array('eq', $id);
            $this->assign('id', $id);
            $this->assign('suffix', $id);
            $Members = D('MemberLevelPriceMap');
            $prices = $Members->where($where)->order('suffix')->select();

            if (!$register) {
                $register = M('DomainSuffix')->where(array('name' => $id))->getField('registrar');
            }
            $register = ucfirst($register);
            $priceModel = D('DomainSuffixPrice' . $register);
            $cc_prices = $priceModel->where(array('name' => $id))->find();
            $data = array();
            foreach ($prices as $value) {
                $data[$value['level']] = $value;
            }
            $this->assign('cc_prices', $cc_prices);
            $this->assign('data', $data);
        } else {
            if (!$suffix) {
                exit('请选择一个后缀');
            }
            $res = M('MemberLevels')->field('id')->select();
            $data = array();
            foreach ($res as $value) {
                $data[$value['id']] = $value['id'];
            }
            $this->assign('suffix', $suffix);
            $this->assign('data', $data);
            if (!$register) {
                $register = M('DomainSuffix')->where(array('name' => $suffix))->getField('registrar');
            }
            $register = ucfirst($register);
        }
        $this->assign('register', $register);
        $this->display();
    }

}

