<?php

namespace Agent\Controller;

use Think\Upload\Driver\Upyun;
use Common\Extend\BrmApi;

class AccountController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 账户资料
     */
    final public function index() {
//        $certif = D('UserCertify')->getListById($this->userinfo['id']);
//        $result['idcard'] = $certif[1];
//        $result['qualify'] = $certif[2];
//        $this->assign('certif', $result);
        $this->assign('title', '我的信息');
        $this->display();
    }

    public function add() {
        $certif = D('UserCertify')->getListById($this->userinfo['id']);
        $result['idcard'] = $certif[1];
        $result['qualify'] = $certif[2];
        $this->assign('title', '我的信息修改');
        $this->assign('certif', $result);
        $this->display();
    }

    /**
     * 修改密码
     */
    final public function password() {
        if (IS_POST) {
            $cpass = I('post.cpass');
            $pass = I('post.pass');
            $brmApi = new BrmApi();
            $result = $brmApi->changeAgentPassword($this->userinfo['agent_id'], $cpass, $pass);

            if ($result) {
                $mes = array("s" => 1, "m" => "修改成功");
            } else {
                $mes = array("s" => 0, "m" => $result);
            }
            echo json_encode($mes);
            exit();
        }
        $this->assign('title', '修改密码_账户资料');
        $this->display();
    }

    /**
     * 添加代理人
     */
    final public function save() {
        if (IS_POST) {
            $email = I('post.mail');
            $password = I('post.userpassword');
            $nick_name = I('post.name');
            $code = I('post.code');
            $province = I('post.province');
            $city = I('post.city');
            $stree = I('post.stree');
            $company = I('post.company');
            $company_id = I('post.company_id');
            $phone = I('post.phone');

            $api = new BrmApi();
            $temp = $api->checkUserName($email);
            if ($temp['s'] < 1) {
                $this->ajaxReturn(array('code' => '201', 'msg' => '用户名已存在'));
            }
            $temp = $api->checkEmail($email);
            if ($temp['s'] < 1) {
                $this->ajaxReturn(array('code' => '202', 'msg' => '邮箱已存在'));
            }
            $api_data = $api->register($email, $password, $email, '', $province, $city);

            if ($api_data['s'] < 1) {
                $this->ajaxReturn(array('code' => '203', 'msg' => $api_data['r']));
            }

            $data = array(
                'id' => $api_data['r']['customerId'],
                'ctime' => date('Y-m-d H:i:s'),
                'nickname' => $nick_name,
                'name' => $nick_name,
                'mail' => $email,
                'ip' => get_client_ip(),
                'phone' => $phone,
                'type' => '1',
                'verify' => '1',
                'reg_code' => md5($email . time())
            );


            $model_user = M('User');
            $model_user->startTrans();
            $model_user->add($data);
            $id = $model_user->getLastInsID();
            if ($id) {
                $userDaili = array("uid" => $id, 'area_id' => $city, 'company_area' => $city, 'company_id' => $company_id, 'check_status' => '1');
                $api_data = $api->setAgent($this->userinfo['agent_id'], $api_data['r']['customerId']);
                $msg = '';
                if ($api_data['s'] < 1) {
                    $msg = '---代理商变更失败!---BRM: ' . $api_data['r'];
                } else {
                    $userDaili['agent_id'] = $this->userinfo['agent_id'];
                }
                M('UserDaili')->add($userDaili);
                D('UserPhoto')->add(array("name" => "no photo", "photo_thumb_path" => "/Uploads/user/no_photo_user.jpg", "uid" => $id, "ctime" => $data['ctime']));

                $model_user->commit();
                $this->ajaxReturn(array('code' => '200', 'data' => $data['id'], 'msg' => '注册成功!' . $msg));
            } else {
                $model_user->rollback();
                $this->ajaxReturn(array('code' => '210', 'msg' => '注册失败,请稍后重新注册!'));
            }
            exit;
        }
        exit;
    }

}