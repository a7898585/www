<?php

/**
 * @ 好友信息模块
 * 
 * @ Copyright (c) 2004-2012 CNFOL Inc. (http://www.cnfol.com)
 * @ version 3.0.1
 * @ author chensh
 */
class FriendModel extends MY_Model {

    // private $socket;
    //  private $cache;
    //  private $expire;
    private $iscache;

    function __construct() {
        parent::__construct();
        //    $this->socket = &load_class_params('Cnfol_Socket', array('passport_socket'));
        //    $this->cache  = &load_class_params('Cnfol_Cache', array('passport_cache'));
        $this->expire = 60 * 60 * 24 * 7;
        $this->iscache = IS_OPEN_CACHED;
    }

    /**
     * @ follow 添加关注
     *
     * @param array $param
     * UserID 用户编号
     * Type 数据类型 0-好友用户编号，1-好友用户名，2-好友用户昵称
     * FriendData 数据内容
     * FType 好友类别 0-互相关注，1-黑名单，2-关注
     * @access public
     * @return bool
     */
    public function follow($param) {
        $type = 'U110';
        error_log('model' . date("Y-m-d H:i:s") . print_r($param, true), 3, DEFAULT_PATH . '/logs/addblack.log');
        $rs = $this->socket['passport']->senddata($type, $param);
        $rs = xmltoarray($rs);
        error_log('U110xml' . date("Y-m-d H:i:s") . print_r($rs, true), 3, DEFAULT_PATH . '/logs/addblack.log');


        if ($rs['Code'] == '00') {
            $ckey = &config_item("K1094");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{FType}', '0', $ckey);
            $this->cache->delete($ckey);

            $ckey = &config_item("K1094");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{FType}', '3', $ckey);
            $this->cache->delete($ckey);

            //$ckey_black为各用户所有黑名单缓存key
            $ckey_black = config_item('K1075');
            $ckey_black = str_replace('{userid}', $param['UserID'], $ckey_black);
            $this->cache->delete($ckey_black);

            $ckey_black = config_item('K2075');
            $ckey_black = str_replace('{userid}', $param['UserID'], $ckey_black);
            $this->cache->delete($ckey_black);

            $ckey = config_item('K2076');
            $ckey = str_replace('{userid}', $param['FriendData'], $ckey);
            $this->cache->delete($ckey);

            $ckey = config_item('K2077');
            $ckey = str_replace('{userid}', $param['UserID'], $ckey);
            $ckey = str_replace('{fuserid}', $param['FriendData'], $ckey);
            $this->cache->delete($ckey);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @ unfollow 取消关注
     *
     * @param array $param
     * UserID 用户编号
     * Type 数据类型 0-好友用户编号，1-好友用户名，2-好友用户昵称
     * FriendData 数据内容
     * FType 好友类别 0-删除我关注的好友，1-黑名单，2-拒绝好友关注
     * @access public
     * @return bool
     */
    public function unfollow($param) {
        $type = 'U111';
        //   var_dump($param);
        $rs = $this->socket['passport']->senddata($type, $param);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {
            $param['UserID'] = isset($param['act']) ? $param['FriendData'] : $param['UserID'];
            $ckey = &config_item("K1094");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{FType}', '2', $ckey);
            $this->cache->delete($ckey);


            $ckey = &config_item("K1094");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{FType}', '0', $ckey);
            $this->cache->delete($ckey);

            $ckey = &config_item("K1094");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $ckey = str_replace('{FType}', '3', $ckey);
            $this->cache->delete($ckey);

            $ckey_black = config_item('K1075');
            $ckey_black = str_replace('{userid}', $param['UserID'], $ckey_black);
            $this->cache->delete($ckey_black);

            $ckey_black = config_item('K2075');
            $ckey_black = str_replace('{userid}', $param['UserID'], $ckey_black);
            $this->cache->delete($ckey_black);

            $ckey = config_item('K2076');
            $ckey = str_replace('{userid}', $param['FriendData'], $ckey);
            $this->cache->delete($ckey);

            $ckey = config_item('K2077');
            $ckey = str_replace('{userid}', $param['UserID'], $ckey);
            $ckey = str_replace('{fuserid}', $param['FriendData'], $ckey);
            $this->cache->delete($ckey);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @ common 共同好友
     *
     * @param array $param
     * UserID 用户编号
     * VisitantID 访客用户编号
     * StartNo 起始序号
     * QryCount 查询笔数
     * @access public
     * @return bool
     */
    public function common($param) {
        $m_key = 'u_usercard_togetfriends_' . $param['UserID'] . '_' . $param['VisitantID'] . '_' . $param['StartNo'] . '_' . $param['QryCount'];
        $rs = $this->cache->get($m_key);

        if (empty($rs)) {
            $sockets = &load_class_params('Cnfol_Sockets', array('osapi_socket'));
            $type = 'U960';
            $rs = $sockets->senddata($type, $param);
            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                if ($rs['RetRecords'] == 1) {
                    $rs = array($rs['Record']);
                } else {
                    $rs = $rs['Record'];
                }

                $this->cache->set($m_key, $rs, 60);
            } else {
                return false;
            }
        }

        return $rs;
    }

    /**
     * @ verify 验证好友
     *
     * @param array $param
     * UserID 用户编号
     * FUserIDs 好友用户编号，多笔用逗号隔开
     * @access public
     * @return bool
     */
    public function verify($param) {
        $type = 'U107';
        $rs = $this->socket['passport']->senddata($type, $param);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {
            $result = array();

            if ($rs['RetRecords'] == '1') {
                $result = array($rs['Record']);
            } else {
                $result = $rs['Record'];
            }

            /*        unset($rs);
              foreach($result as $v) {
              $rs[$v['FUserID']] = $v['FriendStatus'];
              } */

            return $result;
        } else {
            return false;
        }
    }

    /**
     * @ getUserFriendNum 获取好友数量
     *
     * @param array $param
     * UserID 用户编号
     * @param int $type 好友类型
     * @access public
     * @return bool
     */
    public function getUserFriendNum($param, $type) {
        $m_key = 'u_' . $param['UserID'] . '_friend_num_info';
        $rs = $this->cache->get($m_key);

        switch ($type) {
            case '0':
                $ftype = 'FollowingNum'; //关注数量
                break;

            case '1':
                $ftype = 'FllowerNum'; //粉丝数量
                break;

            case '2':
                $ftype = 'FriendNum'; //好友数量
                break;
        }

        if (empty($rs)) {
            $type = 'U115';
            $rs = $this->socket['passport']->senddata($type, $param);
            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                $rs = $rs['Record'];
                $this->cache->set($m_key, $rs, 3600);
            } else {
                $rs = array('FollowingNum' => 0, 'FllowerNum' => 0, 'FriendNum' => 0);
            }
            return $rs[$ftype];
        } else {
            return $rs[$ftype];
        }
    }

    /*
     *  搜索好友高级搜索时的数量
     */

    public function searchFriendsNum($param) {

        $result = array(
            'count' => 0,
            'list' => ''
        );
        $rs = "";
        if (empty($rs)) {

            $type = 'U100';
            $param['StartNo'] = -1;
            $param['QryCount'] = -1;
            $rs = $this->socket['passport']->senddata($type, $param);
            $rs = xmltoarray($rs);

            $count = $rs['TtlRecords'];

            if ($rs['Code'] == '00') {
                $result = array(
                    'count' => $count,
                    'list' => ''
                );
            } else {
                return false;
            }
        }
        return $result;
    }

    /*
     *  搜索好友,由于搜索具有随机性，所以不做缓存
     */

    public function searchFriends($param) {
        $type = 'U104';
        $rs = $this->socket['passport']->senddata($type, $param);
        $rs = xmltoarray($rs);



        if ($this->_checkrs($rs)) {
            //error_log(print_r($rs,true), 3, '/home/www/html/logs/a1.log');
            //if($rs['RetRecords']==1&&$param['StartNo']!=-1)
            //{
            //	$rs['Record']=array($rs['Record']);
            //}
            return $rs;
        }
        return false;
    }

    /*
     *   获取好友列表 U104
     */
    /*
      public function getFriendList($param)
      {

      $result = array(
      'count' => 0,
      'list'  => ''
      );

      $c_key  = 'u_get_friend_list_counts';
      $count  = $this->cache->get($c_key);

      //   $page   = ceil(($param['StartNo']+1)/$param['QryCount']);
      $l_key  = 'u_'.$param['UserID'].'_friend_'.$ftype.'_list_'.$page;

      $count  = false;
      if(empty($count))
      {
      $type = 'U104';
      //		var_dump($param);
      $rs = $this->socket['passport']->senddata($type, $param);
      $rs = xmltoarray($rs);
      //	var_dump($rs);
      if($rs['Code'] == '00')
      {
      #记录条数
      $count  = $rs['TtlRecords'];
      $this->cache->set($c_key,$count,$this->expire);

      $list   = $rs['Record'];
      $this->cache->set($l_key,$list,$this->expire);

      if($rs['RetRecords']==1)
      {
      $list = array('0' => $list);#构造成二维数组
      }
      $result = array(
      'count' => $count,
      'list'  => $list
      );

      }
      else
      {
      return false;
      }

      }

      return $result;

      }
     */

    public function getFriendList($data) {
        $type = 'U104';

        if (ISCACHE) {
            if ($data['StartNo'] == '-1') {
                $ckey = &config_item("K1094");
                $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                $ckey = str_replace('{FType}', $data['FType'], $ckey);

                $rs = $this->cache->get($ckey);
                //$rs = "";   //上线后改回来			
                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_2);
                        return $rs;
                    }
                    return false;
                }
                return $rs;
            } else {
                $ckey = &config_item("K1095");
                $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
                $ckey = str_replace('{FType}', $data['FType'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $rs = $this->cache->get($ckey);
                //$rs = "";  //上线后改回来
                if (!$rs || $rs['FlagCode'] != $data['FlagCode']) {
                    $rs = $this->socket['passport']->senddata($type, $data);

                    $rs = xmltoarray($rs);

                    if ($this->_checkrs($rs, $type, $data)) {
                        $rs['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $rs, EXPIRETIME_2);
                        return $rs;
                    }

                    return false;
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs)) {
                return $rs;
            }
        }
    }

    /* 获取共同关注的好友 */

    function getJointly($data) {
        $type = 'U108';

        if (ISCACHE) {

            if ($data['StartNo'] == -1) {
                $ckey = config_item('K2018');
                $ckey = str_replace('{hostUserid}', $data['UserID'], $ckey);
                $ckey = str_replace('{guestUserid}', $data['VisitantID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if (isset($rs['TtlRecords']) && $rs['TtlRecords'] > 0) {
                        $this->cache->set($ckey, $rs['TtlRecords'], EXPIRETIME_3);
                        return $rs['TtlRecords'];
                    }

                    return false;
                }

                return $rs;
            } else {
                $ckey = config_item('K2019');
                $ckey = str_replace('{hostUserid}', $data['UserID'], $ckey);
                $ckey = str_replace('{guestUserid}', $data['VisitantID'], $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs) {

                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);

                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        if ($rs['RetRecords'] == 1) {
                            $this->cache->set($ckey, array('0' => $rs['Record']), EXPIRETIME_3);
                            return array('0' => $rs['Record']);
                        } else {
                            $this->cache->set($ckey, $rs['Record'], EXPIRETIME_3);
                            return $rs['Record'];
                        }
                    }

                    return false;
                }

                return $rs;
            }
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    if (isset($rs['TtlRecords']) && $rs['TtlRecords'] > 0) {
                        return $rs['TtlRecords'];
                    }
                    return false;
                } else {
                    if (isset($rs['RetRecords']) && $rs['RetRecords'] > 0) {
                        if ($rs['RetRecords'] == 1) {
                            return array('0' => $rs['Record']);
                        } else {
                            return $rs['Record'];
                        }
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
    }

}

//end class
?>