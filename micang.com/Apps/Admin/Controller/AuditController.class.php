<?php

/**
 * AuditController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-12-09
 */

namespace Admin\Controller;

use Common\Extend\PageForAdmin;
use Common\Extend\PDCApi;

class AuditController extends AdminCommonController {

    public function realname($p = 1) {
        //开始筛选
        $where['auth_status'] = '2';
        if (I('get.id') != '') {
            $where['id'] = I('get.id');
        }
        if (I('get.username') != '') {
            $where['username'] = I('get.username');
        }
        $total = D('Members')->where($where)->count();
        $members = D('Members')->where($where)->page(max(1, intval($p)))->limit(20)->getField('id,username,register_time,login_time,level');
        if (is_array($members) && !empty($members)) {
            //获取等级映射表
            $memberLevelMap = M('MemberLevels')->getField('id,name');
            $this->assign('memberLevelMap', $memberLevelMap);
            //获取本页所有的用户ID
            $memberIds = array_keys($members);
            //获取本页所有用户的详细资料
            $memberProfiles = M('MembersProfile')->where(array('mid' => array('IN', $memberIds)))->getField('mid,realname,province,city,county,address');
            if (is_array($memberProfiles) && !empty($memberProfiles)) {
                $areaIds = array();
                foreach ($memberProfiles as $item) {
                    $areaIds[] = $item['province'];
                    $areaIds[] = $item['city'];
                    $areaIds[] = $item['county'];
                }
                $areaIds = array_unique(array_filter($areaIds));
                if (!empty($areaIds)) {
                    $areas = PDCApi::getInfoByAreaId($areaIds);
                }
                foreach ($memberProfiles as $k => $item) {
                    $memberProfiles[$k]['province'] = $areas[$item['province']]['simple'];
                    $memberProfiles[$k]['city'] = $areas[$item['province']]['city'];
                    $memberProfiles[$k]['county'] = $areas[$item['province']]['county'];
                }
            }
            $this->assign('memberProfiles', $memberProfiles);
            $page['data'] = $members;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //实名认证审核
    public function change_realname_status($mid) {
        if (IS_POST) {
            //todo 发站内信和邮件
            $data['id'] = $mid;
            $data['auth_status'] = I('post.auth_status');
            $data['auth_reject_reason'] = (I('post.auth_status') == '3') ? I('post.auth_reason', '') : '';
            $result = M('Members')->data($data)->save();
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '审核失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '0', '实名认证审核', '实名认证审核操作;操作ID:' . $data['id'] . ',操作状态status:' . $data['auth_status'] . ',备注：' . $data['auth_reject_reason'], $id, '变更状态为：' . ($data['auth_status'] == '1' ? '通过' : '不通过' ) . '; 备注：' . $data['auth_reject_reason']);
            $this->ajaxReturn(array('status' => 200, 'message' => '审核成功。'));
        }
        $memberInfo = M('Members')->where(array('id' => $mid))->find();
        $this->assign('memberInfo', $memberInfo);
        $profile = M('MembersProfile')->where(array('mid' => $mid))->find();
        $profile['pic'] = json_decode($profile['idcard_pic'], true);
        $this->assign('profile', $profile);
        $this->display();
    }

    /**
     * 域名代购
     */
    final public function purchase() {
        $where = array();
        $name = I('get.domain', '', 'trim');
        if ($name) {
            $where['domain'] = array('like', '%' . $name . '%');
        }
        if (I('get.status') != '') {
            $where['status'] = I('get.status');
        }
        if (I('get.role') != '') {
            $where['role'] = array('eq', I('get.role'));
        }
        $total = M('DomainPurchase')->where($where)->count();
        $result = M('DomainPurchase')->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 委托审核
     * @param type $id
     */
    public function check_purchase($id) {
        if (IS_POST) {
            $data['id'] = $id;
            $data['status'] = I('post.status');
            $data['remark'] = I('post.remark');
            $data['uptime'] = time();
            $result = M('DomainPurchase')->data($data)->save();
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '变更状态失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '1', '委托审核', '委托审核操作：操作ID:' . $data['id'] . ',操作状态status:' . $data['status'] . ',备注：' . $data['remark'], $id, '变更状态为：' . getPurchaseParam($data['status']) . '; &nbsp;&nbsp;备注：' . $data['remark']);
            $this->ajaxReturn(array('status' => 200, 'message' => '变更状态成功。'));
        }
        if (!$id) {
            exit('不存在');
        }
        $info = M('DomainPurchase')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 审核详情
     */
    public function check_purchase_detail($id) {
        $info = M('DomainPurchase')->where(array('id' => $id))->find();
        $data = M('AdminOperLog')->where(array('oper_id' => $id, 'type' => '1'))->select();
        if(!empty($data)){
            foreach ($data as &$value) {
                $value['account'] = M('Administrators')->where(array('id' => $value['admin_id']))->find();
            }
        }
        $this->assign('info', $info);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 域名中介
     */
    final public function agency() {
        $where = array();
        $name = I('get.domain', '', 'trim');
        if ($name) {
            $where['domain'] = array('like', '%' . $name . '%');
        }
        if (I('get.status') != '') {
            $where['status'] = I('get.status');
        }
        if (I('get.role') != '') {
            $where['role'] = array('eq', I('get.role'));
        }
        $total = M('DomainAgency')->where($where)->count();
        $result = M('DomainAgency')->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 中介审核
     * @param type $id
     */
    public function check_agency($id) {
        if (IS_POST) {
            $data['id'] = $id;
            $data['status'] = I('post.status');
            $data['remark'] = I('post.remark');
            $data['uptime'] = time();
            $result = M('DomainAgency')->data($data)->save();
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '变更状态失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '2', '中介审核', '中介审核操作：操作ID:' . $data['id'] . ',操作状态status:' . $data['status'] . ',备注：' . $data['remark'], $data['id'], '变更状态为：' . getAgencyParam($data['status']) . '; 备注：' . $data['remark']);
            $this->ajaxReturn(array('status' => 200, 'message' => '变更状态成功。'));
        }
        if (!$id) {
            exit('不存在');
        }
        $info = M('DomainAgency')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $this->display();
    }

}