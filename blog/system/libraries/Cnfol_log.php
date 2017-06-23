<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 日志接口  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('cnfol_log');
| $this->cnfol_log->write_log('内容', test.log, __METHOD__,'INFO');
|--------------------------------------------------------------------------
*/
class CI_Cnfol_Log
{

    var $log_path;                               #日志路径
    var $log_root        = '/home/html/logs/';  #日志根目录
    var $log_date_format = 'Y-m-d H:i:s';        #时间格式
    var $log_enabled     = true;                 #开启日志

    /*
    *  构造函数，初始化配置
    */
    function CI_Cnfol_Log()
    {
        # 取得配置信息
        $config =& get_config();
        $this->log_path        = $this->log_root.$config['log_application'].'/';
        $this->log_date_format = $config['log_date_format'];
        $this->log_enabled     = $config['log_enabled'];
    }

    /*
    *  写日志
    *
    *  $logtype：     'ERROR'，'DEBUG'，'INFO'，'ALL',默认为‘INFO’
    *  $msg：         记录日志的信息,字符串。
    *  $method：      原调用类和函数或调用文件名，php5可以通过魔术变量：__METHOD__获取
    *  $filename ：   为存储的路径及文件名,例如:  2009/08/01/cnfol.log
    */

    function write_log($msg, $filename, $method,$logtype='INFO')
    {
        if ($this->log_enabled === false)
        {
            return false;
        }
        $filename = $this->log_path.$filename; #完整日志路径
        if(!file_exists($filename))
        {
            $this->_mkdir($filename);              #创建文件夹
        }
        #日志内容
        $content = $logtype.'||'.date("{$this->log_date_format}").'||'.$method.'||'.$msg.chr(13).chr(10);

        return error_log($content,3,$filename);
    }

    /*
    *  设置日志根路径
    *
    */

    function set_log_root($log_root)
    {
        $this->log_root = $log_root;
    }

    /*
    *  设置日志时间格式
    *
    */

    function set_date_format($date_format)
    {
        $this->log_date_format = $date_format;
    }

    /**
     * 创建文件夹
     *
     */
    function _mkdir($_path, $_mode=0777)
    {
        if(substr($_path,-1)=='/')
        {
            $fullpath = '';
            $_path = explode('/', $_path);
            while ( list(,$v) = each($_path) )
            {
                $fullpath .= "$v/";
                if (!file_exists($fullpath))
                {
                    if (mkdir($fullpath, $_mode) == false)
                    {
                        return false;
                    }
                }
            }
            return true;
        }else{
              $_path = dirname($_path).'/';
              $this->_mkdir($_path, $_mode);
        }
    }

}//end calss
?>
