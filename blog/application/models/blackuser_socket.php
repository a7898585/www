<?php

/*
  --|个人博客添加黑名单的操作
  --|modify  2011-8-22  lifeng
 */

class Blackuser_socket extends MY_Model {

    function Blackuser_socket() {
        parent::MY_Model();
    }

    /*
      --|查詢博客黑名单
     */

    function getBlackUserList($data) {

        //$type = 'B032';
        $type = 'U103';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 50;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1059');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs, EXPIRETIME_2);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K1060');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $ckey = str_replace('{PageNo}', ($data['StartNo'] / $data['QryCount'] + 1), $ckey);
                $rs = $this->cache->get($ckey);

                if (!$rs || ($data['FlagCode'] != $rs['FlagCode'])) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $getData = array();
                        $getData['RetRecords'] = 0;
                        if ($rs['RetRecords'] > 0) {
                            $getData['Record'] = $rs['Record'];
                            $getData['RetRecords'] = $rs['RetRecords'];
                        }
                        $getData['FlagCode'] = $data['FlagCode'];
                        $this->cache->set($ckey, $getData, EXPIRETIME_2);
                        unset($rs);
                        return $getData;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);


            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {
                    return $rs;
                }
                $getData = array();
                $getData['RetRecords'] = 0;
                if ($rs['RetRecords'] > 0) {
                    $getData['Record'] = $rs['Record'];
                    $getData['RetRecords'] = $rs['RetRecords'];
                }
            }
            unset($rs);
            return $getData;
        }
    }

    /*
      --|查詢博客黑名单(暂时仅用户留言用)
     */

    function getBlackUserListOther($data) {

        $type = 'U103';
        $data['StartNo'] = isset($data['StartNo']) ? $data['StartNo'] : 0;
        $data['QryCount'] = isset($data['QryCount']) ? $data['QryCount'] : 50;

        if (ISCACHE) {
            if ($data['StartNo'] == -1) {
                $ckey = config_item('K1059');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $rs = $this->cache->get($ckey);
                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        $rs['FlagCode'] = md5($rs['TtlRecords'] . time());
                        $this->cache->set($ckey, $rs);
                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            } else {
                $ckey = config_item('K2075');

                $ckey = str_replace('{userid}', $data['UserID'], $ckey);

                $rs = $this->cache->get($ckey);
                //error_log(print_r($rs,true), 3, '/home/www/html/logs/a12312.log');
                if (!$rs) {
                    $rs = $this->socket['passport']->senddata($type, $data);
                    $rs = xmltoarray($rs);
                    if ($this->_checkrs($rs, $type)) {
                        if ($rs['TtlRecords'] == 1) {
                            $rs['Record'] = array($rs['Record']);
                        }

                        $this->cache->set($ckey, $rs);

                        return $rs;
                    } else {
                        return false;
                    }
                }
                return $rs;
            }
        } else {
            $rs = $this->socket['passport']->senddata($type, $data);
            $rs = xmltoarray($rs);


            if ($this->_checkrs($rs, $type)) {
                if ($data['StartNo'] == -1) {

                    return $rs;
                }


                if ($rs['RetRecords'] > 0) {
                    if ($rs['TtlRecords'] == 1) {
                        $rs['Record'] = array($rs['Record']);
                    }

                    return $rs;
                }
                return false;
            }

            return false;
        }
    }

    /*
      --|增加黑名单
     */

    function addBlackUser($data) {

        //$type = 'B030';
        $type = 'U110';
        $data['Type'] = isset($data['Type']) ? $data['Type'] : 1;

        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($rs['Code'] == '00' || $this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1059');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $this->cache->delete($ckey);

                //$ckey_black为各用户所有黑名单缓存key
                $ckey_black = config_item('K1075');
                $ckey_black = str_replace('{userid}', $data['UserID'], $ckey_black);
                $this->cache->delete($ckey_black);

                $ckey_black = config_item('K2075');
                $ckey_black = str_replace('{userid}', $data['UserID'], $ckey_black);
                $this->cache->delete($ckey_black);

                $ckey = config_item('K2076');
                $ckey = str_replace('{userid}', $data['UserID'], $ckey);
                $this->cache->delete($ckey);
            }
            return $rs;
        }
        return false;
    }

    /*
      --|删除黑名单
     */

    function delBlackUser($data) {
        $type = 'U111';
        $rs = $this->socket['passport']->senddata($type, $data);
        $rs = xmltoarray($rs);

        if ($this->_checkrs($rs, $type)) {
            if (ISCACHE) {
                $ckey = config_item('K1059');
                $ckey = str_replace('{MemberID}', $data['UserID'], $ckey);
                $this->cache->delete($ckey);

                //$ckey_black为各用户所有黑名单缓存key
                $ckey_black = config_item('K1075');
                $ckey_black = str_replace('{userid}', $data['UserID'], $ckey_black);
                $this->cache->delete($ckey_black);

                $ckey_black = config_item('K2075');
                $ckey_black = str_replace('{userid}', $data['UserID'], $ckey_black);
                $this->cache->delete($ckey_black);

                $ckey = config_item('K2076');
                $ckey = str_replace('{userid}', $data['UserID'], $ckey);
                $this->cache->delete($ckey);
            }
            return $rs;
        }
        return false;
    }

}

//end class
?>