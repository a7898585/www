<?php if(!defined('ROOT')) exit('access deny!');
class CSocket  
{
    var $socket;
    var $timeout = 30;
    var $socket_key = '                                ';

    /**
     * 构造函数
     */

    function CSocket()
    {
		global $config;
        if($config['blog_socket']['key']!='')
        {
            $this->socket_key = $config['blog_socket']['key'];
        }
    }

    /*
    * 发送数据到socket并取得返回数据.
    */
    function senddata($type, $data)
    {
		global $config;
		$filename = '/home/html/logs/statnew_senddata_'.date("Ymd").'.log';  
		if (file_exists($filename) === false) {
			$fp = fopen($filename, 'w+'); 
			chmod($filename, 0777);
			fclose($fp);
		}
        $this->socket = fsockopen($config['blog_socket']['host'], $config['blog_socket']['port'], $errno, $errstr, $this->timeout);
        if(!$this->socket)
		{
			error_log(date("Y-m-d H:i:s")." | ".__FILE__." | ".__METHOD__." | socket connect error $errstr ($errno) | type:$type \r\n", 3, $filename);
			return false;
        }
		else
		{
            $strSend = $this->array2str($type,$data);

            if (fwrite($this->socket, $strSend) === false)
			{
				error_log("写socket失败 \r\n", 3, $filename);
                return false;
            }
            $strHead  = fread($this->socket,12);//获取12位头信息
            if ($strHead === false)
			{
				error_log("获取12位头信息失败 \r\n", 3, $filename);
                return false;
            }
			else
			{
                $strLen = substr($strHead,4);//截取后面8位的数值
                $strLen = ltrim($strLen,'0');//截取左边多余的0
            }

            $response = '';
            while (0 < $strLen)
            {
                $content    = fread($this->socket, $strLen);
                $response   .= $content;
                $len        = strlen($content);
                $strLen    -= $len;
            }
        }
		$response = $this->xml2array($response);
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

	function xml2array($xml)
	{
		if (empty($xml))
		{
			return false;
		}
		$rs = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

		if ($rs) 
		{
			$this->_objectToArray($rs);
			return $rs;
		}
		else
		{
			return false;
		}
	}
	
	function _objectToArray(&$object) 
	{
		if(!empty($object))
		{
			$object = (array)$object;
			foreach ($object as $key => $value)
			{
				if (is_object($value)||is_array($value))
				{   
					$this->_objectToArray($value);
					$object[$key] = $value;
				} 
			}
		}
		else
		{
			$object = '';
		}
	}
}

// END Socket Class
