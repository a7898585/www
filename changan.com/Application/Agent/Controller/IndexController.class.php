<?php

namespace Agent\Controller;

use Common\Extend\BrmApi;

class IndexController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    final public function index() {
        $api = new BrmApi();
        $api_data = $api->agentInfo($this->userinfo['agent_id']);
        if ($api_data['s'] == 1) {
            $api_agent = $api_data['r'];
            $data = array(
                'mobile' => $api_agent['mobile'],
                'tel' => $api_agent['tele'],
                'province' => $api_agent['province'],
                'city' => $api_agent['city'],
                'email' => $api_agent['email'],
                'company' => $api_agent['company'],
                'contact' => $api_agent['realName'],
                'address' => $api_agent['address'],
                'money' => $api_agent['money'],
            );
            M('UserAgent')->where(array('agent_id' => $this->userinfo['agent_id']))->data($data)->save();
            $data['agent_id'] = $this->userinfo['agent_id'];
            $data['user_name'] = $this->userinfo['user_name'];
            session('bx_agent_info', $data);
            $this->assign("userInfo", $data);
        }
        $where['agent_id'] = $this->userinfo['agent_id'];
        $where['vip'] = '1';
        $where['buy_type'] = array('gt', 0);
        $data = D('UserDaili')->getList($where, 5, 'uid desc');
        $this->assign('data', $data);

        $this->assign('title', '代理商后台');
        $this->display();
    }

}