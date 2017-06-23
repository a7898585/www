<?php
/**
 * Solarwind.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-09
 */

namespace Common\Extend\Translate\Driver;


use Common\Extend\Translate\Translate;

class Solarwind extends Translate{
    /**
     * 翻译API的地址
     */
    const DOMAIN_HOST = 'http://www.9181.cn';
    /**
     * @var array 配置
     */
    private static $config = array(
        'to'        => 'addr'
    );
    private static $to = array(
        'org'   => array('file'=>'/c2e_org_v2.asp', 'param'=>'c_org'),
        'addr'  => array('file'=>'/c2e_add_v2.asp', 'param'=>'c_add'),
        'name'  => array('file'=>'/c2e_name_v2.asp', 'param'=>'c_name'),
        'duty'  => array('file'=>'/c2e_tit_v2.asp', 'param'=>'c_tit')
    );
    public function __construct(array $config){
        self::$config = array_merge(self::$config, $config);
    }
    public function setConfig($name, $value){
        self::$config[$name] = $value;
    }
    public function run($content){
        if (!in_array(self::$config['to'], array('addr','org','name','duty'))){
            return array('status'=>404, 'message'=>'无此类型。');
        }
        $url = self::DOMAIN_HOST.self::$to[self::$config['to']]['file'].'?'.self::$to[self::$config['to']]['param'].'='.urlencode(mb_strlen($content,'UTF-8')==1?($content.' '):$content);
        $result = $this->get($url);
        if (!$result){
            return array('status'=>500, 'message'=>'调用API时出现网络错误或API服务器不可用，请稍后重试。');
        }
        $result = json_decode(substr($result, 1, strlen($result)-3), true);
        return array('status'=>200, 'message'=>str_replace(' ', '', trim($result['data'])));
    }
}