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


class CI_Sendmail
{

    /**
    * 设置数据到cache
    *
    */
    function send($info,$action="post")
    {
        $url        = "http://mail.api.cnfol.net/index.php";
        //$url        = "http://mail.cnfol.net/index.php";
        $key        = 'da2f00b38ed9273b974f254b7ba27571';
        $emails     = $info['mailto'];
        $subject    = trim($info['subject']);
        $content    = trim($info['content']);
        $charset    = $info['charset']?$info['charset']:'utf8';
        if ($subject<>''&&$content<>'')
        {
            $emails = str_replace("，",",",$emails);//全角转半角
 
            $emails = explode(',', $emails);
            foreach($emails as $v)
            {
                if (!empty($v) && preg_match("/^[\w\.\-]+@([\w\-]+\.)+[a-z]{2,4}$/",$v))
                {
                    $emailto .= trim($v).',';
                }
                else
                {
                    log_write("发送邮件，邮箱未：$v，有误",$this->sendmail_log,__METHOD__ ,'ERROR');
                }
            }
            $emailto = substr($emailto,0,-1);
            if ($emailto<>'')
            {
                require_once 'Snoopy.class.php';
                $sp = new Snoopy;
 
                $info['key']      = $key;
                $info['content']  = $content;
                $info['mailto']   = $emailto;
                $info['subject']  = $subject;
                $info['Original'] = 'passport';
                $info['charset']  = $charset;
                #add by huanggy 20110201 linfeng
				#$info['smtpID']   = rand(14,17);
				$info['smtpID'] = rand(18,24);

                $result = $sp->submit($url, $info);

                if ($result)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }
}

// END Sendsms Class

/* End of file Sendsms.php */
/* Location: ./system/libraries/Sendsms.php */
