<?php
namespace Common\Extend;
class Login {
	public $version = '3.0.0';
	protected $variables = array ();
	protected $domains = '';
	protected $path = '/';
	protected $expiretime = 1200;
	protected $hashcode = '1l2y3s';
	protected $timestamp = 0;

	public function __construct() {
	if (C("COOKIE_DOMAIN")){
			$this->domains = C("COOKIE_DOMAIN");
		}
		
		if (C("COOKIE_PATH")){
			$this->path = C("COOKIE_PATH");
		}

		if (C("COOKIE_EXPIRETIME")){
			$this->expiretime = C("COOKIE_EXPIRETIME");
		}
		
		if (C("COOKIE_HASHCODE")) {
			$this->hashcode = C("COOKIE_HASHCODE");
		}
		$this->timestamp = time ();
	}
	public function __destruct() {
		unset ( $this );
	}

	public function Set($Name, $Value = null) {
		if ($Value) {
			$this->$Name = $Value;
			$this->Update ();
		} else {
			$Value = $this->Get ( $Name );
			if ($Value) {
				$this->$Name = $Value;
				$this->Update ();
			}
		}
		return $Value;
	}
	
	public function logOut(){
		Cookie::delete(C('LOGIN_KEY'));
	}

	public function ClearSession($Name) {
		$this->$Name = false;
		$this->Update ();
	}

	public function getenv($strName) {
		$r = NULL;
		if (isset ( $_SERVER [$strName] )) {
			return $_SERVER [$strName];
		} elseif (isset ( $_ENV [$strName] )) {
			return $_ENV [$strName];
		} elseif ($r = getenv ( $strName )) {
			return $r;
		} elseif (function_exists ( 'apache_getenv' ) && $r = apache_getenv ( $strName, true )) {
			return $r;
		}
		return '';
	}
	public function get_cookie_domain() {
		$host_domain = $_SERVER ["HTTP_HOST"];
		if (strpos ( $host_domain, ':' ) !== false) {
			$host_domain = substr ( $host_domain, 0, strpos ( $host_domain, ':' ) );
		}
		$host_domains = explode ( ".", $host_domain );
		$host_domains_count = count ( $host_domains );
		$domain = '';
		for($i = ($host_domains_count - 1); $i > 0; $i --) {
			if ($host_domains [$i])
			$domain = ($host_domains_count > 1 ? '.' : '') . $host_domains [$i] . $domain;
		}
		return $domain;
	}
	public function Update($ob_clearn = false) {
		if ($ob_clearn) {
			ob_clean ();
		}
		$private_hashcode = substr ( md5 ( uniqid ( rand (), true ) ), - 6 );
		foreach ( $this->variables as $key => $value ) {
			if ($value){
				$value = base64_encode ( $value . $this->md5 ( $key, $value, $private_hashcode ) . $private_hashcode );
			}
			//		setcookie ( $this->prefix . $key, $value, $this->timestamp + $this->expiretime, $this->path, $this->domains, $_SERVER ['SERVER_PORT'] == 443 ? 1 : 0 );
			Cookie::set($key, $value,$this->expiretime,$this->path,$this->domains);
		}
	}
	
	
	public function Get($variable) {
		//	global $_COOKIE;
		$cookie_value = Cookie::get($variable);
		if ($cookie_value !== null) {
			$strTempCookie = '';
			$strTempCookie = base64_decode ( $cookie_value );
			$validate_string = substr ( $strTempCookie, - 38 );
			$validate = substr ( $validate_string, 0, 32 );
			$private_hashcode = substr ( $validate_string, - 6 );
			$value = substr ( $strTempCookie, 0, - 38 );
			unset ( $strTempCookie );
				
			$this->variables [$variable] = $value;
			$this->Update ();
			return unserialize($value);
				
			if ($validate == $this->md5 ( $variable, $value, $private_hashcode )) {

			}
		}
		return false;
	}
	public function md5($variable, $value, $private_hashcode) {
		return md5 ( $this->getenv ( 'HTTP_USER_AGENT' ) . $this->hashcode . $variable . $value . $private_hashcode . $this->domains . $this->path . $this->expiretime );
	}
	public function __get($varibale_name) {
		if (isset ( $this->variables [$varibale_name] )) {
			return $this->variables [$varibale_name];
		} else {
			return $this->get ( $varibale_name );
		}
	}
	public function clear() {
		$this->variables = array ();
	}
	public function __set($varibale_name, $varibale_value) {
		$this->variables [$varibale_name] = $varibale_value;
	}
	public function __isset($varibale_name) {
		return isset ( $this->variables [$varibale_name] );
	}
	public function __unset($varibale_name) {
		unset ( $this->variables [$varibale_name] );
	}
}