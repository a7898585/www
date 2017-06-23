<?php

/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Admin\Controller;

use Common\Extend\PageForAdmin;

//后台管理员
class AdministratorController extends AdminCommonController {

    //用户列表
    public function accounts() {
        //取分组列表
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        $group = $groupTable->getField('id,title');
        $this->assign('group', $group);
        //开始筛选
        $where = array();
        if (I('get.account')) {
            $where['account'] = array('like', '%' . I('get.account') . '%');
        } elseif (I('get.realname')) {
            $where['realname'] = array('like', '%' . I('get.realname') . '%');
        } elseif (I('get.group_id')) {
            $groupAccessTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP_ACCESS')));
            $userGroupMap = $groupAccessTable->where(array('group_id' => I('get.group_id')))->getField('uid,group_id');
            $userIds = array_keys($userGroupMap);
            $where['id'] = array('IN', $userIds);
            //变量清理
            unset($userIds, $groupAccessTable, $userGroupMap);
        }
        $adminTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_USER')));
        $total = $adminTable->where($where)->count();
        $result = $adminTable->where($where)->page(max(1, I('get.p', 1, 'intval')))->limit(20)->select();
        if (is_array($result)) {
            foreach ($result as $v) {
                $userIds[$v['id']] = $v['id'];
            }
            //取列表中的用户所属的分组ID
            $groupAccessTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP_ACCESS')));
            $groupAccess = $groupAccessTable->where(array('uid' => array('IN', $userIds)))->getField('uid,group_id');
            //根据分组ID取出分组
            foreach ($result as $k => $v) {
                $result[$k]['group_name'] = $group[$groupAccess[$v['id']]];
            }
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //添加用户
    public function account_add() {
        if (IS_POST) {
            $data['account'] = I('post.account');
            $data['password'] = md5(I('post.password', '', ''));
            $data['realname'] = I('post.realname');
            $data['mobile'] = I('post.mobile');
            $data['email'] = I('post.email');
            $data['status'] = '1';
            $data['create_time'] = time();
            $adminTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_USER')));
            $adminTable->startTrans();
            $userId = $adminTable->data($data)->add();
            if (!$userId) {
                $adminTable->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '创建管理员帐号失败，请重试。'));
            }
            unset($data);
            $data['uid'] = $userId;
            $data['group_id'] = I('post.group_id');
            $groupAccessTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP_ACCESS')));
            $result = $groupAccessTable->data($data)->add();
            if (!$result) {
                $adminTable->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '创建管理员帐号失败，请重试。'));
            }
            $adminTable->commit();
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '创建管理员帐号', '创建管理员帐号操作,操作ID:' . $userId . ',账号：' . $data['account'] . ',字段：' . json_encode($data), $userId);
            $this->ajaxReturn(array('status' => 200, 'message' => '创建管理员帐号成功。'));
        }
        //取分组列表
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        $group = $groupTable->getField('id,title');
        $this->assign('group', $group);
        $this->display();
    }

    //启用、禁用帐号
    public function account_status($uid, $status) {
        $data['id'] = $uid;
        $data['status'] = $status;
        $adminTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_USER')));
        $result = $adminTable->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更帐号状态失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '变更帐号状态', '变更帐号状态操作,操作ID:' . $uid . ',状态：' . $status, $uid);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更帐号状态成功。'));
    }

    //删除帐号
    public function account_delete($uid) {
        if ($uid == 1) {
            $this->ajaxReturn(array('status' => 500, 'message' => '超级管理员帐号不能删除。'));
        }
        //删除用户
        $adminTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_USER')));
        $adminTable->startTrans();
        $result = $adminTable->where(array('id' => $uid))->delete();
        if (!$result) {
            $adminTable->rollback();
            $this->ajaxReturn(array('status' => 500, 'message' => '删除帐号失败-01，请重试。'));
        }
        //删除对应权限
        $groupAccessTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP_ACCESS')));
        $result = $groupAccessTable->where(array('uid' => $uid))->delete();
        if (!$result) {
            $adminTable->rollback();
            $this->ajaxReturn(array('status' => 500, 'message' => '删除帐号失败-02，请重试。'));
        }
        $adminTable->commit();
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '删除帐号成功', '删除帐号成功操作,操作ID:' . $uid, $uid);
        $this->ajaxReturn(array('status' => 200, 'message' => '删除帐号成功。'));
    }

    //变更帐号分组
    public function account_group_change($uid, $group_id) {
        if ($uid == 1) {
            $this->ajaxReturn(array('status' => 500, 'message' => '超级管理员帐号不能变更分组。'));
        }
        $groupAccessTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP_ACCESS')));
        $result = $groupAccessTable->where(array('uid' => $uid))->data(array('group_id' => $group_id))->save();
        if (!$result && $groupAccessTable->getDbError()) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更用户分组失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '变更用户分组', '变更用户分组操作,操作ID:' . $uid . ',操作group_id:' . $group_id, $uid);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更用户分组成功。'));
    }

    //检查账号是否已注册
    public function account_check($account) {
        $adminTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_USER')));
        $result = $adminTable->where(array('account' => $account))->find();
        if (!$result) {
            $this->ajaxReturn(array('valid' => true));
        }
        $this->ajaxReturn(array('valid' => false));
    }

    //用户组列表
    public function groups() {
        //取分组列表
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        //开始筛选
        $where = array();
        if (I('get.title')) {
            $where['title'] = I('get.title');
        }
        $total = $groupTable->where($where)->count();
        $result = $groupTable->where($where)->page(max(1, I('get.p', 1, 'intval')))->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //添加分组
    public function group_add() {
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        if (IS_POST) {
            if (!I('post.title', '', 'trim')) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入分组名称。'));
            }
            if (!is_array(I('post.rule'))) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请指定权限。'));
            }
            $data['title'] = I('post.title', '', 'trim');
            $data['status'] = 1;
            $data['rules'] = implode(',', I('post.rule'));
            $result = $groupTable->data($data)->add();
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '保存分组失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '添加分组', '添加分组操作,操作ID:' . $result . ',标题：' . $data['title'] . ',字段：' . json_encode($data), $result);
            $this->ajaxReturn(array('status' => 200, 'message' => '保存分组成功。'));
        }
        //取所有可用权限
        $ruleTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_RULE')));
        $rules = $ruleTable->field('id,name,title,pid,default')->where(array('status' => 1))->select();
        $rules = list_to_tree($rules);
        $this->assign('rules', $rules);
        $this->display();
    }

    //启用、禁用分组
    public function group_status($id, $status) {
        $data['id'] = $id;
        $data['status'] = $status;
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        $result = $groupTable->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更分组状态失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '变更分组状态', '变更分组状态操作,操作ID:' . $uid . ',状态：' . $status, $uid);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更分组状态成功。'));
    }

    //删除分组
    public function group_delete($id) {
        if ($id == 1) {
            $this->ajaxReturn(array('status' => 500, 'message' => '超级管理员分组不能删除。'));
        }
        //有用户的分组不能删除
        $groupAccessTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP_ACCESS')));
        $result = $groupAccessTable->where(array('group_id' => $id))->count();
        if ($result > 0) {
            $this->ajaxReturn(array('status' => 500, 'message' => '该分组下有帐号存在，不能删除。'));
        }
        //删除分组
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        $result = $groupTable->where(array('id' => $id))->delete();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除分组失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '删除分组成功', '删除分组成功操作,操作ID:' . $id, $id);
        $this->ajaxReturn(array('status' => 200, 'message' => '删除分组成功。'));
    }

    //修改分组权限
    public function group_rule_change($id) {
        $groupTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_GROUP')));
        if (IS_POST) {
            if (!is_array(I('post.rule'))) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请指定权限。'));
            }
            $data['id'] = I('get.id');
            $data['rules'] = implode(',', I('post.rule'));
            $result = $groupTable->data($data)->save();
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '保存分组权限失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '10', '保存分组权限', '保存分组权限成功操作,操作ID:' . $id, $id);
            $this->ajaxReturn(array('status' => 200, 'message' => '保存分组权限成功。'));
        }
        //取当前分组已配置的权限
        $groupRules = $groupTable->where(array('id' => $id))->getField('rules');
        $this->assign('groupRules', $groupRules);
        //取所有可用权限
        $ruleTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_RULE')));
        $rules = $ruleTable->field('id,name,title,pid,default')->where(array('status' => 1))->select();
        $rules = list_to_tree($rules);
        $this->assign('rules', $rules);
        $this->display();
    }

//后台操作日志
    public function adminlog() {
        $where = array();
        if (I('post.str_time') && I('post.end_time')) {
            $where['addtime'] = array(array('egt', strtotime(I('post.str_time'))), array('lt', strtotime(I('post.end_time') . ' 23:59:59')));
            $this->assign('str_time', urldecode(I('post.str_time')));
            $this->assign('end_time', urldecode(I('post.end_time')));
        } elseif (I('post.str_time') && I('post.end_time') == '') {
            $where['addtime'] = array('egt', I('post.str_time'));
            $this->assign('str_time', urldecode(I('post.str_time')));
        }
        if (I('post.type') != '') {
            $where['type'] = array('eq', I('post.type'));
            $this->assign('type', I('post.type'));
        }
        $total = M('AdminOperLog')->where($where)->count();
        $result = M('AdminOperLog')->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //后台操作日志详情
    function adminlog_view($id) {
        $detail = M('AdminOperLog')->where(array('id' => $id))->find();
        $detail['account'] = M('Administrators')->where(array('id' => $detail['admin_id']))->find();
        $this->assign('detail', $detail);
        $this->display();
    }

}

