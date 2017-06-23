<?php

namespace Member\Controller;

class SetController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'account');
    }

    public function index() {
        $data = M('MembersSet')->where(array('mid' => session('MEMBERINFO.id')))->select();
        if (empty($data)) {
            $data = defaultMembersSet(session('MEMBERINFO.id'));
        }
        $result = array();
        foreach ($data as $value) {
            $result[$value['type']][$value['mode']] = $value;
        }
        foreach ($result[2] as &$value) {
            $value['other_data'] = unserialize($value['other']);
        }
        $this->assign('safe', $result[0]);
        $this->assign('notice', $result[1]);
        $this->assign('default', $result[2]);
        $templates_com = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id'), 'status' => '1', 'type' => '0'))->getField('id,title');
        $templates_cn = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id'), 'status' => '1', 'type' => '1'))->getField('id,title');
        $this->assign('templates_com', $templates_com);
        $this->assign('templates_cn', $templates_cn);
        $this->display();
    }

    /**
     * 添加安全设置
     */
    public function add_safe() {
        if (IS_POST) {
            $data = I('post.');
            $where['mid'] = session('MEMBERINFO.id');
            $where['type'] = '0';
            if ($data['stype'] == '1') {
                for ($index = 0; $index < 11; $index++) {
                    $where['mode'] = $index;
                    switch ($index) {
                        case '0':
                            $param['code'] = '0';
                            break;
                        case '10':
                            $param['code'] = '0';
                            break;
                        default:
                            $param['code'] = '1';
                            break;
                    }
                    $param['email'] = '0';
                    $param['mobile'] = '0';
                    M('MembersSet')->where($where)->save($param);
                }
            } else {
                foreach ($data['mode'] as $mode) {
                    $where['mode'] = $mode;
                    $param['code'] = $data['code'][$mode];
                    $param['mobile'] = $data['mobile'][$mode];
                    $param['email'] = $data['email'][$mode];
                    M('MembersSet')->where($where)->save($param);
                }
            }
            $this->success('设置成功', '/set/');
        }
    }

    /**
     * 添加常用提醒
     */
    public function add_notice() {
        if (IS_POST) {
            $data = I('post.');
            $where['mid'] = session('MEMBERINFO.id');
            $where['type'] = '1';
            if ($data['ntype'] == '1') {
                for ($index = 0; $index < 11; $index++) {
                    $where['mode'] = $index;
                    $param['email'] = '1';
                    $param['mobile'] = '1';
                    M('MembersSet')->where($where)->save($param);
                }
            } else {
                foreach ($data['mode'] as $mode) {
                    $where['mode'] = $mode;
                    $param['mobile'] = $data['mobile'][$mode];
                    $param['email'] = $data['email'][$mode];
                    M('MembersSet')->where($where)->save($param);
                }
            }
            $this->success('设置成功', '/set/?t=1');
        }
    }

    /**
     * 添加默认设置
     */
    public function add_default() {
        if (IS_POST) {
            $data = I('post.');
            $where['mid'] = session('MEMBERINFO.id');
            $where['type'] = '2';
            if ($data['dtype'] == '1') {
                for ($index = 0; $index < 3; $index++) {
                    $where['mode'] = $index;
                    $param['other'] = '';
                    M('MembersSet')->where($where)->save($param);
                }
            } else {
                foreach ($data['mode'] as $mode) {
                    $where['mode'] = $mode;
                    $param['email'] = '0';
                    $param['mobile'] = '0';
                    $param['other'] = serialize($data['other'][$mode]);
                    M('MembersSet')->where($where)->save($param);
                }
            }
            $this->success('设置成功', '/set/?t=2');
        }
    }

}