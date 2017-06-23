<?php
namespace Admin\Controller;
use Think\Controller;
class AdminCommonController extends Controller {
    public $userinfo = fasle;
    public function _initialize() {
        $admin_user = session('admin_user');
        //未登录，跳转登录口
        if (!$admin_user){
            $this->redirect('Public/login');
        }
        $this->userinfo = $admin_user;
        $this->assign('home_url', C('HOME_URL'));
        $this->assign('YOU_PAI_YUN',C('YOU_PAI_YUN'));
    }
}

?>