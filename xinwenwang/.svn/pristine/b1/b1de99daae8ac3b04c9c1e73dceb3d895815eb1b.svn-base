<?php

namespace Port\Controller;
use Port\Model\UsersModel;
use Think\Controller;
use Think\Log;

class PortCommonController extends Controller {
    protected $responce = nulll;
    protected $uid = 0;
    public function _initialize() {
        $data = I('request.');
        if(!is_array($data)){
            $this->ajaxReturn(array('code'=>0,'respanse'=>'','msg'=>'错误'));
        }else{
            if($data['login_key']){
                $um = new UsersModel();
                $this->uid = $um->getUidByLoginKey($data['login_key']);
            }
            $this->responce = $data;
        }
    }
}
?>