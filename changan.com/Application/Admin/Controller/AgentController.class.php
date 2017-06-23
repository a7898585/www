<?php

namespace Admin\Controller;

use Common\Extend\BrmApi;

class AgentController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'agent');
    }
    final public function index() {
        $name = I('get.name', '', 'trim');
        $where = array();
        if ($name) {
            $where['user_name'] = array('like', '%' . $name . '%');
        }
        $list = M('UserAgent')->where($where)->order('ctime desc')->select();
        foreach($list as &$item){
            $item['province_show'] = getAreaName($item['province']);
            $item['city_show'] = getAreaName($item['city']);
        }
        $this->assign('list', $list);
        $this->display();
    }
    /**
     *添加代理商
     */
    final public function add_agent(){
        $id = I('post.agent_id','0','intval');
        if(empty($id)){
            $this->ajaxReturn(array('code'=>203,'msg'=>'代理商ID有误请重试!'));
        }
        $count = M('UserAgent')->where(array('agent_id'=>$id))->count();
        if($count){
            $this->ajaxReturn(array('code'=>202,'msg'=>'代理商已存在常安!'));
        }
        $api = new BrmApi();
        $api_data = $api->agentInfo($id);
        if($api_data['s']<1){
            $this->ajaxReturn(array('code'=>201,'msg'=>'此代理商不存在!!!'));
        }
        $api_agent = $api_data['r'];
        $data = array(
            'agent_id'=>$api_agent['agentId'],
            'user_name'=>$api_agent['userName'],
            'mobile'=>$api_agent['mobile'],
            'tel'=>$api_agent['tele'],
            'province'=>$api_agent['province'],
            'city'=>$api_agent['city'],
            'email'=>$api_agent['email'],
            'company'=>$api_agent['company'],
            'contact'=>$api_agent['realName'],
            'address'=>$api_agent['address'],
            'money'=>$api_agent['money'],
            'ctime'=>$api_agent['insertTime'],
            'utime'=>$api_agent['updateTime']
        );
        $id = M('UserAgent')->data($data)->add();
        if(!$id&&M('UserAgent')->getDbError()){
            $this->ajaxReturn(array('code'=>204,'msg'=>'添加失败'));
        }
        $this->ajaxReturn(array('code'=>200,'msg'=>'代理商添加成功'));
    }
    final public function agent_status(){
        $agent_id = I('post.agent_id',0,'intval');
        $agent = M('UserAgent')->where(array('agent_id'=>$agent_id))->find();
        if($agent){
            $status = $agent['status']?'0':'1';
            $temp = M('UserAgent')->data(array('status'=>$status))->where(array('agent_id'=>$agent_id))->save();
            if(!$temp&&M('UserAgent')->getDbError()){
                $this->ajaxReturn(array('code'=>201,'msg'=>'操作失败'));
            }
            $this->ajaxReturn(array('code'=>200));
        }
        $this->ajaxReturn(array('code'=>202,'msg'=>'代理商不存在'));

    }

    /*     * *************代理人*************** */


    final public function add() {
        if (IS_POST) {
            $aid = I('post.id');
            $agentArr = explode(',', str_replace('，', ',', $aid));

            foreach ($agentArr as $agent_id) {
                $u = D('UserAgent')->getInfo($agent_id);
                if ($u) {
                    continue;
                }
                $brmApi = new BrmApi();
                $agent = $brmApi->agentInfo($agent_id); //'500002906'
                if (!empty($agent)) {
                    $data['agent_id'] = $agent['agentId'];
                    $data['user_name'] = $agent['userName'];
                    $data['password'] = $agent['password'];
                    $data['mobile'] = $agent['mobile'];
                    $data['tel'] = $agent['tele'];
                    $data['email'] = $agent['email'];
                    $data['address'] = $agent['address'];
                    $data['company'] = $agent['company'];
                    $data['province'] = $agent['province'];
                    $data['city'] = $agent['city'];
                    $data['web_site'] = $agent['typeOne'];
                    $data['ctime'] = $agent['insertTime'];
                    $data['status'] = $agent['status'];
                    $data['money'] = $agent['money'];
                    $data['lastlogintime'] = $agent['lastLoginTime'];
                    $data['contact'] = $agent['realName'];
                    $userAgent = M('UserAgent');
                    if ($userAgent->create($data)) {
                        $r = $userAgent->add();
                    }
                }
            }
            if ($r) {
                $this->success('添加成功');
            } else {
                $this->error('已经添加过代理商');
            }
            exit;
        }
        $this->display();
    }

    /**
     * 代理人审核
     */
    final public function setStatus() {
        $id = I('post.id');
        $status = I('post.status');
        $temp = M('UserAgent')->data(array('status' => $status, 'agent_id' => $id))->save();
        if (!$temp && M('UserAgent')->getDbError()) {
            $this->ajaxReturn(array('code' => 201));
        }
        $this->ajaxReturn(array('code' => 200));
    }

}