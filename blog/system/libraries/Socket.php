<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Socket  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('Socket');
|--------------------------------------------------------------------------
*/


class CI_Socket  {

    var $socket;
    var $timeout = 10;
    var $socket_key = '                                ';
    var $configs;
    var $debug;
    var $socket_log = 'base/socket.log';

    /**
     * 构造函数
     */

    function CI_Socket($socket='passport_socket')
    {
        # 取得配置信息
        $config        = & get_config();
		
        $this->configs = $config[$socket];
        if($this->configs['key']!='')
        {
            $this->socket_key = $this->configs['key'];
        }
        $this->socket_log = 'base/socket'.date('Ymd').'.log';
    }

    /*
    * 发送数据到socket并取得返回数据
    */
     function senddata($type,$data)
    {	
    	$time1 = microtime(true);
    	
    	
        
        $this->socket = fsockopen($this->configs['host'],$this->configs['port'], $errno, $errstr,$this->timeout);
        //if($data['UserID']=='6536425')
        //{
         //$i_blog_log = '/var/tmp/i_blog_log_'.date('Y_m_d').'.log';
        //	 error_log(date('H:i:s').":GWTYPE-{$type}:".print_r($data, true).PHP_EOL, 3, $i_blog_log);
        //}
        if(!$this->socket){
             log_write("$errstr ($errno)",$this->socket_log,__METHOD__ ,'ERROR');
             return false;
        }else{
            $strSend = $this->array2str($type,$data);

            if (fwrite($this->socket, $strSend) === false) {
                log_write("写socket失败",$this->socket_log,__METHOD__ ,'ERROR');
                return false;
            }
            $strHead  = fread($this->socket,12);//获取12位头信息
            if ($strHead === false) {
                log_write("获取12位头信息失败",$this->socket_log,__METHOD__ ,'ERROR');
                return false;
            }else{
                $strLen = substr($strHead,4);//截取后面8位的数值
                $strLen = ltrim($strLen,'0');//截取左边多余的0
            }

            $response = '';
            while (0<$strLen)
            {
                $content    = fread($this->socket, $strLen);
                $response   .= $content;
                $len        = strlen($content);
                $strLen    -= $len;
            }
        }
        $time2 = microtime(true);
        //记录所有请求时间超过10秒的请求
        if((intval($time2) - intval($time1)) > 10)
        {
        	$slow_request_log = '/var/tmp/slow_request_log_'.date('Y_m_d').'.log';
        	error_log(date('H:i:s')."	".(intval($time2) - intval($time1)).":GWTYPE-{$type}:".print_r($data, true).PHP_EOL.":response-".var_export($response, true).PHP_EOL, 3, $slow_request_log);
        }
        
        if(empty($response))
        {
        	$fail_request_log = '/var/tmp/fail_request_log_'.date('Y_m_d').'.log';
        	error_log(date('H:i:s').":GWTYPE-{$type}:".print_r($data, true).PHP_EOL, 3, $fail_request_log);
        }
       // $response = $this->str2array($response);
        fclose($this->socket);
        return $response;
     }


    /*
       转换输入字符串格式
    */
    function array2str($type,$array)
    {
        $tmp = '';
        $strData  = '<CNFOLGW><Parameters>';
        if(is_array($array) && !empty($array))
        {
            foreach($array as $k=>$v)
            {
                $tmp .= '<'.$k.'>'.$v.'</'.$k.'>';
            }
            unset($array);
        }
        $strData .= $tmp.'</Parameters></CNFOLGW>';
        $strLen   = str_pad(strlen($strData),8,'0',STR_PAD_LEFT);//计算长度并左边补零
        $strData  = $type.$this->socket_key.$strLen.$strData;//连接数据
        return $strData ;
    }


    /*
       xml转换成数组
    
    function str2array($str)
    {
        if($str=='')
        {
           return false;
        }
        include_once('SofeeXmlParser.php');
        $xml = new CI_SofeeXmlParser();
        $xml->parseString($str);
        $array = $xml->getTree();
        unset($xml);
        return $array ;
    }*/

}

// END Socket Class

/* End of file Socket.php */
/* Location: ./system/libraries/Socket.php */
