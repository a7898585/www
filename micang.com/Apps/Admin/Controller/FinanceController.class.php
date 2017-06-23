<?php

/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Admin\Controller;

use Common\Extend\PageForAdmin;

//后台管理员
class FinanceController extends AdminCommonController {

    public function domain() {
        //开始筛选
        $where = array();
        if (I('request.str_time') && I('request.end_time')) {

            $where['time'] = array(array('egt', strtotime(I('request.str_time'))), array('lt', strtotime(I('request.end_time')) + 86400));

            $this->assign('str_time', urldecode(I('request.str_time')));
            $this->assign('end_time', urldecode(I('request.end_time')));
        }
        $Members = D('PlatformConsumeDetail');
        $SumData = $Members->field("sum(money_factory) as pay,sum(money_selling) as income")->where($where)->find();
        //  echo $Members->getLastSql();
        $SumData['lilun'] = $SumData['income'] - $SumData['pay'];
        $total = $Members->where($where)->count();
        $result = $Members->where($where)->page(max(1, I('get.p', 1, 'intval')))->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->assign('SumData', $SumData);
        $this->display();
    }

    /**
     * 提现申请 
     */
    public function deposit() {
        $where = array();
        if (I('post.str_time') && I('post.end_time')) {
            $where['addtime'] = array(array('egt', I('post.str_time')), array('lt', I('post.end_time') . ' 23:59:59'));
            $this->assign('str_time', urldecode(I('post.str_time')));
            $this->assign('end_time', urldecode(I('post.end_time')));
        } elseif (I('post.str_time') && I('post.end_time') == '') {
            $where['addtime'] = array('egt', I('post.str_time'));
            $this->assign('str_time', urldecode(I('post.str_time')));
        }
        if (I('post.mid')) {
            $where['mid'] = array('eq', I('post.mid'));
            $this->assign('mid', I('post.mid'));
        }
        if (I('post.status') != '') {
            $where['status'] = array('eq', I('post.status'));
            $this->assign('status', I('post.status'));
        }
        $Members = D('MembersDepositDetail');
        $Account = D('MembersAccount');
        $total = $Members->where($where)->count();
        $result = $Members->where($where)->page(max(1, I('get.p', 1, 'intval')))->order(array('status' => 'ASC'))->limit(20)->select();
        if (is_array($result)) {
            foreach ($result as &$value) {
                $value['account'] = $Account->field('account')->where(array('id' => $value['account_id']))->find();
            }
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 提现审核
     */
    public function check_deposit($id) {
        $Members = D('MembersDepositDetail');
        if (IS_POST) {
            $data['status'] = I('post.status');
            $data['remark'] = I('post.remark');
            $data['tid'] = I('post.tid');
            $data['notify_time'] = date('Y-m-d H:i:s');
            $data['id'] = $id;
            $res = $Members->data($data)->save();
            if (!$res) {
                $this->ajaxReturn(array('status' => 500, 'message' => '审核失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '3', '提现审核', '提现审核操作,操作ID:' . $data['id'] . ',操作状态status:' . $data['status'] . ',备注：' . $data['remark'] . ',汇款流水号：' . $data['tid']);
            $this->ajaxReturn(array('status' => 200, 'message' => '审核成功。'));
        }

        $detail = $Members->where(array('id' => $id))->find();
        $Account = D('MembersAccount');
        $detail['account'] = $Account->where(array('id' => $detail['account_id']))->find();
        $this->assign('detail', $detail);
        $this->display();
    }

}

