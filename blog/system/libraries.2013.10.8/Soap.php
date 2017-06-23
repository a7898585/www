<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Soap Ver0.1
|--------------------------------------------------------------------------
| PHP5 使用PHP5自带的Soap,请开启SOAP扩展
| PHP4 使用NuSoap
|--------------------------------------------------------------------------
| 使用方法:
| $this->load->library('soap');
| $wsdl = 'xxx';
| $options = array('aa','bb');
| $this->soap->client_init($wsdl);
| $result = $this->soap->call('function', $options);
*/

class CI_Soap {
	var $client;
	var $server;
	var $phpver;
	function CI_Soap() {
		$this->phpver = phpversion();
	}

	function client_init($wsdl,$options=array()) {
		if($this->phpver>='5.0.0') {
			if(!class_exists('SoapClient')) show_error("Unable to load the requested base class: SoapClient");
			$this->client = new SoapClient($wsdl,$options);
		} else {
			$CI = & get_instance();
			$CI->load->plugin('nusoap');
			$this->client = new soapclient($wsdl, true);
			$this->client->soap_defencoding = 'UTF-8';
			$this->client->decode_utf8 = false; 
		}
	}

	function call($func,$options) {
		if($this->phpver>='5.0.0') {
			if(!is_object($this->client)) show_error("the client is not object");
			return $this->client->__soapCall($func,$options);
		} else {
			return $this->client->call($func,$options);
		}
	}
}
?>