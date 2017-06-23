<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Systeminfo 服务器信息  0.1
|--------------------------------------------------------------------------
|
| 此文件放于system/libraries/下
|
| 使用方法:
| $this->load->library('Systeminfo');
| $this->mysqli->loadavg();
|--------------------------------------------------------------------------
*/

class CI_Systeminfo
{
    var $configs;
    var $cpu = 4;
    var $_debug = false;
    var $_log = 'base/systeminfo.log';

    function CI_Systeminfo()
    {
        # 取得配置信息
        $config = & get_config();
        $this->configs = $config['systeminfo'];
        unset($config);
        $this->_debug = $this->configs['debug'];
        $this->cpu = $this->configs['cpu'];
    }


    /**
     * 获取负载
     * 第一个值 表1分钟的负载
     * 如果  负载  >  (cpu个数 × 4) 认为系统非常忙。（假设io等待<10%）
     * 返回值：1表示繁忙，0表示不繁忙
     */
    function loadavg()
    {
        $cmd = 'cat /proc/loadavg';
        $rs = exec($cmd);
        $result = explode(' ',$rs);
        $cpunum = $this->cpuinfo()*$this->cpu;
        if($result[0]>$cpunum)
        {
            log_write('负载值为'.$result[0],$this->_log,__METHOD__ ,'INFO');

            if($this->_debug)
            {
                $this->debug('负载值为'.$result[0]);
            }
            return 1;
        }else{
            return 0;
        }
    }


    /**
     * 获取CPU个数
     * cat /proc/cpuinfo | grep processor |tail -n 1 这是cpu的“个数”
     */
    function cpuinfo()
    {
        $cpunum = 1;
        $cmd = 'cat /proc/cpuinfo | grep processor |tail -n 1';
        $rs = exec($cmd);
        $result = explode(':',$rs);
        $cpunum = trim($result[1])+1;
        return $cpunum;
    }



}

// END Systeminfo Class

/* End of file Systeminfo.php */
/* Location: ./system/libraries/Systeminfo.php */
