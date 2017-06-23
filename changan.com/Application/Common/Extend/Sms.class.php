<?php
namespace Common\Extend;
class Sms {
	public static function sendSMS($mobile,$content,$t) {
		$url='http://smsapi.c123.cn/OpenPlatform/OpenApi';
		$data = array (
	        'action'=>'sendOnce',
	        'ac'=>'1001@500941890002',
	        'authkey'=>'1BE3321F8D5FA2C791BE556B718AC7AE',
	        'cgid'=>'1664',
	        'csid'=>'1354',
	        'm'=>$mobile,
	        'c'=>$content,
	        't'=>$t
		);
		//POST方式提交
		$xml= self::postSMS($url,$data);
		$xml=simplexml_load_string(utf8_encode($xml));
		$r['result'] = json_decode($xml->attributes()->result);
		$r['msg'] = self::msgResult($r['result'],$mobile);
		return $r;
	}

	public static function msgResult($result,$mobile){
		$msg = "";
		switch($result){
			case  0: $msg = "帐户格式不正确(正确的格式为:员工编号@企业编号)";break;
			case  -1: $msg = "服务器拒绝(速度过快、限时或绑定IP不对等)如遇速度过快可延时再发";break;
			case  -2: $msg = " 密钥不正确";break;
			case  -3: $msg = "密钥已锁定";break;
			case  -4: $msg = "参数不正确(内容和号码不能为空，手机号码数过多，发送时间错误等)";break;
			case  -5: $msg = "无此帐户";break;
			case  -6: $msg = "帐户已锁定或已过期";break;
			case  -7:$msg = "帐户未开启接口发送";break;
			case  -8: $msg = "不可使用该通道组";break;
			case  -9: $msg = "帐户余额不足";break;
			case  -10: $msg = "内部错误";break;
			case  -11: $msg = "扣费失败";break;
			default:break;
		}
		return $msg;
	}

	public static function postSMS($url,$data='') {
		$row = parse_url($url);
		$host = $row['host'];
		$port = $row['port'] ? $row['port']:80;
		$file = $row['path'];
		while (list($k,$v) = each($data))
		{
			$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
		}
		$post = substr( $post , 0 , -1 );
		$len = strlen($post);
		$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
		if (!$fp) {
			return "$errstr ($errno)\n";
		} else {
			$receive = '';
			$out = "POST $file HTTP/1.0\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post;
			fwrite($fp, $out);
			while (!feof($fp)) {
				$receive .= fgets($fp, 128);
			}
			fclose($fp);
			$receive = explode("\r\n\r\n",$receive);
			unset($receive[0]);
			return implode("",$receive);
		}
	}
}
?>