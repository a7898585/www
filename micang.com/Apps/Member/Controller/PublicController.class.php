<?php

/**
 * PublicController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-09-29
 */
namespace Member\Controller;

use Common\Extend\PDCApi;
use Common\Extend\Translate;

class PublicController extends MemberCommonController{
    public function ajax_get_area($type, $parent){
        $this->ajaxReturn(PDCApi::$type($parent));
    }
    public function ajax_translate_solarwind($content, $type){
        $translate = new Translate('solarwind', array('to'=>$type));
        $result = $translate->run($content);
        $this->ajaxReturn($result);
    }
    /*
     * ajax检查安全码
     */
    public function ajax_seccode(){
        $returnType = I('request.' . C('VAR_JSONP_HANDLER'))?'jsonp':'';
        $mid = session('MEMBERINFO.id');
        if(!$mid){
            $this->ajaxReturn(array('status'=>3, 'message'=>'登陆失败'), $returnType);
        }
        if(isSeccodeTimeout(false) == 1){
            // 安全还在有效期，无需验证
            $this->ajaxReturn(array('status'=>1, 'message'=>'验证成功'), $returnType);
        }
        if(!I("request.seccode")) {
            $this->ajaxReturn(array('status'=>4, 'message'=>'请输入安全码'), $returnType);
        }
        // 验证状态
        $memberSeccode = M('Members')->where(array('id'=>$mid))->field('seccode')->find();
        if (!is_array($memberSeccode)){
            $this->ajaxReturn(array('status'=>2, 'message'=>'验证失败'), $returnType);
        }
        if (empty($memberSeccode)){
            $this->ajaxReturn(array('status'=>2, 'message'=>'您还没有设置安全码，请先设置。'), $returnType);
        }
        if(md5(I("request.seccode")) != $memberSeccode['seccode']){
            $this->ajaxReturn(array('status'=>2, 'message'=>'安全码输入错误，请重试。'), $returnType);
        }
        session('MEMBER_SECCODE_TIMEOUT', time() + 300);
        $this->ajaxReturn(array('status'=>1, 'message'=>'验证成功'), $returnType);
    }
    /**
     * 获取最新消息
     */
    public function showmessage(){
        $mid = session('MEMBERINFO.id');
        $info = D('Message')->getInfoByMid($mid);
        if(!empty($info)){
            $this->ajaxReturn(array('status'=>200, 'message'=>'成功', 'data'=>$info));
        }else{
            $this->ajaxReturn(array('status'=>300, 'message'=>'无数据'));
        }
    }
    
    /**
     * 是否绑定微信
     */
    public function ajax_get_member_winxin(){
        $where['id'] = session('MEMBERINFO.id');
        if(!$where['id']){
            $this->ajaxReturn(array('status'=>400, 'message'=>'请登录'));
        }
        $memberInfo = M('Members')->where($where)->find();
        if($memberInfo['weixin']){
            $weixinConfig = C('WEIXIN_CONFIG');
            $this->weixin = new \Common\Extend\WeiXin($weixinConfig['APP_ID'], $weixinConfig['APP_SECRET'], $weixinConfig['TOKEN'], $weixinConfig['ASE_KEY']);
            $weixinInfo = $this->weixin->getUserInfo($memberInfo['weixin']);
            $weixinInfo['headimgurl'] = substr($weixinInfo['headimgurl'], 0, -1) . '64';
            $weixinInfo['weixin_url'] = $this->weixin->getQcode($where['id']);
            $this->ajaxReturn(array('status'=>200, 'message'=>'成功', 'data'=>$weixinInfo));
        }else{
            $this->ajaxReturn(array('status'=>300, 'message'=>'无数据'));
        }
    }
}