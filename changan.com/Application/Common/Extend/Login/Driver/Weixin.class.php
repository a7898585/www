<?php
/**
 * 微信登录接口类
 * @author Jansen<6206574@qq.com>
 * @copyright 258.com
 * @since 2015-01-15
 */
namespace Common\Extend\Login\Driver;
use Common\Extend\Login\ThinkOauth;
class Weixin extends ThinkOauth{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://open.weixin.qq.com/connect/qrconnect';
    
    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    
    /**
     * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = 'scope=snsapi_login&state=test';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://api.weixin.qq.com/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    微博API
     * @param  string $param  调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false){
        /* 腾讯QQ调用公共参数 */
        $params = array(
            'access_token'       => $this->Token['access_token'],
            'openid'             => $this->openid()
        );
        
        $data = $this->http($this->url($api), $this->param($params, $param), $method);
        return json_decode($data, true);
    }
    
    /**
     * 解析access_token方法请求后的返回值 
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend){
        $data = json_decode($result, true);
        if($data['access_token'] && $data['expires_in']){
            $this->Token    = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            E("获取微信 ACCESS_TOKEN 出错：{$result}");
    }
    
    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid(){
        $data = $this->Token;
        if(isset($data['openid']))
            return $data['openid'];
        elseif($data['access_token']){
            $data = $this->http($this->url('sns/oauth2/refresh_token'), array('appid'=>$this->AppKey,'refresh_token'=>$data['access_token'],'grant_type'=>'refresh_token'));
            $data = json_decode(trim(substr($data, 9), " );\n"), true);
            if(isset($data['openid']))
                return $data['openid'];
            else
                E("获取用户openid出错：{$data['errmsg']}");
        } else {
            E('没有获取到openid！');
        }
    }
    
    /**
     * 请求code
     */
    final public function getRequestCodeURL(){
        $this->config();
        //Oauth 标准参数
        $params = array(
                'appid'         => $this->AppKey,
                'redirect_uri'  => $this->Callback,
                'response_type' => $this->ResponseType,
        );
    
        //获取额外参数
        if($this->Authorize){
            parse_str($this->Authorize, $_param);
            if(is_array($_param)){
                $params = array_merge($params, $_param);
            } else {
                E('AUTHORIZE配置不正确！');
            }
        }
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }
    
    /**
     * 获取access_token
     * @param string $code 上一步请求到的code
     */
    public function getAccessToken($code, $extend = null){
        $this->config();
        $params = array(
                'appid'     => $this->AppKey,
                'secret' => $this->AppSecret,
                'grant_type'    => $this->GrantType,
                'code'          => $code
        );
    
        $data = $this->http($this->GetAccessTokenURL, $params, 'POST');
        $this->Token = $this->parseToken($data, $extend);
        return $this->Token;
    }
}