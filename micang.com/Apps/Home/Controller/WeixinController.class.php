<?php

/**
 * WeixinController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-19
 */

namespace Home\Controller;

use Common\Extend\WeiXin;

class WeixinController extends HomeCommonController {

    private $weixin;

    public function _initialize() {
        $weixinConfig = C('WEIXIN_CONFIG');
        $this->weixin = new WeiXin($weixinConfig['APP_ID'], $weixinConfig['APP_SECRET'], $weixinConfig['TOKEN'], $weixinConfig['ASE_KEY']);
    }

    public function getTenplateId() {
        $this->weixin->getTenplateId();
    }

    public function getQcode() {
        $url = $this->weixin->getQcode();
        echo '<img src="' . $url . '">';
    }

    public function check_sign($signature, $timestamp, $nonce) {
        $echostr = I('get.echostr');
        if ($this->weixin->checkSignature($signature, $timestamp, $nonce)) {
            if ($echostr) {
                echo $echostr;
                exit;
            }
            $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
            $postObj = @simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = (array) $postObj->FromUserName;
            $toUsername = (array) $postObj->ToUserName;
            $createTime = (array) $postObj->CreateTime;
            $msgType = (array) $postObj->MsgType;
            $content = trim($postObj->Content) . '. 么么哒！';
            if ($msgType[0] == 'event') {
                //事件
                if ($postObj->Event[0] == 'subscribe') {//关注
                    $data['event'] = 1;
                    $eventKey = $postObj->EventKey[0];
                    $content = '感谢你的关注！米仓微信功能还在开发中，敬请期待。  么么哒！';
                    if ($eventKey) {
                        $eventKey = explode('_', $eventKey);
                        $data['mid'] = $eventKey[1];
                        $content = '感谢你的关注！您的微信已经绑定米仓ID:' . $data['mid'] . '。米仓微信功能还在开发中，敬请期待。  么么哒！';
                    }
                } elseif ($postObj->Event[0] == 'unsubscribe') {//取消关注
                    $data['event'] = 2;
                    $where['weixin'] = $fromUsername[0];
                    M('Members')->where($where)->save(array('weixin' => '', 'code_status' => '1'));
                } elseif ($postObj->Event[0] == 'SCAN') {//扫描二维码
                    $data['event'] = 3;
                    $eventKey = (array) $postObj->EventKey;
                    $data['mid'] = $eventKey[0];
                    $content = '您的微信已经绑定过米仓ID:' . $data['mid'] . '，无需重新绑定。米仓微信功能还在开发中，敬请期待。  么么哒！';
                }
                if ($data['mid']) {//绑定微信号
                    $id = M('Members')->where(array('weixin' => $fromUsername[0]))->getField('id');
                    if ($id) {
                        $content = '感谢你的关注！您的微信已经绑定过米仓ID:' . $id . '。不能重复绑定！';
                    } else {
                        M('Members')->where(array('id' => $data['mid']))->save(array('weixin' => $fromUsername[0]));
                    }
                }
            }
            $data['from_uname'] = $fromUsername[0];
            $data['to_uname'] = $toUsername[0];
            $data['addtime'] = $createTime[0];
            $data['content'] = trim($postObj->Content);
            $data['msg_type'] = $msgType[0];
            M('MembersWeixinLog')->add($data);
//            error_log(print_r($data, true) . "\r\n", 3, 'log.log');
            $resultStr = sprintf(C('WEIXIN_TEMPLATE.text_tpl'), $postObj->FromUserName, $postObj->ToUserName, time(), 'text', $content);
            echo $resultStr;
            exit;
        }
    }

    public function send_template_message($openId) {
        exit;
        if (!$this->weixin->getAccessToken())
            exit('get access token failed.');
        if (!$openId)
            return false;
//        $templateId = 'CzPcuS9uB0Xe9mLjLshyVkpyqzU3PR8iVSUSmKOoA3o';
        $templateId = 'MT307qD8w9UJgkEjJ1URRDKdSE0bszt22lVq4QodJF8';
        $url = 'http://www.micang.com';
        $data['first'] = array(
            'value' => '尊敬的蒋帅',
            'color' => '#173177'
        );
        $data['keyword1'] = array(
            'value' => '执行域名PUSH操作',
            'color' => '#173177'
        );
        $data['keyword2'] = array(
            'value' => rand(100000, 999999),
            'color' => '#ff0000'
        );
        $data['remark'] = array(
            'value' => "若本次交易非本人发起，请速修改您的密码或联系客服人员。[米仓网]\r\n【发送时间" . date('H:i:s') . '】',
            'color' => '#9999FF'
        );
        $result = $this->weixin->sendTemplateMessage($templateId, $openId, $url, $data);
        if (!$result)
            exit('验证码发送失败,请重试.');
        exit('验证码已发送.');
    }

}