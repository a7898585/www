<?php

namespace Admin\Controller;

use Org\Util\String;
use Think\Verify;
use Think\Controller;

class ApiController extends Controller {

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

    /**
     * 上传头像
     */
    public function uploadtx() {
        if ($_FILES) {
            
            $rootPath = 'Uploads/company/' . date('Y-m-d') . '/';
            $pic_type = substr($_FILES['Filedata']["name"], strrpos($_FILES['Filedata']["name"], "."));
            $upload = new \Think\Upload(array("rootPath" => "./", "savePath" => $rootPath, "replace" => true, "subName" => "", "saveName" => ""));

            if ($_FILES['Filedata']) {
                $r = $upload->upload(array(
                    "file_name" => array("name" => md5(time() . 'company') . $pic_type,
                        "type" => "image/jpeg",
                        "tmp_name" => $_FILES['Filedata']['tmp_name'])
                        ));
            }
            $pic_path = $r['file_name']['savepath'] . $r['file_name']['savename'];

            $filedata = array(
                'result' => 'true',
                'name' => $_FILES['Filedata']["name"],
                'filepath_url' => C('YOU_PAI_YUN') . '/' . $pic_path,
                'filepath' => '/' . $pic_path,
                'type' => I("get.t"),
            );
            echo json_encode($filedata);
            exit;
        }
    }

}