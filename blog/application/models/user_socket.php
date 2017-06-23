<?php

/*
  --|用户信息
  --|modify  2011-8-23  lifeng
 */

class User_socket extends MY_Model {

    function User_socket() {
        parent::MY_Model();
    }

    /**
     * 功能：    获取用户的基本资料
     * @data
      UserID           用户ID
     * @return   array
     */
    function getUserBaseInfo($data) {
        if (ISCACHE) {
            $ckey = config_item('K1001');
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->getUserBase($data);
                if ($rs['Code'] == '00') {
                    $rs = $rs['Record'];
                    $this->cache->set($ckey, $rs, EXPIRETIME_3);
                    return $rs;
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->getUserBase($data);
            if ($rs['Code'] == '00') {
                $rs = $rs['Record'];
                return $rs;
            }
        }
    }

    /**
     * 功能：    查看用户是否存在
     * @data
      UserID           用户ID
     * @return   array
     */
    function haveUserBaseInfo($data) {
        $rs = $this->getUserBase($data);
//        error_log(date("Y-m-d H:i:s").print_r($data,true) . "<<$$--**>>\n".print_r($rs,true). "<<$$--**>>\n", 3, '/home/www/html/logs/1q.log');
        if ($rs['Code'] == '00') {
            $rs = $rs['Record'];
            return $rs;
        }
    }

    /**
     * 功能：    获取用户的详细资料资料
     * @data
      UserID  用户ID
     * @return   array
     */
    function getUserInfo($data) {
        $type = 'U006';
        if (ISCACHE) {
            $ckey = config_item('K1002');
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($rs['Code'] == '00') {
                    $rs = $rs['Record'];
                    $this->cache->set($ckey, $rs, EXPIRETIME_3);
                    return $rs;
                }
                return false;
            }
            return $rs;
        } else {

            $rs = $this->socket['passport']->senddata($type, $data);

            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                $rs = $rs['Record'];
                return $rs;
            } else {
                return false;
            }
        }
    }

    /**
     * 功能：获取用户基本信息的公用函数 (调用者自行缓存)
     * @data
     * @return array
     */
    private function getUserBase($data) {
        $type = 'U005';


        if (ISCACHE) {
            $ckey = config_item('K2035');
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);
            //error_log(print_r($rs, true).'|'.print_r($data, true), 3, '/home/www/html/logs/a111.log');
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($rs['Code'] == '00') {
                    $this->cache->set($ckey, $rs, EXPIRETIME_1);
                    return $rs;
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                return $rs;
            }
            return false;
        }
    }

    /**
     * 功能：查询用户验证信息,实名认证
     * @data
     * @return array
     */
    public function realNameAuth($param) {
        if (ISCACHE) {
            $ckey = $this->config->item("K1096");
            $ckey = str_replace('{UserID}', $param['UserID'], $ckey);
            $rs = $this->cache->get($ckey);
            //error_log(print_r($rs,true),3, '/home/www/html/logs/a123123.log');
            if (!$rs) {
                $type = 'U037';
                $res = $this->socket['passport']->senddata($type, $param);
                $res = xmltoarray($res);
                if ($res['TtlRecords'] == 1 && $res['Record']['Status'] == 1) {
                    $rs = $res;
                } else {
                    $rs = 2;
                }

                $this->cache->set($ckey, $rs, EXPIRETIME_3);
            }
            return $rs;
        } else {
            $type = 'U037';
            $res = $this->socket['passport']->senddata($type, $param);
            $res = xmltoarray($res);

            if ($res['TtlRecords'] == 1 && $res['Record']['Status'] == 1) {
                $rs = $res;
            } else {
                $rs = 2;
            }
            return $rs;
        }
    }

    /**
     * 功能：  获取用户站内信息数
     * @param  数组格式
      UserID      用户ID
     * @return 数组类型
     */
    function getMsgCount($data) {
        $type = 'U522';
        if (ISCACHE) {
            $ckey = config_item('K1003');
            $ckey = str_replace('{UserID}', $data, $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $param = array('UserID' => $data);
                $rs = $this->socket['passport']->senddata($type, $param);
                $rs = xmltoarray($rs);
                $this->_checkrs($rs, $type);
                $this->cache->set($ckey, $rs, EXPIRETIME_3);
            }
            return $rs;
        } else {
            $param = array('UserID' => $data);
            $rs = $this->socket['passport']->senddata($type, $param);
            $rs = xmltoarray($rs);
            $this->_checkrs($rs, $type);
            return $rs;
        }
    }

    /**
     * 功能：获取用户等级
     * @data
     * @return array
     */
    function getUserGrade($data) {
        $type = 'U060';
        if (ISCACHE) {
            $ckey = config_item('K1004');
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);
                if ($this->_checkrs($rs, $type)) {
                    if ($rs['RetRecords'] > 0) {
                        $this->cache->set($ckey, $rs['Record'], EXPIRETIME_3);
                    }
                    return $rs['Record'];
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                $rs = $rs['Record'];
            }
            return $rs;
        }
    }

    /**
     * 功能：    搜索用户
     * @data
      UserName:用戶名
      NickName:用戶暱稱
      Sex:性別 0：男，1：女
      Province:省份
      City:城市
      SchoolAgeID 學歷ID
      IndustryID  職業ID
     * @return   array
     */
    function searchUser($data) {
        $type = 'U100';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00') {
            #记录条数
            $count = $rs['RetRecords'];
            if ($rs['RetRecords'] == 1) {
                $rs['Record'] = array(0 => $rs['Record']); #构造成二维数组
            }
            $rs = array(
                'count' => $count,
                'list' => $rs['Record']
            );
        }
        return $rs;
    }

    /**
     * 功能：获取用户组别
     * @data
     * @return array
     */
    function getUserGroup($data) {
        $type = 'U052';

        if (ISCACHE) {
            $ckey = config_item('K1000');
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    $rs = $rs['Record'];
                }
                $this->cache->set($ckey, $rs, EXPIRETIME_3);
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            if ($this->_checkrs($rs, $type)) {
                $rs = $rs['Record'];
            }
            return $rs;
        }
    }

    //增修用戶積分記錄
    function addUserPoint($data) {
        $type = 'U619';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            return true;
        }
        return false;
    }

    function getUserKeyStr($data) {
        $type = 'U008';

        if (ISCACHE) {
            $ckey = config_item('K2036');
            $ckey = str_replace('{UserID}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);


            if ($rs && $data['UserID']) {
                $cookie = $this->input->cookie('cookie');
                if ($cookie && isset($cookie['passport']['keystr'])) {
                    $cookie = $cookie['passport']['keystr'];
                } else {
                    $cookie = 0;
                }

                //error_log(print_r($rs, true).'|'.print_r($cookie, true), 3, '/home/www/html/logs/a111.log');
                if ($cookie != $rs['KeyStr']) {
                    $rs = '';
                }
            }

            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    $rs = $rs['Record'];
                    $this->cache->set($ckey, $rs, EXPIRETIME_1);
                    return $rs;
                }
                return false;
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);

            if ($this->_checkrs($rs, $type)) {
                return $rs['Record'];
            }
            return false;
        }
    }

    //粉丝，我关注的，相互关注数
    function getFriendContact($data) {
        $type = 'U115';

        if (ISCACHE) {
            $ckey = config_item('K2076');
            $ckey = str_replace('{userid}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                    $rs = $rs['Record'];
                    $this->cache->set($ckey, $rs, EXPIRETIME_3);
                    return $rs;
                } else {
                    return array('FollowingNum' => 0, 'FllowerNum' => 0, 'FriendNum' => 0);
                }
            }
            return $rs;
        } else {

            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            //error_log(print_r($data,true).'|'.print_r($rs,true).'|',3, '/home/www/html/logs/a111111111.log');
            if ($this->_checkrs($rs, $type) && isset($rs['Record'])) {
                $rs = $rs['Record'];
                return $rs;
            } else {
                return array('FollowingNum' => 0, 'FllowerNum' => 0, 'FriendNum' => 0);
            }
        }
    }

    /* 查询好友关系状态 */

    function getFriendsStatus($data) {
        $type = 'U107';
        /*
          $rs = $this->socket['passport']->senddata($type, $data);
          $rs = xmltoarray($rs);
          if ($this->_checkrs($rs, $type)) {
          return $rs['Record'];
          } else {
          return 0;
          }
         */


        if (ISCACHE) {
            $ckey = config_item('K2077');
            $ckey = str_replace('{userid}', $data['UserID'], $ckey);
            $ckey = str_replace('{fuserid}', $data['FUserIDs'], $ckey);
            $rs = $this->cache->get($ckey);
            //error_log(print_r($rs,true),3, '/home/www/html/logs/a111111111111.log');
            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    $rs = $rs['Record'];
                    $this->cache->set($ckey, $rs, EXPIRETIME_3);
                    return $rs;
                } else {
                    return 0;
                }
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            //error_log(print_r($rs,true),3, '/home/www/html/logs/a111111111111.log');
            if ($this->_checkrs($rs, $type)) {
                $rs = $rs['Record'];
                return $rs;
            } else {
                return 0;
            }
        }
    }

    /**
     * 功能：获取用户黑名单

     */
    function getBlackList($data) {
        $type = 'U103';

        if (ISCACHE) {
            $ckey = config_item('K1075');
            $ckey = str_replace('{userid}', $data['UserID'], $ckey);
            $rs = $this->cache->get($ckey);

            if (!$rs) {
                $rs = $this->socket['passport']->senddata($type, $data);
                $rs = xmltoarray($rs);

                if ($this->_checkrs($rs, $type)) {
                    $rs = $rs['Record'];
                }
                $this->cache->set($ckey, $rs, EXPIRETIME_5);
            }
            return $rs;
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);
            //error_log(print_r($data,true).'|'.print_r($rs,true), 3, '/home/www/html/logs/a23132.log');
            if ($this->_checkrs($rs, $type)) {
                $rs = $rs['Record'];
            }
            return $rs;
        }
    }

}

//end class
?>