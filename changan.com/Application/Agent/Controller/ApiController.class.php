<?php

namespace Agent\Controller;

use Org\Util\String;
use Think\Verify;
use Common\Extend\BrmApi;
class ApiController extends CommonController {

    /**
     * 省份
     */
    final public function province() {
        $list = D('Area')->province();
        foreach ($list as $item) {
            $data[$item['id']] = $item['name'];
        }
        echo json_encode($data);
        exit;
    }

    /**
     * 获取城市选项
     */
    final public function area() {
        $id = I('get.id');
        $level = I('get.level');
        $city = D('Area')->city($id);
        $data = array();
        $ids = array('001001001', '001002001', '001003001', '001004001');
        if (in_array($id, $ids) && $level == 2) {
            $name = getAreaName($id);
            $data[$id] = $name;
        } else {
            $list = D('Area')->city($id);
            foreach ($list as $item) {
                $data[$item['id']] = $item['name'];
            }
        }
        echo json_encode($data);
        exit;
    }

    /**
     * 账号验证 
     */
    public function check_email() {
        $email = I('post.param');
        $if_exist = D('User')->getOneRow(array("mail" => $email));
        if ($if_exist) {
            echo json_encode(array("info" => "验证未通过，用户名已存在！", "status" => "n"));
            exit;
        }
        $api = new BrmApi();
        $temp = $api->checkUserName($email);
        if ($temp['s'] < 1) {
            $this->ajaxReturn(array('code' => '201', 'msg' => '用户名已存在'));
        }
        $temp = $api->checkEmail($email);
        if ($temp['s'] < 1) {
            $this->ajaxReturn(array('code' => '202', 'msg' => '邮箱已存在'));
        }
        echo json_encode(array("info" => "验证通过！", "status" => "y"));
        exit;
    }

    /**
     * 验证码 显示
     */
    public function verifycode() {
        $verify = new Verify(array('length' => 4));
        $verify->entry('baoxianla_code');
    }

    /**
     * 验证码验证
     */
    public function verifycheck() {
        $verify = new Verify();
        $code = I('post.param');
        if ($verify->check($code, 'baoxianla_code')) {
            echo json_encode(array("info" => "验证通过！", "status" => "y"));
        } else {
            echo json_encode(array("info" => "验证未通过！", "status" => "n"));
        }
        exit;
    }

    public function news_comment() {
        $p = I('get.p');
        $id = I('get.id');
        $comment = W('News/comment', array($id, 1, $p, 5, I('get.showid'), 'news_comment'));
//        $this->ajaxReturn(array("info" => $comment, "status" => "y"));
    }

    public function company() {
        $query = I('get.query');
        $list = D('Company')->getAllRow(array('name' => array('like', '%' . $query . '%')), 'id,name');
        $data_all = array();
        foreach ($list as $item) {
            $data_all[] = array('value' => $item['name'], 'data' => $item['id']);
        }
        $array = array();
        $array['query'] = $query;
        $array['suggestions'] = $data_all;
        echo json_encode($array);
        exit;
    }

}