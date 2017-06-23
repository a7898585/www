<?php
/*****************************************************
 * CI_Passport:  the interface of Users's information
 * Author:       Rover
 * Copyright:    CNFOL
 * Version:      1.0
 * Add:          2010-7-1
 * Update        2010-7-1
*****************************************************/

class CI_Passport
{

    private $socket;
    private $cache;
    private $expire;

    function CI_Passport()
    {
        $this->socket = &load_class_params('Socket',array('passport_socket'));
        $this->cache  = &load_class('Cache');
        $this->expire = 2592000;
    }

    /**
    * @用户信息接口
    * @param        array
    * @return       array or string
    **/
    public function getInfo($params)
    {
        error_reporting(2047);
        $param              = array();
        if (@is_numeric($params['UserID']) && $params['UserID'])
        {
            $param['UserID'] = $params['UserID'];
        }

        if (@is_numeric($params['Mobile']) && $params['Mobile'])
        {
            $param['Mobile'] = $params['Mobile'];
        }

        switch($params['Type'])
        {
            case 'getnewmsgcount':
                $return = $this->getMsgCount($param);
                return $return['UnReads'];
            break;

            case 'getmsgcount':
                $return = $this->getMsgCount($param);
                return $return['UnReads'].'/'.$return['TtlRecords'];
            break;

            case 'money':
                $return = $this->getUserMoney($param);
                return $return['Golden'];
            break;

            case 'point':
                $return = $this->getUserPoint($param);
                return $return;
            break;

            case 'usedpoint':
                $return = $this->getUserUsedPoint($param);
                return $return;
            break;

            case 'gradebyuid':
                $return = $this->getUserGrade($param);
                return $return['GradeSymbol'];
            break;

            case 'username':
                $return = $this->getUserBaseInfo($param);
                return $return['UserName'];
            break;

            case 'nickname':
                $return = $this->getUserBaseInfo($param);
                return $return['NickName'];
            break;

            case 'userbaseinfo':
                $return = $this->getUserBaseInfo($param);
                return serialize($return);
            break;

            case 'userdetailinfo':
                $return = $this->getUserDetailInfo($param);
                return serialize($return);
            break;

            case 'userinfobymobile':
                $return = $this->getUser($param);
                return serialize($return['Record']);
            break;

            default:
                return '没有参数';
            break;
        }
    }

    /**
    * @用户在线接口
    * @param        array
    * @return       string
    **/
    public function getOnline($params)
    {
        $c_key  = 'u_'.$params['UserID'].'_login';
        $result = $this->cache->get_all($c_key);

        $flag = 0;
        $info = '';

        if (!$result)
        {
            $activetime = time()-$result['settime'];

            if ($activetime < 900 && $result['info'] == 1)
            {
                $flag = 1;
            }
        }

        if ($params['Type'])
        {
            if ($flag)
            {
                return $info = 'var online_'.$params['UserID'].'="online"';
            }
            else
            {
                return $info = 'var online_'.$params['UserID'].'="offline"';
            }
        }
        else
        {
            if ($flag)
            {
                return $info = 'online';
            }
            else
            {
                return $info = 'offline';
            }
        }
    }

    /**
    * @用户积分接口
    * @param        array
    * @return       array or string
    **/
    public function changePoint($params)
    {
        $ip_arr = explode('.', $_SERVER['REMOTE_ADDR']);
        $ip_str = $ip_arr[0].'.'.$ip_arr[1];
        $ips    = array('127.0','192.168','172.20','220.162');

        if (in_array($ip_str, $ips))
        {
            if ($params['UserID'] !='' && $params['Type'] != '')
            {
                $param['Type']          = 1;//0-不修改 1-修改总积分 2-修改消费积分
                $param['UserID']        = $params['UserID'];
                $param['RewardEName']   = $params['TypeID'];

                if ($params['Golden'] != '')
                {
                    $param['WeightingPoint'] = $params['Golden'];
                }

                $g_type = 'U619';

                $result = $this->socket->senddata($g_type, $param);
                $result = $this->xmltoarray($result);

                if ($result['Code'] == '00')
                {
                    $userid = explode(',',$param['UserID']);
                    $count  = count($userid);

                    if ($count>1)
                    {
                        foreach($userid as $val)
                        {
                            $c_key = 'u_'.$val.'_usertotalpoint';
                            $this->cache->delete($c_key);
                            $c_key = 'u_'.$val.'_userusedpoint';
                            $this->cache->delete($c_key);
                        }
                    }
                    else
                    {
                        $c_key = 'u_'.$param['UserID'].'_usertotalpoint';
                        $this->cache->delete($c_key);
                        $c_key = 'u_'.$param['UserID'].'_userusedpoint';
                        $this->cache->delete($c_key);
                    }
                }
            }
            else
            {
                echo '没有用户ID或类型ID';
            }
        }
    }

    /**
    * @用户基本信息
    * @param        array
    * @return       array
    **/
    private function getUserBaseInfo($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_userbaseinfo';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $result = $this->getUser($data);

            if ($result['Code'] == '00')
            {
                $result = $result['Record'];
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户详细信息
    * @param        array
    * @return       array
    **/
    private function getUserDetailInfo($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_userinfo';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $g_type = 'U006';

            $result = $this->socket->senddata($g_type, $data);
            $result = $this->xmltoarray($result);

            if ($result['Code'] == '00')
            {
                $result = $result['Record'];
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户信件
    * @param    array
    * @return   array
    **/
    private function getMsgCount($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_usermsgcount';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $g_type = 'U522';

            $result = $this->socket->senddata($g_type, $data);
            $result = $this->xmltoarray($result);

            if ($result['Code'] == '00')
            {
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户金币
    * @param    array
    * @return   array
    **/
    private function getUserMoney($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_usermoney';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $result = $this->getUser($data);

            if ($result['Code'] == '00')
            {
                $result = array('Golden'=>$result['Record']['Golden'],'ConsumeGold'=>$result['Record']['ConsumeGold'],'RechargeMoney'=>$result['Record']['RechargeMoney']);
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户总积分
    * @param        array
    * @return       string
    **/
    private function getUserPoint($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_usertotalpoint';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $result = $this->getUser($data);

            if ($result['Code'] == '00')
            {
                $result = $result['Record']['Point'];
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户消费积分
    * @param        array
    * @return       string
    **/
    private function getUserUsedPoint($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_userusedpoint';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $result = $this->getUser($data);

            if ($result['Code'] == '00')
            {
                $result = $result['Record']['UsedPoint'];
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户等级
    * @param    array
    * @return   array
    **/
    private function getUserGrade($data)
    {
        $c_key  = 'u_'.$data['UserID'].'_usergrade';
        $result = $this->cache->get($c_key);

        if (!$result)
        {
            $g_type = 'U060';

            $result = $this->socket->senddata($g_type, $data);
            $result = $this->xmltoarray($result);

            if ($result['Code'] == '00')
            {
                $result = $result['Record'];
                $this->cache->set($c_key, $result, $this->expire);
            }
        }

        return $result;
    }

    /**
    * @用户信息
    * @param    array
    * @return   array
    **/
    private function getUser($data)
    {
        $g_type = 'U005';

        $result = $this->socket->senddata($g_type, $data);
        $result = $this->xmltoarray($result);

        return $result;
    }

    private function xmltoarray($string)
    {
        $xml    = &load_class('xmlClass');
        $xml    = new CI_xmlClass($string);
        $rs     = $xml->loadStr($string);
        $result = $rs['Status'];

        if (is_array($rs['Records']['Record']) && !empty($rs['Records']['Record']))
        {
            $rs['Record'] = $rs['Records'];
            $result       = array_merge($rs['Record'], $result);
        }

        return $result;
    }
}
?>