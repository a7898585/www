<?php
/**
 * 百度翻译API
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-09
 */

namespace Common\Extend\Translate\Driver;


use Common\Extend\Translate\Translate;

class Baidu extends Translate{
    /**
     * 翻译API的地址
     */
    const DOMAIN_HOST = 'http://openapi.baidu.com/public/2.0/bmt/translate';
    /**
     * @var array 配置
     */
    private static $config = array(
        'from'      => 'auto',
        'to'        => 'auto',
        'clientId'  => ''
    );
    /**
     * @var array 错误信息
     */
    private static $error = array(
        500   => '调用API时出现网络错误或API服务器不可用，请稍后重试。',
        5004  => '调用API时缺少必要参数，请检查程序。',
        52001 => '待翻译内容过长，请分次调用翻译接口。',
        52002 => '未知翻译API错误，请联系API管理员。',
        52003 => '开发者ID错误，请确认后修正。'
    );
    public function __construct(array $config){
        self::$config = array_merge(self::$config, $config);
    }
    public function setConfig($name, $value){
        self::$config[$name] = $value;
    }
    /**
     * @param string $content 待翻译内容
     * @return array
     */
    public function run($content){
        $query['from'] = self::$config['from'];
        $query['to'] = self::$config['to'];
        $query['client_id'] = self::$config['clientId'];
        $query['q'] = $content;
        $result = $this->post(self::DOMAIN_HOST, $query);
        if (!$result){
            return array('status'=>500, 'message'=>self::$error[500]);
        }
        $result = json_decode($result, true);
        if (isset($result['error_code'])){
            return array('status'=>$result['error_code'], 'message'=>self::$error[$result['error_code']]);
        }
        $message = array();
        foreach($result['trans_result'] as $item){
            $message[] = $item['dst'];
        }
        return array('status'=>200, 'message'=>$message);
    }

}