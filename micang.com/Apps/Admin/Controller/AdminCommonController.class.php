<?php
/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;
//总管理后台公共基类模块
class AdminCommonController extends Controller {
    //无需验证登录状态的控制器
    protected static $publicController = array('PUBLIC');
    protected function _initialize(){
        //验证控制器是否需要做登录验证
        if (in_array(strtoupper(CONTROLLER_NAME), self::$publicController)){
            return true;
        }
        //session不存在时，不允许直接访问
        if(!session('ADMIN_ID')){
            $this->error('还没有登录，正在跳转到登录页',U('Public/login'));
        }
        //下面代码动态判断权限
        $auth = new Auth();
        if(!$auth->check(CONTROLLER_NAME.'/'.ACTION_NAME,session('ADMIN_ID')) && session('ADMIN_ID') != 1){
            $this->error('您没有操作此功能的权限，请联系管理员开通。');
        }
        $this->assign('menu', $this->getMenu());
    }
    private function getMenu(){
        $ruleTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_RULE')));
        $field = 'id,name,title';
        $data = $ruleTable->field($field)->where(array('pid'=>0, 'status'=>1, 'show'=>'1'))->order(array('sort'=>'ASC'))->select();
        $auth = new Auth();
        //没有权限的菜单不显示
        foreach ($data as $k => $v){
            if(!$auth->check($v['name'], session('ADMIN_ID')) && session('ADMIN_ID') != 1){
                unset($data[$k]);
            }else{
                // status = 1    为菜单显示状态
                $data[$k]['children'] = $ruleTable->field($field)->where(array('pid'=>$v['id'], 'status'=>1, 'show'=>'1'))->select();
                foreach ($v['children'] as $k2 => $v2){
                    if(!$auth->check($v2['name'], session('ADMIN_ID')) && session('ADMIN_ID') != 1){
                        unset($v['children'][$k2]);
                    }
                }
            }
        }
        return $data;
    }
    protected function buildModelName($table){
        return str_replace(' ', '', ucwords(str_replace(array(C('DB_PREFIX'), '_'), array('',' '), $table)));
    }
    // 空方法
	final public function _empty(){
        send_http_status(404);
        $this->display('Public/404');
        exit();
    }

}



