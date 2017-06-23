<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('sendsms');
| $this->sendsms->send();
|--------------------------------------------------------------------------
*/


class CI_Sendsms
{

    var $sms_mobiles;//必须是数组
    var $sms_content;
    var $channel;
    var $priority;
    var $checkcm;
     /**
     * 设置数据到cache
     *
     */
    function send($sms_mobiles, $sms_content,$checkcm=false,$channel=3,$priority=5)
    {
		// 发送内容或好吗为空，退出
		if(empty( $sms_mobiles )|| $sms_content=='')
 			return false;	  

	   /* 
	    *checkcm=true，移动的发信息机其它发短信王
	    *checkcm=FALSE，按默认的发送
	   */
		if($checkcm)
		{
  			$mobile = $this->checkcm( $sms_mobiles );
			$return = $this->send_mobile($mobile['yd'],1000);
			foreach ($return as $key)
			{
				$this->sendsms($key,$sms_content, 1);
			}

			$return = $this->send_mobile($mobile['qt'],1000);
			foreach ($return as $key)
			{
				$this->sendsms($key,$sms_content, 3);
			}

 		}
		else
		{
			$return = $this->send_mobile($sms_mobiles,1000);
   			foreach ($return as $key)
			{
				$mobile = implode(",", $key);
 				$this->sendsms($mobile,$sms_content, $channel);
			}
		
		}
 				
	}

    function sendmsg($sms_mobiles, $sms_content,$channel=3)
    {
		// 发送内容或好吗为空，退出
		if(empty( $sms_mobiles )|| $sms_content=='')
 			return false;	  

  		$this->sendsms($sms_mobiles,$sms_content, $channel);
	}

 	/**
	* Check if China Mobile
	*
	*/
	function checkcm( $mobile )
	{

		$prefix = array( 134, 135, 136, 137, 138, 139, 150, 151, 152, 156, 157, 158, 159, 188 );
		foreach ($sms_mobiles as $v)
		{
			 if( in_array( substr($mobile, 0, 3), $prefix ))
			 {
				$mobile['yd'][] = $v;
			 }
			 else
			 {
				$mobile['qt'][] = $v;
			 
			 }
		}
		return $mobile;

 	}


	/**
	* 短信发送
	*
	* @param $mobile 手机号, Post支持1000个  ,号隔开 send_mobile设置1000
	* @param $content  post不要使用urlencode, 本函数不设计此过程, 暂时只支持发GBK
	* @param $sendChannel 发送通道: 1. 移动信息机  2. 短信猫  3. 短信王  4. 莫名
	* @Channel 注: 短信王  移动号码返回021开头 .. 联通和电信, 1065, 小灵通0898
	*/
	function sendsms($mobile, $content, $sendChannel=3 )
	{
		require_once 'Snoopy.class.php';
		$sp = new Snoopy;
		$url = "http://mobilemessager.api.cnfol.net:8080/sms";

		$formvars['p.apiKey']      = "7777772E636E666F6C2E636F6D";
		$formvars['p.receiver']    = $mobile;
		$formvars['p.msg']         = iconv('utf-8','gbk',$content);
		$formvars['p.priority']    = 5;
		$formvars['p.sendChannel'] = $sendChannel;
  		$sp->submit($url, $formvars);
	}


	/**
	* 手机群划分
	*/
	function send_mobile($mobiles,$size)
	{   echo $mobiles;
        $mos=array_chunk($mobiles,$size);
		return $mos ? $mos : array();
	}
}

// END Sendsms Class

/* End of file Sendsms.php */
/* Location: ./system/libraries/Sendsms.php */
