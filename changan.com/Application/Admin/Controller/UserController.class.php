<?php

namespace Admin\Controller;

use Common\Extend\BrmApi;

class UserController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'user');
    }

    final public function index() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_type_id', 0, 'intval');
        if ($name) {
            $where['bxu.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxc.id'] = $company_type_id;
        }

        $where['type'] = '0';

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('User bxu');
        $list = $conn->where($where)
                ->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')
                ->join('LEFT JOIN bx_company bxc ON bxc.id=bxud.company_id')
                ->field("bxu.id,bxu.is_hide,bxu.name,bxu.phone,bxu.score,bxu.exp,bxud.rank,bxud.vip,bxud.check_status,bxud.replys,bxud.company_id,bxu.utime,bxc.short_name as company_name")
                ->order('bxu.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $companys = M('Company')->field('id,short_name as name')->where(array('is_hide' => '1'))->select();
        $this->assign('companys', $companys);
        $this->display();
    }

    /*     * *************代理人*************** */

    final public function daili() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_id', 0, 'intval');
        $is_test = I('get.is_test', 1, 'intval');
        $this->assign('is_test', $is_test);
        if ($name) {
            $where['bxu.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxud.company_id'] = $company_type_id;
        }
        if ($is_test) {
            $where['is_test'] = $is_test == 1 ? '0' : '1';
        }

        $where['type'] = '1';

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('User bxu');
        $list = $conn->where($where)
                ->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')
                ->join('LEFT JOIN bx_company bxc ON bxc.id=bxud.company_id')
                ->field("bxu.id,bxu.name,bxu.phone,bxu.score,bxu.exp,bxud.rank,bxud.vip,bxud.check_status,bxud.top,bxud.replys,bxud.company_id,bxu.utime,bxc.short_name as company_name,bxud.agent_id")
                ->order('bxu.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $companys = M('Company')->field('id,short_name as name')->where(array('is_hide' => '1'))->select();
        $this->assign('companys', $companys);
        $agents = M('UserAgent')->field('agent_id as id,company as name')->where(array('status' => '1'))->select();
        $this->assign('agents', $agents);
        $this->display();
    }

    final public function agent_daili() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_id', 0, 'intval');
        if ($name) {
            $where['bxu.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxud.company_id'] = $company_type_id;
        }
        $where['bxu.is_test'] = '0';
        $where['type'] = '1';
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('User bxu');
        $list = $conn->where($where)
                ->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')
                ->join('LEFT JOIN bx_company bxc ON bxc.id=bxud.company_id')
                ->field("bxu.id,bxu.name,bxu.phone,bxud.rank,bxud.vip,bxud.check_status,bxud.top,bxud.replys,bxud.company_id,bxu.utime,bxc.short_name as company_name,bxud.agent_id")
                ->order('bxu.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $companys = M('Company')->field('id,short_name as name')->where(array('is_hide' => '1'))->select();
        $this->assign('companys', $companys);
        $agents = M('UserAgent')->field('agent_id as id,company as name')->where(array('status' => '1'))->select();
        $this->assign('agents', $agents);
        $this->display();
    }

    /**
     * 分配代理人到代理商
     */
    final public function change_agent() {
        $uid = I('post.uid', 0, 'intval');
        $agent_id = I('post.agent_id', 0, 'intval');
        if (!$uid || !$agent_id) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数异常!'));
        }
        $temp = M('UserDaili')->where(array('uid' => $uid))->find();
        if (!$temp) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户不存在!'));
        }
        $api = new BrmApi();
        $api_data = $api->setAgent($agent_id, $uid);
        if ($api_data['s'] < 1) {
            $this->ajaxReturn(array('code' => 204, 'msg' => '代理商变更失败!---BRM: ' . $api_data['r']));
        }
        $temp = M('UserDaili')->where(array('uid' => $uid))->data(array('agent_id' => $agent_id))->save();
        if (!$temp && M('UserDaili')->getDbError()) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '修改失败!'));
        }
        $this->ajaxReturn(array('code' => 200));
    }

    /**
     * 未审核的代理人
     */
    final public function c_daili() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_id', 0, 'intval');
        $is_test = I('get.is_test', 1, 'intval');
        $this->assign('is_test', $is_test);
        if ($name) {
            $where['bxu.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxud.company_id'] = $company_type_id;
        }
        if ($is_test) {
            $where['is_test'] = $is_test == 1 ? '0' : '1';
        }
//        $where['is_test'] = '0';
        $where['type'] = '1';

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('User bxu');
        $list = $conn->where($where)
                ->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')
                ->join('LEFT JOIN bx_company bxc ON bxc.id=bxud.company_id')
                ->field("bxu.id,bxu.name,bxu.phone,bxu.score,bxu.exp,bxud.rank,bxud.vip,bxud.check_status,bxud.top,bxud.replys,bxud.company_id,bxu.utime,bxc.short_name as company_name,bxud.agent_id")
                ->order('bxu.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $companys = M('Company')->field('id,short_name as name')->where(array('is_hide' => '1'))->select();
        $this->assign('companys', $companys);
        $agents = M('UserAgent')->field('agent_id as id,company as name')->where(array('status' => '1'))->select();
        $this->assign('agents', $agents);
        $this->display();
    }

    /**
     * 代理人编辑
     */
    final public function add() {
        $id = I('get.id');
        $userDaili = D('UserDaili');
        if (IS_POST) {
            $data = array();
//            $data['title'] = I('post.title', '', 'trim');
            $id = I('post.id');
            if ($id) {
                $temp = $userDaili->where(array('id' => $id))->save($data);
            } else {
                $data['add_time'] = date('Y-m-d H:i:s');
                $temp = $userDaili->add($data);
            }
            if (!$temp && $userDaili->getDbError()) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功');
                $key = 'userdailiren_' . $id;
                S($key, null);
            }
            exit;
        }
        $info = $userDaili->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个用户');
        }
        $data = D('UserCertify')->getListById($id);
//        print_r($info);
        $this->assign('info', $info);
        $this->assign('data', $data);
        $this->display();
    }

    final public function check() {
        $id = I('get.id');
        $info = D('UserDaili')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个用户');
        }
        $data = D('UserCertify')->getListById($id);
        $this->assign('info', $info);
        $this->assign('data', $data);
        $this->display();
    }

    final public function certify() {
        $data = I('post.');
        if ($data) {

            foreach ($data['c'] as $key => $value) {
                $cdata['refuse'] = $value['refuse'];
                $cdata['status'] = $value['value'] ? $value['value'] : 0;
                switch ($key) {
                    case 'idcard':
                        $type = '1';
                        break;
                    case 'quality':
                        $type = '2';
                        break;
                    case 'practice':
                        $type = '3';
                        break;
                    case 'industry':
                        $type = '4';
                        break;
                    case 'other':
                        $type = '5';
                        break;
                    default:
                        $type = '1';
                        break;
                }
                if ($value['id']) {
                    $r = M('UserCertify')->where(array('uid' => $data['id'], 'type' => $type))->save($cdata);
                } else {
                    $cdata['addtime'] = time();
                    $cdata['uid'] = $data['id'];
                    $cdata['type'] = $type;
                    $cdata['pinyin'] = $key;
                    $r = M('UserCertify')->add($cdata);
                }
                unset($cdata, $value, $key);
            }
            $this->ajaxReturn(array('code' => 200));
            exit;
        }
    }

    final public function view() {
        $id = I('get.id');
        $info = D('UserDaili')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个用户');
        }
        $data = D('UserCertify')->getListById($id);
//        print_r($info);
        $this->assign('info', $info);
        $this->assign('data', $data);
        $this->display();
    }

    final public function m_view() {
        $id = I('get.id');
        $info = D('UserMember')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个用户');
        }
//        $data = D('UserCertify')->getListById($id);
//        print_r($info);
        $this->assign('info', $info);
//        $this->assign('data', $data);
        $this->display();
    }

    final public function insure_view() {
        $id = I('get.id');
        $info = D('InsurancePlan')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个用户');
        }
        $this->assign('info', $info);
        $this->display();
    }

    final public function top() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $company_type_id = I('get.company_id', 0, 'intval');
        if ($name) {
            $where['bxu.name'] = array('like', '%' . $name . '%');
        }
        if ($company_type_id) {
            $where['bxud.company_id'] = $company_type_id;
        }
        $where['bxud.top'] = '1';
        $where['bxu.type'] = '1';

        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('User bxu');
        $list = $conn->where($where)
                ->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')
                ->join('LEFT JOIN bx_company bxc ON bxc.id=bxud.company_id')
                ->field("bxu.id,bxu.name,bxu.phone,bxu.score,bxu.exp,bxud.rank,bxud.vip,bxud.sorts,bxud.check_status,bxud.top,bxud.replys,bxud.company_id,bxu.utime,bxc.short_name as company_name")
                ->order('bxud.sorts,bxu.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->join('LEFT JOIN bx_user_daili bxud ON bxud.uid=bxu.id')->count();
        $pager = showWebPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);

        $companys = M('Company')->field('id,short_name as name')->where(array('is_hide' => '1'))->select();
        $this->assign('companys', $companys);

        $this->display();
    }

    /**
     * 代理人审核
     */
    final public function daili_shenhe() {
        $id = I('post.id');
        $status = I('post.status');
        $data = array('check_status' => $status, 'uid' => $id);
        if (I('post.rank')) {
            $data['rank'] = I('post.rank');
        }
        $temp = M('UserDaili')->data($data)->save();
        if (!$temp && M('UserDaili')->getDbError()) {
            $this->ajaxReturn(array('code' => 201));
        }
        $key = 'userdailiren_' . $id;
        S($key, null);
        $this->ajaxReturn(array('code' => 200));
    }

    final public function user_shenhe() {
        $id = I('post.id');
        $status = I('post.status');
        $data = array('is_hide' => $status);
        M('UserMember')->where(array('uid' => intval($id)))->data($data)->save();
        $temp = M('User')->where(array('id' => intval($id)))->data($data)->save();
        if (!$temp && M('User')->getDbError()) {
            $this->ajaxReturn(array('code' => 201));
        }
        $this->ajaxReturn(array('code' => 200));
    }

    /**
     * 代理人置顶
     */
    final public function daili_setTop() {
        $id = I('post.id');
        $top = I('post.top');
        $temp = M('UserDaili')->data(array('top' => $top, 'uid' => $id))->save();
        if (!$temp && M('UserDaili')->getDbError()) {
            $this->ajaxReturn(array('code' => 201));
        }
        $this->ajaxReturn(array('code' => 200));
    }

    /**
     * 排序
     */
    final public function upSorts() {
        $id = I('post.id');
        $sorts = I('post.sorts');
        $temp = M('UserDaili')->data(array('sorts' => $sorts, 'uid' => $id))->save();
        if (!$temp && M('UserDaili')->getDbError()) {
            $this->ajaxReturn(array('code' => 201));
        }
        $this->ajaxReturn(array('code' => 200));
    }

    /**
     * 开通VIP
     */
    final public function upgrade_vip() {
        $uid = I('post.id');
        $vip = M('UserDaili')->where(array('uid' => $uid))->getField('vip');
        if ($vip == 0) {
            M('UserDaili')->data(array('vip' => 1, 'uid' => $uid))->save();
        }
        D('User')->upgradeVip($uid);
        $this->ajaxReturn(array('code' => 200));
    }

//`id` int(10) NOT NULL AUTO_INCREMENT,
//`name` varchar(255) DEFAULT NULL,
//`sex` enum('女','男') DEFAULT '男',
//`mail` varchar(255) DEFAULT NULL,
//`phone` varchar(255) DEFAULT NULL,
//`describe` varchar(5000) NOT NULL,
//`uid` int(10) DEFAULT NULL,
//`ctime` timestamp NULL DEFAULT NULL,
//`utime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
//`area_id` varchar(20) DEFAULT '' COMMENT '地域ID',
//`is_hide` enum('1','0') NOT NULL DEFAULT '1',
    /**
     *
     */
    final public function online_insure() {
        $name = I('get.name');
        $where = array();
        if (checkemail($name)) {
            $where['email'] = $name;
        } else if (preg_match("/1[3458]{1}\d{9}$/", $name)) {
            $where['phone'] = $name;
        } else if (is_numeric($name)) {
            $where['id'] = $name;
        } else {
            $where['name'] = array('like', $name . '%');
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('InsurancePlan bxoi');
        $list = $conn->where($where)
//            ->join('LEFT JOIN bx_area bxa ON bxa.area_id=bxoi.area_id')
//            ->field("bxoi.id,bxoi.name,bxoi.sex,bxoi.mail,bxoi.phone,bxoi.is_hide")
                ->order('bxoi.utime desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showWebPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

}