<?php

namespace Port\Controller;
use Common\Extend;
class MailController extends PortCommonController{
	var $socket; //socket 句柄
	var $host; //主机
	var $port; //端口一般为25
	var $user; //SMTP认证的帐号
	var $pass; //认证密码
	var $debug = false;   //是否显示和服务器会话信息？
	var $conn;
	var $result_str;   //结果
	var $in; //客户机发送的命令
	var $from_r; //真实的源信箱,一般与smtp服务器的用户名一样，否则可能由于smtp服务器的设置而发送不成功
	var $mailformat = 0; //邮件格式 0=普通文本 1=html邮件

	function __construct($host = 'smtp.126.com', $port = 25, $user = 'ydleternal@126.com', $pass = 'nwcziontig', $debug = false) {
		$host = gethostbyname($host);
		$this->host = $host;
		$this->port = $port;
		$this->user = base64_encode($user);
		$this->pass = base64_encode($pass);
		$this->debug = $debug;
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); //具体用法请参考手册
		if ($this->socket) {
			$this->result_str = "创建SOCKET:" . socket_strerror(socket_last_error());
			$this->debug_show($this->result_str);
		} else {
			exit("初始化失败");
		}
		$this->conn = socket_connect($this->socket, $this->host, $this->port);
		if ($this->conn) {
			$this->result_str = "创建SOCKET连接:" . socket_strerror(socket_last_error());
			$this->debug_show($this->result_str);
		} else {
			exit("初始化失败，请检查您的网络连接和参数");
		}
		$this->result_str = "服务器应答：<font color=#cc0000>" . socket_read($this->socket, 1024) . "</font>";
		$this->debug_show($this->result_str);
	}

	function debug_show($str) {
		if ($this->debug) {
			echo $str . "<p>\r\n";
		}
	}

	function send($name, $from, $to, $subject, $body) {
		if ($from == "" || $to == "") {
			exit("请输入信箱地址");
		}
		if ($subject == "")
			$subject = "无标题";
                $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
		if ($body == "")
			$body = "无内容";
		$All = "From:" . $name . "<" . $from . ">\r\n";
		$All.= "To:" . $to . "\r\n";
		$All.= "Subject:" . $subject . "\r\n";
                $All.= "MIME-Version: 1.0\r\n";
		if ($this->mailformat == 1)
			$All.= "Content-Type: text/html;charset=utf-8;\r\n";
		else
			$All .= "Content-Type: text/plain;charset=utf-8;\r\n";
		$All.= "Content-Transfer-Encoding: 8bit\r\n\r\n"; //告诉浏览器信件内容进过了base64编码，最后必须要发一组\r\n产生一个空行,表示头部信息发送完毕
		$All.= $body;
		/*
		  如果把$All的内容再加处理，就可以实现发送MIME邮件了
		  不过还需要加很多程序
		 */

		//以下是和服务器会话
		$this->in = "EHLO HELO\r\n";
		$this->docommand();

		$this->in = "AUTH LOGIN\r\n";
		$this->docommand();

		$this->in = $this->user . "\r\n";
		$this->docommand();

		$this->in = $this->pass . "\r\n";
		$this->docommand();

		if (!eregi("235", $this->result_str)) {
			$this->result_str = "smtp 认证失败";
			$this->debug_show($this->result_str);
			return 0;
		}

		$this->in = "MAIL FROM:<" . $from . ">\r\n";
		$this->docommand();

		$this->in = "RCPT TO:<" . $to . ">\r\n";
		$this->docommand();

		$this->in = "DATA\r\n";
		$this->docommand();

		$this->in = $All . "\r\n.\r\n";
		$this->docommand();

		if (!eregi("250", $this->result_str)) {
			$this->result_str = "邮件发送失败";
			$this->debug_show($this->result_str);
			return 0;
		}
		$this->in = "QUIT\r\n";
		$this->docommand();

		//结束，关闭连接  
		return 1;
	}

	function docommand() {
		socket_write($this->socket, $this->in, strlen($this->in));
		$this->debug_show("客户机命令：" . $this->in);
		$this->result_str = "服务器应答：<font color=#cc0000>" . socket_read($this->socket, 1024) . "</font>";
		$this->debug_show($this->result_str);
	}

	//设置邮件格式为HTML格式
	function isHTML() {
		$this->mailformat = 1;
	}
        
}

//*******实例******//
//$mail = new smtp_mail('smtp.exmail.qq.com', 25, 'admin@zhezhekan.com', '12345678ztt');
//$mail->isHTML();
//$mail->send('发件人名称', 'zhezhekan@g9x.com', '收件邮箱', '邮件标题', '邮件内容');