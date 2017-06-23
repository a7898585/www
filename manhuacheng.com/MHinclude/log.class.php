<?php

class Logs {

	private $dir = null;
	private $_filepath; //文件路径
	private $_filename; //日志文件名
	private $_filehandle; //文件句柄

	private function setLogDir($logType){
		
		switch($logType){
			
			case 'domain':
			$this->dir = 'log/domain/';
			break;
			case 'admin':
			$this->dir = 'log/admin/';
			break; 
			case 'user':
			$this->dir = 'log/user/';
			break;
			case 'group':
			$this->dir = 'log/group/';
			break;
			default:
			$this->dir = 'log/sys/';
			break;
		}
		return $this->dir .= date("Y/m/", time());
	}
	public function Logs($logType = null) {
		
		$this->_filepath = $this->setLogDir($logType);
		$this->_filename =  date('Y-m-d', time()).'.log';
		$path = $this->_createPath($this->_filepath, $this->_filename);
		if (!$this->_isExist($path)) { 
			if (!empty ($this->_filepath)) {
				if (!$this->_createDir($this->_filepath)) {
					die("创建目录失败!");
				}
			}
			if (!$this->_createLogFile($path)) {
				die("创建文件失败!");
			}
		}
		$path = $this->_createPath($this->_filepath, $this->_filename);
		$this->_filehandle = fopen($path, "a+");
	}

	public function writeLog($log) {
		$str = "";
		if (is_array($log)) {
			$arr = array('time' => date("Y-m-d H:i:s",time()));
			$log = array_reverse(array_merge($log,$arr));
			foreach ($log as $k => $v) {
				$str .= "[".$k . ":" . $v . "] ";
			}
		} else {
			$str = '[time:'.date("Y-m-d H:i:s",time()).']'.$log;
		}
		$str .="\n";
		if (!fwrite($this->_filehandle, $str)) {
			die("写入日志失败");
		}
	}
	
	private function _isExist($path) {
		return file_exists($path);
	}

	private function _createDir($dir) {
		return is_dir($dir) or ($this->_createDir(dirname($dir)) and mkdir($dir, 0777));
	}

	private function _createLogFile($path) {
		$handle = fopen($path, "w");
		fclose($handle);
		return $this->_isExist($path);
	}

	private function _createPath($dir, $filename) {
		if (empty ($dir)) {
			return $filename;
		} else {
			return $dir . "/" . $filename;
		}
	}

	function __destruct() {
		fclose($this->_filehandle);
	}
}
?>
