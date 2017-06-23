<?php

namespace Admin\Controller;

class InsuranceController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'insurance');
    }

    final public function index() {
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
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function view() {
        $id = I('get.id');
        $info = D('InsurancePlan')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个表单');
        }
        $this->assign('info', $info);
        $this->display();
    }

    final public function set() {
        $id = I('get.id');
        $info = D('InsurancePlan')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个表单');
        }
        $province = D('Area')->province();
        $this->assign('province', $province);
        $this->assign('info', $info);
        $this->display();
    }

    public function divide() {
        if (IS_POST) {
            $data = I('post.');
            if ($data['id'] && $data['did']) {
                $param['daili_id'] = $data['did'];
                $param['divide_time'] = time();
                $r = M('InsurancePlan')->where(array('id' => $data['id']))->save($param);
                if (!$r && M('InsurancePlan')->getDbError()) {
                    echo json_encode(array("msg" => '分配失败', "s" => "0"));
                } else {
                    $key = 'user_online_insure' . $data['id'];
                    S($key, null);
                    echo json_encode(array("msg" => '分配成功', "s" => "1"));
                }
                exit;
            }
        }
    }

    public function getDaili() {
        $id = I('get.id');
        $daili = M('UserDaili')->field('uid,vip')->where(array('area_id' => $id))->order('vip desc')->select();
        foreach ($daili as $key => $value) {
            $user = M('User')->field('name,score')->where(array('id' => $value['uid']))->find();
            $daili[$key]['name'] = $user['name'];
            $daili[$key]['score'] = $user['score'];
            $insure = M('InsurancePlan')->field('divide_time')->where(array('daili_id' => $value['uid']))->order('id desc')->select();
            $daili[$key]['num'] = count($insure);
            if ($daili[$key]['num'] > 0) {
                $daili[$key]['time'] = date('Y-m-d H:i', $insure[0]['divide_time']);
            }
        }
        $result = get_array_multisort($daili, 'vip', 'score');
        $html = '';
        if ($result) {
            foreach ($result as $v) {
                $str = '';
                $str .= $v['vip'] ? 'VIP,' : '';
                $str .= '分配' . $v['num'] . '次';
                if ($v['num'] > 0) {
                    $str .= ',最近一次' . $v['time'];
                }
                $html .='<label style="width:350px;"><input type="radio" name="daili_id" value="' . $v['uid'] . '"/><b>' . $v['name'] . '</b>(' . $str . ')</label>';
            }
            echo json_encode(array("data" => $html, "s" => "1"));
        } else {
            echo json_encode(array("data" => $html, "s" => "0"));
        }
        exit;
    }

}