<?php
/*
 * @thinkphp3.2.2  auth认证
 * @wamp2.1a  php5.3.3  mysql5.5.8
 * @Created on 2015/08/18
 * @Author  夏日不热    757891022@qq.com
 *
 */
namespace Admin\Controller;

use Think\Upload;
//不验证的控制器
class PublicController extends AdminCommonController {
    //登录验证
    public function login(){
		if(session('ADMIN_ID')){
			$this->redirect('已登录，正在跳转到主页',U('Index/index'));
		}
    	if(IS_POST){
    		$map['account'] = I('account');   //用户名
    		$map['password'] = md5(I('password', '', ''));	//密码
            $adminTable = M($this->buildModelName(C('AUTH_CONFIG.AUTH_USER')));
    		$result = $adminTable->where($map)->find();
    		if(empty($result)) {
                $this->ajaxReturn(array('status'=>404, 'message'=>'登录失败，用户名或密码错误。'));
            }
            if($result['status'] == '0'){
                $this->ajaxReturn(array('status'=>500, 'message'=>'登录失败，账号被禁用。'));
            }
            session('ADMIN_ID',$result['id']);	//管理员ID
            session('ADMIN_ACCOUNT',$result['account']);	//管理员用户名
            //保存登录信息
            $data['id'] = $result['id'];	//用户ID
            $data['login_ip'] = get_client_ip();	//最后登录IP
            $data['login_time'] = time();		//最后登录时间
            $data['login_count'] = array('EXP', '`login_count`+1');
            $adminTable->data($data)->save();
            $this->ajaxReturn(array('status'=>200, 'message'=>'登录成功。'));
    	}
        $this->display();
    }
    /**
     * UEditor图片上传配置相关
     * @param unknown $action
     */
    public function editor($action) {
        if ($action == 'config'){
            $upaiyun = C('UPAIYUN_CONFIG.UPYUN_UPLOAD_CONFIG');
            $config['imageActionName'] = 'upload';
            $config['imageFieldName'] = 'file';
            $config['imageMaxSize'] = 512000;
            $config['imageAllowFiles'] = array_map(function($value){
                return '.'.$value;
            }, explode(',', $upaiyun['suffix']));
            $config['imageInsertAlign'] = 'none';
            $config['imageUrlPrefix'] = 'http://'.$upaiyun['buckets'].'.b0.upaiyun.com';
            $config['imagePathFormat'] = '/activity/'.date('Ym').'/{random32}{.suffix}';
            $this->ajaxReturn($config);
        }elseif ($action == 'upload'){
            $upaiyun = C('UPAIYUN_CONFIG.UPYUN_UPLOAD_CONFIG');
            C('FILE_UPLOAD_TYPE', 'Upyun');
            C('UPLOAD_TYPE_CONFIG', array(
                'host' => $upaiyun['host'],
                'username'  => $upaiyun['username'],
                'password'  => $upaiyun['password'],
                'bucket'    => $upaiyun['buckets']
            ));
            $config['rootPath'] = '';
            $config['savePath'] = '/product/'.date('Ym').'/';
            $config['saveName'] = str_replace('.', '', uniqid('article', true));
            $config['exts'] = explode(',', $upaiyun['suffix']);
            $config['autoSub'] = false;
            $config['maxSize'] = 512000;
            $uploader = new Upload($config);
            $result = $uploader->upload();
            if (!$result){
                $this->ajaxReturn(array(
                    'state' => $uploader->getError(),
                    'url'   => '',
                    'title' => '',
                    'original'  => '',
                    'type'  => '',
                    'size'  => ''
                ));
            }else{
                $this->ajaxReturn(array(
                    'state'     => 'SUCCESS',
                    'url'       => $result['file']['savepath'].$result['file']['savename'],
                    'title'     => $result['file']['savename'],
                    'original'  => $result['file']['name'],
                    'type'      => $result['file']['ext'],
                    'size'      => $result['file']['size']
                ));
            }
        }
    }
}




