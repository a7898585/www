<?php
/**
 * MY_Model
 *
 * @uses Model
 * @package CNFOL.com
 * @version $Id$
 * @copyright Copyright (C) 2008 Cnfol.com. All rights reserved.
 * @author Avenger
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

class MY_Model extends Model 
{
	var $socket;
	var $cache;
	var $expire;
	
    function MY_Model() 
	{
        parent::Model();
		
		load_class('Socket',false);
		//连接用户中心GW
		$this->socket['passport'] = new CI_Socket('passport_socket');
		//连接博客系统GW
		$this->socket['newblog']  = new CI_Socket('newblog_socket');
		
		if(ISCACHE)
		{
			$this->cache   = &load_class('Memcache');
			$this->cache->expire  = EXPIRETIME;
			$this->cache->addServer();
		}
    }


	//验证gateway信息
	function _checkrs($rs,$type='',$data='')
	{
		$gwcodes = array('00','100078');
		$filename = '/home/www/html/logs/socketfailed_'.date('Ymd').'.log';
		if (file_exists($filename) === false) {
			$fp = fopen($filename, 'w+'); 
			chmod($filename, 0777);
			fclose($fp);
		}
		
		error_log(date('Y-m-d H:i:s')." | ".__FILE__." | ".__METHOD__." | type:{$type} | code:{$rs['Code']} \r\n", 3, $filename);
		
		if( !in_array($rs['Code'],$gwcodes) )
		{
			$error = array2str($rs);
			$error2  = array2str($data);
			$filename = '/home/www/html/logs/socketerror_'.date('Ymd').'.log';
			if (file_exists($filename) === false) {
				$fp = fopen($filename, 'w+'); 
				chmod($filename, 0777);
				fclose($fp);
			}
			error_log(date('Y-m-d H:i:s')." | ".__FILE__." | ".__METHOD__." | type:{$type} | error:$error | $error2\r\n", 3, $filename);
			return false;
		}
		
		return true;
	}

    function sayhello() {
        echo "Hello, I'm MY_Model!";
    }
	
	/* function list_key(&$cache) {

		$all_items = $cache->getExtendedStats('items');
		$keys      = array();
		$config = config_item('cache');
		
		foreach($config['dbcache']['server'] as $o){
			$config_options[] = $o['host'].':'.$o['port'];
		}
		
		foreach ($config_options as $option) {
		
			if (isset($all_items[$option]['items'])) {
				$items = $all_items[$option]['items'];
				foreach ($items as $number => $item) {
					$str    = $cache->getExtendedStats('cachedump', $number, 0);
					$line   = $str[$option];
					if (is_array($line) && count($line) > 0){
						foreach ($line as $key => $value) {
							if(substr($key,0, 12) == $config['dbcache']['prefix']){
								$keys[] = $key;
							}
						}
					}
				}
			}
		}

		return array_unique($keys);

	} */

}

?>
