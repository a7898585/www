<?php

/**
 * AccountController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-09-30
 */
namespace Member\Controller;

use Common\Extend\WeiXin;

class AccountController extends MemberCommonController{
    public function _initialize(){
        parent::_initialize();
        $this->assign('m_tab', 'account');
    }
    public function index(){}
    
    /**
     * 个人资料修改
     */
    public function profile(){
        if(IS_POST){
            $data['mid'] = session('MEMBERINFO.id');
            // $data['realname'] = I('post.realname');
            // $data['mobile'] = I('post.mobile');
            $data['head_url'] = I('post.head_url');
            $data['tele'] = I('post.tele');
            $data['fax'] = I('post.fax');
            $data['province'] = I('post.province');
            $data['city'] = I('post.city');
            $data['county'] = I('post.county');
            $data['address'] = I('post.address');
            $data['postcode'] = I('post.postcode');
            $isExest = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->count();
            $op = ($isExest > 0)?'save':'add';
            try{
                $result = M('MembersProfile')->data($data)->$op();
            }catch(\Exception $e){
                $this->ajaxReturn(array('status'=>500, 'message'=>'保存个人资料失败，请重试。'));
            }
            if(!$result && $op == 'add'){
                $this->ajaxReturn(array('status'=>500, 'message'=>'保存个人资料失败，请重试。'));
            }
            D('Message')->addMessage(session('MEMBERINFO.id'), '个人资料变动', '尊敬的客户,您的个人资料已更改');
            $this->ajaxReturn(array('status'=>200, 'message'=>'保存个人资料成功。'));
        }
        $profile = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->find();
        $this->assign('profile', $profile);
        // 生成上传相关参数
        $upYun = getUpaiyunConfig('head');
        $this->assign('policy', $upYun['policy']);
        $this->assign('sign', $upYun['signature']);
        $this->display();
    }
    
    /**
     * 认证列表
     */
    public function auth(){
        $where['id'] = session('MEMBERINFO.id');
        $memberInfo = M('Members')->where($where)->find();
        $this->assign('memberInfo', $memberInfo);
        $profile = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->find();
        $this->assign('profile', $profile);
        $this->display();
    }
    
    /**
     * 手机认证
     */
    public function mobile(){
        $refer=I('get.refer');
        if(IS_POST){
            $data['mid'] = session('MEMBERINFO.id');
            $mobile = I('post.mobile');
            $data['mobile'] = $mobile;
            $code = I('post.code');
            if(!$code){
                $this->ajaxReturn(array('status'=>500, 'message'=>'手机验证码不能为空。'));
            }
            if(!$mobile){
                $this->ajaxReturn(array('status'=>500, 'message'=>'手机号不能为空。'));
            }
            $r = D('MobileCodes')->validCheckCode($mobile, $code);
            if($r['status'] == 200){
                $isExest = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->count();
                $op = ($isExest > 0)?'save':'add';
                try{
                    M('MembersProfile')->data($data)->$op();
                }catch(\Exception $e){
                    $this->ajaxReturn(array('status'=>500, 'message'=>'手机认证提交失败，请重试。'));
                }
                M('Members')->where(array('id'=>session('MEMBERINFO.id')))->data(array('mobile_status'=>'1'))->save();
                D('Message')->addMessage(session('MEMBERINFO.id'), '手机认证成功提醒', '尊敬的客户,您的手机已认证成功，已经绑定账户');
                $this->ajaxReturn(array('status'=>200, 'message'=>'认证成功'));
            }elseif($r['status'] == 300){
                $this->ajaxReturn(array('status'=>500, 'message'=>'您的验证码已经过期，请重新发送。'));
            }elseif($r['status'] == 400){
                $this->ajaxReturn(array('status'=>500, 'message'=>'请输入正确的验证码。'));
            }
        }
        $where['id'] = session('MEMBERINFO.id');
        $memberInfo = M('Members')->where($where)->find();
        $this->assign('memberInfo', $memberInfo);
        $profile = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->find();
        $this->assign('profile', $profile);
        $this->assign('refer', $refer);
        $this->display();
    }
    
    /**
     * 实名认证
     */
    public function shiming(){
        if(IS_POST){
            $data['mid'] = session('MEMBERINFO.id');
            $data['idcard'] = I('post.idcard');
            $pic = I('post.pic');
            $data['idcard_pic'] = json_encode($pic);
            $data['realname'] = I('post.realname');
            if(!$data['idcard']){
                $this->ajaxReturn(array('status'=>500, 'message'=>'身份证不能为空。'));
            }
            if(!$data['realname']){
                $this->ajaxReturn(array('status'=>500, 'message'=>'名字不能为空。'));
            }
            if(empty($pic)){
                $this->ajaxReturn(array('status'=>500, 'message'=>'身份证照片不能为空。'));
            }
            $isExest = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->count();
            $op = ($isExest > 0)?'save':'add';
            try{
                $result = M('MembersProfile')->data($data)->$op();
            }catch(\Exception $e){
                $this->ajaxReturn(array('status'=>500, 'message'=>'实名认证提交失败，请重试。'));
            }
            if(!$result){
                $this->ajaxReturn(array('status'=>500, 'message'=>'实名认证提交失败，请重试。'));
            }
            M('Members')->where(array('id'=>session('MEMBERINFO.id')))->data(array('auth_status'=>'2'))->save();
            $this->ajaxReturn(array('status'=>200, 'message'=>'实名认证已提交成功，我们尽快帮您审核。'));
        }
        // 生成上传相关参数
        $upYun = getUpaiyunConfig();
        $this->assign('policy', $upYun['policy']);
        $this->assign('sign', $upYun['signature']);
        
        $where['id'] = session('MEMBERINFO.id');
        $memberInfo = M('Members')->where($where)->find();
        $this->assign('memberInfo', $memberInfo);
        $profile = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->find();
        $profile['pic'] = json_decode($profile['idcard_pic'], true);
        $this->assign('profile', $profile);
        $this->display();
    }
    
    /**
     * 绑定微信
     */
    public function weixin(){
        $where['id'] = session('MEMBERINFO.id');
        if(I('get.type') == 'unsub'){
            $where['id'] = session('MEMBERINFO.id');
            $where['weixin'] = I('get.weixin');
            $r = M('Members')->where($where)->save(array('weixin'=>'', 'code_status'=>'1'));
            if($r){
                Header('Location:/account/weixin');
            }
            exit();
        }
        $weixinConfig = C('WEIXIN_CONFIG');
        $this->weixin = new WeiXin($weixinConfig['APP_ID'], $weixinConfig['APP_SECRET'], $weixinConfig['TOKEN'], $weixinConfig['ASE_KEY']);
        $memberInfo = M('Members')->where($where)->find();
        if($memberInfo['weixin']){
            $weixinInfo = $this->weixin->getUserInfo($memberInfo['weixin']);
            $this->assign('weixinInfo', $weixinInfo);
        }
        $this->assign('memberInfo', $memberInfo);
        $this->assign('weixin_url', $this->weixin->getQcode($memberInfo['id']));
        $this->display();
    }
    public function password(){
        $op = I('get.op');
        if(IS_POST){
            if($op == 'login'){
                $oldPassword = I('post.password_old', '', '');
                $newPassword = I('post.password_new', '', '');
                $confirmPassword = I('post.password_new_confirm', '', '');
                $result = $this->editPassword($oldPassword, $newPassword, $confirmPassword);
                if(!$result){
                    $this->ajaxReturn(array('status'=>500, 'message'=>'修改登录密码失败，请重试。'));
                }
                $this->ajaxReturn(array('status'=>200, 'message'=>'修改登录密码成功，下次登录请使用新密码。'));
            }elseif($op == 'seccode'){
                $mobile = M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->getField("mobile");
                $code = I('post.phone_code');
                $r = D('MobileCodes')->validCheckCode($mobile, $code);
                if($r['status'] == 200){
                    $newSeccode = I('post.seccode_new', '', '');
                    $confirmSeccode = I('post.seccode_new_confirm', '', '');
                    if($newSeccode != $confirmSeccode){
                        $this->ajaxReturn(array('status'=>500, 'message'=>'两次安全码输入不一样'));
                    }
                    $result = $this->editSecurityCode($newSeccode);
                    if(!$result){
                        $this->ajaxReturn(array('status'=>500, 'message'=>'安全码操作失败，请重试。'));
                    }
                    $this->ajaxReturn(array('status'=>200, 'message'=>'安全码操作成功。'));
                }elseif($r['status'] == 300){
                    $this->ajaxReturn(array('status'=>500, 'message'=>'您的验证码已经过期，请重新发送。'));
                }elseif($r['status'] == 400){
                    $this->ajaxReturn(array('status'=>500, 'message'=>'请输入正确的验证码。'));
                }
            }
        }
        $where['id'] = session('MEMBERINFO.id');
        $memberInfo = M('Members')->where($where)->find();
        $this->assign('op', $op);
        $this->assign('memberInfo', $memberInfo);
        $this->assign('mobile', M('MembersProfile')->where(array('mid'=>session('MEMBERINFO.id')))->getField("mobile"));
        $this->display();
    }
    
    // 修改登录密码
    private function editPassword($old, $new, $confirm){
        if($new != $confirm) return false;
        $where['id'] = session('MEMBERINFO.id');
        $where['password'] = md5($old);
        $memberInfo = M('Members')->where($where)->find();
        if(!is_array($memberInfo) || empty($memberInfo)) return false;
        $data['id'] = session('MEMBERINFO.id');
        $data['password'] = md5($new);
        $result = M('Members')->data($data)->save();
        return $result?true:false;
    }
    //修改安全码
    private function editSecurityCode($new){
        // 更新新安全码
        $data['id'] = session('MEMBERINFO.id');
        $data['seccode'] = md5($new);
        $result = M('Members')->data($data)->save();
        if(!$result) return false;
        session('MEMBERINFO.seccode', md5($new));
        return true;
    }
}